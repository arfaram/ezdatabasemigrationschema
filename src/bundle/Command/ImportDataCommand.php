<?php

namespace EzPlatform\DatabaseSchemaMigrationBundle\Command;

use eZ\Publish\API\Repository\Exceptions;
use EzPlatform\DatabaseSchemaMigration\Installer\Data\DbDataInstaller;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportDataCommand extends ContainerAwareCommand
{
    /** @var \EzPlatform\DatabaseSchemaMigration\Installer\Data\DbDataInstaller */
    private $dbDataInstaller;

    /**
     * ImportDataCommand constructor.
     * @param \EzPlatform\DatabaseSchemaMigration\Installer\Data\DbDataInstaller $dbDataInstaller
     */
    public function __construct(
        DbDataInstaller $dbDataInstaller
    ) {
        parent::__construct();
        $this->dbDataInstaller = $dbDataInstaller;
    }

    protected function configure()
    {
        $this->setName('db_schema_migration:data:import');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws Exceptions\InvalidArgumentException
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->dbDataInstaller->setOutput($output);
        $this->dbDataInstaller->importData();
    }
}
