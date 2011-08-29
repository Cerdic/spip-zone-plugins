<?php
/**
 * Plugin Zen-Garden pour Spip 2.0
 * Licence GPL (c) 2006-2009 Cedric Morin
 *
 */

if (!defined('_DIR_PLUGIN_THEME')){
	if (!defined('_DIR_THEMES'))
		define('_DIR_THEMES',_DIR_RACINE."themes/");
	
	// si on est en mode apercu, il suffit de repasser dans l'espace prive pour desactiver l'apercu
	if (test_espace_prive()){
		if (isset($_COOKIE['spip_zengarden_theme'])){
			include_spip('inc/cookie');
			spip_setcookie('spip_zengarden_theme',$_COOKIE['spip_zengarden_theme']='',-1);
		}
	}
	elseif(isset($GLOBALS['meta']['zengarden_switcher']) OR defined('_ZEN_VAR_THEME')){
		if (!is_null($arg = _request('var_theme'))){
			include_spip('inc/cookie');
			if ($arg)
				spip_setcookie('spip_zengarden_theme',$_COOKIE['spip_zengarden_theme'] = $arg);
			else
				spip_setcookie('spip_zengarden_theme',$_COOKIE['spip_zengarden_theme']='',-1);
		}
	}
	
	// ajouter le theme au path
	if (
	(
		// on est en mode apercu
		(isset($_COOKIE['spip_zengarden_theme']) AND $t = $_COOKIE['spip_zengarden_theme'])
        // ou avec le cookie du switcher
		OR
		// ou un theme est vraiment selectionne
		(isset($GLOBALS['meta']['zengarden_theme']) AND $t = $GLOBALS['meta']['zengarden_theme'])
	)
	AND is_dir(_DIR_THEMES . $t)){
		_chemin(_DIR_THEMES.$t);
		$GLOBALS['marqueur'] = (isset($GLOBALS['marqueur'])?$GLOBALS['marqueur']:"").":$t";
		// @experimental : sauver le nom du repertoire theme utilise
		// a defaut de connaitre le vrai prefixe
		if (!defined('NOM_THEME')) { define('NOM_THEME', basename($t));}
	}
	
	// @experimental : balise #THEME qui retourne le nom du theme selectionne
	function balise_THEME_dist($p){
		$p->code = "(defined('NOM_THEME') ? NOM_THEME : '')";
		return $p;
	}
}

// Déclaration du pipeline permettant d'ajouter traitements lors de la preview et de l'activiation
$GLOBALS['spip_pipeline']['zengarden_activer_theme'] = '';

function zengarden_affichage_final($texte){
	if ($GLOBALS['html'] and isset($GLOBALS['meta']['zengarden_switcher'])){
		include_spip('prive/zengarden_theme_fonctions');
		// on passe le theme selectionne en js pour ne pas polluer le cache du switcher
		$code = 
			"<script type='text/javascript'>var theme_selected='".$_COOKIE['spip_zengarden_theme']."'</script>"
			. recuperer_fond('inclure/zengarden_switcher');
		// On rajoute le code du selecteur de squelettes avant la balise </body>
		$texte=str_replace("</body>",$code."</body>",$texte);
	}
	return $texte;
}

?>
