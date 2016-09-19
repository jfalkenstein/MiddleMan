<?php

namespace PB\Modules\Demo\walkthrough;

use PB\Core\Exceptions\ModuleExecutionException;
use PB\Core\Interfaces\IAppFactory;
use PB\Core\Interfaces\IExecutable;
use PB\Core\Request\Request;
use PB\Modules\Demo\walkthrough\interfaces\IRepository;

/**
 * This performs the main function of the walkthrough module.
 *
 * @author jfalkenstein
 */
class Executable implements IExecutable {

    private $repo;
    
    public function __construct(IRepository $repo) {
        $this->repo = $repo;
    }
    
    public function execute(Request $request, IAppFactory $appFactory) {
        //Get the personKey parameter from the request
        $personKey = $request->Data['personKey'];
        //If the parameter we need is not present, throw a ModuleExecutionException.
        if(is_null($personKey)){
            throw new ModuleExecutionException("personKey is a required parameter");
        }
        $data = $this->repo->getByPersonKey($personKey);
        return $data;
    }
}
