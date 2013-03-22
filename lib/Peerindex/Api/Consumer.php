<?php

namespace Peerindex\Api;

class Consumer
{
    const API_ENDPOINT = 'https://api.peerindex.com/1';

    private $key;
    private $client;

    public function __construct($client, $key)
    {
        $this->client = $client;
        $this->key = $key;
    }

    /**
     * @param $actor
     * @param string $type
     * @throws \RuntimeException
     * @return Actor
     */
    public function getActor($actor, $type = Actor::PEERINDEX_ID)
    {
        if (!$actor instanceof Actor)
            if ($type)
                $actor = Actor::getInstance(array($type => $actor));

        $user = array();

        $result = $this->call($actor, $type, 'extended');
        if($result->isSuccessful())
            $user = json_decode($result->getContent(), true);
        else
            throw new \RuntimeException(sprintf('API error: %s',$result->getContent()),$result->getStatusCode());

        $result = $this->call($actor, $type, 'topic');
        if($result->isSuccessful())
            $user['topics'] = json_decode($result->getContent(), true);
        else
            throw new \RuntimeException(sprintf('API error: %s',$result->getContent()),$result->getStatusCode());

        $result = $this->call($actor, $type, 'graph');
        if($result->isSuccessful())
            $user['graph'] = $this->processGraph(json_decode($result->getContent(), true));
        else
            throw new \RuntimeException(sprintf('API error: %s',$result->getContent()),$result->getStatusCode());

        if ($user != array('topics' => null, 'graph' => null))
            return Actor::getInstance(array_merge($actor->toArray(), $user));
        else
            throw new \RuntimeException('User not found');

    }

    /**
     * @param $array
     * @return mixed
     */
    private function processGraph($array)
    {
        if( ! is_array($array)) return null;

        $output = array();

        foreach($array as $key => $group)
            if(is_array($group))
                foreach($group as $item)
                    if(is_array($item) && array_key_exists('peerindex_id', $item))
                        $output[$key][] = Actor::getInstance($item);

        return $output;
    }

    /**
     * @param $actor
     * @param $type
     * @param $method
     * @return Buzz\Response
     */
    private function call($actor, $type, $method)
    {
        return $this->client->call(
            sprintf(
                '%s/actor/%s?api_key=%s&%s=%s',
                self::API_ENDPOINT,
                $method,
                $this->key,
                $type,
                $actor->getId($type)
            ),
            'get',
            array(),
            ''
        );
    }
}