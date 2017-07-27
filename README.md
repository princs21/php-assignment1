# RSS feed assignment (Test task for PHP developer position)

*Instructions:*

1. clone project into directory available to your web server.
2. rss feeds can be updated using command: 
   
   **php app/Console/cake.php rss [rss_url] [category]**
   
   undefined category defaults to 'default'.
   
   command called without arguments just updates all feeds.
   
   example: php app/Console/cake.php rss http://www.feedforall.com/sample.xml news
   
3. Feeds can be viewed by accessing localhost/feeds?category=<insert category here>
    example: localhost/feeds?category=news
    
    
Designed for RSS version 2.0