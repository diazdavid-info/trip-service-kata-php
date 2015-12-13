<?php

namespace TripServiceKata\Trip;

use TripServiceKata\Exception\UserNotLoggedInException;
use TripServiceKata\User\User;
use TripServiceKata\User\UserSession;

class TripService
{
    /**
     * @var UserSession
     */
    private $userSession;
    /**
     * @var TripDAO
     */
    private $tripDao;

    /**
     * TripService constructor.
     * @param UserSession $userSession
     * @param TripDAO $tripDao
     */
    public function __construct(UserSession $userSession, TripDAO $tripDao)
    {
        $this->userSession = $userSession;
        $this->tripDao = $tripDao;
    }

    /**
     * @param User $user
     * @return array|void
     */
    public function getTripsByUser(User $user)
    {
        return $this->getTripsWithMyFriend($user);
    }

    /**
     * @param User $user
     * @return array|void
     * @throws UserNotLoggedInException
     * @throws \TripServiceKata\Exception\DependentClassCalledDuringUnitTestException
     */
    private function getTripsWithMyFriend(User $user)
    {
        $loggedUser = $this->userSession->getLoggedUser();
        $this->isLogged($loggedUser);

        return ($user->isMyFriend($loggedUser)) ? $this->tripDao->findListTripByUser($user) : array();
    }

    /**
     * @param User|null $loggedUser
     * @throws UserNotLoggedInException
     */
    private function isLogged(User $loggedUser = null)
    {
        if ($loggedUser == null) {
            throw new UserNotLoggedInException();
        }
    }
}
