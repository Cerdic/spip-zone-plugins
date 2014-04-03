<?php
include_spip('lib/elfinder/php/elFinderConnector.class.php');
include_spip('lib/elfinder/php/elFinder.class.php');
include_spip('lib/elfinder/php/elFinderVolumeDriver.class.php');
include_spip('lib/elfinder/php/elFinderVolumeLocalFileSystem.class.php');
include_spip('inc/elFinderVolumeSPIP.class.php');

$opts = array(
	'roots' => array(

		array(
			'driver'        => 'SPIP',   // driver for accessing file system (REQUIRED)
			'path'			=> 0,
			'host'          => MYSQL_HOST,
			'user'          => MYSQL_USER,
			'pass'          => MYSQL_PWD,
			'db'            => MYSQL_DATABASE,
            'tmbPath' => _DIR_RACINE . _NOM_TEMPORAIRES_INACCESSIBLES,
            'tmbURL' => dirname($_SERVER['PHP_SELF']) . '/../files/.dbtmb/',
			)
		
	),

);
// run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();



