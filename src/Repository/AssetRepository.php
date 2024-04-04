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

    protected string $class = '\Vector\DataObject\Asset';
    protected string $tablename = 'assets';

}