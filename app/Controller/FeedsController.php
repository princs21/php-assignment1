<?php

class FeedsController extends AppController {
    public function index() {
        $category = array_key_exists('category', $this->request->query) ?
            $this->request->query['category'] : null;
        $feeds = array();
        if ($category) {
            $feeds = $this->Feed->find('all', array(
                'conditions' => array(
                    'Feed.category' => $category,
                )
            ));
        } else {
            $feeds = $this->Feed->find('all');
        }

        // Workaround for inaccessible model methods
        for ($i = 0; $i < sizeof($feeds); $i++) {
            $feed = $feeds[$i];
            $recentArticle = $this->Feed->Items->find('first', array(
                'order' => array('Items.published DESC'),
                'conditions' => array('Items.feed_id =' => $feed['Feed']['id'])
            ));
            if (!empty($recentArticle)) {
                //Attaching recent article to feed
                $feeds[$i]['Feed']['recentArticle']= $recentArticle['Items'];
            }
        }

        $this->set('feeds', $feeds);
        $this->set('categories', array_unique($this->Feed->find('list', array(
            'fields' => array('Feed.category')
        ))));
        $this->set('category', $category);
    }
}