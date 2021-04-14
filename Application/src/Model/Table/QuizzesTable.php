<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Application\Model\Entity\QuizEntity;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Filter;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\I18n;
use Laminas\InputFilter;
use Laminas\Paginator\Adapter\LaminasDb\DbSelect;
use Laminas\Paginator\Paginator;
use Laminas\Validator;

class QuizzesTable extends AbstractTableGateway
{
    protected $table = 'app_quizzes';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchAllQuizzes(bool $paginate = false)
    {
        $sqlQuery = $this->sql->select()
            ->join('app_categories', 'app_categories.category_id='.$this->table.'.category_id', 'category')
            ->join(
                'user_auth',
                'user_auth.auth_id=' . $this->table . '.auth_id',
                ['username', 'picture']
            )
            ->order($this->table.'.created DESC');

        $entity    = new QuizEntity();
        $method    = new ClassMethodsHydrator();
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
        $result  = $sqlStmt->execute();

        $resultSet->initialize($result);

        return $resultSet;
    }

    public function fetchQuizzesByAuthId(int $authId)
    {
        $sqlQuery = $this->sql->select()
            ->join(
                'app_categories',
                'app_categories.category_id=' . $this->table . '.category_id',
                'category'
            )
            ->join(
                'user_auth',
                'user_auth.auth_id=' . $this->table . '.auth_id'
            )
            ->where([$this->table . '.auth_id' => $authId])
            ->order($this->table.'.created ASC');

        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $result  = $sqlStmt->execute();

        $method = new ClassMethodsHydrator();
        $entity = new QuizEntity();

        $resultSet = new HydratingResultSet($method, $entity);
        $resultSet->initialize($result);

        return $resultSet;
    }

    public function fetchQuizById(int $quizId)
    {
        $sqlQuery = $this->sql->select()
            ->join(
                'app_categories',
                'app_categories.category_id=' . $this->table . '.category_id',
                ['category_id', 'category']
            )
            ->join(
                'user_auth',
                'user_auth.auth_id=' . $this->table . '.auth_id',
                ['username', 'picture']
            )
            ->where([$this->table . '.quiz_id' => $quizId]);

        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $handler = $sqlStmt->execute()->current();

        if (!$handler) {
            return null;
        }

        $method = new ClassMethodsHydrator();
        $entity = new QuizEntity();

        $method->hydrate($handler, $entity);

        return $entity;
    }

    public function getCreateFormFilter()
    {
        $inputFilter = new InputFilter\InputFilter();
        $factory = new InputFilter\Factory();

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'title',
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
                                'max' => 100,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Quiz title must have at least 2 characters',
                                    Validator\StringLength::TOO_LONG => 'Quiz title must have at most 100 characters'
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
                    'name' => 'category_id',
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
                                'table' => 'app_categories',
                                'field' => 'category_id',
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
                    'name' => 'timeout',
                    'required' => true,
                    'filters' => [
                        ['name' => Filter\StripTags::class],
                        ['name' => Filter\StringTrim::class],
                    ],
                    'validators' => [
                        ['name' => Validator\NotEmpty::class],
                        [
                            'name' => Validator\InArray::class,
                            'options' => [
                                'haystack' => ['1 day', '3 days', '7 days']
                            ]
                        ]
                    ],
                ]
            )
        );

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'question',
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
                                'min' => 4,
                                'max' => 300,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Quiz question must have at least 4 characters',
                                    Validator\StringLength::TOO_LONG => 'Quiz question must have at most 300 characters'
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
                    'name' => 'allow',
                    'required' => true,
                    'filters' => [
                        ['name' => Filter\StripTags::class],
                        ['name' => Filter\StringTrim::class],
                        ['name' => Filter\ToInt::class],
                    ],
                    'validators' => [
                        ['name' => Validator\NotEmpty::class],
                        ['name' => I18n\Validator\IsInt::class],
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

        return $inputFilter;
    }

    public function insertQuiz(array $data)
    {
        $values = [
            'category_id' => $data['category_id'],
            'auth_id'     => $data['auth_id'],
            'title'       => $data['title'],
            'question'    => $data['question'],
            'allow'       => $data['allow'],
            'timeout'     => date('Y-m-d H:i:s', strtotime("+" . $data['timeout'])),
            'created'     => date('Y-m-d H:i:s')
        ];

        $sqlQuery = $this->sql->insert()->values($values);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function updateComments(int $quizId)
    {
        $sqlQuery = $this->sql->update()
            ->set(['comments' => new Expression('comments + 1')])
            ->where(['quiz_id' => $quizId]);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function updateTotal(int $quizId)
    {
        $sqlQuery = $this->sql->update()
            ->set(['total' => new Expression('total + 1')])
            ->where(['quiz_id' => $quizId]);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function updateViews(int $quizId)
    {
        $sqlQuery = $this->sql->update()
            ->set(['views' => new Expression('views + 1')])
            ->where(['quiz_id' => $quizId]);

        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }
}

