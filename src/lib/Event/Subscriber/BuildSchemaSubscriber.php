<?php

declare(strict_types=1);

namespace EzPlatform\DatabaseSchemaMigration\Event\Subscriber;

use EzPlatform\DatabaseSchemaMigration\Event\SchemaBuilderEvents;
use EzSystems\DoctrineSchema\API\Event\SchemaBuilderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BuildSchemaSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $schemaFilePath;

    /**
     * BuildSchemaSubscriber constructor.
     * @param string $schemaFilePath
     */
    public function __construct(string $schemaFilePath)
    {
        $this->schemaFilePath = $schemaFilePath;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SchemaBuilderEvents::INSTALLER_BUILD_SCHEMA => ['onBuildSchema', 180],
        ];
    }

    /**
     * @param \EzSystems\DoctrineSchema\API\Event\SchemaBuilderEvent $event
     */
    public function onBuildSchema(SchemaBuilderEvent $event): void
    {
        $event
            ->getSchemaBuilder()
            ->importSchemaFromFile($this->schemaFilePath);
    }
}
