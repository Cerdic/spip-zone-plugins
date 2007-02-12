<?php
#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#

include_spip('inc/texte');
include_spip('inc/layer');
include_spip("inc/presentation");

// compatibilite spip 1.9
if ($GLOBALS['spip_version_code']<1.92) { function fin_gauche(){return false;} }

function exec_tweak_test() {
tweak_log("Début : exec_tweak_test()");
	global $connect_statut, $connect_toutes_rubriques;
	
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	// initialisation generale forcee : recuperation de $tweaks;
	tweak_initialisation(true);

	if ($GLOBALS['spip_version_code']<1.92) 
  		debut_page(_T('tweak:titre_tests'), 'configuration', 'tweak_spip');
  	else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('tweak:titre_tests'), "configuration", "tweak_spip");
	}
	
	echo "<br /><br /><br />";
	gros_titre(_T('tweak:titre_tests'));

	echo '<div style="width:95%; text-align:left">';
	tweak_array($_SERVER, '$_SERVER[]');
	tweak_array($_ENV, '$_ENV[]');
	global $HTTP_ENV_VARS;
	tweak_array($HTTP_ENV_VARS, 'global $HTTP_ENV_VARS');
	$a = array('DOCUMENT_ROOT'=>getenv('DOCUMENT_ROOT'), 
			'REQUEST_URI'=>getenv('REQUEST_URI'), 
			'SCRIPT_NAME'=>getenv('SCRIPT_NAME'),
			'PHP_SELF'=>getenv('PHP_SELF'),
		);
	tweak_array($a, 'getenv()');
	// test de tweak_htmlpath()
	$relative_path = dirname(find_in_path('img/smileys/test'));
	$realpath = str_replace("\\", "/", realpath($relative_path));
	$root = preg_replace(',/$,', '', $_SERVER['DOCUMENT_ROOT']);
	$test_result=substr($realpath, strlen($root));
	$dir = dirname(!empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] :
			(!empty($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : 
			(!empty($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : str_replace('\\','/',__FILE__)
		)));
	$a = array('DOCUMENT_ROOT'=>$_SERVER['DOCUMENT_ROOT'], 
			'REQUEST_URI'=>$_SERVER['REQUEST_URI'], 
			'SCRIPT_NAME'=>$_SERVER['SCRIPT_NAME'],
			'PHP_SELF'=>$_SERVER['PHP_SELF'],
			'__FILE__'=>__FILE__,
			'$root'=>$root,
			"find_in_path('img/smileys/test')"=>find_in_path('img/smileys/test'),
			"dirname(find_in_path('img/smileys/test'))"=>$relative_path,
			"str_replace('\\', '/', realpath('$relative_path'))"=>$realpath,
			"substr('$realpath', strlen('$root'))"=>tweak_red($test_result),
			"return?"=>(strlen($root) && strpos($realpath, $root)===0)?'oui':'non',
			"tweak_htmlpath('$relative_path')"=>tweak_htmlpath($relative_path),
			'$dir'=>$dir,
			"tweak_canonicalize('$dir'.'/'.'$relative_path')"=>tweak_red(tweak_canonicalize($dir.'/'.$relative_path)),
			
		);
	tweak_array($a, 'tweak_htmlpath()');

	// test de tweak_canonicalize()
	$dir = $dir.'/'.$relative_path;
	$address = str_replace("//", "/", $dir);
	$address1 = $address2 = explode('/', $address);
	$keys = array_keys($address2, '..');
	foreach($keys as $keypos => $key) array_splice($address2, $key - ($keypos * 2 + 1), 2);
	$address3 = preg_replace(',([^.])\./,', '\1', implode('/', $address2));
	$a = array('$dir'=>$dir,
			'$address'=>$address,
			"explode('/', '$address')"=>$address1, 
			'array_keys($dessus, "..")'=>$keys,
			'array_spliced()'=>$address2, 
			'$resultat'=>tweak_red($address3), 
			
		);
	tweak_array($a, 'tweak_canonicalize()');

	echo '</div>';

	echo fin_page();
tweak_log("Fin   : exec_tweak_test()");
}

function tweak_array($a, $name) {
	static $i;
	debut_cadre_trait_couleur('administration-24.gif','','',++$i.". $name");
	foreach($a as $s=>$v) if(is_array($v))
			foreach($v as $s2=>$v2) echo "\n<strong>{$s}[$s2]</strong> = ".trim($v2)."<br />";
		else echo "\n<strong>$s</strong> = ".trim($v)."<br />";
	fin_cadre_trait_couleur();
}

function tweak_red($s){ return "<span style='color:red;'>$s</span>"; }
?>