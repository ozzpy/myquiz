<?php

declare(strict_types=1);

namespace User\Form\Setting;

use Laminas\Form\Element;
use Laminas\Form\Form;

class UploadForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('upload_avatar');
        $this->setAttributes(
            [
                'method'  => 'post',
                'enctype' => 'multipart/form-data',
            ]
        );

        $this->add(
            [
                'type' => Element\File::class,
                'name' => 'picture',
                'options' => [
                    'label' => 'Select an image file to upload',
                    'label_attributes' => [
                        'class' => 'custom-file-label',
                    ],
                ],
                'attributes' => [
                    'required' => true,
                    'class'    => 'custom-file-input',
                    'id'       => 'picture',
                    'multiple' => false,
                ],
            ]
        );

        $this->add(
            [
                'type' => Element\Submit::class,
                'name' => 'upload_picture',
                'attributes' => [
                    'value' => 'Submit',
                    'class' => 'btn btn-secondary',
                ],
            ]
        );
    }
}
