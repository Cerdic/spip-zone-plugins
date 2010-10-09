<?php

/**
 * API de gettext pour SPIP 
 * Licence GPL (c) 2010 Matthieu Marcillaud 
**/


/**
 * Retourne la liste des fichiers de langue spip trouves
 * 
 * @param string $chemin chemin vers un repertoire lang : ../plugins/monplugin/lang/

 * @return array liste des fichiers de langue spip du repertoire
**/
function trouver_langues_spip_chemin($chemin) {
	$chemin = rtrim($chemin, '/') . '/';
	$files = glob($chemin . '*.php');
	return $files;
}

/**
 * Retourne la liste des fichiers de langue po trouves
 * 
 * @param string $chemin
 *  chemin vers un repertoire lang : ../plugins/monplugin/lang/
 * 	Cherchera des fichiers lang/fr/LC_MESSAGES/*.po
 * 
 * @return array liste des fichiers de langue po du repertoire
**/
function trouver_langues_po_chemin($chemin) {
	$chemin = rtrim($chemin, '/') . '/';
	$files = glob($chemin . '*/LC_MESSAGES/*.po');
	return $files;
}


/**
 * Retourne la liste des [module][lg][identifiant] = 'texte',  d'un repertoire de langue spip
 * 
 * @param string $chemin chemin vers un repertoire lang : ../plugins/monplugin/lang/

 * @return array tableau des textes trouves [module][lg][identifiant] = 'texte'
**/
function lire_langues_spip_chemin($files) {
	

	$trads = array();
	$GLOBALS['idx_lang'] = 'gti18n';
	$GLOBALS['idx_lang_meta'] = 'gti18n_meta';
	foreach ($files as $f) {
		$file = basename($f, '.php');
		list($nom, $lang) = explode('_', $file, 2);
		include ($f);
		if (!isset($trads[$nom])) {
			$trads[$nom] = array();
		}
		$trads[$nom][$lang]['meta'] = $GLOBALS['gti18n_meta'];
		$trads[$nom][$lang]['strings'] = $GLOBALS['gti18n'];
		ksort($trads[$nom][$lang]['strings']);
		unset ($GLOBALS['gti18n'], $GLOBALS['gti18n_meta']);
	}

	foreach ($trads as $module=>$null) {
		ksort($trads[$module]);
	}
	ksort($trads);

	return $trads;
}




/**
 * Retourne la liste des [module][lg][strings/meta][identifiant] = 'texte'),
 * d'un repertoire de langue, des fichiers po
 * 
 * @param string $chemin chemin vers un repertoire lang : ../plugins/monplugin/lang/

 * @return array tableau des textes trouves [module][lg][identifiant] = 'texte'
**/
function lire_langues_po_chemin($files) {

	$trads = array();
	foreach($files as $file) {
		$path = pathinfo($file);
		$module = $path['filename']; // php 5.2
		
		if (!isset($trads[$module])) {
			$trads[$module] = array();
		}
		$lang = basename( dirname($path['dirname']) );
		if (!isset($trads[$module][$lang])) {
			$trads[$module][$lang] = array();
		}

		$po = SPIP_File_Gettext::factory('PO', $file);
		$po->load();
		$data = $po->toArray();
		ksort($data['strings']);
		$trads[$module][$lang] = $data;
	}
	foreach ($trads as $module=>$null) {
		ksort($trads[$module]);
	}
	
	ksort($trads);
	return $trads;
}


/**
 * 
 * Créee les fichiers .po a partir d'une liste de fichiers de langue (_DIR_PLUGIN_XX/lang/fichier_lg.php)
 * en lisant son repertoire lang/ et en creant
 * les fichiers lang/[lg]/LC_MESSAGES/[module].po 
 *
 * @param string $files liste de fichiers a transformer.
 * @return array(bool, string)
 * 		bool opération réussie ?
 * 		string message d'erreur éventuel
 * 
**/
function creer_fichiers_langues_po_depuis_spip($files, $dir_dest = '') {
	if (!is_array($files)) $files = array($files);
	$dir = $dir_dest ?
		rtrim($dir_dest, '/') . '/'  :
		dirname($files[0]) . '/';
		
	if (!is_writable($dir)) {
		return array(false, _T("gettext:erreur_reperoire_pas_accessible_ecriture", array('dir'=>joli_repertoire($dir))));
	}
	
	
	$trads = lire_langues_spip_chemin($files);

	foreach ($trads as $domain=>$langs) {
		
		foreach ($langs as $lang=>$infos) {
			if ($infos['strings']) {
				// creer la langue dans lang/
				sous_repertoire($dir, $lang);
				sous_repertoire($dir . $lang . '/', 'LC_MESSAGES');
				$lieu = $dir . $lang . '/LC_MESSAGES/';

				$po = SPIP_File_Gettext::factory('PO', $lieu . $domain . '.po');
				$po->fromArray(array(
					'meta' => array_merge($infos['meta'], array(
						"MIME-Version" => "1.0",
						"Content-Type" => "text/plain; charset=UTF-8",
						"Content-Transfer-Encoding" => "8bit"
					)),
					'strings' => $infos['strings'],
				));
				$po->save();
			}
		} 
	}
	return array(true, '');
}




/**
 * 
 * Créee les fichiers de langue SPIP a partir d'une liste de fichiers PO dans (_DIR_PLUGIN_XX/lang)
 * issus lang/[lg]/LC_MESSAGES/[module].po
 * pour creer les fichiers lang/[module]_[lg].php correspondants
 *
 * @param string $files liste de fichiers a transformer
 * @return array(bool, string)
 * 		bool opération réussie ?
 * 		string message d'erreur éventuel
 * 
**/
function creer_fichiers_langues_spip_depuis_po($files, $dir_dest = '') {
	if (!is_array($files)) $files = array($files);
	// lang/fr/LC_MESSAGES/file.po
	$dir = $dir_dest ?
		rtrim($dir_dest, '/') . '/' :
		dirname(dirname(dirname($files[0]))) . '/';
	
	if (!is_writable($dir)) {
		return array(false, _T("gettext:erreur_reperoire_pas_accessible_ecriture", array('dir'=>joli_repertoire($dir))));
	}

	
	$trads = lire_langues_po_chemin($files);

	foreach ($trads as $domain=>$langs) {
		
		foreach ($langs as $lang=>$infos) {
			if ($infos['strings']) {
				$contenu = creer_contenu_fichier_lang_spip($domain, $lang, $infos);
				$file = $dir . $domain . '_' . $lang . '.php';
				if (is_file($file) and !is_writable($file)) {
					return array(false, _T('gettext:erreur_fichier_non_accessible_ecriture', array('file' => $file)));
				}
				if ($contenu) {
					ecrire_fichier($file, $contenu);
				}
			}
		} 
	}
	return array(true, '');
}


/**
 * Retourne le contenu d'un fichier de lang SPIP. 
 *
 * @param 
 * @return 
**/
function creer_contenu_fichier_lang_spip($domain, $lang, $infos) {
	$texte = '<' . '?php

# This is a SPIP lang file
# autogenerated by "gettext" plugin
# for module "' . $domain . '" and lang "' . $lang . '"

';

	if ($infos['meta']) {
		$texte .= '
$GLOBALS[$GLOBALS[\'idx_lang_meta\']] = array(
';
		foreach ($infos['meta'] as $cle=>$valeur) {
			$texte .= "\t'$cle' => \"$valeur\",\n";
		}
		$texte .= ');
';
	}
	$texte .= '
$GLOBALS[$GLOBALS[\'idx_lang\']] = array(
';

	$initiale_precedente = '';
	foreach ($infos['strings'] as $cle=>$valeur) {
		$initiale = strtoupper($cle[0]);
		if ($initiale != $initiale_precedente) {
			$initiale_precedente = $initiale;
			$texte .= "\n\t// $initiale\n";
		}
		$texte .= "\t'$cle' => \"$valeur\",\n";
	}
	$texte .= ');
?' . '>';

	return $texte;
}





include_spip('lib/File/Gettext');

// ajouter 'lib/' a l'include path de php pour que File_Gettext fonctionne
if (!defined('ADD_INCLUDE_PATH_LIB_GETTEXT')) {
	set_include_path(get_include_path() . PATH_SEPARATOR . (_ROOT_CWD . _DIR_PLUGIN_GETTEXT . 'lib'));
	define('ADD_INCLUDE_PATH_LIB_GETTEXT',true);
}

/**
 * Class pour lire ou ecrire des .po (ou .mo)
 *
 * @param 
 * @return 
**/
class SPIP_File_Gettext extends File_Gettext {

    /**
     * Raise PEAR error
     * (surcharge pour ne pas utiliser PEAR::)
     *
     * @param string $error Error message
     * @param int    $code  Error constant
     *
     * @static
     * @access  protected
     * @return  object
     */
    function raiseError($error = null, $code = null)
    {
		if ($error) spip_log($error . ($code ? ' :: '.$code : ''));
    }
}







/***
 *
 * tentative infructueuse de lecture
 *
 *   __('module:identifiant')
 *
 * ...
 *
 *
 ****/

/*
function set_gettext_source($module, $lang = false) {
	if (!$lang) $lang = $GLOBALS['spip_lang'];
	
	putenv('LANG=' . $lang);
	setlocale(LC_ALL, $lang);

	// Spécifie la localisation des tables de traduction
	// La traduction est cherché dans ./locale/de_DE/LC_MESSAGES/myPHPApp.mo
	$x =  realpath(_ROOT_CWD . constant('_DIR_PLUGIN_' . strtoupper($module)) . "lang");
	bindtextdomain($module, $x);

	// Choisit le domaine
	textdomain($module);
}



/**
 * Raccourcis pour la fonction spip_gettext() 
 * Mêmes paramètres.
 * 
** /
function __($msgid, $args = array(), $lang = false) {
	if (!$lang) $lang = $GLOBALS['spip_lang'];
	return spip_gettext($msgid, $args, $lang);
}



function spip_gettext($msgid, $args = array(), $lang = false) {
	if (!$lang) $lang = $GLOBALS['spip_lang'];

	list($module, $msgid) = explode(':', $msgid);
	
	if (!$msgid) {
		$msgid = $module;
		$module = 'spip';
	}
	
	set_gettext_source($module, $lang);
	$text = gettext($msgid);
	
	if (!$text and $lang != 'fr') {
		set_gettext_source($module, 'fr');
		$text = gettext($texte);
	}
	

	if (!strlen($text))
		// pour les chaines non traduites, assurer un service minimum
		$text = str_replace('_', ' ',
			 (($n = strpos($texte,':')) === false ? $texte :
				substr($texte, $n+1)));
				
	return _L($text, $args, $lang);
}
*/

?>
