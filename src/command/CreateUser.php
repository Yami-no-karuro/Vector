<?php

namespace Vector\Command;

use Vector\Module\Console\AbstractCommand;
use Vector\Module\SqlClient;
use Vector\Module\Console\Application;
use Vector\Repository\UserRepository;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class CreateUser extends AbstractCommand
{
    protected SqlClient $sql;
    protected UserRepository $repository;

    /**
     * @package Vector
     * __construct()
     * @param array $args
     */
    public function __construct(?array $args)
    {
        parent::__construct($args);
        $this->sql = SqlClient::getInstance();
        $this->repository = UserRepository::getInstance();
    }

    /**
     * @package Vector
     * Vector\Command\CreateUser->execute()
     * @return int
     */
    public function execute(): int
    {

        /**
         * @var array $userdata
         * Collect user data from command line input interface.
         * Email address must be validated.
         */
        $userdata = [];
        $email = Application::in('Email address:');
        if (false !== filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $userdata['email'] = $email;
        } else {
            Application::out('Invalid email address: "' . $email .  '"');
            return self::EXIT_FAILURE;
        }
        $userdata['password'] = Application::in('Password:');
        $userdata['username'] = Application::in('Username: (press Enter to leave empty)');
        $userdata['firstname'] = Application::in('Firstname: (press Enter to leave empty)');
        $userdata['lastname'] = Application::in('Lastname: (press Enter to leave empty)');

        /**
         * @var array $user
         * Looks for already existing users by email.
         */
        $user = $this->repository->getByEmail($email);
        if (null !== $user) {
            Application::out('User (email: "' . $email . '") already exists on the database.');
            return self::EXIT_FAILURE;
        }

        /**
         * @param array $result
         * Proceed to insert the new record.
         */
        $result = $this->sql->exec("INSERT INTO `users` 
            (`ID`, `email`, `password`, `username`, `firstname`, `lastname`) 
            VALUES (NULL, ?, ?, ?, ?, ?)", [
                ['type' => 's', 'value' => $email],
                ['type' => 's', 'value' => hash('sha256', trim($user['password']))],
                ['type' => 's', 'value' => trim($userdata['username'])],
                ['type' => 's', 'value' => trim($userdata['firstname'])],
                ['type' => 's', 'value' => trim($userdata['lastname'])]
        ]);
        if (true === $result['success']) {
            Application::out('User (email: "' . $email .  '") was succesfully created!');
        } else {
            Application::out('Unable to create User (email:"' . $email .  '").');
            return self::EXIT_FAILURE;
        }

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
