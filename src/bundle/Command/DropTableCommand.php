<?php

namespace EzPlatform\DatabaseSchemaMigrationBundle\Command;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DropTableCommand extends ContainerAwareCommand
{
    /** @var \Doctrine\DBAL\Connection */
    private $db;

    /**
     * DropTableCommand constructor.
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(
        Connection $db
    ) {
        parent::__construct();
        $this->db = $db;
    }

    protected function configure()
    {
        $this->setName('db_schema_migration:table:drop');
        $this->addArgument(
                'table',
                InputArgument::REQUIRED,
                'Provide tablename to drop ! use with caution'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = $input->getArgument('table');

        if ($table) {
            $helper = $this->getHelper('question');
            if (!$helper->ask(
                $input,
                $output,
                new ConfirmationQuestion('<question>This command will drop the "' . $table . '" table . Do you want to continue Y/N ?</question>', false)
            )
            ) {
                $output->writeln('<comment>Dropping table "' . $table . '" is cancelled!</comment>');

                return 0;
            }
        }
        $this->db->getSchemaManager()->dropTable($table);

        $outputStyle = new OutputFormatterStyle('black', 'green', ['bold', 'blink']);
        $output->getFormatter()->setStyle('fire', $outputStyle);
        $output->writeln('<fire>Table "' . $table . '" has been dropped</fire>');
    }
}
