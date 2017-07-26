<?php

class RssShell extends AppShell {
    public $uses = array('Feed', 'Item');

    public function update() {

    }


    public function add() {
        $url = $this->args[0];
        $category = $this->args[1];

        $this->params;
        $rss = new SimpleXMLElement(file_get_contents($url));

        $newFeedData = array(
            'url' => $url,
            'title' => $rss->channel->title[0],
            'last_update' => DateTime::createFromFormat(
                'D, j M Y G:i:s O',
                $rss->channel->lastBuildDate)
                ->format('Y-m-d H:i:s'),
            'category' => $category
        );

        $feed = $this->Feed->find('first', array(
            'conditions' => array(
                'Feed.url' => $newFeedData['url'],
                'Feed.title' => $newFeedData['title'],
                'Feed.category' => $newFeedData['category'],
            )
        ));



        if (empty($feed)) {
            $this->Feed->create();
            $newFeed = $this->Feed->save($newFeedData);
            $items = $this->getItems($rss, $this->Feed->id);
            if (!empty($newFeed)) {
                $this->Feed->Items->saveMany($items);
            }
            $this->out('Saved feed with ' . count($items) . ' items.');
        } else {
            //Check if feed has been updated
            if ($feed['Feed']['last_update'] < $newFeedData['last_update']) {
                $this->out($feed['Feed']['last_update'] . '<' . $newFeedData['last_update']);
                $items = $this->getItems($rss, $feed['Feed']['id']);
                $this->Feed->set(array(
                    'id' => $feed['Feed']['id'],
                    'last_update' => $newFeedData['last_update']
                ));
                $this->Feed->save();
                $this->Feed->Items->saveMany($items);
                $this->out('Updated feed with ' . count($items) . ' items.');
            } else {
                $this->out('No new entries.');
            }
        }

    }

    public function listFeeds($feeds = null) {
        $feeds = $feeds ? $feeds : $this->Feed->find('all');
        $this->out('Current feeds (' . count($feeds) . '): ');
        foreach ($feeds as $feed) {
            $this->out('Title: ' . $feed['Feed']['title']);
            $this->out('URL: ' . $feed['Feed']['url']);
            $this->out('Updated: ' . $feed['Feed']['last_update']);
            $this->out('Category: ' . $feed['Feed']['category']);
            $this->out('Items: ' . count($feed['Items']) );
            $this->out();
        }
    }

    private function getItems($xmlObject, $feedId) {
        $items = array();
        foreach ($xmlObject->channel->item as $item) {
            array_push($items, array(
                'title' => $item->title,
                'link' => $item->link,
                'description' => $item->description,
                'published' => DateTime::createFromFormat(
                    'D, j M Y G:i:s O',
                    $item->pubDate)
                    ->format('Y-m-d H:i:s'),
                'feed_id' => $feedId
            ));
        }
        return $items;
    }

    public function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser->addArguments(array(
            'url' => array('help' => 'RSS feed url'),
            'category' => array('help' => 'RSS feed category')
        ));
        return $parser;
    }
}