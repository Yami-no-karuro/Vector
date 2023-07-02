<?php

namespace Vector\Command;

use Vector\Module\Console\AbstractCommand;
use Vector\Module\SqlClient;
use Vector\Module\ApplicationLogger\FileSystemLogger;
use Vector\Module\Console\Application;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class CreateUser extends AbstractCommand
{
    protected SqlClient $sql;
    protected FileSystemLogger $logger;

    /**
     * @package Vector
     * __construct()
     * @param array $args
     */
    public function __construct(?array $args)
    {
        parent::__construct($args);
        $this->sql = SqlClient::getInstance();
        $this->logger = new FileSystemLogger('command');
    }

    /**
     * @package Vector
     * Vector\Command\CreateUser->execute()
     * @return int
     */
    public function execute(): int
    {
        $user = [];
        Application::out('Email address:');
        $user['email'] = Application::in();
        Application::out('Password:');
        $user['password'] = Application::in();
        Application::out('Username: (press Enter to leave empty)');
        $user['username'] = Application::in();
        Application::out('Firstname: (press Enter to leave empty)');
        $user['firstname'] = Application::in();
        Application::out('Lastname: (press Enter to leave empty)');
        $user['lastname'] = Application::in();
        Application::out('--------');
        $duplicate = $this->sql->getResults("SELECT `ID` FROM `users` WHERE `email` = ? LIMIT 1", [
            ['type' => 's', 'value' => trim($user['email'])]
        ]);
        if (true === $duplicate['success'] and !empty($duplicate['data'])) {
            Application::out('User (email: "' . trim($user['email']) . '") already exists on the database.');
            return 0;
        }
        $execResult = $this->sql->exec("INSERT INTO `users` 
            (`ID`, `email`, `password`, `username`, `firstname`, `lastname`, `secret`) 
            VALUES (NULL, ?, ?, ?, ?, ?, ?)", [
                ['type' => 's', 'value' => trim($user['email'])],
                ['type' => 's', 'value' => hash('sha256', trim($user['password']))],
                ['type' => 's', 'value' => trim($user['username'])],
                ['type' => 's', 'value' => trim($user['firstname'])],
                ['type' => 's', 'value' => trim($user['lastname'])],
                ['type' => 's', 'value' => bin2hex(random_bytes(32))]
        ]);
        if (true === $execResult['success']) {
            Application::out('User (email: "' . trim($user['email']) .  '") was succesfully created!');
        } else {
            Application::out('Unable to create User (email:"' . $user['email'] .  '").');
        }
        return 0;
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
