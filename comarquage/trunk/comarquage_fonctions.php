<?php

/* Plugin Comarquage -flux V2-
 * Copyright (C) 2006 Cedric Morin
 * Copyright (C) 2010 Vernalis Interactive
 *
 * Licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

include_spip('inc/comarquage');

function comarquage_run($parametres_defaut) {
	$url_base = preg_replace(',\?.*$,', '', self());
	$parametres =& comarquage_parametres($parametres_defaut, $url_base);

	$ma_page =& comarquage_compile_page_xml($parametres, $url_base);

	if (!is_string($ma_page)) {
		if ($ma_page == -20) {
			$ma_page = _T('comarquage:avis_serveur_indisponible');
		} else {
			$ma_page = _T('comarquage:avis_erreur');
		}
	}

	return $ma_page;
}

// recuperer les parametres specifiques au comarquage et les autres
function & comarquage_parametres($defaut, &$urlbase) {
	$parametres = array();
	$parametres_attendus = array(
		'xml' => ',^[a-z0-9_\-]*[.]xml$,i',
		'xsl' => ',^[a-z0-9_\-]*[.]xsl$,i',
		'lettre' => ',^[a-z]$,i',
		'motcle' => '',
		'categorie' => '',
	);
	include_spip('inc/config');
	include_spip('inc/flock');
	include_spip('inc/utils');

	foreach ($parametres_attendus as $k => $reg) {
		$p = _request($k);

		if (($p == null) && isset($defaut[$k])) {
			$p = $defaut[$k];
		}

		if (strlen($reg) AND !preg_match($reg, $p)) {
			$p = null;
		}
		if ($p == null) {
			$fichier = lire_config('comarquage/default' . $k . '_file');
			//$p = isset($GLOBALS['meta']['comarquage_default_'.$k.'_file'])?$GLOBALS['meta']['comarquage_default_'.$k.'_file']:NULL;
			$p = isset($fichier) ? $fichier : null;
			//$p = isset(lire_config('comarquage/default'.$k.'_file')) ? lire_config('comarquage/default'.$k.'_file'):NULL;
		}
		if ($p !== null) {
			$parametres[$k] = $p;
			$urlbase = parametre_url($urlbase, $k, '');
		}
	}
	if (strpos($urlbase, '?') === false) {
		$urlbase .= '?';
	} else {
		$urlbase .= '&';
	}

	if (isset($parametres['xml'])) {
		$parametres['xml'] = basename($parametres['xml'], '.xml') . '.xml';

		// Regarde si on un xml local dans le répertoire du plugin (pour utiliser Glossaire, Dossiercat, etc...)
		$xml_local = _DIR_PLUGIN_COMARQUAGE . 'xml/' . $parametres['xml'];

		// Si le xml dans le dossier du plugin existe, on le prend
		// Sinon on regarde dans le répertoire de cache de la categorie
		if (file_exists($xml_local)) {
			$parametres['xml_full_path'] = _DIR_PLUGIN_COMARQUAGE . 'xml/' . $parametres['xml'];
		} else {
			spip_log("XML LOCAL non trouvé : $xml_local", "comarquage");
			$parametres['xml_full_path'] = sous_repertoire(_DIR_CACHE, _DIR_CACHE_COMARQUAGE_XML . '_' . $parametres['categorie']) . $parametres['xml'];
		}

	}
	if (isset($parametres['xsl'])) {
		$parametres['xsl'] = basename($parametres['xsl'], '.xsl') . '.xsl';
	}
	//  $parametres['xsl_full_path'] = _DIR_PLUGIN_COMARQUAGE.'xsl/'.$parametres['categorie'].'/'.$parametres['xsl'];

	$parametres['xsl_full_path'] = _DIR_PLUGIN_COMARQUAGE . 'xsl/' . $parametres['xsl'];

	return $parametres;
}

/* Fonction appelée par le modèle */
function comarquage_init($parametres) {
	if (comarquage_processeur_disponible()) {
		comarquage_run($parametres);
	}
}
