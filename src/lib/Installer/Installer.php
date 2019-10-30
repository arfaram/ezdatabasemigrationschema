<?php

declare(strict_types=1);

namespace EzPlatform\DatabaseSchemaMigration\Installer;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use EzSystems\DoctrineSchema\API\Builder\SchemaBuilder;
use EzSystems\PlatformInstallerBundle\Installer\CoreInstaller;
use Symfony\Component\Console\Helper\ProgressBar;

class Installer extends CoreInstaller
{
    /** @var $databasePlatform */
    public $databasePlatform;

    /**
     * Installer constructor.
     * @param Connection $db
     * @param SchemaBuilder $schemaBuilder
     */
    public function __construct(Connection $db, SchemaBuilder $schemaBuilder)
    {
        parent::__construct($db, $schemaBuilder);
    }

    /**
     * @throws DBALException
     */
    public function getDatabasePlatform()
    {
        return $this->databasePlatform = $this->db->getDatabasePlatform();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * $queries = Array
     * (
     *      [0] => DROP TABLE table-name
     * )
     */
    public function dropSchema()
    {
        // note: schema is built using Schema Builder event-driven API
        $schema = $this->schemaBuilder->buildSchema();
        $queries = $schema->toDropSql($this->getDatabasePlatform());
        $this->execQueries($queries);
    }

    /**
     * @throws DBALException
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function execQueries($queries)
    {
        $queriesCount = \count($queries);
        $this->output->writeln(
            sprintf(
                '<info>Executing %d queries on database <comment>%s</comment> (<comment>%s</comment>)</info>',
                $queriesCount,
                $this->db->getDatabase(),
                $this->getDatabasePlatform()->getName()
            )
        );
        $progressBar = new ProgressBar($this->output);
        $progressBar->start($queriesCount);

        try {
            $this->db->beginTransaction();
            foreach ($queries as $query) {
                $this->db->exec($query);
                $progressBar->advance(1);
            }
            $this->db->commit();
        } catch (DBALException $e) {
            $this->db->rollBack();
            throw $e;
        }

        $progressBar->finish();
        // go to the next line after ProgressBar::finish
        $this->output->writeln('');
    }
}
