<?php

declare(strict_types=1);

namespace Application\Form\Quiz;

use Application\Model\Table\CategoriesTable;
use Laminas\Form\Element;
use Laminas\Form\Form;

class CreateForm extends Form
{
    public function __construct(CategoriesTable $categoriesTable)
    {
        parent::__construct('new_quiz');
        $this->setAttribute('method', 'post');

        $this->add(
            [
                'type' => Element\Text::class,
                'name' => 'title',
                'options' => [
                    'label' => 'Quiz Title',
                ],
                'attributes' => [
                    'required'    => true,
                    'maxlength'   => 100,
                    'title'       => 'Provide a title',
                    'class'       => 'form-control',
                    'placeholder' => 'Enter a title',
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Select::class,
                'name' => 'category_id',
                'options' => [
                    'label'  => 'Quiz Category',
                    'empty_option'  => 'Select...',
                    'value_options' =>
                        $categoriesTable->fetchAllCategories(),
                ],
                'attributes' => [
                    'required' => true,
                    'class'    => 'custom-select',
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Select::class,
                'name' => 'timeout',
                'options' => [
                    'label' => 'Quiz Duration',
                    'empty_option'  => 'Select...',
                    'value_options' => [
                        '1 day'  => '1 day',
                        '3 days' => '3 days',
                        '7 days' => '7 days',
                    ],
                ],
                'attributes' => [
                    'required' => true,
                    'class'    => 'custom-select',
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Textarea::class,
                'name' => 'question',
                'options' => [
                    'label' => 'Question',
                ],
                'attributes' => [
                    'required'    => true,
                    'rows'        => 3,
                    'maxlength'   => 300,
                    'title'       => 'Provide a question',
                    'class'       => 'form-control',
                    'placeholder' => 'Type a question',
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Text::class,
                'name' => 'answers[]',
                'options' => [
                    'label' => 'Quiz Answers',
                ],
                'attributes' => [
                    'required'  => true,
                    'maxlength' => 100,
                    'title'     => 'Provide a possible answer',
                    'class'     => 'form-control',
                    'placeholder' => 'Enter a possible answer',
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Button::class,
                'name' => 'add_more',
                'options' => [
                    'label' => '+ Add Another Answer',
                ],
                'attributes' => [
                    'class' => 'btn btn-sm btn-secondary',
                    'id'    => 'add_more',
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Checkbox::class,
                'name' => 'allow',
                'options' => [
                    'label' => 'Allow this quiz to have comments?',
                    'label_attributes'   => [
                        'class' => 'custom-control-label',
                    ],
                    'use_hidden_element' => true,
                    'checked_value'      => 1,
                    'unchecked_value'    => 0,
                ],
                'attributes' => [
                    'value' => 1,
                    'class' => 'custom-control-input',
                ],
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
                'name' => 'post_quiz',
                'attributes' => [
                    'value' => 'Post Quiz',
                    'class' => 'btn btn-primary btn-lg btn-block',
                ],
            ]
        );
    }
}
