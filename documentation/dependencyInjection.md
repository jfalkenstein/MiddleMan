Dependency Injection in MiddleMan
====

What is dependency injection?
-----
Dependency Injection is, in its most basic form, a way to ensure that every part
of an application is "loosely coupled" with other parts.

When dependencies are injected, classes don't directly reference other classes that
they need to operate. Classes should rarely use the "new" keyword. Instead, when classes
rely on other classes to function, they instead rely upon interfaces that define the
funtionality they need to operate.

The best way to explain how this works in the context of MiddleMan is to give an example.

The @link PB::Core::Application::Application Application @endlink class is responsible
for managing the lifecycle of MiddleMan. It doesn't need to know HOW the various components
work together. All it needs to know is that an implementation of @link PB::Core::Interfaces::IManager IManager
@endlink will take care of those details. It doesn't care "*who*" the manager is,
just as long as the manager knows what to do when the Application calls
@link PB::Core::Interfaces::IManager::serializeResponse() serializeResponse()@endlink.

To facilitate this, the IManager is injected as a constructor parameter on the Application
class, and then saved as a private field. It looks like this:

    class Application implements IApplication, ISubscriber{    
        private $manager;
        
        public function __construct(IManager $manager, IEventManager $eventManager) {
            $this->manager = $manager;
            $eventManager->addSubscriber($this);
        }
    }

This way, *some* implementation of IManager is injected into the constructor,
and that IManager is what the Application uses.

The benefit of this kind of architecture is that parts of an application can be
readily swapped out without having to change any other parts.

Now, the default implementation (at the time this is being written) of IManager
is the @link PB::Core::Manager::Manager Manager @endlink class. This *probably* won't
be switched out because it is a core class. However, it *could* be.

A different example might be more enlightening:

When @link PB::Core::Manager::Manager::serializeResponse() serializeResponse() @endlink 
is called on the Manager class, it obtains an instance using the AppFactory (more on that
later) of the @link PB::Core::Interfaces::IResponseSerializer IResponseSerializer @endlink.
This interface has only one function signature:

     public function Serialize(Request $request, Response $response);

The Manager just needs an IResponseSerializer that knows how to respond to this function
call. Again, the Manager doesn't need to know *how* it serializes the response,
only that it *does* serialize the response and that it returns a string when it does.

**This is very useful**. Here's why: Right now, the functioning implementation of
IResponseSerializer is @link PB::Core::Response::JsonPSerializer JsonPSerializer @endlink.
The client javascript library that uses MiddleMan is set up to work with JSONP. However,
There may very well come a time when we decide to use JSON or XML or some other structure
of serialized data. To make this change, all we would need to do is swap out what
implementation of IResponseSerializer we want to use and create just one class
for that new serializer. That's it! Nothing in the application would break when we changed
what dependency the Manager was using. It doesn't care **who** is serializing
the response, as long as it **can** serialize it.

What is a dependency injection framework?
-----
A DI framework (most simply) knows how to instantiate objects that have dependencies,
and it knows (at run time) what concrete implementations to use in place of the
requested interfaces.

Many DI Frameworks (like the one utilized by MiddleMan) utilized what is called 
a Dependency Injection Container (or DI Container). This enables you to request from
the container an instance of a certain interface, and it will give you the concrete
implementation of it, all constructed and ready to use.

Going back to the previous example of the Application class:

     class Application implements IApplication, ISubscriber{    
        private $manager;
        
        public function __construct(IManager $manager, IEventManager $eventManager) {
            $this->manager = $manager;
            $eventManager->addSubscriber($this);
        }
    }

Notice that Application implements @link PB::Core::Interfaces::IApplication IApplication @endlink.
Thus, when the @link PB::Core::Bootstrapper::Bootstrapper @endlink needs the application
to run, it doesn't call

    $app = new Application();

Instead, it requests from the DI container an instance of IApplication:

    $app = $container->get(IApplication::class);

From this, the DI framework will check the definitions (more on this later) and see
that the currently configured mapping for IApplication is Application. Then it
will attempt to construct the Application class. **But wait!** The Application
class has dependencies (IManager and IEventManager). The framework will then 
check the mappings and instantiate the concrete implementations of those classes
as well.

Each concrete implementation instantiated is cached in the DI container. This way,
there is only ever ONE manager and ONE event manager ever referenced. Even if
more than one class needs the same dependency, it will be a shared reference in
memory. This allows, for example, the @link PB::Core::ConfigManager::ConfigManager
ConfigManager @endlink to be used throughtout the application, with modifications happening
to the configArray that it contains at different points.

The dependency injection framework that MiddleMan utilizes is PHP-DI (obtained
as a Composer package). For more info on PHP-DI, you can [view their website](http://php-di.org/).

How does PHP-DI know what interfaces are mapped to what classes?
-----
The mappings from interfaces to classes are (mostly) found within the config\diDefinitions.php
file. This file is an associative array, where the interface names are the keys
and the values are the definitions of how to construct the objects. This definition
array is provided to the container when it is being built. You can find this in the
@link PB::Core::Bootstrapper::Bootstrapper::getDIContainer() Bootstrapper::getDIContainer()@endlink
method.

Furthermore, the PHP-DI container provides a set() method, which allows more definitions
to be added after the container is built. This functionality is used by modules, which
need to register their dependencies after the application is already bootstrapped.

How does MiddleMan use PHP-DI?
-----
MiddleMan, true to the spirit of Dependency Injection, encapsulates and abstracts the
dependency injection framework as well. Thus, in theory, PHP-DI could be swapped out
for another framework and the application would still work just as it does not.

This abstraction is done with the @link PB::Core::Interfaces::IContainer IContainer@endlink
interface. This interface is almost verbatim the sum of the method signatures of the PHP-DI
container, with one addition: @link PB::Core::Interfaces::IContainer::getRegistrationService()
 getRegistrationService()@endlink. This function provides a @link PB::Core::Interfaces::IRegistrationService 
 IRegistrationService@endlink. An IRegistrationService provides
access to the set() method on the IContainer, but does not allow other access to the IContainer.
This is for module functionality, where the IRegistrationService is necessary for
module initialization, but where it is not ideal to provide total access to the IContainer.

The concrete implementation of IContainer in MiddleMan is @link PB::Core::Container::ContainerWrapper
ContainerWrapper@endlink, which receives the PHP-DI container as a constructor
parameter and then encapsulates it. The methods on ContainerWrapper (*with the exception of
getRegistrationService()*) simply call the same named methods on the PHP-DI container.

What is the AppFactory?
----- 
Just as the IContainer provides an IRegistrationService, which exclusively provides
access to the IContainer's set() method, the @link PB::Core::AppFactory::AppFactory 
AppFactory@endlink does the same with the get()
method.

The AppFactory's purpose is to provide access to all registered dependencies within
the MiddleMan application. It receives the IContainer as a constructor argument,
then exclusively uses the IContainer's get() method to provide instances of all
the mapped dependencies.

The AppFactory allows the @link PB::Core::Manager::Manager Manager@endlink and all
modules to have access to application dependencies when they need them. 