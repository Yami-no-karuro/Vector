<?php

namespace Vector\Repository;

use Vector\Module\AbstractRepository;

if (!defined('NO_DIRECT_ACCESS')) {
    header('HTTP/1.1 403 Forbidden');
    die();
}

class UserRepository extends AbstractRepository
{
    protected string $class = '\Vector\DataObject\User';
    protected string $tablename = 'vct_users';
}
