<?php

namespace Vector\Repository;

use Vector\Module\AbstractRepository;
use Vector\DataObject\User;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class UserRepository extends AbstractRepository
{

    protected static string $class = '\Vector\DataObject\Asset';
    protected static string $tablename = 'assets';

    /**
     * @package Vector
     * Vector\Repository\UserRepository->getById()
     * @param int $id
     * @return ?User
     */
    public function getById(int $id): ?User
    {
        $query = "SELECT * FROM `users` WHERE `ID` = :id LIMIT 1";
        $q = $this->sql->prepare($query);

        $q->bindParam('id', $id, PDO::PARAM_INT);
        $q->execute();

        if (false !== ($results = $q->fetch(PDO::FETCH_ASSOC))) {
            return new User($results);
        }

        return null;
    }

}
