<?php

class RssShell extends AppShell {
    public $uses = array('Feed', 'Item');

    public function update() {

    }

    public function add() {
        $url = $this->args[0];
        $category = $this->args[1];

        $rss = new SimpleXMLElement(file_get_contents($url));



        //Converting last updated time from RFC 822 to database format
        //TODO timezone conversion to local
        //TODO do not duplicate feeds (update last updated)
        $lastUpdate = DateTime::createFromFormat(
            'D, j M Y G:i:s O',
            $rss->channel->lastBuildDate)
            ->format('Y-m-d H:i:s');

        $this->Feed->create();
        $newFeed = $this->Feed->save(array(
            'url' => $url,
            'title' => $rss->channel->title[0],
            'last_update' => $lastUpdate,
            'category' => $category
        ));

        $items = array();
        foreach ($rss->channel->item as $item) {
            //TODO don't save if already exists
            array_push($items, array(
                'title' => $item->title,
                'link' => $item->link,
                'description' => $item->description,
                'published' => DateTime::createFromFormat(
                    'D, j M Y G:i:s O',
                    $item->pubDate)
                    ->format('Y-m-d H:i:s'),
                'feed_id' => $this->Feed->id
            ));
        }

        if (!empty($newFeed)) {
            $this->Feed->Items->saveMany($items);
        }

//        $this->out(print_r($rss, true));
        $this->listFeeds($newFeed);
    }

    public function listFeeds($feeds = null) {
        $feeds = $feeds ? $feeds : $this->Feed->find('all');
        $this->out('Current feeds (' . count($feeds) . '): ');
        foreach ($feeds as $feed) {
            $this->out(print_r($feed, true));
        }
    }
}