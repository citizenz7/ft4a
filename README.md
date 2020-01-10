# ft4a
#### Xbtt tracker front-end in PHP + MySQL + PDO

### Prerequisites
- Nginx
- PHP 7.+
- MySQL server
- Xbt Tracker installed and running (https://github.com/citizenz7/xbt)

### About XBTT tracker
/!\ A major update has just been released a few days ago in official Github XBTT repo. Don't use the files from official repo or it will fail. Please use my repo with "old stable" version: https://github.com/citizenz7/xbt


### Site settings
#### WARNINGs: 
- details and comments are in french in all files
- you MUST adapt some important files described below and put your own settings (site name, site URL = http + https, paths, announce port, mail, etc.)
- You should not change anything from $ANNOUNCEURL part unless Editorial ($EDITO). Default settings should be fine.

#### CONFIG:
- General site settings are in /web/includes/config.php
- First member (with ID #1) is admin and can access web/admin/ part of the site. See right menu for links.
- Admin part : Torrents list (edit, delete), Categories list (edit, delete, add), Licenses list (edit, delete, add), Members list (edit, delete, add), Message to all members, logs
- you can add/edit News upper part and footer text in web/includes/config.php

### Nginx settings
You have to add a "rewrite" part to Nginx virtualhost file like this :

``rewrite ^/c-(.*)$ /catpost.php?id=$1 last;``

``rewrite ^/l-(.*)$ /licpost.php?id=$1 last;``

``rewrite ^/a-(.*)-(.*)$ /archives.php?month=$1&year=$2 last;``

``if (!-d $request_filename){``

``   set $rule_2 1$rule_2;``

``}``

``if (!-f $request_filename){``

``   set $rule_2 2$rule_2;``

``}``

``if ($rule_2 = "21"){``

``   rewrite ^/(.*)$ /viewpost.php?id=$1 last;``

``}``

You should configure a HTTPS access. Maybe with Lets'encrypt (french tuto: https://www.citizenz.info/let-s-encrypt-et-nginx-config-rapide-sous-ubuntu)

### MySQL settings
You need a MySQL database. Import ft4a.sql file in phpMyAdmin or in command line.
Please add your settings in xbt_config table :
- redirect_url
- pid_file
- torrent_pass_private_key
- listen_port

MySQL connection settings are in /web/includes/sql.php

You need to change a few info in xbt_config table:
```
redirect_url,	http://www.example.com
pid_file,	/var/run/xbt_tracker_example.pid
torrent_pass_private_key,	MyPrivateKeyWithLettersAndNumbers
listen_port,	xxxxx
```
### Crontab
/private/crontab.php : this will delete all info older that 5 minutes in xbt_announce_log table. Usefull for peers.php stats to show seeders/leechers names for a torrent... (WARNING: it seems a bit buggy though... sometimes)
You have to set up a crontab like this : ``*/5 * * * * php /var/www/example.com/private/crontab.php``

### Mail settings
You have to change Google Re-Captcha keys in :
- web/contact.php
- web/signup.php
- web/recup_pass.php

You have to change mail login, pass and smtp server in web/includes/config.php:
``define('SITEMAIL','contact@example.com');``

``define('SITEMAILPASSWORD','xxxxxxxxxxxxxxxxxxxx');``

``define('SMTPHOST','mail.example.com');``

``define('SMTPPORT','587');``
