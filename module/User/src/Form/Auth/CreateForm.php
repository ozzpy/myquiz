<?php

declare(strict_types=1);

namespace User\Form\Auth;

use Laminas\Form\Element;
use Laminas\Form\Form;

class CreateForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('new_account');
        $this->setAttribute('method', 'post');

        $this->add([
            'type' => Element\Text::class,
            'name' => 'username',
            'options' => [
                'label' => 'Username'
            ],
            'attributes' => [
                'required' => true,
                'maxlength' => 25,
                'title' => 'Username must consist of alphanumeric characters only',
                'class' => 'form-control',
                'placeholder' => 'Enter Your Username',
                'pattern' => '^[a-zA-Z0-9]+$'
            ]
        ]);

        $this->add([
            'type' => Element\Select::class,
            'name' => 'gender',
            'options' => [
                'label' => 'Gender',
                'empty_option' => 'Select...',
                'value_options' => [
                    'Female' => 'Female',
                    'Male' => 'Male',
                    'Other' => 'Other'
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'custom-select'
            ]
        ]);

        $this->add(
            [
                'type' => Element\Email::class,
                'name' => 'email',
                'options' => [
                    'label' => 'Email Address',
                ],
                'attributes' => [
                    'required'    => true,
                    'maxlength'   => 128,
                    'title'       => 'Provide a valid and working email address',
                    'class'       => 'form-control',
                    'placeholder' => 'Enter Your Email Address',
                    'pattern'     => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
                ],
            ]
        );

        $this->add([
            'type' => Element\DateSelect::class,
            'name' => 'birthday',
            'options' => [
                'label' => 'Select Your Date of Birth',
                'create_empty_option' => true,
                'max_year' => date('Y') - 16,
                'day_attributes' => [
                    'class' => 'custom-select w-30 mr-2 ml-2',
                    'id' => 'day'
                ],
                'month_attributes' => [
                    'class' => 'custom-select w-30',
                    'id' => 'month'
                ],
                'year_attributes' => [
                    'class' => 'custom-select w-30',
                    'id' => 'year'
                ],
                'render_delimiters' => false,
            ],
            'attributes' => [
                'required' => true
            ]
        ]);

        $this->add([
            'type' => Element\Password::class,
            'name' => 'password',
            'options' => [
                'label' => 'Password'
            ],
            'attributes' => [
                'required' => true,
                'maxlength' => 25,
                'title' => 'Password must have between 8 and 25 characters',
                'class' => 'form-control',
                'placeholder' => 'Enter Your Password'
            ]
        ]);

        $this->add([
            'type' => Element\Password::class,
            'name' => 'confirm_password',
            'options' => [
                'label' => 'Verify Password'
            ],
            'attributes' => [
                'required' => true,
                'maxlength' => 25,
                'title' => 'Password must match that provided above',
                'class' => 'form-control',
                'placeholder' => 'Enter Your Password Again'
            ]
        ]);

        $this->add(
            [
                'type' => Element\Csrf::class,
                'name' => 'csrf',
                'options' => [
                    'csrf_options' => [
                        'timeout' => 1440
                    ]
                ]
            ]
        );

        $this->add([
            'type' => Element\Submit::class,
            'name' => 'create_account',
            'attributes' => [
                'value' => 'Create Account',
                'class' => 'btn btn-primary btn-lg btn-block'
            ]
        ]);
    }
}
