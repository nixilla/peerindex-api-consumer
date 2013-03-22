<?php

// make sure that you run `composer install` before executing this script
// run it with php ./example.php

require_once './vendor/autoload.php';

//$key = 'YOUR_KEY_HERE';
$key = 'ggh3dzhktmhcprr9quzz7fyu';
$client = new Buzz\Browser(new Buzz\Client\Curl());
$consumer = new Peerindex\Api\Consumer($client, $key);

try
{
    $actor = $consumer->getActor('rasmus', Peerindex\Api\Actor::TWITTER_SCREEN_NAME);
}
catch (\RuntimeException $e)
{
    exit($e->getMessage()."\n");
}

printf("My id: %s\n", $actor->getId());

echo sprintf("Actor is%s loaded\n", $actor->isLoaded() ? '' : ' not');

//echo "\n";
//print_r($actor->toArray());
//echo "\n";
//exit();


if(count($actor->getTopics()))
{
    echo "My Topics:\n";
    foreach($actor->getTopics() as $key => $group)
        if(is_array($group))
            foreach($group as $topic)
                printf("\tGroup: %s, topic: %s (%s)\n", $key, $topic['name'], $topic['topic_score']);
}

printf("\nGraph: (%s)\n\n", count($actor['graph']['influences']));

foreach($actor['graph']['influences'] as $someone)
{
    if( ! $someone->isLoaded())
    {

        echo "\n";
        print_r($someone->toArray());
        echo "\n";
//        $someone = $consumer->getActor($someone);
//        echo sprintf(
//            "Peerindex for user %s: %s\n",
//            $someone->getId(),
//            $someone->getPeerindex()
//        );
//        if(count($someone->getTopics()))
//        {
//            echo "Topics:\n";
//            foreach($actor->getTopics() as $key => $group)
//                if(is_array($group))
//                    foreach($group as $topic)
//                        printf("\tGroup: %s, topic: %s (%s)\n", $key, $topic['name'], $topic['topic_score']);
//        }
    }
}
//
//printf("\nnmyInfluencees (%s)\n\n", $actor['influence']['myInfluenceesCount']);
//
//foreach($actor['influence']['myInfluencees'] as $someone)
//{
//    if( ! $someone->isLoaded())
//    {
//        $someone = $consumer->getActor($someone);
//        echo sprintf(
//            "Peerindex for user %s: %s, no of topics: %s, influencers %s, influencees: %s\n",
//            $someone['nick'],
//            $someone->getPeerindex(),
//            count($someone->getTopics())
//        );
//        if(count($someone->getTopics()))
//        {
//            echo "Topics:\n";
//            foreach($someone->getTopics() as $topic)
//                printf("\t%s\n", $topic['displayName']);
//        }
//
//    }
//}

// you can anything that comes from API using ArrayAccess

//print_r($actor->toArray());
//echo "\n";