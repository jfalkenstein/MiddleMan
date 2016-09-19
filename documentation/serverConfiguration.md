Server Configuration for MiddleMan
=====

MiddleMan currently is configured to run with Apache 2.4 and PHP 7, though none of the
code should be invalid for PHP 5.6.

It is currently running on linux (ubuntu), but can just as easily run on a windows
host.

Here are some relevant Apache directives for the current configuration:
@codetly using. It is not necessary, as
#MiddleMan could listen on any port. However,
#This is the port number MiddleMan is curren whatever port being used, Apache
#needs to be configured accordingly.
Listen 8081

<VirtualHost *:8081>
    # The ServerName directive sets the request scheme, hostname and port that
    # the server uses to identify itself. This is used when creating
    # redirection URLs. In the context of virtual hosts, the ServerName
    # specifies what hostname must appear in the request's Host: header to
    # match this virtual host.
    #ServerName www.example.com
    ServerName 10.2.0.70:8081

    ServerAdmin jfalkenstein@nazarene.org
    #Specify the location of MiddleMan's server code
    DocumentRoot /var/www/middleman/public_html
    #Specify the file to run.
    DirectoryIndex index.php
    <Directory /var/www/middleman/public_html>
        Options -Indexes +FollowSymLinks +MultiViews
        #Allow use of .htaccess files to override Apache settings. THIS IS NECESSARY.
        AllowOverride all
    </Directory>
</VirtualHost>
@endcode

In this particular configuration, MiddleMan can be accessed at %http://10.2.0.70:8081.

You will need the **mod_rewrite** and **mod_headers** modules installed.

Within the root directory of MiddleMan, is @link .htaccess @endlink. This file is **ESSENTIAL** for
successful operation of MiddleMan. Here is the full text of this file:
@code
#Allow cross-origin requests
Header set Access-Control-Allow-Origin "*"
#Enable rewriting of the url
RewriteEngine on
#If the directory "d" or file "F" requested do not exist, rewrite the url
#%{REQUEST_FILENAME} : The full local filesystem path to the file or script matching the request.
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-F
#Rewrite the url to index.php and append the chopped off url segments
#in a query string for the keyword of "path" (QSA signifies "Query String Append").
RewriteRule ^(.*)$ index.php?path=$1 [L,QSA]
#If this is set to 'development', error reporting will be turned on. Otherwise,
#they are off by default.
SetEnv APPLICATION_ENV 'development'
#Only allow access within GMC
Require ip 10
@endcode

This file will override any urls directed at MiddleMan and will direct all requests
to index.php, appending any URL segments to the query string as "path."

Thus, %http://10.2.0.70:8081/Demo/GetInfo would become %http://10.2.0.70:8081/index.php?path=Demo/GetInfo.
