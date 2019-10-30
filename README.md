# EzPlatformDatabaseSchemaMigrationBundle

This bundle comes with several commands to export, import and drop database table(s) using schema files during or after site development. It allows you also to add data to your tables

Basically, you can use `DoctrineMigrationsBundle` or `KaliopMigrationBundle` to achieve database migration but this bundle uses the `doctrine-dbal-schema` bundle(used only during eZ Platform installation) and coming with ezplatform v2.5. It uses the same concept and classes to migrate custom schema either using `mysql` or `postgresql` databases.  

## Requirement

eZPlatform 2.5+ (Open Source or EE), PHP 7+

## Installation

```
composer require arfaram/ezdatabasemigrationschema:^0.1
```
- Activate the Bundle in AppKernel.php

```
    public function registerBundles()
    {
        $bundles = array(
            new EzPlatform\DatabaseSchemaMigrationBundle\EzPlatformDatabaseSchemaMigrationBundle(),
        );
```

## Commands

### Export Schema

```
php bin/console db_schema_migration:schema:export
```

Parameters:

- `-t, --table[=TABLE]` : The table name to be exported. It will create an export file using the same table name and current timestamp e.g `ezcobj_state_1572446173.yaml`
- `all`: (default) If you don't specify explicitly the table name then it will export the entire db schema in `db_schema_<timestamp>>.yaml`

Note: You can specify the export folder path in `parameters.yml` otherwise it will create the dump files in your installation root folder:

Example:
```
parameters:
    database_schema_migration.export.schema.folder.path: '%kernel.root_dir%/../var/schemaexport/'
```

Note: Using eZPlatform Cloud you should mount the export path folder in `.platform.app.yaml`:

### Import Schema

```
php bin/console db_schema_migration:schema:import
```

This command imports custom schema definition and you have to add the schema file path in `parameters.yml`

Example:

```
parameters:
    database_schema_migration.schema.file.path: '%kernel.root_dir%/../<Schema-file-path>'
```

You can find a schema example in `bundle/doc/schema.yml` or the legacy ezplatform [schema.yaml](https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Bundle/EzPublishCoreBundle/Resources/config/storage/legacy/schema.yaml)

### Import Data

Sometimes you want also to import data to an existing database table. This is also possible using below command:

```
php bin/console db_schema_migration:data:import
```

You have to add your sql file path in `parameters.yml`

Example:

```
parameters:
    database_schema_migration.import.data.file.path: '%kernel.root_dir%/../<mysql-or-postgresql-file-path>'
```

You can find an sql file example in `bundle/doc/mysql/data.sql` or just check more ezplatform [mysql](https://github.com/ezsystems/ezpublish-kernel/blob/master/data/mysql/cleandata.sql) or [postgresql](https://github.com/ezsystems/ezpublish-kernel/blob/master/data/postgresql/cleandata.sql) clean data installation example.

### Drop table(s) using schema file

```
php bin/console db_schema_migration:schema:drop
```

This command removes table(s) using an existing schema file.You have to add the file path in `parameters.yml`

Example:

```
parameters:
    database_schema_migration.schema.file.path: '%kernel.root_dir%/../<Schema-file-path>'
```

### Drop table(s) using table name

```
php bin/console db_schema_migration:table:drop <Table-Name>
```

This command is using doctrine schema manager to drop db table.


## How it works:


### SchemaBuilder

The `Installer.php` class extends the `CoreInstaller` (used only during eZ Platform installation) but its constructor becomes a custom `SchemaBuilder`. It has the same structure like the `EzSystems\DoctrineSchema\Builder\SchemaBuilder` introduced in the `doctrine-dbal-schema` bundle.

The EventsSubscriber `BuildSchemaSubscriber` class will load the schema using the `importSchemaFromFile()`  method defined in the `SchemaBuilder`.

You can also add a custom Subscriber which contains your custom schema. See the example in `services.yml`. 

Take into account to use the `SchemaBuilderEvents::INSTALLER_BUILD_SCHEMA` event name to dispatch your subscriber during execution.

### ImportData

The `DbDataInstaller` extend the `DbBasedInstaller` coming with the `PlatformInstallerBundle` and being used by 
 `CoreInstaller.php` (used only during eZ Platform installation)

This class hat its own constructor and `importData()` method which runs your custom queries.
