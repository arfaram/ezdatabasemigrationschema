<?php

namespace EzPlatform\DatabaseSchemaMigration\Installer\Data;

use Doctrine\DBAL\Connection;
use EzSystems\PlatformInstallerBundle\Installer\DbBasedInstaller;

class DbDataInstaller extends DbBasedInstaller
{
    /** @var string $sqlDataFile */
    private $sqlDataFile;

    /**
     * DbDataInstaller constructor.
     * @param \Doctrine\DBAL\Connection $db
     * @param $sqlDataFile
     */
    public function __construct(Connection $db, $sqlDataFile)
    {
        parent::__construct($db);
        $this->sqlDataFile = $sqlDataFile;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function importData()
    {
        $this->runQueriesFromFile($this->sqlDataFile);
    }
}
