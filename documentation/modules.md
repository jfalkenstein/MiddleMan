Creating Modules
===
This is the guide on how to create modules for MiddleMan.

@tableofcontents

@section modules_beforeRead Before you read this guide...
It is important that you read the following:

*   @ref index "ReadMe"
*   @ref md_documentation_lifecycle
*   @ref md_documentation_dependencyInjection (**Read this one twice!**)
*   @link PB::Core::ConfigManager::ConfigManager ConfigManager class reference@endlink.

You need to know how requests move through the lifecycle and you need to know
how MiddleMan manages its dependencies. Pay special attention to the section on
namespaces and directory structure. Also read up on how configurations are accessed.

@section modules_whatIsModule What is a module?
Quite simply, modules are units of code that MiddleMan knows how to locate, initialize, and use.
Generally speaking, a module receives input parameters and outputs data in the form of a single
data object or array.

With that said, it is *very* possible to make a module that accepts no parameters.
It is also very possible to make a module that doesn't output a response. However,
if a response is created, it MUST be able to be serialized (usually in JSON). Thus,
it should only have members of basic data types (string, integer, boolean, etc...).

@section modules_basicRequirements Basic requirements for all modules

1.  All modules **must** implement the @link PB::Core::Interfaces::IModule IModule@endlink
interface. A partial implementation is already provided as an abstract class in 
@link PB::Core::ModuleManager::Module Module@endlink. This class has some default functionality
built in and it is **strongly recommended** that modules extend this class.
2. All modules must exist within a domain. This is simply a category or grouping
for better organization of modules. More than one module can exist within a domain.
Use a domain that has some meaning like "Insurance" or "DC" or "BasicInfo".
3.   All modules must use a namespace & folder heirarchy within PB\\Modules.
4.   All modules must be within a namespace & folder heirarchy for their domain
and module name. For example, if the module "GetInfo" existed within the "Demo" domain, the module
would have the namespace and folder heirarchy of: %PB\\Modules\\Demo\\Domain\\GetInfo\\.
5.   All modules must have a file name and class name that begins with "mod_". Thus, 
using the Demo\GetInfo module, the actual module class would be called "mod_GetInfo"
and the file would be "mod_GetInfo.php." Thus, its directory structure would be:
%PB\\Modules\\Demo\\GetInfo\\mod_GetInfo.php. 
6.   All modules (whether they extend @link PB::Core::ModuleManager::Module Module@endlink
or not) must have constructors with no parameters. Any dependency injection
required for the modules may be accessed with the initialize() function.
7.   If a module provides a configuration array, the structure of the config array must begin
with "modules" => domain name => module name => [].
For example, for the module named "GetInfo" in the "Demo" domain, the structure
would look like this:
@code
     return [
         'Demo' => [
             'GetInfo' => [
                 ... Whatever key/value pairs are required ...
             ]
         ]
     ];
@endcode
8.  All modules must provide an @link PB::Core::Interfaces::IExecutable IExecutable@endlink
in their implementation of @link PB::Core::Interfaces::IModule::getExecutable() IModule::getExecutable()@endlink.
More on this later.
9.  All modules must provide documentation, utilizing Doxygen's notation in a markdown (i.e. .md) file.
You can find out how to use doxygen [here](http://www.stack.nl/~dimitri/doxygen/index.html) and
you can learn more about markdown [here](https://daringfireball.net/projects/markdown/).
Documentation on a module must, at minimum, have the following sections:
    *   **Required Input** - The input parameters (and format) required for the module to operate.
    *   **Structure of output** - The structure of the json-encoded data object to expect in response.
    *   (*if the module has any configuration*) **Where is this configured?** - The 
        location of the config file or wherever else the configuration array is set.
    *   (*if the module has any configuration*) **What is the configuration schema?** - 
        The definition of the key/value pairs used in configuration for the module.

@section modules_configuration A Note about Configurations
Modules provide configurations when the module's 
@link PB::Core::ModuleManager::Module::getConfigs() getConfigs()@endlink method is called.
A module can provide any configurations it desires in one of two ways:

*    They can come via a simple returned associative array right within the function body.
This is the simplest approach, but the least maintainable, because the module's code has to
be searched through to see what configurations are provided.
*    They can come from a file placed wherever is accessible to MiddleMan. This is a better
approach, as the config file can be easily found and accessed.

The recommended @link PB::Core::ModuleManager::Module Module@endlink class provides 
a default location and implementation of getConfigs(), and it is strongly encouraged
that this be the convention followed. It will look for a file called "config.php" 
in the same directory as the module file and, if it exists, include it.

If this default implementation is used, the config.php must be a return statement
that returns an associative array of config values.

If this default implementation is not overridden and if config.php doesn't exist
in the module directory, getConfigs() will return an empty array with the effect
of no action being taken regarding configurations.

@section modules_exceptions Throwing exceptions in your module

@subsection modules_exceptions_whyThrow Why throw exceptions?
There may be times (such as when necessary parameters are not present in the request)
that you may want to throw an exception. The benefit of doing this is that all further
execution stops and the exception will be caught by the global exception handler
in @link PB::Core::Application::Application::run() Application::run()@endlink. When
the global exception handler receives an exception, it will serialize it and send it
to the client. This is very useful because it will trigger the client's onFailure
callback, which they can use to help resolve the issue.

@subsection modules_exceptions_guidelines Guidelines for throwing exceptions in modules

*   **You can throw a standard PHP Exception**, like this: @code{.php}throw new Exception();@endcode
If you throw a standard Exception, the @link 
PB::Core::ModuleManager::ModuleManager ModuleManager@endlink will catch this and create
a generic @link PB::Core::Exceptions::ModuleLoadException ModuleLoadException@endlink
(if it had to do with the loading process) or  @link 
PB::Core::Exceptions::ModuleExecutionException ModuleExecutionException@endlink (if
it happened during th execution process). There will be a generic message sent to the client
saying something along the lines of "There was a problem executing the module." However, you might want
to communicate a more specific message. If so, see the remaining options.
*   **You can throw a ModuleLoadException or ModuleExecutionException yourself**, with
a message of your choosing, such as this: @code{.php} throw new ModuleExecutionException("SSN is a required parameter.");@endcode
In this case, global exception handler will send the message "SSN is a required 
parameter" as the message in the response to the client.
*   **You can create your own Exception class(es)** and throw those. If you do this,
**it is very important that you extend PB::Core::Exceptions::PbException**. If you
don't and just extend Exception, this will be treated just like the first case listed
above. Thus, your exception would look like this:
@code{.php}
namespace PB\Modules\MyDomain\MyModule;

/**
 * An exception encounted during authentication.
 *
 * @author jfalkenstein
 */
class MyNewException extends PbException{
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
@endcode
Now, when you want to throw your new exception, you would just do this:
@code{.php}
throw new MyNewException("You gave me bad parameters, you silly goose!");
@endcode

@section modules_events Using events in your module
You might want to use events in your module. PHP unfortunately doesn't have a native
event system. However, MiddleMan has it's own custom event notification system
that you can use. To use it, simply access PB::Core::Interfaces::IEventManager from
the @link PB::Core::Interfaces::IAppFactory IAppFactory@endlink that is provided
you and then you can implement these events.

For more info on using events, see the documentation for @link PB::Core::Interfaces::IEventManager
IEventManager@endlink and @link PB::Core::Events::EventManager EventManager@endlink.


@section modules_lifecycle The IModule's Lifecycle
In the course of the application lifecycle, the module has it's own sub-lifecycle.

1.  The module is located according to its domain and module name <em>
by @link PB::Core::ModuleManager::ModuleManager::getModule() ModuleManager::getModule()@endlink.</em>
2.   __Construct() - <em>by @link PB::Core::ModuleManager::ModuleManager::getModule() ModuleManager::getModule()@endlink.</em>
The module is instantiated.
3.  @link PB::Core::Interfaces::IModule::getConfigs() getConfigs()@endlink - 
<em>by @link PB::Core::ModuleManager::ModuleManager::loadModule() ModuleManager::loadModule()@endlink</em>. 
The module provides its configurations (if any).
4.  @link PB::Core::Interfaces::IModule::registerDependencies() registerDependencies()@endlink - 
<em>by @link PB::Core::ModuleManager::ModuleManager::loadModule() ModuleManager::loadModule()@endlink</em>.
The module registers its dependencies using the @link PB::Core::Interfaces::IRegistrationService IRegistrationService@endlink.
5.  @link PB::Core::Interfaces::IModule::initialize() initialize()@endlink - 
<em>by @link PB::Core::ModuleManager::ModuleManager::loadModule() ModuleManager::loadModule()@endlink</em>.
The module runs any initialization code that might be configured.
6.  @link PB::Core::Interfaces::IModule::getExecutable() getExecutable()@endlink - 
<em>by @link PB::Core::ModuleManager::ModuleManager::executeModule() ModuleManager::executeModule()@endlink</em>.
The module provides an instance of its @link PB::Core::Interfaces::IExecutable IExecutable@endlink.
7.  The IExecutable's @link PB::Core::Interfaces::IExecutable::execute() execute()@endlink 
function is called on the IExecutable <em>by 
@link PB::Core::ModuleManager::ModuleManager::executeModule() ModuleManager::executeModule()@endlink</em>, 
which returns data to be later serialized.


@section modules_ready Once you've read all the resources...
Once you've read all of the above and have read all of the recommended resources,
you are now ready to read and follow along with @ref modules_walkthrough.

  
@page modules_walkthrough A Walkthrough to Creating a New Module
@tableofcontents
This guide will walk you through how to create a new module for MiddleMan.

We are going to create a module called "walkthrough" in the "Demo" domain.

@section modules_step1 Step 1. Create the Directory Structure
The directory structure for the module needs to be as follows:

    PB\
    +--Modules\
        +--Demo\
            +--walkthrough\
                +--documentation\
                +--interfaces\

@section modules_step2 Step 2. Create the files you will need.
Create the the following files in the structure as depicted below:

    PB\
    +--Modules\
        +--Demo\
            +--walkthrough\
                +--mod_walkthrough.php
                +--Executable.php
                +--Repository.php
                +--config.php
                +--documentation\
                |   +--walkthrough.md
                +--interfaces\
                    +--IRepository.php

*A note on these files:* Technically, if your module class implemented both IModule
*and* IExecutable, you would only really need one file to make a complete module
implementation. However, this is not very maintainable and describing this would
not be very helpful in understanding how all the parts work together.

@section modules_step3 Step 3. Begin by creating mod_walkthrough.php

###Begin with a skeleton, extending IModule:
@code{.php}
<?php

use PB\Core\ModuleManager\Module;
namespace PB\Modules\Demo\walkthrough;
/**
 * Provide a meaningful description here.
 *
 * @author jfalkenstein
 */
class mod_walkthrough extends Module {

    public function initialize(IAppFactory $appFactory) {
        
    }

    public function registerDependencies(IRegistrationService $regService) {
        
    }
    
    public function getExecutable(IAppFactory $appFactory){
        
    }
}
@endcode

A few things to notice:
*   The module extends @link PB::Core::ModuleManager::Module Module@endlink.
*   The namespace is %PB\\Modules\\Demo\\walkthrough
*   The name of the class (mod_walkthrough) is prefixed with "mod_"
*   Some of the methods required by @link PB::Core::Interfaces::IModule IModule@endlink 
(which Module implements) are provided for free by extending Module. 
Thus, @link PB::Core::Interfaces::IModule::getName() getName()@endlink,
@link PB::Core::Interfaces::IModule::getDomain() getDomain()@endlink, 
and @link PB::Core::Interfaces::IModule::getConfigs() getConfigs()@endlink are not necessary
to define, because we are relying on the default implementations in Module.

To fill out this module's class, however, we need to do some other things first.

@section modules_step4 Step 4. Create the IRespository Interface
In order to make your code the most maintainable, it's best to make use of the
dependency injection capabilities of MiddleMan. This way, if you ever need to change
your database source or query format, you can and your module will still work just fine.

@code{.php}
<?php
namespace PB\Modules\Demo\walkthrough\interfaces;
/**
 * This interface determines the methods you want your repository to have.
 * In this case, we're just going to need one: getByPersonKey()
 *
 * @author jfalkenstein
 */

interface IRepository {
    /**
     * You should provide some comments on what this function is intended to do
     * and return.
     * @param int $personkey The person key of the person you want to find.
     */
    public function getByPersonKey($personkey);
}
@endcode

A few things to notice:

*   The namespace continues to coinside with the directory structure.
*   Some documentation is provided on the method signature.

@section modules_step5 Step 5. Configuration
Our dummy module we are creating will use a configuration. We'll add more later, but
for now, edit the config.php file to set it up for later addition.

@code{.php}
<?php

return [
    'modules' => [
        'Demo' => [
            'walkthrough' => [
                'table' => 'table1'
            ]
        ]
    ]
];
@endcode

Notice here that the configuration array follows the schema of "modules" => domain => module.
For our stubbed repository and it's fake database, we'll need to know which table to access.
So, we'll create a configuration value we can access later for "table" and give it the value
we want ("table1"). This is good because if the table needs to be changed at a later time, we
can easily go back to this config file and change the table name without having to modify any
of our module's code.

@section modules_step6 Step 6. Create the Repository
A repository, quite simply, is a place where things are stored and where they
can be obtained. You don't actually need a class called "repository" for your module.
However, it is likely your module will be built to get *something* from *somewhere.*
If this is the case for you, you might as well call it a repository.

A repository really can be anything. It will probably be the "meat and potatoes"
of your module. It is beyond the scope of this guide to go into how to use PHP to
access databases. It will suffice here to create a repository "stub" with some
data hard-coded.

@code{.php}
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
@endcode
 
A few things to notice:

*   The class implements the interface we created, IRepository. This is necessary
for the dependency injection to work.
*   The constructor for this class has a dependency on IConfigManager. If this 
repository is being constructed using the AppFactory (we'll see this later on), 
the DI container will inject the IConfigManager so that this module can access it.
Modules may do this for any dependency defined in PB::Core or for any dependencies
defined when the modules' dependencies are registered. 
*   The getByPersonKey() method uses the ConfigManager to obtain which table it
needs to access. It is very likely that in your respository, you will need things
such as ip addresses, usernames, passwords, and other sensitive information that
might change at some point. It is best to keep these things in a configuration file
and access them with the ConfigManager.
*   The return value for getByPersonKey() is either an array (if the record is found)
or a string (if no record is found). Whatever you return from this function will end up
being passed along as the "data" property on the response, which will be received 
by the client.

@section modules_step7 Step 7. Create the IExecutable
The IExecutable is provided by the module and is the class that executes whatever
the module's function is. In this case, we'll call our IExecutable simply "Executable.

@code
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
@endcode

A few things to notice:

*   The Executable implements IExecutable. **This is required**.
*   The Executable's dependency on IRepository is injected in the constructor.
While you could have simply obtained this dependency from the execute() function's
IAppFactory, dependency injection in the constructor is more efficient, less prone to
errors, and provides better IDE type hinting.
*   The required parameter (personKey) is obtained from the passed in @link 
PB::Core::Request::Request Request@endlink. Any parameters needed would be accessible
from Request->Data.
*   If the parameter is not found, a @link PB::Core::Exceptions::ModuleExecutionException 
ModuleExecutionException@endlink is thrown with a relevant message.
*   The data returned by the repository call is returned from this function.


@section modules_step8 Step 8. Implement these changes in mod_walkthrough
Now that the configuration is set up and the interface and repository are configured,
now you just need to "wire it together" in the module class.

@code{.php}
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
@endcode

A few things to notice:

*   For this particular module, no initialization code is necessary. However, if
you needed to do something like gather files and network resources before you created
the executable, this could be a place to do it. If you have no intialization code,
you can leave that function empty.
*   Notice how the registration service is used in the registerDependencies() function.
Both IExecutable and IRepository are associated with their implementations in this module.
*A tip:* the "class" constant that is being used ensures that the fully qualified
full name is used as the parameter. Because the example above uses "IExecutable" instead
of "%PB\\Core\\Interfaces\\IExecutable", making use of a use statement above that,
the "class" constant is used in place of that. This way cumbersomely long class
names with namespace are replaced with "IExecutable::class" and "Repository::class."
This step registers the dependencies that will be used later on.
*   In the getExecutable() function, the IAppFactory is used to obtain an instance
of IExecutable, which we registered in the registerDependencies() function. This
enables the IExecutable to have any dependencies it needs automatically injected 
into its constructor. In the case of this walkthrough, the Executable has a dependency
on IRepository, which has a dependency on IConfigManager. In this one function call,
the Executable is entirely ready to be used.

@section modules_step9 Step 9. Test it out!
Using the middleMan.js script library, you can now make the following function call:

@code{.js}
middleMan.get({
    domain:"Demo",
    module: "walkthrough",
    data:{
        personKey:"1234"
    },
    onSuccess: function(data){
        //Do something with the data
    },
    onFailure: function(failureType, failureMessage){
        //Do something with the failure
    }
});
@endcode
If you've followed along with this guide, this function call should now work
and return the date we've stubbed out in Repository.

@section modules_step10 Step 10. Create Documentation
A module is only as useful as the documentation provided for it. Every module
**must** have documentation. Specifically, the module needs to provide *at least*
these sections:

*   **Required Input** - The input parameters (and format) required for the module to operate.
*   **Structure of output** - The structure of the json-encoded data object to expect in response.
This should include all possible scenarios, such as what a successful response would look like
as well as what the response would look like if the record or resource could not be found.
*   (*if the module has any configuration*) **Where is this configured?** - The 
    location of the config file or wherever else the configuration array is set.
*   (*if the module has any configuration*) **What is the configuration schema?** - 
    The definition of the key/value pairs used in configuration for the module.

Furthermore, a reference to your module's documentation should be included in the
documentation\moduleIndex.md file in MiddleMan's root documentation folder. 

To create proper documentation, you will need to understand markdown and Doxygen's
special notation, specifically how to use the \\link, \\ref, \\page, \\section, and
\\subsection special commands. You can find doxygen's documentation 
[here](http://www.stack.nl/~dimitri/doxygen/manual/index.html).

You should put your documentation file in your module's documentation folder.