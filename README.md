# ft4a
#### Xbtt tracker front-end in PHP + MySQL + PDO

### Prerequisites
- PHP 7.+
- MySQL server
- Xbt Tracker installed and running (https://github.com/OlafvdSpek/xbt)

### Site settings
- General site settings are in /web/includes/config.php
- First member (with ID #1) is admin and can access to web/admin/ part of the site.
- Admin part : Torrents list (+ edit and delete), Categories list (edit, delete, add), Licenses list (edit, delete, add), Members list (edit, delete, add), Message to all members, logs
- you can add/edit News upper part and footer text in web/includes/config.php

### MySQL settings
MySQL settings are in /web/includes/sql.php

### Crontab
/private/crontab.php : delete all info older that 5 minutes in xbt_announce_log table. Usefull for peers.php stats
You have to set up a crontab like this : ``*/5 * * * * php /var/www/example.com/private/crontab.php``

### Mail settings
You have to change Google Re-Captcha keys in :
- web/contact.php
- web/signup.php
- web/recup_pass.php

