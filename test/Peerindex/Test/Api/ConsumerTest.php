<?php

namespace Peerindex\Test\Api;

use Peerindex\Api\Consumer;
use Peerindex\Api\Actor;

class ConsumerTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessGraph()
    {
        $method = new \ReflectionMethod('Peerindex\Api\Consumer', 'processGraph');

        $method->setAccessible(true);

        $this->assertNull($method->invoke(new Consumer($this->getClient(),'api_key'), 'not array'));
        $this->assertEquals(array(), $method->invoke(new Consumer($this->getClient(),'api_key'), array()));

        $this->assertEquals(
            array(),
            $method->invoke(
                new Consumer($this->getClient(),'api_key'),
                array('first' => array('ok'))
            )
        );

        $this->assertEquals(
            array(),
            $method->invoke(
                new Consumer($this->getClient(),'api_key'),
                array('first' => array(array('this_is_not_peerindex_id' => 1234)))
            )
        );

        $this->assertEquals(
            array('first' => array(Actor::getInstance(array('peerindex_id' => 1234)))),
            $method->invoke(
                new Consumer($this->getClient(),'api_key'),
                array('first' => array(array('peerindex_id' => 1234)))
            )
        );
    }

    public function testCall()
    {
        $method = new \ReflectionMethod('Peerindex\Api\Consumer', 'call');
        $method->setAccessible(true);

        $client = $this->getClient();

        $actor = $this->getMock('Peerindex\Api\Actor', array('getId'));

        foreach(array(Actor::PEERINDEX_ID, Actor::TWITTER_ID, Actor::TWITTER_SCREEN_NAME) as $type)
        {
            foreach(array('extended','topic','graph') as $func)
            {
                $actor
                    ->expects($this->at(0))
                    ->method('getId')
                    ->with($this->equalTo($type))
                    ->will($this->returnValue('tester'));

                $this->setExpectation(0, $client, sprintf('https://api.peerindex.com/1/actor/%s?api_key=api_key&%s=tester', $func, $type));
                $this->assertNull($method->invoke(new Consumer($client,'api_key'), $actor, $type, $func));
            }
        }
    }

    public function testContructor()
    {
        $client = $this->getClient();

        $consumer = new Consumer($client, 'api_key');

        $this->assertAttributeEquals($client, 'client', $consumer);
        $this->assertAttributeEquals('api_key', 'key', $consumer);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetActorWithFirstError()
    {
        $client = $this->getClient();

        $this->setExpectation(
            0,
            $client,
            sprintf('https://api.peerindex.com/1/actor/%s?api_key=api_key&%s=tester', 'extended', Actor::PEERINDEX_ID),
            $this->getResponseObject(false)
        );

        $consumer = new Consumer($client, 'api_key');

        $consumer->getActor('tester');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetActorWithSecondError()
    {
        $client = $this->getClient();

        $this->setExpectation(
            0,
            $client,
            sprintf('https://api.peerindex.com/1/actor/%s?api_key=api_key&%s=tester', 'extended', Actor::PEERINDEX_ID),
            $this->getResponseObject(true)
        );
        $this->setExpectation(
            1,
            $client,
            sprintf('https://api.peerindex.com/1/actor/%s?api_key=api_key&%s=tester', 'topic', Actor::PEERINDEX_ID),
            $this->getResponseObject(false)
        );

        $consumer = new Consumer($client, 'api_key');

        $consumer->getActor('tester');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetActorWithThirdError()
    {
        $client = $this->getClient();

        $this->setExpectation(
            0,
            $client,
            sprintf('https://api.peerindex.com/1/actor/%s?api_key=api_key&%s=tester', 'extended', Actor::PEERINDEX_ID),
            $this->getResponseObject(true)
        );
        $this->setExpectation(
            1,
            $client,
            sprintf('https://api.peerindex.com/1/actor/%s?api_key=api_key&%s=tester', 'topic', Actor::PEERINDEX_ID),
            $this->getResponseObject(true)
        );
        $this->setExpectation(
            2,
            $client,
            sprintf('https://api.peerindex.com/1/actor/%s?api_key=api_key&%s=tester', 'graph', Actor::PEERINDEX_ID),
            $this->getResponseObject(false)
        );

        $consumer = new Consumer($client, 'api_key');

        $consumer->getActor('tester');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetActorWithFourthError()
    {
        $client = $this->getClient();

        $this->setExpectation(
            0,
            $client,
            sprintf('https://api.peerindex.com/1/actor/%s?api_key=api_key&%s=tester', 'extended', Actor::PEERINDEX_ID),
            $this->getResponseObject(true, 'not json string')
        );
        $this->setExpectation(
            1,
            $client,
            sprintf('https://api.peerindex.com/1/actor/%s?api_key=api_key&%s=tester', 'topic', Actor::PEERINDEX_ID),
            $this->getResponseObject(true, 'not json string')
        );
        $this->setExpectation(
            2,
            $client,
            sprintf('https://api.peerindex.com/1/actor/%s?api_key=api_key&%s=tester', 'graph', Actor::PEERINDEX_ID),
            $this->getResponseObject(true, 'not json string')
        );

        $consumer = new Consumer($client, 'api_key');

        $consumer->getActor('tester');
    }

    public function testGetActorOK()
    {
        $client = $this->getClient();

        $this->setExpectation(
            0,
            $client,
            sprintf('https://api.peerindex.com/1/actor/%s?api_key=api_key&%s=tester', 'extended', Actor::PEERINDEX_ID),
            $this->getResponseObject(true, '{"peerindex_id": "tester"}')
        );
        $this->setExpectation(
            1,
            $client,
            sprintf('https://api.peerindex.com/1/actor/%s?api_key=api_key&%s=tester', 'topic', Actor::PEERINDEX_ID),
            $this->getResponseObject(true, '{}')
        );
        $this->setExpectation(
            2,
            $client,
            sprintf('https://api.peerindex.com/1/actor/%s?api_key=api_key&%s=tester', 'graph', Actor::PEERINDEX_ID),
            $this->getResponseObject(true, '{}')
        );

        $consumer = new Consumer($client, 'api_key');

        $this->assertNotNull($consumer->getActor('tester'));
    }


    private function getClient()
    {
        return $this->getMock('Buzz\Browser', array('call'));
    }

    private function setExpectation($index, &$client, $url, $return = null)
    {
        $client
            ->expects($this->at($index))
            ->method('call')
            ->with(
                $this->equalTo($url),
                $this->equalTo('get'),
                $this->equalTo(array()),
                $this->equalTo('')
            )
            ->will($this->returnValue($return));
    }

    private function getResponseObject($successful = true, $content = '[]')
    {
        $response = $this->getMock('Buzz\Message\Response', array('isSuccessful','getContent'));

        $response
            ->expects($this->at(0))
            ->method('isSuccessful')
            ->will($this->returnValue($successful));
        $response
            ->expects($this->at(1))
            ->method('getContent')
            ->will($this->returnValue($content));

        return $response;
    }
}
