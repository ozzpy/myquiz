<?php

declare(strict_types=1);

namespace Application\Form\Comment;

use Laminas\Form\Element;
use Laminas\Form\Form;

class CreateForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('new_comment');
        $this->setAttribute('method', 'post');

        $this->add(
            [
                'type' => Element\Textarea::class,
                'name' => 'comment',
                'options' => [
                    'label' => 'Leave a comment',
                ],
                'attributes' => [
                    'required'    => true,
                    'row'         => 3,
                    'maxlength'   => 500,
                    'title'       => 'Provide your comment',
                    'class'       => 'form-control',
                    'placeholder' => 'Type a comment...',
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Hidden::class,
                'name' => 'quiz_id',
            ]
        );

        $this->add(
            [
                'type' => Element\Hidden::class,
                'name' => 'auth_id',
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
                'name' => 'post_comment',
                'attributes' => [
                    'value' => 'Post Comment',
                    'class' => 'btn btn-primary btn-lg btn-block',
                ],
            ]
        );
    }
}
