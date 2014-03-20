<?php
define('MODE', 'LOCAL');

define('_DIR_RACINE_OUTILS',realpath('.'));

//Paramètres de connexion à la base
define('MYSQL_HOST'	, '127.0.0.1');
define('MYSQL_PORT'	, '3306');
define('MYSQL_DATABASE', 'robindesbio');
define('MYSQL_USER'	, 'guillaume');
define('MYSQL_PWD'		, 'glut');


error_reporting(E_ALL & ~E_NOTICE);
#
@ini_set("display_errors", "On");
define('_DIR_RACINE', '../../');
set_include_path(_DIR_RACINE.'/'. PATH_SEPARATOR . get_include_path());

if (!defined('_DIR_RESTREINT_ABS')) define('_DIR_RESTREINT_ABS', '../../../ecrire/');
include_once '../../../ecrire/inc_version.php';

include_spip('inc/cookie');
//include_once _DIR_RESTREINT_ABS.'base/abstract_sql.php';
//$session = charger_fonction('session', 'inc');
//$auteur=sql_fetsel('*','spip_auteurs','id_auteur=4');
//$id_auteur=$session($auteur);

//var_dump($id_auteur);
//var_dump(session_get('id_auteur'));
//var_dump($GLOBALS['visiteur_session']);
//exit();

include_spip('lib/elfinder/php/elFinderConnector.class.php');
include_spip('lib/elfinder/php/elFinder.class.php');
include_spip('lib/elfinder/php/elFinderVolumeDriver.class.php');
include_spip('lib/elfinder/php/elFinderVolumeLocalFileSystem.class.php');
// Required for MySQL storage connector
//include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeMySQL.class.php';
include_spip('inc/elFinderVolumeSPIP.class.php');
// Required for FTP connector support
// include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'elFinderVolumeFTP.class.php';


/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from  '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}

$opts = array(
	// 'debug' => true,
	'roots' => array(

		array(
			'driver'        => 'SPIP',   // driver for accessing file system (REQUIRED)
			'path'			=> 8,
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



