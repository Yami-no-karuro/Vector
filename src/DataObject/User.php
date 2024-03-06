<?php

namespace Vector\DataObject;

use Vector\Module\AbstractObject;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class User extends AbstractObject
{

    /**
     * @var ?int $ID
     * User ID, autogenerated on creation.
     */
    protected ?int $ID = null;

    /**
     * @var string $email
     * User email, required.
     */
    protected string $email;

    /**
     * @var string $password
     * User password.
     */
    protected ?string $password = null;

    /**
     * @var ?string $username
     * User username.
     */
    protected ?string $username = null;

    /**
     * @var ?string $firstname
     * User firstname.
     */
    protected ?string $firstname = null;

    /**
     * @var ?string $lastname
     * User lastname.
     */
    protected ?string $lastname = null;

    /**
     * @package Vector
     * Vector\DataObject\User->getId()
     * @return ?int
     */
    public function getId(): ?int
    {
        return $this->ID;
    }

    /**
     * @package Vector
     * Vector\DataObject\User->getEmail()
     * @return ?string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @package Vector
     * Vector\DataObject\User->setEmail()
     * @param string $email
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = trim($email);
    }

    /**
     * @package Vector
     * Vector\DataObject\User->getPassword()
     * @return ?string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @package Vector
     * Vector\DataObject\User->setPassword()
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = hash('sha256', $password);
    }

    /**
     * @package Vector
     * Vector\DataObject\User->getUsername()
     * @return ?string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @package Vector
     * Vector\DataObject\User->setUsername()
     * @param string $email
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = trim($username);
    }

    /**
     * @package Vector
     * Vector\DataObject\User->getFirstname()
     * @return ?string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @package Vector
     * Vector\DataObject\User->setFirstname()
     * @param string $firstname
     * @return void
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = trim($firstname);
    }

    /**
     * @package Vector
     * Vector\DataObject\User->getLastname()
     * @return ?string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @package Vector
     * Vector\DataObject\User->setLastname()
     * @param string $lastname
     * @return void
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = trim($lastname);
    }

    /**
     * @package Vector
     * Vector\DataObject\User->save()
     * @return void 
     */
    public function save(): void
    {
        $result = $this->client->exec("INSERT INTO `users` 
            (`ID`, `email`, `password`, `username`, `firstname`, `lastname`) VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE `password` = ?, `username` = ?, `firstname` = ?, `lastname` = ?", [
                ['type' => 's', 'value' => $this->getId()],
                ['type' => 's', 'value' => $this->getEmail()],
                ['type' => 's', 'value' => $this->getPassword()],
                ['type' => 's', 'value' => $this->getUsername()],
                ['type' => 's', 'value' => $this->getFirstname()],
                ['type' => 's', 'value' => $this->getLastname()],
                ['type' => 's', 'value' => $this->getPassword()],
                ['type' => 's', 'value' => $this->getUsername()],
                ['type' => 's', 'value' => $this->getFirstname()],
                ['type' => 's', 'value' => $this->getLastname()]
        ]);
        if ($result['success'] && null !== ($insertedId = $result['data']['inserted_id'])) {
            $this->ID = $insertedId;
        }
    }

    /**
     * @package Vector
     * Vector\DataObject\User->delete()
     * @return void 
     */
    public function delete(): void
    {
        if (null !== $this->getId()) {
            $this->client->exec("DELETE FROM `users` WHERE `ID` = ?", [
                ['type' => 'd', 'value' => $this->getId()],
            ]);
        }
    }

}
