<?php

class Item extends AppModel {
    public $useTable = 'items';
    public $belongsTo = 'Feed';

    protected $_schema = array(
        'title' => array(
            'type' => 'string',
            'length' => 255
        ),
        'link' => array(
            'type' => 'string',
            'length' => 255
        ),
        'description' => array(
            'type' => 'text'
        ),
        'published' => array(
            'type' => 'datetime'
        )
    );

    //TODO add validation
}