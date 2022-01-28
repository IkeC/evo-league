## Introduction

The code here was used to run the evo-league.com website from 2004 to 2014. It consists of

a) A PHP-based ladder system where you can report wins against another player

b) A Python-based online server for Pro Evolution Soccer 6 ("Sixserver")

The evo-league.com website was hacked in late 2014 and all content was deleted. The guys stealing my code started running a website which is now defunct. It's not fair that thieves can run my code and others can't, so I'm sharing my work with everyone.

Please be aware that the code here may be vulnerable and you're running it **at your own risk**. If you find security issues, please commit your fixes to the project.


## Content

Folders:

**/http** - The actual website (PHP 5.5 required)

**/cron** - scripts that should be run from cron jobs (if Sixserver is used)

**/Sixserver** - PES6 online server based on fiveserver (https://sites.google.com/site/fiveservercom/home)

**/setup** - database setup script


## Requirements

PHP 5.5 and a MySQL database are required. PHP must be configured to allow short tags (short_open_tag = On in php.ini).

You'll need at least a virtual server to run this. I recommend a Linux-based server (eg. Debian).

To run Sixserver properly, I recommend a server with at least 1GB RAM.


## Installation

a) Create an empty database (eg. 'evo') and import the setup script (/setup/dbinit.sql).

b) Copy the contents of /http to your server document root (eg. /var/www/yoursite/http/)

c) Edit http/config.php to configure your domain and database connection information

d) Configure your web server (eg. Apache) to serve pages from this directory

You should be able to login using username: *Admin*, password: *changeme*. Edit your profile (http://yoursite/editprofile.php) immediately to change your password.

You can reach the administration panel at http://yoursite/Admin/


## Sixserver

This PES6 online server was created by juce and reddwarf in Python.

Homepage: https://sites.google.com/site/fiveservercom/home

I changed the code so it interfaces with the website and fixed a couple of bugs. Please refer to the homepage for installation instructions. The required database tables for Sixserver are already included in the database setup script.


## Cron jobs

If you want Sixserver to work with the website, you'll have to install a couple of cron jobs. An example crontab is listed below.
```
#m h dom mon dow user        command
# Site
40  *     * * *   root        nice /usr/bin/php /var/www/yoursite/cron/updateTeamladder.php > /dev/null 2>&1
59 23     * * *   root        /var/www/yoursite/cron/last-day-of-month.sh && /var/www/yoursite/cron/newLadderSeason.php
# Sixserver
*/3 *     * * *   root        nice /usr/bin/php /var/www/yoursite/cron/setDisconnectsInGames.php > /dev/null 2>&1
*/5 *     * * *   root        nice /usr/bin/php /var/www/yoursite/cron/setDisconnects.php > /dev/null 2>&1
*/7 *     * * *   root        nice /usr/bin/php /var/www/yoursite/cron/setWordBans.php > /dev/null 2>&1
*/2 *     * * *   www-data    nice /usr/bin/php /var/www/yoursite/cron/reportSixToLadder.php > /dev/null 2>&1
35 23     * * *   root        /var/www/yoursite/cron/last-day-of-month.sh && /var/www/yoursite/cron/maintenance.sh
58 23     * * *   root        /var/www/yoursite/cron/last-day-of-month.sh && /var/www/yoursite/cron/newSixserverSeason.php
```

## License

All code written by me is covered by the MIT license, which means you can basically use it as you like. In addition, I request that:

- You do not remove the credits page
- You do not remove the 'powered by evo-league' in the bottom right corner
- You commit fixes for security issues back to this project


## Support

You may open an issue at https://github.com/IkeC/evo-league/issues if you have a questions or problems. Please include as much information as you can, eg. server log files, system, component versions and so on. Please note that I probably can't or won't help with issues such as how to run a webserver, install PHP or similar tasks.


## Thanks

Many thanks to:

* Peter Hendrix for the WebLeague php module that I started the site with back in 2004
* juce and reddwarf for their PES6 online server
* Vjacheslav Trushkin (cyberalien) for the phpbb Morpheus skin
* All administrators, moderators and players developing the site over the years

*IkeC &copy; 2015*
