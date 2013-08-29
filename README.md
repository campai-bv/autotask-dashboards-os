# Campai Autotask Dashboards
Slick ticket performance dashboard to provide your teams with an at-a-glance status overview. Great for big screens and a perfect show-off for visiting customers. All dashboards are configurable and compatible with mobile and tablet screen sizes.

# Requirements
To use this application you'll need:
- a Webserver with a running CakePHP 2.x project (For details on how to install CakePHP, please see this manual: http://book.cakephp.org/2.0/en/installation.html).
- an Autotask user that has API access.

# Installation
* Copy the files in this directory into app/Plugin/Autotask.
* Load the Plugin by editing your app/Config/bootstrap.php like so:
  ```
  CakePlugin::load( 'Autotask', array(
  'bootstrap' => true
  ,  'routes' => true
  ) );
  ```
* Make sure to check if the /app/Config/core.php file of your CakePHP installation enables the checking of cache by looking at line #130, which reads:
  ```
  Configure::write('Cache.check', true);
  ```
  Uncomment if commented.
* Copy
  ```
  app/Plugin/Autotask/Config/bootstrap.php.default
  ```
  to
  ```
  app/Plugin/Autotask/Config/bootstrap.php
  ```
  and enter your Autotask credentials (and optionally other values to find interesting).
* Run
  ```
  app/Plugin/Autotask/Config/Schema/autotask.sql
  ```
  to setup your database.
* Setup the cronjob that imports your Autotask data:
  ```
  sh </path/to/your/cake/installation/>/lib/CakeConsole/cake -app /path/to/your/application/folder/app Autotask.import_from_autotask
  ```

# Upgrading
* Copy the files in this directory into app/Plugin/Autotask.
* Run the upgrade SQL statement(s) depending on which version you were running.
  For instance, when you have 1.2 installed, use the file /app/Plugin/Autotask/Config/Schema/upgrade-1.2.0-to-1.3.0.sql.
  If you want to get a clean start, use the autotask-1.3.0.sql file to setup the default database.
* Delete the cache created by the existing dashboards. If you open up the console of the VM (or your server) you can remove all cached files with (path may differ):
  ```
  rm -f /var/www/app/tmp/cache/models/* /var/www/app/tmp/cache/persistent/* /var/www/app/tmp/cache/views/*
  ```

# Getting started
After you've installed the plugin and the cronjob has run (at least once) you'll have all data from Autotask present. You know the cronjob has run
when there's a cronjob.log in your app/tmp/logs folder.

The only thing you'll need to do now is:
* Specify the names of the queues (/autotask/queues)
* Specify the names of the Ticket Statuses (/autotask/ticketstatuses)

Now you can go ahead and create your first dashboard at /autotask/dashboards :-)

# Issues / FAQ
Please report any issues you have with the plugin to the issue tracker on our website at http://autotask.campai.nl.

# License
Autotask Dashboards is offered under the MIT License (http://opensource.org/licenses/mit-license.php).