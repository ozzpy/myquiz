<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Application\Model\Entity\CommentEntity;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Filter;
use Laminas\I18n;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\InputFilter;
use Laminas\Paginator\Adapter\LaminasDb\DbSelect;
use Laminas\Paginator\Paginator;
use Laminas\Validator;

class CommentsTable extends AbstractTableGateway
{
    protected $table = 'app_comments';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function deleteComment(int $commentId, $authId)
    {
        $sqlQuery = $this->sql->delete()
            ->where(['auth_id' => $authId])
            ->where(['comment_id' => $commentId]);

        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
        return $sqlStmt->execute();
    }

    public function fetchCommentById(int $commentId)
    {
        $sqlQuery = $this->sql->select()
            ->join('user_auth', 'user_auth.auth_id='.$this->table.'.auth_id',
                   ['username', 'picture'])
            ->where(['comment_id' => $commentId]);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $result   = $sqlStmt->execute()->current();

        if (!$result) {
            return null;
        }

        $method = new ClassMethodsHydrator();
        $entity = new CommentEntity();
        $method->hydrate($result, $entity);

        return $entity;
    }

    public function fetchCommentsByQuizId(int $quizId, bool $paginate = false)
    {
        $sqlQuery = $this->sql->select()
            ->join(
                'user_auth',
                'user_auth.auth_id=' . $this->table . '.auth_id',
                ['username', 'picture']
            )
            ->where(['quiz_id' => $quizId])
            ->order('created DESC');

        $entity = new CommentEntity();
        $method = new ClassMethodsHydrator();
        $resultSet = new HydratingResultSet($method, $entity);

        if ($paginate) {
            $paginatorAdapter = new DbSelect(
                $sqlQuery,
                $this->adapter,
                $resultSet
            );

            return new Paginator($paginatorAdapter);
        }

        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $results = $sqlStmt->execute();
        $resultSet->initialize($results);

        return $resultSet;
    }

    public function getCreateFormFilter()
    {
        $inputFilter = new InputFilter\InputFilter();
        $factory = new InputFilter\Factory();

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'comment',
                    'required' => true,
                    'filters' => [
                        ['name' => Filter\StripTags::class],
                        ['name' => Filter\StringTrim::class],
                    ],
                    'validators' => [
                        ['name' => Validator\NotEmpty::class],
                        [
                            'name' => Validator\StringLength::class,
                            'options' => [
                                'encoding' => 'UTF-8',
                                'min' => 2,
                                'max' => 500,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Comment must have at least 2 characters',
                                    Validator\StringLength::TOO_LONG => 'Comment must have at most 500 characters'
                                ]
                            ],
                        ],
                    ],
                ]
            )
        );

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'auth_id',
                    'required' => true,
                    'filters' => [
                        ['name' => Filter\StripTags::class],
                        ['name' => Filter\StringTrim::class],
                        ['name' => Filter\ToInt::class],
                    ],
                    'validators' => [
                        ['name' => Validator\NotEmpty::class],
                        ['name' => I18n\Validator\IsInt::class],
                        [
                            'name' => Validator\Db\RecordExists::class,
                            'options' => [
                                'table' => 'user_auth',
                                'field' => 'auth_id',
                                'adapter' => $this->adapter
                            ]
                        ]
                    ],
                ]
            )
        );

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'quiz_id',
                    'required' => true,
                    'filters' => [
                        ['name' => Filter\StripTags::class],
                        ['name' => Filter\StringTrim::class],
                        ['name' => Filter\ToInt::class],
                    ],
                    'validators' => [
                        ['name' => Validator\NotEmpty::class],
                        ['name' => I18n\Validator\IsInt::class],
                        [
                            'name' => Validator\Db\RecordExists::class,
                            'options' => [
                                'table' => 'app_quizzes',
                                'field' => 'quiz_id',
                                'adapter' => $this->adapter
                            ]
                        ]
                    ],
                ]
            )
        );

        return $inputFilter;
    }

    public function insertComment(array $data)
    {
        $values = [
            'comment' => $data['comment'],
            'auth_id' => $data['auth_id'],
            'quiz_id' => $data['quiz_id'],
            'created' => date('Y-m-d H:i:s')
        ];

        $sqlQuery = $this->sql->insert()->values($values);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function updateComment(array $data)
    {
        $values = [
            'comment' => $data['comment'],
            'auth_id' => $data['auth_id']
        ];

        $sqlQuery = $this->sql->update()
            ->set($values)
            ->where(['quiz_id' => (int)$data['quiz_id']]);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }
}
