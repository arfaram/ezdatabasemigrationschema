<?php

declare(strict_types=1);

namespace EzPlatform\DatabaseSchemaMigration\Installer\Builder;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaConfig;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use EzPlatform\DatabaseSchemaMigration\Event\SchemaBuilderEvents;
use EzSystems\DoctrineSchema\API\Builder\SchemaBuilder as APISchemaBuilder;
use EzSystems\DoctrineSchema\API\Event\SchemaBuilderEvent;
use EzSystems\DoctrineSchema\API\SchemaImporter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * SchemaBuilder implementation.
 *
 * @see \EzSystems\DoctrineSchema\API\Builder\SchemaBuilder
 *
 * @internal type-hint against the \EzSystems\DoctrineSchema\API\Builder\SchemaBuilder interface
 */
class SchemaBuilder implements APISchemaBuilder
{
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    /** @var \EzSystems\DoctrineSchema\API\SchemaImporter */
    private $schemaImporter;

    /** @var \Doctrine\DBAL\Schema\Schema */
    private $schema;

    /** @var array */
    private $defaultTableOptions;

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \EzSystems\DoctrineSchema\API\SchemaImporter $schemaImporter
     * @param array $defaultTableOptions
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        SchemaImporter $schemaImporter,
        array $defaultTableOptions = []
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->schemaImporter = $schemaImporter;
        $this->defaultTableOptions = $defaultTableOptions;
    }

    /**
     * @return Schema
     */
    public function buildSchema(): Schema
    {
        $config = new SchemaConfig();
        $config->setDefaultTableOptions($this->defaultTableOptions);

        $this->schema = new Schema([], [], $config);
        if ($this->eventDispatcher->hasListeners(SchemaBuilderEvents::INSTALLER_BUILD_SCHEMA)) {
            $event = new SchemaBuilderEvent($this, $this->schema);
            $this->eventDispatcher->dispatch(SchemaBuilderEvents::INSTALLER_BUILD_SCHEMA, $event);
        }

        return $this->schema;
    }

    /**
     * @param string $schemaFile
     * @return Schema
     * @throws InvalidArgumentException
     * @throws \Doctrine\DBAL\DBALException
     * @throws \EzSystems\DoctrineSchema\API\Exception\InvalidConfigurationException
     */
    public function importSchemaFromFile(string $schemaFilePath): Schema
    {
        return $this->schemaImporter->importFromFile($schemaFilePath, $this->schema);
    }
}
