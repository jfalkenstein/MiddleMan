MiddleMan Application LifeCycle
=====
@tableofcontents

From received request to ouput response, MiddleMan follows a set path, referred to 
here as a lifecycle. This path could be traced by analyzing the code of the @link PB::Core::Application::Application
Application @endlink and @link PB::Core::Manager::Manager Manager @endlink classes.
However, it might be useful to view them from a high level here. This guide will
trace the path of a successful lifecycle.

@section lifecycle_step1 Step 1. The Client Sends the HTTP Request

At this time, MiddleMan (because it is set to work with JSONP) only accepts HTTP GET
requests, though the server could easily process another type, if the serialization
format were different. For more on JSONP, see @ref jsonp.

The request should include the domain and module in url segments, as well as
other required query string parameters. An example of a full request would be:
@code
http://10.2.0.70/Demo/GetInfo?
    callback=middleMan.callback
    &ssn=111-11-1111
    &returnVals%5B%5D=counter
    &counter=0
    &error=middleMan.error
    &finalCall=middleMan.onFinish
    &token=729f07427e3e8f2360ce9d4aa4c788a5
    &XDEBUG_SESSION_START=netbeans-xdebug
    &_=1471896935663
@endcode

@section lifecycle_step2 Step 2. Apache server redirects the request to index.php
Apache server redirects the request using its mod_rewrite module,
chopping off any url segments past the first one and adding it to the request
as a new query string parameter with the name "path."

Now, the query string, specifically, will be:

@code 
path=Demo/GetInfo
    &callback=middleMan.callback
    &ssn=111-11-1111
    &returnVals%5B%5D=counter
    &counter=0&error=middleMan.error
    &finalCall=middleMan.onFinish
    &token=729f07427e3e8f2360ce9d4aa4c788a5
    &XDEBUG_SESSION_START=netbeans-xdebug
    &_=1471896935663"
@endcode

For more information on server configuration, see @ref md_documentation_serverConfiguration.

@section lifecycle_step3 Step 3. Index.php Bootstraps the application
Index.php initializes the autoloading scripts and then calls
@link PB::Core::Bootstrapper::Bootstrapper::initialize() Bootstrapper::initialize()
@endlink, which initializes the dependency injection framework, loads the @link 
PB::Core::Application::Application application @endlink, then runs the application.

For more information on dependency injection, see @ref md_documentation_dependencyInjection.

The rest of the lifecyle is now managed by the @link PB::Core::Application::Application 
Application @endlink.

@section lifecycle_step4 Step 4. The request is processed.
The input from url parameters and the query string is then processed by the @link
PB::Core::Request::RequestFactory RequestFactory @endlink, filtering the input and
assigning the values to a @link PB::Core::Request::Request Request @endlink object.

@section lifecycle_step5 Step 5. The request is authenticated.
If you notice in the above request example, one of the query string parameters is 
"token." At this time, authentication is done using a salt & hashing system, making use
of MD5 hashing algorithms as well as algorithms known by both client and server to create
the hash itself.

For more information on how authentication is implemented, see @link 
PB::Core::Authentication::MD5Authenticator MD5Authenticator @endlink.

During the process of authentication, a server token is generated. This is a token
that is created to return in the response so that the client can authenticate the response.
Because JSONP is essentially foreign code being executed on the eform, the client
has certain authentication requirements before the code is executed.

If authentication fails, the remaining steps until response packaging are skipped
and an authentication fail response is sent to the client.

@section lifecycle_step6 Step 6. The module is located and set.
Using the Domain and Module segments of the URL, MiddleMan locates the module
from installed code and instantiates the module. This is handled by the @link
PB::Core::ModuleManager::ModuleManager ModuleManager @endlink.

@section lifecycle_step7 Step 7. The module is initialized.
The @link PB::Core::ModuleManager::ModuleManager ModuleManager @endlink obtains any configuration
settings provided by the module, registers any dependencies required by the module,
and runs any initialization code provided by the module. At this point the module
is ready to be executed.

@section lifecycle_step8 Step 8. The module is executed.
The module provides an @link PB::Core::Interfaces::IExecutable IExecutable @endlink
and then the @link PB::Core::ModuleManager::ModuleManager ModuleManager @endlink
executes that code. When executing, the module has full access to all registered
dependencies of MiddleMan, all configuration settings, and the @link PB::Core::Request::Request 
Request @endlink object that was processed in step 4. This enables the module to
access any required parameters.

Any result of execution is returned and stored.

@section lifecycle_step9 Step 9. The response is processed.
Now that the result of the module execution has been received, the response is
ready to be prepared and processed. The @link PB::Core::Response::ResponseFactory ResponseFactory 
@endlink now takes the request, the authentication token, and the received data
from module execution, and packages it into a @link PB::Core::Response::Response Response @endlink
object.

@section lifecycle_step10 Step 10. The response is serialized.
The response object is then passed to a serializer to convert it to string for output.
At this time, the method of serialization is JSONP. For details on how JSONP is output,
see @ref jsonp and @link PB::Core::Response::JsonPSerializer JsonPSerializer @endlink.

@section lifecycle_step11 Step 11. The serialized response is output.
Quite simply, MiddleMan echoes the serialized string to the client.


@section lifecycle_exception What if an exception is encountered during the course of execution?
The @link PB::Core::Application::Application Application @endlink (when run)
has a global exception handler, which will catch any exceptions that bubble up.
These exceptions will then be prepared as a response and (after being converted
to avoid any undesired details being revealed) output to the client. Thus,
exceptions will still be output, though with different information on them.

@section lifecycle_authentication What if authentication fails?
If authentication fails, the user will be notified of this authentication failure.
No module will be executed. Quite simply, the failure will cause lifecycle to skip
to step 9 and a special authentication failure response is prepared, then output.