<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Element;
use Laminas\Form\Form;

class UsernameForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('update_username');
        $this->setAttribute('method', 'post');

        $this->add([
            'type' => Element\Text::class,
            'name' => 'current_username',
            'options' => [
                'label' => 'Current Username'
            ],
            'attributes' => [
                'required' => true,
                'maxlength' => 25,
                'title' => 'My account\'s current username',
                'class' => 'form-control',
                'readonly' => true,
                'pattern' => '^[a-zA-Z0-9]+$'
            ]
        ]);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'new_username',
            'options' => [
                'label' => 'New Username'
            ],
            'attributes' => [
                'required' => true,
                'maxlength' => 25,
                'title' => 'Username must consist of alphanumeric characters only',
                'class' => 'form-control',
                'placeholder' => 'Enter Your New Username',
                'pattern' => '^[a-zA-Z0-9]+$'
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
            'name' => 'update_username',
            'attributes' => [
                'value' => 'Save Changes',
                'class' => 'btn btn-primary btn-lg btn-block'
            ]
        ]);
    }
}
