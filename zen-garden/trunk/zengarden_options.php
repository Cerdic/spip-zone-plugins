<?php
/**
 * Plugin Zen-Garden pour Spip 3.0
 * Licence GPL (c) 2006-2013 Cedric Morin
 * 
 * Fichier des options du plugins
 * 
 * @package SPIP\Zen-Garden\Options
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_DIR_PLUGIN_THEME')){
	// si on est en mode apercu, il suffit de repasser dans l'espace prive pour desactiver l'apercu
	if (test_espace_prive()){
		if (isset($_COOKIE['spip_zengarden_theme'])){
			include_spip('inc/cookie');
			spip_setcookie('spip_zengarden_theme',$_COOKIE['spip_zengarden_theme']='',-1);
		}
	}
	// si le switcher est actif ou la globale var_theme
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
	AND is_dir(_DIR_RACINE . $t)){
		_chemin(_DIR_RACINE . $t);
		$GLOBALS['marqueur'] = (isset($GLOBALS['marqueur'])?$GLOBALS['marqueur']:"").":theme-$t";
		// @experimental : sauver le nom du repertoire theme utilise
		// a defaut de connaitre le vrai prefixe
		if (!defined('NOM_THEME')) { define('NOM_THEME', basename($t));}
	}
	
	// @experimental : balise #THEME qui retourne le nom du theme selectionne
	function balise_THEME_dist($p){
		$p->code = champ_sql('theme', $p,"(defined('NOM_THEME') ? NOM_THEME : '')");
		return $p;
	}
}

/**
 * Insertion dans le pipeline affichage_final (SPIP)
 * 
 * Ajoute le switcher de thème dans l'espace public
 * 
 * @param string $texte
 * 		Le contenu html de la page avant affichage au client
 * @return string $texte
 * 		Le contenu html de la page modifié
 */
function zengarden_affichage_final($texte){
	if ($GLOBALS['html'] and isset($GLOBALS['meta']['zengarden_switcher'])){
		include_spip('prive/zengarden_theme_fonctions');
		// on passe le theme selectionne en js pour ne pas polluer le cache du switcher
		$theme = isset($_COOKIE['spip_zengarden_theme']) ? $_COOKIE['spip_zengarden_theme'] : '';
		$code = 
			"<script type='text/javascript'>var theme_selected='$theme'</script>"
			. recuperer_fond('inclure/zengarden_switcher');
		// On rajoute le code du selecteur de squelettes avant la balise </body>
		$texte=str_replace("</body>",$code."</body>",$texte);
	}
	return $texte;
}

?>
