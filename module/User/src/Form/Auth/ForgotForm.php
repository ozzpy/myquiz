<?php

declare(strict_types=1);

namespace User\Form\Auth;

use Laminas\Captcha\Image;
use Laminas\Form\Element;
use Laminas\Form\Form;

class ForgotForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('lost_password');
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
                'type' => Element\Captcha::class,
                'name'  => 'turing',
                'options' => [
                    'label' => 'Verify that you are human?',
                    'captcha' => new Image(
                        [
                            'font'  => BASE_PATH . DS . 'addons' . DS. 'ft' . DS . 'Ubuntu-BoldItalic.ttf',
                            'fsize' => 55,
                            'wordLen' => 6,
                            'imgAlt' => 'image captcha',
                            'height' => 100,
                            'width'  => 300,
                            'dotNoiseLevel'  => 220,
                            'lineNoiseLevel' => 18,
                        ]
                    ),
                ],
                'attributes' => [
                    'required'    => true,
                    'maxlength'   => 6,
                    'title'       => 'Provide the text displayed',
                    'class'       => 'form-control w-100 mt-2',
                    'placeholder' => 'Type in characters displayed',
                    'pattern'     => '^[a-zA-Z0-9]+$',
                    'captcha'     => (new Element\Captcha())->getInputSpecification(),
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
                'name' => 'forgot_password',
                'attributes' => [
                    'value' => 'Send Message',
                    'class' => 'btn btn-primary btn-lg btn-block',
                ],
            ]
        );
    }
}
