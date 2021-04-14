<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Element;
use Laminas\Form\Form;

class EmailForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('');
        $this->setAttribute('method', 'post');

        $this->add(
            [
                'type'       => Element\Email::class,
                'name'       => 'current_email',
                'options'    => [
                    'label' => 'Current Email Address',
                ],
                'attributes' => [
                    'required'  => true,
                    'maxlength' => 128,
                    'title'     => 'My account\'s current email address',
                    'class'     => 'form-control',
                    'readonly'  => true,
                    'pattern'   => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Email::class,
                'name'       => 'new_email',
                'options'    => [
                    'label' => 'New Email Address',
                ],
                'attributes' => [
                    'required'    => true,
                    'maxlength'   => 128,
                    'title'       => 'Provide a valid and working email address',
                    'class'       => 'form-control',
                    'placeholder' => 'Enter Your New Email Address',
                    'pattern'     => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Email::class,
                'name'       => 'confirm_new_email',
                'options'    => [
                    'label' => 'Verify New Email Address',
                ],
                'attributes' => [
                    'required'    => true,
                    'maxlength'   => 128,
                    'title'       => 'Email address must match that provided above',
                    'class'       => 'form-control',
                    'placeholder' => 'Enter Your New Email Address Again',
                    'pattern'     => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
                ],
            ]
        );

        $this->add(
            [
                'type'    => Element\Csrf::class,
                'name'    => 'csrf',
                'options' => [
                    'csrf_options' => [
                        'timeout' => 1440,
                    ],
                ],
            ]
        );

        $this->add(
            [
                'type'       => Element\Submit::class,
                'name'       => 'update_email',
                'attributes' => [
                    'value' => 'Save Changes',
                    'class' => 'btn btn-primary btn-lg btn-block',
                ],
            ]
        );
    }
}
