<?php

namespace Vector\Command;

use Vector\Module\Console\AbstractCommand;
use Vector\Module\Console\Application;
use Vector\Repository\UserRepository;
use Vector\DataObject\User;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class CreateUser extends AbstractCommand
{
    protected UserRepository $repository;

    /**
     * @package Vector
     * __construct()
     * @param array $args
     */
    public function __construct(?array $args)
    {
        parent::__construct($args);
        $this->repository = new UserRepository();
    }

    /**
     * @package Vector
     * Vector\Command\CreateUser->execute()
     * @return int
     */
    public function execute(): int
    {

        $email = Application::in('Email address:');
        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Application::out('Command failed, invalid email address.');
            return self::EXIT_FAILURE;
        }

        $user = new User();
        $user->setEmail($email);

        $password = Application::in('Password:');
        $user->setPassword($password);

        $username = Application::in('Username:');
        $user->setUsername($username);

        if (null !== $this->repository->getBy('email', $email, PDO::PARAM_STR)) {
            Application::out('Command failed, user (email: "' . $email . '") already exists.');
            return self::EXIT_FAILURE;
        }

        $user->save();
        Application::out('User (email: "' . $user->getEmail() .  '") was succesfully created!');
        return self::EXIT_SUCCESS;
    }

    /**
     * @package Vector
     * Vector\Command\CreateUser->getCommandName()
     * @return void
     */
    public function getCommandName(): string
    {
        return 'vector:create-user';
    }

}
