<?php

# source script ou commande
define('_SPIP_LOADER_SOURCE_SCRIPT', 'http://www.spip.net/spip-dev/INSTALL/spip_loader.php.txt');
define('_SPIP_LOADER_LOCAL_SCRIPT', _DIR_RACINE.'spip_loader.php');
define('_SVN_COMMAND', 'svn');

# fichier source
$_SPIP_LOADER_UPDATE_FILE = 
	(@is_readable($f = _DIR_ETC.'spip_loader_update_list.txt') ? $f :
	(@is_readable($f = find_in_path('spip_loader_update_list.txt')) ? $f :
	false))
;
$_SVN_UPDATE_FILE = 
	(@is_readable($f = _DIR_ETC.'svn_update_list.txt') ? $f :
	(@is_readable($f = find_in_path('svn_update_list.txt')) ? $f :
	false))
;
if($_SPIP_LOADER_UPDATE_FILE)
	define('_SPIP_MAJ_FILE', $_SPIP_LOADER_UPDATE_FILE);
elseif($_SVN_UPDATE_FILE)
	define('_SPIP_MAJ_FILE', $_SVN_UPDATE_FILE);
else
	define('_SPIP_MAJ_FILE',
		(@is_readable($f = _DIR_ETC.'spip_maj_liste.txt') ? $f :
		(@is_readable($f = find_in_path('spip_maj_liste.txt')) ? $f :
		false))
	);

function tester_spip_loader() {
	$test = file_exists($f = _SPIP_LOADER_LOCAL_SCRIPT);
	return $test  ?$f : false;
}

function tester_svn() {
	$command = _SVN_COMMAND." help";
	$out = array();
	$return = false;
	$test = exec($command,$out, $return);
	return ($return == 0);		
}

function tester_chargeur() {
	return true;
}

function verifier_spip_loader(){
	global $spip_lang_right;
	global $connect_id_auteur;
	$retour = '<p>'._T('spip_loader_ok').'</p>';

	$spip_loader = unserialize($GLOBALS['meta']['spip_loader']);
	$date_verif = $spip_loader['date_verif'] ? 
		date_relative($spip_loader['date_verif']) :
		_L('jamais');
	$verif = _request('verif');

	switch($verif){
		case 'ok':
			$date_script_distant = $spip_loader['date_script_distant']; 
			$date_script_local = $spip_loader['date_script_local'];
			if($date_script_local<$date_script_distant)
				$retour .= '<p>'._L('script distant modifie depuis derniere verif, mise a jour script local conseill&eacute;e').'</p>';
			else
				$retour .= '<p>'._L('votre script local est &agrave; jour').'</p>';
			break;
		case 'ko':
			$retour .= '<p>'._L('Une erreur est survenue pendant la tentive de verification de script de mise &agrave; jour. Reessayez plus tard.').'</p>';
			break;
		default:
			$redirect = generer_url_ecrire('mise_a_jour');
			$action = generer_action_auteur('verifier_spip_loader', $connect_id_auteur, $redirect);
			$retour .= '<p>derniere verification '.$date_verif;
			$retour .= '<form action="'.$action.'" method="post">'."\n";
			$retour .= form_hidden($action);
			$retour .= "<div style='text-align: $spip_lang_right;' id='verifier_spip_loader'"
			. ">\n\t<input type='submit' class='fondo spip_xx-small' value='"
			. _L('verifier_maintenant')
			. "' /></div>\n";
			$retour .= '</form>'."\n";
			break;
	}

	return $retour;
}

function tester_fichier() {
	return _SPIP_MAJ_FILE;
}

function mkdir_r($dest) {
	$dirs = explode('/', $dest);
	$dir='';
	foreach ($dirs as $part) {
		$dir .= $part.'/';
		if (!is_dir($dir) && strlen($dir)>0) {
			mkdir($dir, _SPIP_CHMOD);
			chmod($dir, _SPIP_CHMOD);
		}
	}
}

function spip_loader_liste($fichier, $test_local_script = true, $test_svn = false) {
	$spip_loader_liste = array();
	$config = file($fichier);
	foreach ($config as $l) {
		$l = trim($l);
		if ($l AND substr($l,0,1) != "#") {
			$arguments = explode(' ', $l);
			if(preg_match(',^(https?|svn)://,', $arguments[0], $regs)) {
				$paquet = basename($arguments[0], '.zip');
				$arguments = array_merge(array($paquet), $arguments);
			}
			list($paquet,$url, $dest, $revision, $user) = $arguments;
			$action = ($regs[1] == 'svn' ? 
				($test_svn ? 'svn' : '') :
				($test_local_script ? 'spip_loader' : ''));
			if($action == 'svn' AND $dest == NULL) $dest = '.';
			$spip_loader_liste[$paquet] = array($url, $dest, $revision, $user, $action);
		}
	}
	return $spip_loader_liste;
}

function mettre_en_page($spip_loader_liste, $retour = '') {
	if(!$retour) $retour = generer_url_ecrire('mise_a_jour');
	$spip_maj = unserialize($GLOBALS['meta']['spip_maj']);
	$menu_maj_liste = array();
	foreach($spip_loader_liste as $paquet => $arguments) {
		list($date,$id_auteur) = unserialize($spip_maj['paquet_'.$paquet]);
		if($date AND $r=spip_fetch_array(spip_query("SELECT nom FROM spip_auteurs WHERE id_auteur=$id_auteur")))
			$nom = $r['nom'];
		else
			$nom = _L('Inconnu');
		$info = $date ? ' '._L('mis &agrave jour').' '.date_relative($date).' '._T('public:par_auteur').$nom:'';
		list($url, $dest, $revision, $user, $action) = $arguments;
		if($action != ''){
//			$texte_paquet = '<a href="'.generer_action_auteur($action, $paquet, $retour).'">'.$paquet.'</a>';
			$url_action = '../spip.php?action='.$action.'&amp;paquet='.$paquet;
			$texte_paquet = '<a href="'.$url_action.'">'.$paquet.'</a>';
		}
		else {
			$texte_paquet = $paquet;
		}
		$menu_maj_liste[] = array(
			$texte_paquet,
			_L('depuis').'&nbsp;<tt>'.$url.'</tt>'.(!preg_match(',^\.?$,', $dest)?' '._L('dans').'&nbsp;<tt>'.$dest.'</tt>':' '._L('&nbsp;<tt>&agrave; la racine</tt>')).$info
		);
	}
	return $menu_maj_liste;	
}

?>