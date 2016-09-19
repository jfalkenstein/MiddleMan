<?php

namespace PB\Core\Bootstrapper;

use DI\ContainerBuilder;
use PB\Core\Interfaces\IApplication;
use PB\Core\Interfaces\IContainer;
use const DS;
use const ROOT_DIR;

/**
 * The initial execution class for the middleMan application.
 *
 * @author jfalkenstein
 */
class Bootstrapper {
    
    /**
     * This is responsible of initiating and running the application. It does this
     * by (first) obtaining the di-container and then using that to obtain the 
     * application. Following that, it runs the application.
     */
    public static function initialize(){
        //1. Create DI container. This loads all of the PHP-di.
        $container = self::getDIContainer();
        //2. Instantiate application
        $app = $container->get(IApplication::class);
        //3. Run the application
        $app->run();
    }
    
    /**
     * Builds and obtains the @link Container::ContainerWrapper DI container @endlink .
     * Because this application abstracts the PHP-DI container within the 
     * ContainerWrapper object, this function first obtains the PHP-DI container,
     * then uses it to obtain the IContainer (mapped to ContainerWrapper), which 
     * actually depends upon the PHP-DI %Container.
     * 
     * Buy burying the dependency injection container behind this abstraction,
     * it allows the middleman application to rely entirely upon it's own interfaces.
     * Thus, if we ever wanted to swap out the ACTUAL di container, we can without
     * a problem. Futher, it allowed the IContainer to have more functionality (which I added)
     * than the PHP-DI container has (such as the registration service functionality).
     * 
     * For more information on how dependency injection is managed within MiddleMan,
     * see @ref md_documentation_dependencyInjection.
     * @return IContainer
     */
    private static function getDIContainer()
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(ROOT_DIR . DS . 'config'.DS.'diDefinitions.php');
        $container = $builder->build();
        return $container->get(IContainer::class);
    }
}
