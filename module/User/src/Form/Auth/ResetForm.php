<?php

declare(strict_types=1);

namespace User\Form\Auth;

use Laminas\Form\Element;
use Laminas\Form\Form;

class ResetForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('change_password');
        $this->setAttribute('method', 'post');

        $this->add(
            [
                'type' => Element\Password::class,
                'name' => 'new_password',
                'options' => [
                    'label' => 'New Password',
                ],
                'attributes' => [
                    'required' => true,
                    'maxlength' => 25,
                    'title' => 'Password must have between 8 and 25 characters',
                    'class' => 'form-control',
                    'placeholder' => 'Enter Your New Password',
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Password::class,
                'name' => 'confirm_new_password',
                'options' => [
                    'label' => 'Verify New Password',
                ],
                'attributes' => [
                    'required' => true,
                    'maxlength' => 25,
                    'title' => 'Password must match that provided above',
                    'class' => 'form-control',
                    'placeholder' => 'Enter Your New Password Again',
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Csrf::class,
                'name' => 'csrf',
                'options' => [
                    'csrf_options' => [
                        'timeout' => 1440,
                    ],
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Submit::class,
                'name' => 'reset_password',
                'attributes' => [
                    'value' => 'Reset Password',
                    'class' => 'btn btn-primary btn-lg btn-block',
                ],
            ]
        );
    }
}
