<?php

namespace EzPlatform\DatabaseSchemaMigrationBundle\Command;

use Doctrine\DBAL\Connection;
use eZ\Publish\API\Repository\Exceptions;
use EzSystems\DoctrineSchema\API\SchemaExporter;
use EzSystems\DoctrineSchema\Exporter\Table\SchemaTableExporter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class SchemaExporterCommand extends ContainerAwareCommand
{
    /** @var string */
    private $schemaExportPath;

    /** @var \EzSystems\DoctrineSchema\API\SchemaExporter */
    private $exporter;

    /** @var \Doctrine\DBAL\Connection */
    private $db;

    /** @var \Symfony\Component\Filesystem\Filesystem */
    private $filesystem;
    /** @var \EzSystems\DoctrineSchema\Exporter\Table\SchemaTableExporter */
    private $tableYamlExporter;

    const SCHEMA_EXPORT_FILENAME = 'db_schema';

    /**
     * SchemaExporterCommand constructor.
     * @param $schemaExportPath
     * @param \EzSystems\DoctrineSchema\API\SchemaExporter $exporter
     * @param \Doctrine\DBAL\Connection $db
     * @param \Symfony\Component\Filesystem\Filesystem $filesystem
     * @param \EzSystems\DoctrineSchema\Exporter\Table\SchemaTableExporter $tableYamlExporter
     */
    public function __construct(
        $schemaExportPath,
        SchemaExporter $exporter,
        Connection $db,
        Filesystem $filesystem,
        SchemaTableExporter $tableYamlExporter
    ) {
        parent::__construct();
        $this->schemaExportPath = $schemaExportPath;
        $this->exporter = $exporter;
        $this->db = $db;
        $this->filesystem = $filesystem;
        $this->tableYamlExporter = $tableYamlExporter;
    }

    protected function configure()
    {
        $this->setName('db_schema_migration:schema:export');
        $this->addOption(
                'table',
                't',
                InputOption::VALUE_OPTIONAL,
                'Export table schema',
                'all'
            );
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
        $optionValue = $input->getOption('table');
        if ($optionValue == 'all') {
            $helper = $this->getHelper('question');
            if (!$helper->ask(
                $input,
                $output,
                new ConfirmationQuestion('<question>Export entire database schema or specify table name -t, --table[=TABLE]. Do you want to continue Y/N ?</question>', false)
            )
            ) {
                $output->writeln('<error>Export schema cancelled!</error>');

                return 0;
            }
        }
        $inputSchema = $this->db->getSchemaManager()->createSchema();

        if ($optionValue != 'all') {
            $schemaDefinition = $this->tableYamlExporter->export($inputSchema->getTable($optionValue));
            $dump = Yaml::dump($schemaDefinition, 4);
            $filename = $optionValue;
        } else {
            $dump = $this->exporter->export($inputSchema);
            $filename = self::SCHEMA_EXPORT_FILENAME;
        }

        $filename = $filename . '_' . time() . '.yaml';

        $this->filesystem->dumpFile($this->schemaExportPath . $filename, $dump);

        $outputStyle = new OutputFormatterStyle('black', 'green', ['bold', 'blink']);
        $output->getFormatter()->setStyle('fire', $outputStyle);
        $output->writeln('<fire> Schema Exported to:' . $this->schemaExportPath . $filename . ' </fire>');
    }
}
