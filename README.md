#MiddleMan
A php web service for centralized data access.

##Purpose of Project
This is an http server application that was written to provide data access for
our OnBase document database. It was a project I spent about a month on at my work.

It is intended to be modular, so that a call can be made from OnBase via an http 
request and that request can obtain whatever data the module was configured to access.
That data will be serialized in whatever form is requested. Currently, the serialization
methods available are JSONP, JSON, and all http headers (no body).

##Permissions to post
While this is a product of my work, I have been given permission by my supervisor to
post this on GitHub for the purposes of developing my online portfolio. It has
been stripped of any sensitive data.