<?php

class FeedsController extends AppController {
    public function index($category = null) {
        if ($category) {
            $this->set('feeds', $this->Feed->find('all', array(
                'conditions' => array(
                    'Feed.category' => $category,
                )
            )));
        } else {
            $this->set('feeds', $this->Feed->find('all'));
        }

    }
}