# yCMS
## My simple CMS for managing multiple websites

Creating new subdomains complete with config and giving users a login for managing their sites. *Dependencies* are stated below which, at this moment, are a fork of the original projects. I'm planning to move them to assets, to make them easier to update through the original repositories. I will emphasize any other things I'm planning to update.


## Package dependencies and server dependencies (currently running on):
  - Apache 2.2.22 (Debian)
  - PostgreSQL 9.1.14
  - PHP 5.4.36
    - GeSHi 1.0.8.9 [GitHub](github.com/GeSHi/)
    - PHPMailer 5.1 [GitHub](https://github.com/PHPMailer/PHPMailer)
    - Parsedown 1.5.1 [GitHub](github.com/erusev/parsedown)
  - jQuery 2.1.1 [GitHub](github.com/jquery/jquery)
    - TinyMCE 4.1.7 [GitHub](github.com/tinymce/tinymce)
    - Highcharts 4.1.4 [GitHub](https://github.com/highslide-software/highcharts.com/)


## Features

- Manage a PostgreSQL database, specify user privileges at database-level, automatically giving this user `INSERT`, `SELECT`, `UPDATE` or `DELETE` functionality in this CMS
- Create a subdomain in a *specified* directory, like /var/www/subdomains/subdomain.example.com and create a config.php and an index.html in this directory
- Append a new virtual host to a *specified* file in the Apache directory, requiring a manual Apache restart, or a cronjob to reload Apache. Use the `reload` directive to perform a syntax check and keep your webserver up and running
- Manage files in a *specified* folder


## Installation instructions: create a config.php

### Main settings
#### Error reporting for debugging purposes
```
define('DEBUG_MODE', 0); //Set to 1 when debugging

setlocale(LC_ALL, 'nl_NL.UTF-8');
setlocale(LC_TIME, 'nl_NL.UTF-8');
```

#### Database settings
```
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_NAME', '');
define('DB_PREFIX', '_');
define('DB_LANG', 'nl');
```

#### Mail settings
```
define('MAIL_HOST', 'localhost');
define('MAIL_USER', 'example@example.com');
define('MAIL_PASS', '');

define('MAIL_TO', '');
define('MAIL_TO_NAME', '');
```

#### Title settings

```
define('SITE_TITLE_SEPARATOR', ' - ');
define('SITE_TITLE', 'CMS v2.0');
define('SITE_PATH',  dirname(__FILE__) . '/');
```

#### Date settings
```
define('DT_FORMAT_DB_DATE', 'HH24:MI');
define('DT_FORMAT_DB_TIME', 'TMday DD TMmonth YYYY');
define('DT_FORMAT_DB_DATETIME', 'TMday DD TMmonth YYYY om HH24:MI');
define('DT_FORMAT_DB_RFC', 'Dy, DD Mon YYYY HH24:MI:SS TZ');
define('DT_FORMAT_DB_RSS', 'YYYY-MM-DD\Thh24:MI:SS\Z');
define('DT_FORMAT_DB_YEAR', 'YYYY');
define('DT_FORMAT_PHP_DATE', 'l d F Y');
define('DT_FORMAT_PHP_TIME', 'H:i');
define('DT_FORMAT_PHP_DATETIME', 'l d F Y \o\m H:i');
define('DT_FORMAT_PHP_RFC', 'r');
```

### Definition of tables
```
$__CFG = array(
	'DISABLED' => array(
		'id', 
		'datum_tijd'
	)
);
$__DB = array(
	'bsr_content' => array(
		'FIELDS' => array(
			'id' => 'primary',
			'datum_tijd' => 'datetime',
			'content' => 'editor',
			'pagina' => 'text',
			'verwijderd' => 'boolean'
		),
		'ADD_FIELDS' => 'disabled'
	)
);
```

## Authors
Yupsie

## Contact info
Visit my website on [Yupsie.eu](www.yupsie.eu); use the contact form to submit bugs or to place feature requests

## Read LICENSE
See [GPL](admin.yupsie.eu/GPL.md)

## Read INSTALL
No documentation available yet. I will supply a codesnippet for `git clone` as soon as I find out how it all works. Perhaps I can use an install directive to automatically create a config.php file with the right settings.


[GitHub Repository](github.com/Yupsie/ycms).