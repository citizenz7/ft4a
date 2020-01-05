# ft4a
#### Xbtt tracker front-end in PHP + MySQL + PDO

### Prerequisites
- PHP 7.+
- MySQL server
- Xbt Tracker installed and running (https://github.com/OlafvdSpek/xbt)

### Site settings
General site settings in /web/includes/config.php

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

