<?php //	namespace Controllers;

/**
 *	class C_Sites
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.0.0
 */
class C_Sites extends C_Main {

	/**
	 *	function run
	 *
	 *	@access public
	 *	@return string
	 */
	public function run() {
		chdir('..');

		if ($this->oClean->getParts(2) == 'del') {
			if (unlink(SITES_PATH . '/' . $this->oClean->getParts(1))) {
				echo 'File deleted';
			}
			else {
				echo 'Delete failed';
			}
		}

		//	@todo: Add & edit configs
		//	@todo: Add subdomain & vhost, Apache restart
		//	@todo: Add database, create tables and users with appropriate privileges
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			mkdir('/var/sub/' . $_POST['SITE_PATH'] . '/', 0777);
			mkdir('/var/sub/' . $_POST['SITE_PATH'] . '/css/', 0777);

			file_put_contents('/var/sub/' . $_POST['SITE_PATH'] . '/index.php', '<?php include(\'config.php\');?><!doctype html>
<html>
	<head>
		<title><?php echo SITE_TITLE;?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="css/style.css" type="text/css">
		<link rel="icon" type="image/png" href="http://admin.bsr-venlo.nl/views/images/favicon.png">
		<script type="text/javascript" src="http://cdn.yupsie.eu/jquery/jquery.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
			//	Anything
			});
		</script>
	</head>
	<body>
		<header><h1><?php echo SITE_TITLE;?></h1></header>
		<section>
			<h2>Hello world!</h2>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras rhoncus scelerisque aliquam. Donec vestibulum nibh nisl, vel commodo magna placerat fermentum. Phasellus eget tincidunt dui, fringilla mattis metus. Suspendisse potenti. Phasellus in vulputate orci, eu elementum lacus. Vivamus quis lacus pretium, ultricies massa nec, commodo lorem. Nunc feugiat augue et nisl auctor, ullamcorper accumsan arcu condimentum. Nunc vehicula, sem at pellentesque molestie, elit lectus fermentum leo, vitae dapibus erat nulla eget massa. Proin sit amet urna in massa tempus convallis vitae porta leo. Mauris ut mauris et elit porta tincidunt. Vivamus commodo purus lacus, eu bibendum odio dapibus ac.</p>
			<p>Quisque turpis tortor, mollis bibendum consequat non, fermentum eu metus. Pellentesque malesuada ut velit nec vestibulum. Suspendisse volutpat euismod volutpat. Praesent convallis sed purus et viverra. Donec faucibus erat enim, sit amet adipiscing massa auctor a. Etiam in eros bibendum, egestas orci eu, consectetur urna. Pellentesque viverra hendrerit erat vel placerat. Phasellus accumsan at nisl placerat fermentum. Suspendisse vitae urna varius, congue risus quis, venenatis nibh.</p>
		</section>
	</body>
</html>');
			file_put_contents('/var/sub/' . $_POST['SITE_PATH'] . '/css/style.css', 'html, h1, h2, input, select {
	font-family:"Helvetica","Trebuchet MS",sans-serif;
	font-size:15px;
}
html {
	background-color:#ddddff;
	font-size:15px;
	letter-spacing:1px;
	margin:0px;
	padding:0px;
	height:100%;
}
body {
	height:100%;
	margin:0px;
	padding:0px;
	color:#aaaaff;
}
header {
	background-color:#aaaaff;
	background-color:rgba(85, 85, 255, 0.5);
	height:100px;
	width:100%;
	margin:0 0 30px 0;
	position:fixed;
	top:0;
	background-image:linear-gradient(135deg, rgba(85, 85, 255, 0.1), rgba(85, 85, 255, 0.5));
}
section {
	margin:120px 2% 0 2%;
	padding:2%;
	background-color:#eeeeff;
	width:92%;
}');
			file_put_contents('/var/sub/' . $_POST['SITE_PATH'] . '/config.php', '<?php

/**
 *	Error reporting for debugging purposes
 */
define(\'DEBUG_MODE\', ' . $_POST['DEBUG_MODE'] . '); //Set to 1 when debugging

setlocale(LC_ALL, \'nl_NL.UTF-8\');
setlocale(LC_TIME, \'nl_NL.UTF-8\');
/**
 *	Database settings
 */
define(\'DB_HOST\', \'' . $_POST['DB_HOST'] . '\');
define(\'DB_PORT\', \'' . $_POST['DB_PORT'] . '\');
define(\'DB_USER\', \'' . $_POST['DB_USER'] . '\');
define(\'DB_PASS\', \'' . $_POST['DB_PASS'] . '\');
define(\'DB_NAME\', \'' . $_POST['DB_NAME'] . '\');
define(\'DB_PREFIX\', \'' . $_POST['DB_PREFIX'] . '\');
define(\'DB_LANG\', \'' . $_POST['DB_LANG'] . '\');

/**
 *	Mail settings
 */
define(\'MAIL_HOST\', \'' . $_POST['MAIL_HOST'] . '\');
define(\'MAIL_USER\', \'' . $_POST['MAIL_USER'] . '\');
define(\'MAIL_PASS\', \'' . $_POST['MAIL_PASS'] . '\');

define(\'MAIL_TO\', \'' . $_POST['MAIL_TO'] . '\');
define(\'MAIL_TO_NAME\', \'' . $_POST['MAIL_TO_NAME'] . '\');

/**
 *	Main settings
 */
define(\'SITE_TITLE_SEPARATOR\', \'' . $_POST['SITE_TITLE_SEPARATOR'] . '\');
define(\'SITE_TITLE\', \'' . $_POST['SITE_TITLE'] . '\');
define(\'SITE_PATH\',  \'/var/sub/' . $_POST['SITE_PATH'] . '/\');

/**
 *	Date settings
 */
define(\'DT_FORMAT_DB_DATE\', \'' . $_POST['DT_FORMAT_DB_DATE'] . '\');
define(\'DT_FORMAT_DB_TIME\', \'' . $_POST['DT_FORMAT_DB_TIME'] . '\');
define(\'DT_FORMAT_DB_DATETIME\', \'' . $_POST['DT_FORMAT_DB_DATETIME'] . '\');
define(\'DT_FORMAT_DB_RFC\', \'' . $_POST['DT_FORMAT_DB_RFC'] . '\');
define(\'DT_FORMAT_DB_RSS\', \'' . $_POST['DT_FORMAT_DB_RSS'] . '\');
define(\'DT_FORMAT_DB_YEAR\', \'' . $_POST['DT_FORMAT_DB_YEAR'] . '\');
define(\'DT_FORMAT_PHP_DATE\', \'' . $_POST['DT_FORMAT_PHP_DATE'] . '\');
define(\'DT_FORMAT_PHP_TIME\', \'' . $_POST['DT_FORMAT_PHP_TIME'] . '\');
define(\'DT_FORMAT_PHP_DATETIME\', \'' . $_POST['DT_FORMAT_PHP_DATETIME'] . '\');
define(\'DT_FORMAT_PHP_RFC\', \'' . $_POST['DT_FORMAT_PHP_RFC'] . '\');');

			file_put_contents('/etc/apache2/sites-enabled/sub', '<VirtualHost *:80>
	ServerName ' . str_replace(array('.nl', '.com', '.eu'), '.eu', $_POST['SITE_PATH']) . '
	DocumentRoot /var/sub/' . str_replace(array('.nl', '.com', '.eu'), '.eu', $_POST['SITE_PATH']) . '
</VirtualHost>
<VirtualHost *:80>
	ServerName ' . str_replace(array('.nl', '.com', '.eu'), '.nl', $_POST['SITE_PATH']) . '
	DocumentRoot /var/sub/' . str_replace(array('.nl', '.com', '.nl'), '.eu', $_POST['SITE_PATH']) . '
</VirtualHost>
<VirtualHost *:80>
	ServerName ' . str_replace(array('.nl', '.com', '.eu'), '.com', $_POST['SITE_PATH']) . '
	DocumentRoot /var/sub/' . str_replace(array('.nl', '.com', '.eu'), '.eu', $_POST['SITE_PATH']) . '
</VirtualHost>', FILE_APPEND);
		}

		$aData = array();
		if ($dirHandle = opendir(SITES_PATH)) {
		    while (false !== ($sFileName = readdir($dirHandle))) {
		        if (($sFileName != ".") && ($sFileName != "..")) {
					$aData[] = array(
						'name'	 		=> $sFileName,
						'path'			=> 'http://' . str_replace('admin', 'www', $_SERVER['HTTP_HOST']) . '/uploads',
						'mimetype'	 	=> mime_content_type(SITES_PATH . '/' . $sFileName),
						'permissions' 	=> C_Assets::file_perms(SITES_PATH . '/' . $sFileName),
						'image' 		=> (
							(mime_content_type(SITES_PATH . '/' . $sFileName) == 'image/png') || 
							(mime_content_type(SITES_PATH . '/' . $sFileName) == 'image/gif') || 
							(mime_content_type(SITES_PATH . '/' . $sFileName) == 'image/jpeg') || 
							(mime_content_type(SITES_PATH . '/' . $sFileName) == 'image/jpg')
						)
					);
		        }
		    }
		    closedir($dirHandle);
		}
		if ($this->oClean->getParts(1)) {
			$aData = file_get_contents('/var/sub/' . strtolower($this->oClean->getParts(1)) . '/config.php');
		}
		$this->oView->setContent($aData);
		
		return $this->oView->getHtml();
	}
}