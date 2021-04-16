<?php

declare(strict_types=1);

namespace User\Form\Auth;

use Laminas\Form\Element;
use Laminas\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('authenticate');
        $this->setAttribute('method', 'post');

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
                    'title'       => 'Provide your account\'s email address',
                    'class'       => 'form-control',
                    'placeholder' => 'Enter Your Email Address',
                    'pattern'     => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Password::class,
                'name' => 'password',
                'options' => [
                    'label' => 'Password',
                ],
                'attributes' => [
                    'required'    => true,
                    'maxlength'   => 25,
                    'title'       => 'Provide your account\'s password',
                    'class'       => 'form-control',
                    'placeholder' => 'Enter Your Password',
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Checkbox::class,
                'name' => 'recall',
                'options' => [
                    'label' => 'Remember me?',
                    'label_attributes' => [
                        'class' => 'custom-control-label',
                    ],
                    'use_hidden_element' => true,
                    'checked_value' => 1,
                    'unchecked_value' => 0,
                ],
                'attributes' => [
                    'value' => 0,
                    'class' => 'custom-control-input',
                    'id' => 'recall'
                ],
            ]
        );

        $this->add([
            'type' => Element\Hidden::class,
            'name' => 'returnUrl'
        ]);

        $this->add(
            [
                'type' => Element\Csrf::class,
                'name' => 'csrf',
                'options' => [
                    'csrf_options' => [
                        'timeout' => 300,
                    ],
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Submit::class,
                'name' => 'account_login',
                'attributes' => [
                    'value' => 'Account Login',
                    'class' => 'btn btn-primary btn-lg btn-block',
                ],
            ]
        );
    }
}
