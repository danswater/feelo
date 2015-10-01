##Getting Started

This documentation describes how to get started with SocialEngine 4.

##Requirements
###Minimum
 - Apache-based shared web server
 - Apache mod_rewrite (only for Apache v2.0.29 or below)
 - MySQL 4.1
 - PHP 5.12
 - PHP configuration options
1. magic_quotes_gpc = off
2. safe_mode Off
 - PHP extensions
1. gd2 (image processing)
2. curl (fetching URLs)
 - PHP memory limit: 32M+
 - Sendmail

###Recommended

 - Apache-based VPS or dedicated web server
 - PHP 5.26 or newer
 - MySQL 5.0 or newer
 - crontab

###Optional
 - Apache-based VPS or dedicated web server
 - PHP 5.26 or newer
 - MySQL 5.0 or newer
 - crontab
 - memcached (speed boost)
 - PHP extensions
1. apc (speed boost)
2. dom (required for RSS feeds and link previews)
3. hash OR mhash (required for Amazon S3 support)
4. iconv (required for non-English support)
5. json (speed boost)
6. mbstring (required for non-English support)
7. mysqli OR pdo_mysql (more reliable SQL support)
 - Apache mod_rewrite (for SEO-friendly URLs)

###Optional Requirements for Video Plugin
 - ffmpeg (for encoding video) - without ffmpeg, users can still post YouTube and Vimeo videos.

###Optional Requirements for Chat Plugin
 - VPS or dedicated server - budget/shared servers may not handle a high volume of users chatting at the same time
 
###How do I check if my host is compatible with SocialEngine4?

Most modern hosting providers are compatible with SocialEngine4 but if in doubt you can contact them directly and refer them to this page.

##Installing SocialEngine
###Video
<a href="http://www.youtube.com/embed/P-gUEzsesjU
" target="_blank"><img src="http://img.youtube.com/vi/YOUTUBE_VIDEO_ID_HERE/0.jpg" 
alt="IMAGE ALT TEXT HERE" width="240" height="180" border="10" /></a>

###Instructions
1. In order to install SocialEngine4, you need four pieces of information. If you don't have any of these, please contact your hosting provider and ask them for assistance.
    - MySQL Server Address (often "localhost", "127.0.0.1", or the server IP address)
    - MySQL Database Name
    - MySQL Username
    - MySQL Password
5. Download the SocialEngine4 ZIP file and extract it to your computer.
6. Upload all of the files to your hosting account (it can exist either in the root HTML directory, or a subdirectory).
    - If you are using a Unix server (or Unix variant, like Linux, OS X, FreeBSD, etc.) you must set the permissions (CHMOD) of the following directories and files to 777:
    - 
     (recursively; all directories and files contained within this must also be changed
    ```
        /install/config/ 
        /temporary/
        /public/
        /application/themes/
        /application/packages/
        /application/languages/
        /application/settings/
    ```
7. Access the SocialEngine installer by accessing your website; the installer wizard will automatically begin.


###Signing In
Member and Admin accounts are unified in SocialEngine, so you as the site owner can use the same username and password to access the website.  A user account with super-admin privileges is created for you during the installation process.  Additional user accounts can be created via the signup process, and specific privileges can be assigned by you, the admin, after the account has been created.

###Plugin Installation
Plugin installation will function very similarly to the SocialEngine upgrade process.

###Video
<a href="http://www.youtube.com/embed/EvZV8VA49Fc?list=UUlUy2ac9Xb-Bw95e1EyKlEQ&amp;hl=en_US
" target="_blank"><img src="http://img.youtube.com/vi/YOUTUBE_VIDEO_ID_HERE/0.jpg" 
alt="IMAGE ALT TEXT HERE" width="240" height="180" border="10" /></a>

###Instructions
1. Download each of the plugin TAR files you wish to install.
2. Log into your SocialEngine 4 site and access the Admin area.
3. Access the _Manage_ menu and click on _Packages &amp; Plugins_.
4. Click on the _Install New Packages_ link, then on the _Add Packages_ link.
5. Select the TAR files you download in step 1
6. Follow the step-by-step wizard to complete the installation of the plugin files.


##Upgrading SocialEngine
SocialEngine 4 supports upgrades via our package manager.
###Video
<a href="http://www.youtube.com/embed/wt5ogBYYfto?list=UUlUy2ac9Xb-Bw95e1EyKlEQ&amp;hl=en_US
" target="_blank"><img src="http://img.youtube.com/vi/YOUTUBE_VIDEO_ID_HERE/0.jpg" 
alt="IMAGE ALT TEXT HERE" width="240" height="180" border="10" /></a>

####Instructions
1. Download the latest Core available from your [SocialEngine Clients area](http://www.socialengine.com/client) 
2. Log into your SocialEngine 4 site and access the Admin area.
3. Access the _Manage_ menu and click on _Packages &amp; Plugins_.
4. Click on the _Install New Packages_ link, then on the _Add Packages_ link.
5. Select the TAR files you download in step 1

    Note: if you have trouble uploading the files using the uploader tool, you can also upload the TAR file manually over FTP to the ```/temporary/package/archive/``` directory.
    
6. Follow the step-by-step wizard to complete the installation of the Core.

##Developers Guide
###Tools
 While the following are not necessary for modifying SocialEngine 4, we recommend the following tools for your development environment.
 
 - Netbeans IDE [http://netbeans.org/features/php/](http://netbeans.org/features/php/)
 - Set SocialEngine4 to be in "Development" mode (see _Admin &gt; Settings &gt; General Settings_).  This prevents the use of caching, while also enables error messages to be displayed to the front-end interface.

###Structure
SocialEngine4 is based on the Zend Framework, and is built in an MVC (Model-View-Controller) structure.  It is also built with modularity in mind.

The directory structure is as follows:
 - ```/application/``` This directory contains the majority of SocialEngine files.
    - ```/application/languages/```
    - ```/application/libraries/``` 

    Third party PHP libraries are typically contained in here.  For example, Zend Framework and CSS Scaffold both live in this directory, as does our extensions to Zend Framework (called Engine).
    - ```/application/modules/```
    - ```/application/settings/``` 
    
    These files contain configurations that typically will not be modified after your initial installation.  Things like your database username/password, cache settings, mail settings, etc are stored in here.
    - ```/application/themes/```
 - ```/development/``` 
  
    Various tools that we have used during development are contained in this directory.  You can mostly ignore this hodge-podge of files.
 - ```/externals/```
    
    Most of our images, javascript libraries, and flash applications that are used globally are retained in this directory.  These should contain trusted (i.e. not user-uploaded) content.

 - ```/public/```
 
    User-uploaded content is always contained in here.  This directory should be both web-accessible and writable by the web server (chmod 777 recursively). 

 - ```/temporary/```

 Various temporary files, such as cache files, logs, session files, etc are stored in this directory.  It should not be web-accessible, but must be writable by the web server (chmod 777 recursively).  We provide an ".htaccess" file to block access to this directory from the web browser, but if you are using a web server other than Apache, we recommend you configure the web server accordingly to prevent view access.
 
 
 ###Plugins
 ```/application/modules/*```
 
  Most of SocialEngine's functionality resides here.  Each module contains within it the MVC structure, where the "Model", "views", and "controllers" directories correspond to the MVC paths.  Please see our included skeleton module "HelloWorld" for more information on this.
 
 
 ###Languages
 ``/application/languages/*``
 
 Each language gets its own two-letter (or 5-character with localization support) directory in this sub-directory.  The language files are in multiple CSV files, though they are concatenated into one large CSV file (in no particular order, except that "custom.csv" is the last).  One important point to keep in mind is that duplicate keys override previously defined keys.  So, for example, if you have a key "Turtles are fast!" in both core.csv and custom.csv, since custom.csv is loaded last, the value set to "Turtles run fast!" in custom.csv will be the one used.
 
 The CSV files have several requirements:
 - Each line has at minimum two columns, and can contain more for different pluralizations.
 - Each column is separated using a semi-colon, and is enclosed in "double-quotes" if it contains any sort of white-space (we recommend wrapping your values in quotes in all cases just to be safe).
 - The first column is always going to be the ```key```.  This is the English word or phrase that exists in the view script, controller, or other portion of the code.
 - The second column is always going to be the default translation of the ```key```.
 - If there is an subsequent columns after the second column, it will be for various pluralizations of the ``key``
 Untranslated variables can be contained within a translation ``key`` and translations.  Variable replacement is done using the PHP function [sprintf](http://php.net/sprintf), and so the same rules apply.  When only one variable is being injected into a translation, typically "%s" is the placeholder for that variable.  If multiple variables are being injected, you can use "%1$s", "%2$s", "%3$s" etc to put the variables in their proper positions for your translation.
 If your translation has double-quotes in it, you must use two double-quotes.  So as an example, the sentence 'He said "wow".' would appear in a CSV files as:
            "He said ""wow"".";"He said ""wow""."

###Themes
``/application/themes/*``

We utilize a framework called "CSS Scaffold" which makes editing your community's theme a simple process. Each theme is stored in its own directory within ``/applications/themes/``. A default theme is automatically loaded when you first install SocialEngine. Each theme contains two files: constants.css and theme.css.

At the top of constants.css, you'll find a series of global CSS settings (called "constants"). You can edit these to adjust the colors, fonts, and other styles throughout your entire community.

The other file, theme.css, contains more specific styles that are used throughout your community. Many of these styles inherit values from constants.css. If you want to override any of the default styles on your community, you can edit them here. If they aren't present in theme.css (and are being loaded from outside the theme itself), you can override them by adding new styles to the bottom of theme.css.

More information about how to work with CSS Scaffold is available here:

[http://github.com/sunny/csscaffold](http://github.com/sunny/csscaffold) or [http://www.google.com/search?q=csscaffold](http://www.google.com/search?q=csscaffold) for videos and tutorials.

 ©2010 Webligo Developments