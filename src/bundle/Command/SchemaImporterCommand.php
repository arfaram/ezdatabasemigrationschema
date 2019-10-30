<?php

namespace EzPlatform\DatabaseSchemaMigrationBundle\Command;

use EzPlatform\DatabaseSchemaMigration\Installer\Installer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SchemaImporterCommand extends ContainerAwareCommand
{
    /** @var \EzPlatform\DatabaseSchemaMigration\Installer\Installer */
    private $installer;

    /**
     * SchemaBuilderCommand constructor.
     * @param Installer $installer
     */
    public function __construct(
        Installer $installer
    ) {
        parent::__construct();
        $this->installer = $installer;
    }

    protected function configure()
    {
        $this->setName('db_schema_migration:schema:import');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->installer->setOutput($output);
        $this->installer->importSchema();
    }
}
