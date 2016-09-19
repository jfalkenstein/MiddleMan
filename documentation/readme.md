@tableofcontents
This is the API Documentation for MiddleMan.

@section readme_whatIsMiddleMan What is MiddleMan?
MiddleMan is a middleware server application intended to provide a single
network-only access point to whatever data might be desired on an Onbase Eform.
It provides module-based system that can provide whatever functionality is desired
and make such data accessible via a an api accessible only over the internal network.

@section readme_moduleDocumentation Where can I find module documentation?
See @ref moduleIndex.

@section readme_HowDoesItWork How does it work?
MiddleMan receives http requests at an ip address and port, routed through Apache
via a virtual host. Directed by an .htaccess file in the root directory, Apache will
direct all requests to index.php, removing any further url segments and converting them
to a query string value for "path."

@section readme_urlStructure What is the basic url structure for a request?

    http://ip.ad.dr.ess:port/Domain/Module

Because MiddleMan is only accessible over the local network (it is not publically
exposed for access), the ip address most likely will begin with 10.2.0.

@section readme_domainsAndModules What are domains and modules?
The core code of MiddleMan functions as a support system for modules. Modules are the real
<em>meat and potatoes</em> of MiddleMan. Modules have access to all application
functionality and services via an injected @link PB::Core::Interfaces::IAppFactory IAppFactory @endlink.

Modules can access databases and other files, perform CRUD functions, join multiple data sources
and types, and then return data to the calling client.

All modules exist within a domain. A domain is some kind of grouping or category to indicate
that can group modules together.

For information on how to create modules, see @ref md_documentation_modules. 

@section readme_receiveSend How does it receive requests/send responses?

@subsection jsonp About JSONP
The MiddleMan application does not communicate between server and client using standard
ajax requests with json. The reason for this is because browsers (including the one in OnBase)
do not generally allow for ajax requests to be sent cross-domain. In the case of OnBase,
the "domain" of the client is different from the "domain" of the MiddleMan server. Therefore,
the OnBase browser will disallow any ajax requests.

One way around this limitation is a process called JSONP. A good explanation of
jsonp and how it works can be found [here](https://www.sitepoint.com/jsonp-examples/).

A very simple explanation, however, is relevant here:

Instead of sending a raw json response from the server, the server instead sends a
script file with a function call wrapping the json object as a parameter. This function call's
name is sent as part of the request parameters for the server to use.

For example, instead of sending this json as a response:
    
    {
        firstKey: "firstValue",
        keyToObject: {
            color: "blue",
            height: 50
        }
    }

The equivalent in jsonp, would be:

    callback({
        firstKey: "firstValue",
        keyToObject: {
            color: "blue",
            height: 50
        }
    });

@subsection readme_request The Request
Because of the limits of jsonp, data can only be sent to the server via http GET
requests. Other than the domain and module, which come from position-based url segments,
the remainder or the required parameters for MiddleMan and the requested module
are obtained from a query string.

@subsection readme_response The Response
MiddleMan uses a variation on jsonp to send the response. Instead of sending just a single function call, the server
sends a try/catch/finally block to sufficiently cover error handling. This is necessary
because a jsonp response is loaded as a script file and executed in the global 
namespace. No other level of try/catch will cover the function call.

Thus, instead of one callback parameter being sent to the Server, the middleMan.js
sends 3:
1.  The name of the success function to execute
2.  The name of the error-handling function to execute if an error is caught
3.  The name of the function to call regardless of whether there was an error 
caught or not so the script can know if the request timed out

With these parameters, the server will respond with a script file that looks like this:

    try{
        middleMan.callback({/*The response object*/});
    }catch(error){
        middleMan.error(error);
    }finally{
        middleMan.onFinish();
    }

This effectively abstracts any errors away from the user and can be dealt with
in the "onFailure" callback.

@section readme_secure Is it secure?
There are several levels of security  for MiddleMan.

*   First and foremost is that it is only accessible behind the firewalls of the GMC network. It is not accessible
externally and is not assigned to a domain name. The virtualhost that MiddleMan is served
over requires an ip address that begins with 10. Thus, this forces requests that only
originate from within our internal network.

*   It uses a non-typical port number. As of the date of this document, to access
MiddleMan, you need to call it on its internal network ip address and the port 8081.

*   Requests are authenticated with a hash & salt system. With the request must be a
"token" that is created on the client and replicated on the server, using a hashing
algorithm that is known by both and a secret string that is known by both.

*   The hashing algorithm and secret string used to create this authentication token
for the client is stored on a network drive, separate from the calling page itself. 
Thus, even if someone were to obtain the eform html with the calling script, that person
would not actually know what it would take to submit an authentic request to the server.

For more information on the process of authentication, see @link PB::Core::Authentication::MD5Authenticator the MD5Authenticator @endlink class.

@section readme_process How does MiddleMan process requests and return responses?
See @ref md_documentation_lifecycle.

@section readme_namespace A note about namespace & directory structure
MiddleMan makes use of php5's autoloading functionality. You can find documentation
on the specifics [here](http://php.net/manual/en/function.spl-autoload-register.php).
This is implemented in scripts\autoload.php.

To put it very simply, MiddleMan's autoloader will activate whenever a class is utilized
for which the .php file has not yet been included. The autoloader will (based upon the namespace
and class requested) attempt to locate the .php file and then require it--after which, the calling
code will be able to use that class.

@subsection readme_autoloading Requirements for successful autoloading:
*   All classes must be on individual php files, named after the class they contain.
No other code must exist outside the class other than namespace and use statements.
Thus, for example, the class named Application exists in Application.php.
*   All namespaces must be subnamespaces of the namespace PB.
    *   Core code (i.e. essential to the operation of the application) must exist
        within PB::Core.
    *   Module code must exist within PB::Modules.
*   The Directory structure must correspond with the namespace heirarchy. Thus,
    PB::Core::Request::Request must have the file & directory structure of 
    
        PB\Core\Request\Request.php

*   Modules must all be in %PB\\Core\\Modules\\[Domain Name]\\[Module Name]\\

@section readme_di How does MiddleMan's dependency injection system work?
See @ref md_documentation_dependencyInjection.

@section readme_serverConfig How should the server be configured?
See @ref md_documentation_serverConfiguration.

@section readme_config What are the configuration settings used by MiddleMan?
See @ref md_documentation_configSchema.

@section readme_modules How do I make modules for MiddleMan?
See @ref md_documentation_modules.
