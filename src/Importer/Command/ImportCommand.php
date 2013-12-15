<?php

namespace Importer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Importer\Managers\DataManager;

/**
 * Class for managing the console commands for a CSV import
 * todo: I should move most of logic from the execute function into a seperate class
 * 
 * @package Importer\Command\ImportCommand
 * @author Paul Saunders
 */
class ImportCommand extends Command
{

    /**
     * The data manager class
     * 
     * @var DataManager
     */
    protected $dataManager;

    /**
     * The class constructor
     * 
     * @param DataManager $DataManager The data manager class
     * 
     * @return void
     */
    public function __construct(DataManager $DataManager)
    {
        parent::__construct();
        $this->dataManager = $DataManager;
    }

    /**
     * Configure the command line app
     * 
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('wss:import')
            ->setDescription('Import data')
            ->addArgument(
                'datafile',
                InputArgument::REQUIRED,
                'Which data file would you like to use?'
            )
        ;
    }	

    /**
     * The execute script for the command defined in the configure
     * 
     * @param InputInterface  $input  The input interface
     * @param OutputInterface $output The output interface
     * 
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pathToFile = $input->getArgument('datafile');        

        $output->writeln("<info>Import: Looking for file: ".$pathToFile."</info>");

        if (!file_exists($pathToFile)) {
            $output->writeln("<error>Import: Failed - File not found</error>");
            throw new \InvalidArgumentException('File not found');

        }

        $output->writeln("<info>Import: File found</info>");
        $handle = @fopen($pathToFile, "r");
        
        // DB participant ids start from 2 - Abbott, Lucy
        // So we start from 1 knowing that the first row will be field names
        $i = 1;

        // Array of field names (from first row of csv)
        $fields = array();
        // Array of field names and their keys
        $flagKeys = array();

        while (($row = fgetcsv($handle, 4096)) !== false) {
            // Set array to empty so that i never becomes too large
            $array = array();

            // Get fields from first row of CSV
            if (empty($fields)) {
                $fields = $row;
                $flagKeys = $this->dataManager->getFlagKeys($fields);
                continue;
            }
            $output->writeln("<info>Import: Importing flags for user - ".$i."</info>");

            $preparedData = array();
            foreach ($row as $k=>$value) {
                $preparedData[$fields[$k]] = $value;
            }

            $result = $this->dataManager->manageData($i, $preparedData. $flagKeys);

            if ($result == false) {
                $output->writeln("<error>Import: Failed - Error: participant data is bad</error>");
                throw new \InvalidArgumentException('Data failure - participant no:'.$i);
            }

            // Must increment $i
            $i++;
        }
        if (!feof($handle)) {
            $output->writeln("<error>Import: Failed - Error: unexpected fgets() fail</error>");
        }

        fclose($handle);

        $output->writeln("<info>Import: Success</info>");   
    }
}
