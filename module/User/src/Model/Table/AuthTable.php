<?php

declare(strict_types=1);

namespace User\Model\Table;

use Laminas\Crypt\Password\BcryptSha;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Filter;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\I18n;
use Laminas\InputFilter;
use Laminas\Paginator\Adapter\LaminasDb\DbSelect;
use Laminas\Paginator\Paginator;
use Laminas\Validator;
use User\Model\Entity\AuthEntity;

class AuthTable extends AbstractTableGateway
{
    protected $table = 'user_auth';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchAllAccounts(bool $paginate = false)
    {
        $sqlQuery = $this->sql->select()
            ->join(
                'user_roles',
                'user_roles.role_id=' . $this->table . '.role_id',
                'role'
            )
            ->order('created ASC');

        $entity    = new AuthEntity();
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

    public function fetchAccountByAuthId(int $authId)
    {
        $sqlQuery = $this->sql->select()
            ->join(
                'user_roles',
                'user_roles.role_id=' . $this->table . '.role_id',
                'role'
            )
            ->where(['auth_id' => $authId]);

        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $result  = $sqlStmt->execute()->current();

        if (!$result) {
            return null;
        }

        $entity = new AuthEntity();
        $method = new ClassMethodsHydrator();
        $method->hydrate($result, $entity);

        return $entity;
    }

    public function fetchAccountByEmail(string $email)
    {
        $sqlQuery = $this->sql->select()
            ->join(
                'user_roles',
                'user_roles.role_id=' . $this->table . '.role_id',
                'role'
            )
            ->where(['email' => $email]);

        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $result   = $sqlStmt->execute()->current();

        if (!$result) {
            return null;
        }

        $entity = new AuthEntity();
        $method = new ClassMethodsHydrator();
        $method->hydrate($result, $entity);

        return $entity;
    }

    public function getCreateFormFilter()
    {
        $inputFilter = new InputFilter\InputFilter();
        $factory = new InputFilter\Factory();

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'username',
                    'required' => true,
                    'filters' => [
                        ['name' => Filter\StripTags::class],
                        ['name' => Filter\StringTrim::class],
                        ['name' => I18n\Filter\Alnum::class],
                    ],
                    'validators' => [
                        ['name' => Validator\NotEmpty::class],
                        [
                            'name' => I18n\Validator\Alnum::class,
                            'options' => [
                                'messages' => [
                                    I18n\Validator\Alnum::NOT_ALNUM => 'Username must consist of alphanumeric characters only'
                                ]
                            ]
                        ],
                        [
                            'name' => Validator\StringLength::class,
                            'options' => [
                                'encoding' => 'UTF-8',
                                'min' => 2,
                                'max' => 25,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Username must have at least 2 characters',
                                    Validator\StringLength::TOO_LONG => 'Username must have at most 25 characters'
                                ]
                            ],
                        ],
                        [
                            'name' => Validator\Db\NoRecordExists::class,
                            'options' => [
                                'table' => $this->table,
                                'field' => 'username',
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
                    'name' => 'gender',
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
                                'haystack' => ['Female', 'Male', 'Other']
                            ]
                        ]
                    ],
                ]
            )
        );

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'email',
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
                                'min' => 6,
                                'max' => 128,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Email address must have at least 6 characters',
                                    Validator\StringLength::TOO_LONG => 'Email address must have at most 128 characters'
                                ]
                            ],
                        ],
                        ['name' => Validator\EmailAddress::class],
                        [
                            'name' => Validator\Db\NoRecordExists::class,
                            'options' => [
                                'table' => $this->table,
                                'field' => 'email',
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
                    'name' => 'birthday',
                    'required' => true,
                    'filters' => [
                        ['name' => Filter\StripTags::class],
                        ['name' => Filter\StringTrim::class],
                        ['name' => Filter\DateSelect::class],
                    ],
                    'validators' => [
                        ['name' => Validator\NotEmpty::class],
                        [
                            'name' => Validator\Date::class,
                            'options' => [
                                'format' => 'Y-m-d'
                            ]
                        ]
                    ],
                ]
            )
        );

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'password',
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
                                'min' => 8,
                                'max' => 25,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters',
                                    Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters'
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
                    'name' => 'confirm_password',
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
                                'min' => 8,
                                'max' => 25,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters',
                                    Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters'
                                ]
                            ],
                        ],
                        [
                            'name' => Validator\Identical::class,
                            'options' => [
                                'token' => 'password',
                                'messages' => [
                                    Validator\Identical::NOT_SAME => 'Passwords do not match. Make sure they match'
                                ]
                            ]
                        ]
                    ],
                ]
            )
        );

        return $inputFilter;
    }

    public function getEmailFormFilter()
    {
        $inputFilter = new InputFilter\InputFilter;
        $factory = new InputFilter\Factory();

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'current_email',
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
                                'min' => 6,
                                'max' => 128,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Email address must have at least  c6haracters',
                                    Validator\StringLength::TOO_LONG  => 'Email address must have at most 128 characters',
                                ],
                            ],
                        ],
                        ['name' => Validator\EmailAddress::class],
                        [
                            'name'    => Validator\Db\RecordExists::class,
                            'options' => [
                                'table'   => $this->table,
                                'field'   => 'email',
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
                    'name' => 'new_email',
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
                                'min' => 6,
                                'max' => 128,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'New Email address must have at least  c6haracters',
                                    Validator\StringLength::TOO_LONG  => 'New Email address must have at most 128 characters',
                                ],
                            ],
                        ],
                        ['name' => Validator\EmailAddress::class],
                        [
                            'name' => Validator\Db\NoRecordExists::class,
                            'options' => [
                                'table' => $this->table,
                                'field' => 'email',
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
                    'name' => 'confirm_new_email',
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
                                'min' => 6,
                                'max' => 128,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Email address must have at least  c6haracters',
                                    Validator\StringLength::TOO_LONG  => 'Email address must have at most 128 characters',
                                ],
                            ],
                        ],
                        ['name' => Validator\EmailAddress::class],
                        [
                            'name' => Validator\Identical::class,
                            'options' => [
                                'token' => 'new_email',
                                'messages' => [
                                    Validator\Identical::NOT_SAME => 'New email addresses do not match. Make sure they match',
                                ],
                            ],
                        ],
                    ],
                ]
            )
        );

        return $inputFilter;
    }

    public function getLoginFormFilter()
    {
        $inputFilter = new InputFilter\InputFilter();
        $factory = new InputFilter\Factory();

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'email',
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
                                'min' => 6,
                                'max' => 128,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Email address must have at least 6 characters',
                                    Validator\StringLength::TOO_LONG => 'Email address must have at most 128 characters'
                                ]
                            ],
                        ],
                        ['name' => Validator\EmailAddress::class],
                        [
                            'name' => Validator\Db\RecordExists::class,
                            'options' => [
                                'table' => $this->table,
                                'field' => 'email',
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
                    'name' => 'password',
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
                                'min' => 8,
                                'max' => 25,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Password must have at least 8 characters',
                                    Validator\StringLength::TOO_LONG => 'Password must have at most 25 characters'
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
                    'name' => 'recall',
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
                            'name' => Validator\InArray::class,
                            'options' => [
                                'haystack' => [0, 1]
                            ]
                        ]
                    ],
                ]
            )
        );

        return $inputFilter;
    }

    public function getPasswordFormFilter()
    {
        $inputFilter = new InputFilter\InputFilter();
        $factory = new InputFilter\Factory();

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'current_password',
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
                                'min' => 8,
                                'max' => 25,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Current Password must have at least 8 characters',
                                    Validator\StringLength::TOO_LONG  => 'Current Password must have at most 25 characters',
                                ],
                            ],
                        ],
                    ],
                ]
            )
        );

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'new_password',
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
                                'min' => 8,
                                'max' => 25,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'New Password must have at least 8 characters',
                                    Validator\StringLength::TOO_LONG  => 'New Password must have at most 25 characters',
                                ],
                            ],
                        ],
                    ],
                ]
            )
        );

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'confirm_new_password',
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
                                'min' => 8,
                                'max' => 25,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Confirm New Password must have at least 8 characters',
                                    Validator\StringLength::TOO_LONG  => 'Confirm New Password must have at most 25 characters',
                                ],
                            ],
                        ],
                        [
                            'name' => Validator\Identical::class,
                            'options' => [
                                'token' => 'new_password',
                                'messages' => [
                                    Validator\Identical::NOT_SAME => 'New Passwords do not match. Make sure they match',
                                ],
                            ],
                        ],
                    ],
                ]
            )
        );

        return $inputFilter;
    }

    public function getUploadFormFilter()
    {
        $inputFilter = new InputFilter\InputFilter;
        $factory = new InputFilter\Factory();

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'picture',
                    'required' => true,
                    'validators' => [
                        ['name' => Validator\NotEmpty::class],
                        ['name' => Validator\File\IsImage::class],
                        [
                            'name'    => Validator\File\Extension::class,
                            'options' => [
                                'extension' => 'jpg, jpeg, gif, png',
                            ],
                        ],
                    ],
                    'filters'    => [
                        ['name' => Filter\StripTags::class],
                        ['name' => Filter\StringTrim::class],
                        [
                            'name'    => Filter\File\RenameUpload::class,
                            'options' => [
                                'target' => BASE_PATH . DS . 'images' . DS . 'temp' . DS . 'pix',
                                'use_upload_name'      => false,
                                'use_upload_extension' => true,
                                'overwrite'            => false,
                                'randomize'            => true,
                            ],
                        ],
                    ],
                ]
            )
        );

        return $inputFilter;
    }

    public function getUsernameFormFilter()
    {
        $inputFilter = new InputFilter\InputFilter;
        $factory = new InputFilter\Factory();

        $inputFilter->add(
            $factory->createInput(
                [
                    'name' => 'current_username',
                    'required' => true,
                    'filters' => [
                        ['name' => Filter\StripTags::class],
                        ['name' => Filter\StringTrim::class],
                        ['name' => I18n\Filter\Alnum::class],
                    ],
                    'validators' => [
                        ['name' => Validator\NotEmpty::class],
                        [
                            'name' => Validator\StringLength::class,
                            'options' => [
                                'min' => 2,
                                'max' => 25,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Username must have at least 2 characters',
                                    Validator\StringLength::TOO_LONG  => 'Username must have at most 25 characters',
                                ],
                            ],
                        ],
                        ['name' => I18n\Validator\Alnum::class],
                        [
                            'name' => Validator\Db\RecordExists::class,
                            'options' => [
                                'table' => $this->table,
                                'field' => 'username',
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
                    'name' => 'new_username',
                    'required' => true,
                    'filters' => [
                        ['name' => Filter\StripTags::class],
                        ['name' => Filter\StringTrim::class],
                        ['name' => I18n\Filter\Alnum::class],
                    ],
                    'validators' => [
                        ['name' => Validator\NotEmpty::class],
                        [
                            'name' => Validator\StringLength::class,
                            'options' => [
                                'min' => 2,
                                'max' => 25,
                                'messages' => [
                                    Validator\StringLength::TOO_SHORT => 'Username must have at least 2 characters',
                                    Validator\StringLength::TOO_LONG  => 'Username must have at most 25 characters',
                                ],
                            ],
                        ],
                        ['name' => I18n\Validator\Alnum::class],
                        [
                            'name' => Validator\Db\NoRecordExists::class,
                            'options' => [
                                'table' => $this->table,
                                'field' => 'username',
                                'adapter' => $this->adapter,
                            ],
                        ],
                    ],
                ]
            )
        );

        return $inputFilter;
    }

    public function insertAccount(array $data)
    {
        $values = [
            'username'  => ucfirst(mb_strtolower($data['username'])),
            'email'     => mb_strtolower($data['email']),
            'password'  => (new BcryptSha())->create($data['password']),
            'birthday'  => $data['birthday'],
            'gender'    => $data['gender'],
            'role_id'   => $this->assignRoleId(),
            'created'   => date('Y-m-d H:i:s')
        ];

        $sqlQuery = $this->sql->insert()->values($values);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function updateEmail(string $email, int $authId)
    {
        $values = [
            'email' => strtolower($email),
        ];

        $sqlQuery = $this->sql->update()->set($values)->where(
            ['auth_id' => $authId]
        );
        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function updatePassword(string $password, int $authId)
    {
        $values = [
            'password' => (new BcryptSha())->create($password),
        ];

        $sqlQuery = $this->sql->update()->set($values)->where(
            ['auth_id' => $authId]
        );
        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function updatePicture(string $picture, int $authId)
    {
        $values = [
            'picture' => strtolower($picture),
        ];

        $sqlQuery = $this->sql->update()->set($values)->where(
            ['auth_id' => $authId]
        );
        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    public function updateUsername(string $username, int $authId)
    {
        $values = [
            'username' => ucfirst(strtolower($username)),
        ];

        $sqlQuery = $this->sql->update()->set($values)->where(['auth_id' => $authId]);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }

    private function assignRoleId()
    {
        $sqlQuery = $this->sql->select()->where(['role_id' => 1]);
        $sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $result   = $sqlStmt->execute()->current();

        return (!$result) ? 1 : 2;
    }
}
