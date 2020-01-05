# ft4a
#### Xbtt tracker front-end in PHP + MySQL + PDO

### Prerequisites
- Xbt Tracker installed and running (https://github.com/OlafvdSpek/xbt)

### Site settings
- General site settings in /web/includes/config.php
- MySQL settings are in /web/includes/sql.php
- /private/crontab.php delete all info older that 5 minutes. You have to set up a crontab like this :

``*/5 * * * * php /var/www/example.com/private/crontab.php``
