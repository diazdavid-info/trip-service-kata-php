<?php

namespace Test\TripServiceKata\Trip;

use PHPUnit_Framework_TestCase;
use TripServiceKata\Trip\Trip;
use TripServiceKata\Trip\TripService;
use TripServiceKata\User\User;

class TripServiceTest extends PHPUnit_Framework_TestCase
{
    private $_userSession;
    private $_user;
    private $_tripDao;

    /**
     * @expectedException \TripServiceKata\Exception\UserNotLoggedInException
     * @test
     */
    public function shouldThrowExceptionWhenUserIsNotLogged()
    {
        $this->_user->shouldReceive('getFriends')->andReturn([]);

        $this->_userSession->shouldReceive('getLoggedUser')->andReturnNull();

        $tripService = $this->getTripService();
        $tripService->getTripsByUser($this->_user);
    }

    /**
     * @return TripService
     */
    private function getTripService()
    {
        return new TripService($this->_userSession, $this->_tripDao);
    }

    /**
     * @test
     */
    public function getListTripEmptyWhenUserDoesNotHaveFriend()
    {
        $this->_user->shouldReceive('getFriends', 'isMyFriend')->andReturn([], false);

        $this->_userSession->shouldReceive('getLoggedUser')->andReturn(new User('Pepe'));

        $tripService = $this->getTripService();
        $listTrip = $tripService->getTripsByUser($this->_user);

        $this->assertCount(0, $listTrip);
    }

    /**
     * @test
     */
    public function getListTripEmptyWhenUserHaveFriendButDoesNotHaveTripTogether()
    {
        $this->_user->shouldReceive('getFriends', 'isMyFriend')
            ->andReturn([new User('Ana'), new User('Belen')], true);

        $this->_userSession->shouldReceive('getLoggedUser')->andReturn(new User('Pepe'));

        $this->_tripDao->shouldReceive('findListTripByUser')->andReturn([]);

        $tripService = $this->getTripService();
        $listTrip = $tripService->getTripsByUser($this->_user);

        $this->assertCount(0, $listTrip);
    }

    /**
     * @test
     */
    public function getListTripWhenUserHaveFriendAndHaveTripTogether()
    {
        $this->_user->shouldReceive('getFriends', 'isMyFriend')
            ->andReturn([new User('Pepe'), new User('Belen')], true);

        $this->_userSession->shouldReceive('getLoggedUser')->andReturn(new User('Pepe'));

        $this->_tripDao->shouldReceive('findListTripByUser')->andReturn([new Trip()]);

        $tripService = $this->getTripService();

        $listTrip = $tripService->getTripsByUser($this->_user);

        $this->assertCount(1, $listTrip);
    }

    protected function setUp()
    {
        $this->_userSession = \Mockery::mock('TripServiceKata\User\UserSession');
        $this->_user = \Mockery::mock('TripServiceKata\User\User');
        $this->_tripDao = \Mockery::mock('TripServiceKata\Trip\TripDAO');
    }
}
