<?php

function formulaires_gettext_repertoire_charger_dist() {
	return array(
		'repertoire'=>'',
		'repertoire_propose'=>'',
		'fichiers'=>array(),
		'todo'=>'',
		'exporter_dans_selection'=>'', // 
		'_etapes'=>2,
	);
}

function formulaires_gettext_repertoire_verifier_1_dist() {
	$dir = _request('repertoire');
	$dir2 = _request('repertoire_propose');
	$erreurs = array();
	if ($dir and $dir2) {
		$erreurs['repertoire'] = _T('gettext:erreur_chemin_un_seul');
		$erreurs['repertoire_propose'] = _T('gettext:erreur_chemin_un_seul');
	} elseif(!$dir and !$dir2) {
		$erreurs['repertoire'] = _T('gettext:erreur_chemin_a_definir');
		$erreurs['repertoire_propose'] = _T('gettext:erreur_chemin_a_definir');
	} else {
		$dir = _DIR_RACINE . ($dir ? $dir : $dir2);
		
		if (!is_dir($dir)) {
			$erreurs['repertoire'] = _T('gettext:erreur_chemin_pas_repertoire');
		}
		if (!is_readable($dir)) {
			$erreurs['repertoire'] = _T('gettext:erreur_chemin_pas_accessible_lecture');
		}
		if (_request('exporter_dans_selection') and !is_writable($dir)) {
			$erreurs['exporter_dans_selection'] = _T('gettext:erreur_reperoire_pas_accessible_ecriture', array('dir'=>joli_repertoire($dir)));
		}
	}
	
	if (!_request('exporter_dans_selection')) {
		if (!is_dir(_DIR_RACINE . 'locale')) {
			$erreurs['exporter_dans_selection'] = _T('gettext:erreur_reperoire_pas_accessible_ecriture', array('dir'=>'locale'));
		}
	}
	
	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('gettext:erreur_presente');
	}
	
	return $erreurs;
	
}


function formulaires_gettext_repertoire_verifier_2_dist() {
	$erreurs = array();
	$dir = _DIR_RACINE . sinon(_request('repertoire_propose'), _request('repertoire'));
	
	return $erreurs;
}


function formulaires_gettext_repertoire_traiter_dist() {
	$dir = sinon(_request('repertoire_propose'), _request('repertoire'));
	$message_erreur = '';
	
	include_spip('inc/gettext');
	$todo = _request('todo');
	$files = _request('fichiers');
	foreach ($files as $n=>$nom) {
		$files[$n] = _DIR_RACINE . $dir . '/' . $nom;
	}

	$dir_dest = _DIR_RACINE . (_request('exporter_dans_selection') ? $dir : 'locale');
	if ($todo == 'to_po') {
		# $files = trouver_langues_spip_chemin($chemin); // tout le dossier
		list($ok, $message_erreur) = creer_fichiers_langues_po_depuis_spip($files, $dir_dest);
	}
	elseif ($todo == 'to_spip') {
		# $files = trouver_langues_po_chemin($chemin); // tout le dossier
		list($ok, $message_erreur) = creer_fichiers_langues_spip_depuis_po($files, $dir_dest);
	}

	$retour = array(
		'editable' => true,
	);
	
	if ($message_erreur) {
		$retour['message_erreur'] = $message_erreur;
	} else {
		$retour['message_ok'] = _T('gettext:operation_reussie');
	}
	
	return $retour;
}


function liste_repertoire_lang_spip() {
	$lieux = array();
	$langs = find_all_in_path("lang/", ".*[.]php");
	foreach ($langs as $file=>$lieu) {
		$dir = joli_repertoire(dirname($lieu));
		$lieux[ $dir ] = $dir;
	}
	ksort($lieux);
	
	// ajouter un éventuel repertoire locale/ à la racine de SPIP
	if (is_dir(_DIR_RACINE . 'locale')) {
		$lieux = array('locale'=>'locale') + $lieux;
	}
	
	return $lieux;
}


function liste_fichiers_lang_spip($dir) {
	include_spip('inc/gettext');
	$fichiers = array();
	$files = trouver_langues_spip_chemin(_DIR_RACINE . $dir);
	foreach($files as $f) {
		$file = basename($f);
		$fichiers[$file] = $file;
	}
	ksort($fichiers);
	return $fichiers;
}


function liste_fichiers_lang_po($dir) {
	include_spip('inc/gettext');
	$fichiers = array();
	$files = trouver_langues_po_chemin(_DIR_RACINE . $dir);
	foreach($files as $f) {
		$file = basename($f);
		$dir = basename(dirname(dirname($f))) . '/' . basename(dirname($f)) . '/';
		$fichiers[$dir.$file] = $dir.$file;
	}
	ksort($fichiers);
	return $fichiers;
}


?>
