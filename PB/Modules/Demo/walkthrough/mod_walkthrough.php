<?php
namespace PB\Modules\Demo\walkthrough;

use PB\Core\Interfaces\IAppFactory;
use PB\Core\Interfaces\IExecutable;
use PB\Core\Interfaces\IRegistrationService;
use PB\Core\ModuleManager\Module;
use PB\Modules\Demo\walkthrough\interfaces\IRepository;
/**
 * Provide a meaningful description here.
 *
 * @author jfalkenstein
 */
class mod_walkthrough extends Module {

    public function initialize(IAppFactory $appFactory) {
        //No special initialization is needed for this module, but you could
        //do whatever you needed here.
    }

    public function registerDependencies(IRegistrationService $regService) {
        $regService->set(IExecutable::class, Executable::class);
        $regService->set(IRepository::class, Repository::class);
    }
    
    public function getExecutable(IAppFactory $appFactory){
        return $appFactory->GetOther(IExecutable::class);
    }
}
