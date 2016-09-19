<?php

namespace PB\Modules\Demo\walkthrough;

use PB\Core\Interfaces\IConfigManager;
use PB\Modules\Demo\walkthrough\interfaces\IRepository;

/**
 * Here is where our walkthrough module will obtain data.
 *
 * @author jfalkenstein
 */
class Repository implements IRepository{
    private $configManager;
    
    private $db = [
        'table1' => [
            1234 => [
                'name' => 'John Doe',
                'personKey' => '1234',
                'address' => '123 Demo Lane.'
            ],
            5678 => [
                'name' => 'Jane Doe',
                'personKey' => '5678',
                'address' => '123 Demo Lane.'
            ]
        ],
        'table2' => []
    ];
    
    public function __construct(IConfigManager $config) {
        $this->configManager = $config;
    }

    public function getByPersonKey($personkey) {
        //Get the table name from the config manager.
        $table = $this->configManager->getValue(['modules','Demo','walkthrough','table']);
        //Check if the record exists:
        if(isset($this->db[$table][$personkey])){ //If it exists...
            return $this->db[$table][$personkey]; //Return the record
        }
        //Else, if it doesn't exist...
        return "No record found.";
    }
}
