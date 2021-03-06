<?php

namespace ProVallo\Plugins\Backend\Components;

use ProVallo\Core;
use ProVallo\Plugins\Backend\Models\User\User;

class Auth
{
    /**
     * @var User
     */
    protected $user;

    /**
     * Checks whether the user is logged in.
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        $userID = $this->getUserID();

        return $userID > 0;
    }

    /**
     * Tries to login the user by username and password.
     *
     * If the user is not found or the password is wrong an exception is thrown.
     *
     * @param string      $username
     * @param string      $password
     * @param string|null $permission
     *
     * @return User|null
     * @throws \Exception
     */
    public function login(string $username, string $password, ?string $permission = null): ?User
    {
        $user = User::repository()->findOneBy([ 'username' => $username ]);

        if ($user instanceof User) {
            if (password_verify($password, $user->password)) {
                if (empty($permission) || Core::permission()->validate($permission, $user)) {
                    $this->setUser($user);

                    return $user;
                }

                throw new \Exception('You are not allowed to enter this area.');
            }

            throw new \Exception('Invalid password.');
        }

        throw new \Exception('Invalid username.');
    }

    /**
     * Logging current logged in user out
     */
    public function logout()
    {
        $this->user = null;

        Core::session()->delete('pv_userID');
    }

    /**
     * Sets the given user as logged in.
     *
     * @param \ProVallo\Plugins\Backend\Models\User\User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        $this->setUserByID($user->id);
    }

    /**
     * Sets the given userID as logged in.
     *
     * @param $userID
     */
    public function setUserByID($userID)
    {
        Core::session()->set('pv_userID', $userID);

        if ($this->user === null) {
            $this->user = User::repository()->find($userID);
        }
    }

    /**
     * @return \ProVallo\Plugins\Backend\Models\User\User|null
     */
    public function getUser()
    {
        if ($this->user === null) {
            $userID     = $this->getUserID();
            $this->user = User::repository()->find($userID);
        }

        return $this->user;
    }

    /**
     * Returns the current logged in user id
     *
     * @return integer
     */
    public function getUserID()
    {
        return (int)Core::session()->get('pv_userID');
    }

    /**
     * Hashes a password for database insertion.
     *
     * @param string $password
     *
     * @return string
     */
    public function hash($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}