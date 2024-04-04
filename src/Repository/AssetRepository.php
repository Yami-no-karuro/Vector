<?php

namespace Vector\Repository;

use Vector\Module\AbstractRepository;
use Vector\DataObject\Asset;
use PDO;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class AssetRepository extends AbstractRepository
{

    protected static string $class = '\Vector\DataObject\Asset';
    protected static string $tablename = 'assets';

    /**
     * @package Vector
     * Vector\Repository\AssetRepository->getById()
     * @param int $id
     * @return ?Asset
     */
    public function getById(int $id): ?Asset
    {
        $query = "SELECT * FROM `assets` WHERE `ID` = :id LIMIT 1";
        $q = $this->sql->prepare($query);

        $q->bindParam('id', $id, PDO::PARAM_INT);
        $q->execute();

        if (false !== ($results = $q->fetch(PDO::FETCH_ASSOC))) {
            return new Asset($results);
        }

        return null;
    }

}