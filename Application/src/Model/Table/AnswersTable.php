<?php

declare(strict_types=1);

namespace Application\Model\Table;

use Application\Model\Entity\AnswerEntity;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Expression;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Filter;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\I18n;
use Laminas\InputFilter;
use Laminas\Validator;

class AnswersTable extends AbstractTableGateway
{
    protected $table = 'app_answers';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchAnswersByQuizId(int $quizId)
    {
        $sqlQuery = $this->sql->select()->where([$this->table.'.quiz_id' => $quizId]);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $result   = $sqlStmt->execute();

        $method = new ClassMethodsHydrator();
        $entity = new AnswerEntity();

        $resultSet = new HydratingResultSet($method, $entity);
        $resultSet->initialize($result);

        return $resultSet;
    }

    public function getAnswerFormFilter()
    {
        $inputFilter = new InputFilter\InputFilter();
        $factory = new InputFilter\Factory();

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
                                'adapter' => $this->adapter,
                            ],
                        ],
                    ],
                ]
            )
        );

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'answer_id',
                    'required'   => true,
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
                                'table' => $this->table,
                                'field' => 'answer_id',
                                'adapter' => $this->adapter,
                            ],
                        ],
                    ],
                ]
            )
        );

        return $inputFilter;
    }

    public function insertAnswer(string $answer, int $quizId)
    {
        $values = [
            'quiz_id' => $quizId,
            'answer'  => $answer,
        ];

        $sqlQuery = $this->sql->insert()->values($values);
        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function updateTally(int $answerId, int $quizId)
    {
        $sqlQuery = $this->sql->update()
            ->set(['tally' => new Expression('tally + 1')])
            ->where(['answer_id' => $answerId])
            ->where(['quiz_id' => $quizId]);

        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
        return $sqlStmt->execute();
    }
}
