<?php

use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\TextField;

class IrmaPage extends Page
{
    private static $db = [
        'IRMAAttribute' => 'Varchar',
        'IRMAAttributeValue' => 'Varchar',
        'IRMAWarning' => 'HTMLText',
        'IRMADenied' => 'HTMLText'
    ];

    private static $has_one = [];


    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab('Root.IRMA', [
            TextField::create('IRMAAttribute', 'Attribute'),
            TextField::create('IRMAAttributeValue', 'Required value'),
            HTMLEditorField::create('IRMAWarning', 'Access Warning'),
            HTMLEditorField::create('IRMADenied', 'Access denied message')
        ]);

        return $fields;
    }
}
