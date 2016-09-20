<?php

use PB\Core\AppFactory\AppFactory;
use PB\Core\Application\Application;
use PB\Core\Authentication\MD5Authenticator;
use PB\Core\ConfigManager\ConfigManager;
use PB\Core\Container\ContainerWrapper;
use PB\Core\Events\EventManager;
use PB\Core\Interfaces\IAppFactory;
use PB\Core\Interfaces\IApplication;
use PB\Core\Interfaces\IAuthenticator;
use PB\Core\Interfaces\IConfigManager;
use PB\Core\Interfaces\IContainer;
use PB\Core\Interfaces\IEventManager;
use PB\Core\Interfaces\IManager;
use PB\Core\Interfaces\IModuleManager;
use PB\Core\Interfaces\IRegistrationService;
use PB\Core\Interfaces\IRequestFactory;
use PB\Core\Interfaces\IResponseFactory;
use PB\Core\Interfaces\IResponseSerializer;
use PB\Core\Manager\Manager;
use PB\Core\ModuleManager\ModuleManager;
use PB\Core\Request\RequestFactory;
use PB\Core\Response\HeadersSerializer;
use PB\Core\Response\JsonPSerializer;
use PB\Core\Response\JsonSerializer;
use PB\Core\Response\ResponseFactory;
use function DI\object;


/**
 * This is the dependency injection mapping used by PHP-DI. Specifically, it
 * provides the mapping details between interfaces and the classes to provide
 * when the interface is requested. Thus, the code within PB::Core (for the most part)
 * does not depend on concrete implementations, but rather on interfaces.
 * 
 * The benefit of this mapping is that it will be very easy to swap out what class
 * to use when a particular interface is requested. Thus, if for the serializer
 * xml was now desired rather than jsonp, the class could be written and reconfigured here
 * without having to change any other code within PB::Core.
 */
return [
    IContainer::class => object(ContainerWrapper::class),
    IRegistrationService::class => function (IContainer $container){
        return $container->getRegistrationService();
    },
    IApplication::class => object(Application::class),
    IManager::class => object(Manager::class),
    IRequestFactory::class => object(RequestFactory::class),
    IEventManager::class => object(EventManager::class),
    IAuthenticator::class => object(MD5Authenticator::class),
    IConfigManager::class => object(ConfigManager::class),
    IResponseFactory::class => object(ResponseFactory::class),
    IResponseSerializer::class => function(IConfigManager $config){
        $requested = $config->getValue(['serializer','requested']);
        switch ($requested){
            case 'json':
                return new JsonSerializer($config);
            case 'jsonp':
                return new JsonPSerializer($config);
            case 'headers':
                return new HeadersSerializer($config);
        }
    },
    IAppFactory::class => object(AppFactory::class),
    IModuleManager::class => object(ModuleManager::class),
];
