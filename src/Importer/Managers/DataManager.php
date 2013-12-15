<?php 

namespace Importer\Managers;

/**
 * Data Manager
 * Class for managing importing rows of data into a DB
 * 
 * @package Importer\Managers\DataManager
 * @author Paul Saunders
 */
class DataManager
{
    /**
     * Manages passing the CSV line to the DB correctly
     * 
     * @param integer $participantId The participant id
     * @param array   $array         Data from the csv line 
     * 
     * @return boolean
     */
	public function manageData($participantId, $array)
	{
		//foreach item add a record to the flags answers table
		die('found: manageData');
	}

    /**
     * Builds an array with the flag name as the index and their DB id as the value
     * 
     * @param array $fields The array of all flag names
     * 
     * @return array
     */
	public function getFlagKeys($fields)
	{
		die("found: getFlagKeys");
	}
}
