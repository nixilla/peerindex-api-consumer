<?php

namespace Peerindex\Test\Api;

use Peerindex\Api\Actor;

class ActorTest extends \PHPUnit_Framework_TestCase
{
    public function testAccessors()
    {
        $consumer = $this
            ->getMockBuilder('Peerindex\Api\Consumer')
            ->disableOriginalConstructor()
            ->getMock();

        $consumer
            ->expects($this->once())
            ->method('getActor')
            ->with(
                $this->equalTo('tester'),
                $this->equalTo(Actor::TWITTER_SCREEN_NAME)
            )
            ->will(
                $this->returnValue(Actor::getInstance(array(
                    Actor::PEERINDEX_ID => 12345,
                    'peerindex' => 51,
                    'topics' => array(),
                    'graph' => array('ok')
                )))
            );

        $actor = $consumer->getActor('tester', Actor::TWITTER_SCREEN_NAME);

        $this->assertEquals(12345, $actor->getId(), 'Default id is PEERINDEX_ID');
        $this->assertEquals(12345, $actor->getId(Actor::PEERINDEX_ID), 'PEERINDEX_ID is correct');

        $this->assertEquals(51, $actor->getPeerindex(), 'Accessor method getPeerindex OK');
        $this->assertEquals(51, $actor['peerindex'], 'ArrayAccess method [peerindex] OK');

        $this->assertEquals(array(), $actor->getTopics(), 'Accessor method getTopics OK');
        $this->assertEquals(array(), $actor['topics'], 'Accessor method [topics] OK');

        $this->assertEquals(array('ok'), $actor->getGraph(), 'Accessor method getGraph OK');
        $this->assertEquals(array('ok'), $actor['graph'], 'Accessor method [graph] OK');
    }

    public function testIsLoaded()
    {
        $actor = Actor::getInstance(array(Actor::PEERINDEX_ID => 123321));

        $this->assertFalse($actor->isLoaded(), 'Actor is not loaded');

        $consumer = $this
            ->getMockBuilder('Peerindex\Api\Consumer')
            ->disableOriginalConstructor()
            ->getMock();

        $consumer
            ->expects($this->once())
            ->method('getActor')
            ->with($this->equalTo($actor))
            ->will(
                $this->returnValue(Actor::getInstance(array(
                    Actor::PEERINDEX_ID => 12345,
                    'peerindex' => 51,
                    'topics' => array(),
                    'graph' => array('ok')
                )))
            );

        $actor = $consumer->getActor($actor);

        $this->assertTrue($actor->isLoaded(), 'Actor is loaded');

    }

    /**
     * @expectedException \RuntimeException
     */
    public function testUnset()
    {
        $actor = Actor::getInstance(array(Actor::PEERINDEX_ID => 123321));

        unset($actor[Actor::PEERINDEX_ID]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testSet()
    {
        $actor = Actor::getInstance(array(Actor::PEERINDEX_ID => 123321));

        $actor[Actor::PEERINDEX_ID] = '123321321';
    }

    public function testIsset()
    {
        $actor = Actor::getInstance(array(Actor::PEERINDEX_ID => 123321));

        if(isset($actor[Actor::PEERINDEX_ID]))
            $this->assertTrue(true);
    }
}
