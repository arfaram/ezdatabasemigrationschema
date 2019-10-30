<?php

namespace EzPlatform\DatabaseSchemaMigrationBundle\Command;

use EzPlatform\DatabaseSchemaMigration\Installer\Installer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DropSchemaCommand extends ContainerAwareCommand
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
        $this->setName('db_schema_migration:schema:drop');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        if (!$helper->ask(
            $input,
            $output,
            new ConfirmationQuestion('<question>This command will drop the table(s) defined in your yaml file(s) . Do you want to continue Y/N ?</question>', false)
        )
        ) {
            $output->writeln('<comment>Dropping table(s) is cancelled!</comment>');

            return 0;
        }

        $this->installer->setOutput($output);
        $this->installer->dropSchema();

        $outputStyle = new OutputFormatterStyle('black', 'green', ['bold', 'blink']);
        $output->getFormatter()->setStyle('fire', $outputStyle);
        $output->writeln('<fire>Table(s) defined in your yaml file(s) has/have been dropped</fire>');
    }
}
