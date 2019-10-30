<?php

declare(strict_types=1);

namespace EzPlatform\DatabaseSchemaMigration\Event;

use Symfony\Component\EventDispatcher\Event;

class SchemaBuilderEvents extends Event
{
    const INSTALLER_BUILD_SCHEMA = 'installer.schema.build_schema';
}
