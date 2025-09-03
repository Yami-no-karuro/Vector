<?php

namespace Vector\Repository;

use Vector\Module\Repository\AbstractRepository;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class AssetRepository extends AbstractRepository
{
    protected string $class = '\Vector\DataObject\Asset';
    protected string $tablename = 'vct_assets';
}
