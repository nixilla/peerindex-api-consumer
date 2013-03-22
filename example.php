<?php

// make sure that you run `composer install` before executing this script
// run it with php ./example.php

require_once './vendor/autoload.php';

$key = 'YOUR_KEY_HERE';

$client = new Buzz\Browser(new Buzz\Client\Curl());
$consumer = new Peerindex\Api\Consumer($client, $key);

$actor = $consumer->getActor('rasmus', Peerindex\Api\Actor::TWITTER_SCREEN_NAME);

printf("My id: %s\n", $actor->getId());

echo sprintf("Actor is%s loaded\n", $actor->isLoaded() ? '' : ' not');

if(count($actor->getTopics()))
{
    echo "My Topics:\n";
    foreach($actor->getTopics() as $key => $group)
        if(is_array($group))
            foreach($group as $topic)
                printf("\tGroup: %s, topic: %s (%s)\n", $key, $topic['name'], $topic['topic_score']);
}

printf("\nGraph influences: (%s)\n\n", count($actor['graph']['influences']));

foreach($actor['graph']['influences'] as $someone)
    printf("%s\n",$someone->getId());

printf("\nGraph influenced_by: (%s)\n\n", count($actor['graph']['influenced_by']));

foreach($actor['graph']['influenced_by'] as $someone)
    printf("%s\n",$someone->getId());
