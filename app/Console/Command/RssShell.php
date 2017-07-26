<?php

class RssShell extends AppShell {
    public $uses = array('Feed', 'Item');


    //Runs when command is called
    public function main() {
        $url = array_key_exists(0, $this->args) ? $this->args[0] : null;
        $category = array_key_exists(1, $this->args) ? $this->args[1] : 'default';

        if (!$url){
            $this->out('<warning>No URL given. Updating existing feeds</warning>');
            $this->updateAllFeeds();
            return;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->out('<error>First argument is not a valid URL.</error>');
            $this->out('<warning>Usage: php app/Console/cake.php [rss_url] [category]</warning>');
            return;
        }

        $this->saveFeed($url, $category);

    }

    private function updateAllFeeds() {
        $feeds = $this->Feed->find('all');
        $this->out('Current feeds (' . count($feeds) . '): ');
        foreach ($feeds as $feed) {
            $this->saveFeed($feed['Feed']['url'], $feed['Feed']['category']);
        }
    }

    //Save or update feed
    private function saveFeed($url, $category) {
        try {
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



            if (empty($feed)) { // New feed
                $this->Feed->create();
                $newFeed = $this->Feed->save($newFeedData);
                $items = $this->getItems($rss, $this->Feed->id);
                if (!empty($newFeed)) {
                    $this->Feed->Items->saveMany($items);
                }
                $this->out('Saved feed with ' . count($items) . ' items.');
            } else { //feed already exists
                //Check if feed has been updated
                if ($feed['Feed']['last_update'] < $newFeedData['last_update']) {
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
        } catch (Exception $exc) {
            $this->out('<error>Unexpected error happened. Try changing variables or try again later.</error>');
            $this->out('<warning>' . $exc->getMessage() . '</warning>', 1, Shell::VERBOSE);
        }
    }

    //Get feed entries
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

    //Command help config
    public function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser->addArguments(array(
            'url' => array('help' => 'RSS feed url'),
            'category' => array('help' => 'RSS feed category')
        ));
        $parser->description(array(
            'Command used for adding/updating RSS feeds',
            'Run with no arguments to update all feeds'
        ));
        return $parser;
    }
}