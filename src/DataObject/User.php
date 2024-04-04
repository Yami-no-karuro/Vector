<?php

namespace Vector\DataObject;

use Vector\Module\AbstractObject;
use PDO;

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
     * User's email, required.
     */
    protected string $email;

    /**
     * @var string $password
     * User's password.
     */
    protected ?string $password = null;

    /**
     * @var ?string $username
     * User's username.
     */
    protected ?string $username = null;

    /**
     * @var ?string $firstname
     * User's firstname.
     */
    protected ?string $firstname = null;

    /**
     * @var ?string $lastname
     * User's lastname.
     */
    protected ?string $lastname = null;

    /**
     * @var ?int $createdAt
     * User's creation date.
     */
    protected ?int $createdAt = null;

    /**
     * @var ?int $modifiedAt
     * User's modification date.
     */
    protected ?int $modifiedAt = null;

    /**
     * @var ?int $lastLogin
     * User's last login date or null.
     */
    protected ?int $lastLogin = null;

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
     * Vector\DataObject\User->getCreatedAt()
     * @return ?int
     */
    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    /**
     * @package Vector
     * Vector\DataObject\User->setCreatedAt()
     * @param ?int $time
     * @return void
     */
    protected function setCreatedAt(?int $time): void
    {
        $this->createdAt = $time;
    }

    /**
     * @package Vector
     * Vector\DataObject\User->getModifiedAt()
     * @return ?int
     */
    public function getModifiedAt(): ?int
    {
        return $this->modifiedAt;
    }

    /**
     * @package Vector
     * Vector\DataObject\User->setModifiedAt()
     * @param ?int $time
     * @return void
     */
    public function setModifiedAt(?int $time): void
    {
        $this->modifiedAt = $time;
    }

    /**
     * @package Vector
     * Vector\DataObject\User->getLastLogin()
     * @return ?int
     */
    public function getLastLogin(): ?int
    {
        return $this->lastLogin;
    }

    /**
     * @package Vector
     * Vector\DataObject\User->setLastLogin()
     * @param ?int $time
     * @return void
     */
    public function setLastLogin(?int $time): void
    {
        $this->lastLogin = $time;
    }

    /**
     * @package Vector
     * Vector\DataObject\User->save()
     * @return void 
     */
    public function save(): void
    {
        $query = "INSERT INTO `users` 
            (`ID`, `email`, `password`, `username`, `firstname`, `lastname`, `last_login`, `created_at`, `modified_at`) 
            VALUES (:ID, :email, :password, :username, :firstname, :lastname, :lastLogin, :createdAt, :modifiedAt)
            ON DUPLICATE KEY UPDATE `password` = :password,
                `username` = :username, 
                `firstname` = :firstname, 
                `lastname` = :lastname,
                `last_login` = :lastLogin,
                `modified_at` = :modifiedAt";

        $q = $this->sql->prepare($query);

        $q->bindParam('ID', $this->ID, PDO::PARAM_INT);
        $q->bindParam('email', $this->email, PDO::PARAM_STR);
        $q->bindParam('password', $this->password, PDO::PARAM_STR);
        $q->bindParam('username', $this->username, PDO::PARAM_STR);
        $q->bindParam('firstname', $this->firstname, PDO::PARAM_STR);
        $q->bindParam('lastname', $this->lastname, PDO::PARAM_STR);
        $q->bindParam('lastLogin', $this->lastLogin, PDO::PARAM_INT);

        $now = time();
        $q->bindParam('createdAt', $now, PDO::PARAM_INT);
        $q->bindParam('modifiedAt', $now, PDO::PARAM_INT);
        $q->execute();

        if (null !== ($id = $this->sql->lastInsertId())) {
            $this->ID = $id;
        }
    }

    /**
     * @package Vector
     * Vector\DataObject\User->delete()
     * @return void 
     */
    public function delete(): void
    {
        if (null === $this->getId()) { 
            return; 
        }

        $query = "DELETE FROM `users` WHERE `ID` = :id";
        $q = $this->sql->prepare($query);

        $q->bindParam('id', $this->ID, PDO::PARAM_INT);
        $q->execute();
    }

}
