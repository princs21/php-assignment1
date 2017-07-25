<?php

class Feed extends AppModel {
    public $useTable = 'feeds';

    public $hasMany = array(
        'Items' => array(
            'className' => 'Item',
            'dependent' => true
        )
    );

    protected $_schema = array(
        'url' => array(
            'type' => 'string',
            'length' => 255
        ),
        'title' => array(
            'type' => 'string',
            'length' => 45
        ),
        'last_update' => array(
            'type' => 'datetime'
        ),
        'category' => array(
            'type' => 'string',
            'length' => 255
        )
    );
}