<?php
if (!defined("_ECRIRE_INC_VERSION")) return;




function formulaires_restaurer_plugin_charger_dist() {
	
	// securite a cause du fichier PHP importe
	// il faudrait un export dans un autre format que PHP !!!
	if (!autoriser('webmestre')) { return false; }

	// retrouver les fichiers de sauvegarde des plugins.
	$sauvegardes = array();
	foreach (array(
		_DIR_PLUGINS . FABRIQUE_DESTINATION_PLUGINS,
		_DIR_CACHE . FABRIQUE_DESTINATION_CACHE) as $source)
	{
		$base = rtrim($source, '/');
		if (is_readable($base)) {
			// Trouver toutes les sauvegardes.
			$fichiers =
				new RegexIterator(
				new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator($base)), '/fabrique_[^\/]*\.php$/');
			$fichiers->setMaxDepth(2);

			foreach ($fichiers as $f) {
				$sauvegardes[ (string)$f] = str_replace('../', '', (string)$f);
			}
		}
	}

	$contexte = array(
		'sauvegarde' => '',
		'liste_fichiers' => $sauvegardes,
	);
	return $contexte;
}


function formulaires_restaurer_plugin_verifier_dist(){
	$erreurs = array();

	// sauvegarde envoyee
	if (_request('sauvegarde_locale')) {
		// fichier local defini
	}
	elseif (isset($_FILES['sauvegarde']) and $s = $_FILES['sauvegarde'] and $s['name']) {
		// envoi en erreur
		if ($s['error'] or !$s['size']) {
			$erreurs['sauvegarde'] = _T('fabrique:erreur_envoi_fichier');
		}
	} else {
		// pas de fichier envoye
		$erreurs['sauvegarde'] = _T('fabrique:erreur_envoi_fichier');
		
	}

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T("fabrique:erreurs");
	}

	return $erreurs;
}


function formulaires_restaurer_plugin_traiter_dist(){

	if ($d = _request('sauvegarde_locale')) {
		$dest = $d;
	} else {
		$s = $_FILES['sauvegarde'];
		include_spip('inc/documents');
		sous_repertoire( _DIR_CACHE, rtrim(FABRIQUE_VAR_SOURCE, '/'));
		$dest = _DIR_CACHE . rtrim(FABRIQUE_VAR_SOURCE, '/') . '/donnees_restaurees.php';
		deplacer_fichier_upload($s['tmp_name'], $dest);
	}
	$message = '';
	
	include_once($dest);
	if ($data and count($data)) {
		$data = fabrique_migration($data);
		$images = $data['images'];
		unset($data['images']);
		session_set(FABRIQUE_ID, $data);
		// on stocke les images localement pour les traitements d'image
		sous_repertoire(_DIR_VAR,  rtrim(FABRIQUE_VAR_SOURCE, '/'));
		// logo du plugin
		$prefixe = $data['paquet']['prefixe'];
		$images['paquet'] = fabrique_restaurer_images($prefixe, $images['paquet']);

		// logos des objets
		if (is_array($images['objets'])) {
			foreach ($images['objets'] as $c => $im) {
				$obj = table_objet($data['objets'][$c]['table']);
				$images['objets'][$c] = fabrique_restaurer_images($prefixe . "_" . $obj, $im);
			}
		}
		session_set(FABRIQUE_ID_IMAGES, $images);
		$message = _T('fabrique:chargement_effectue');
	}
	// supprime l'emplacement temporaire du fichier envoye
	if (!_request('sauvegarde_locale')) {
		supprimer_fichier($dest);
	}

	if ($message) {
		$res = array(
			'editable'=>'oui',
			'message_ok' => $message,
		);
	} else {
		$res = array(
			'editable'=>'oui',
			'message_erreur' => _T('fabrique:erreur_chargement_fichier'),
		);
	}
	return $res;
}


// restaurer une description de plusieurs images
function fabrique_restaurer_images($nom_de_base, $images) {
	// $type : 'logo'
	if (is_array($images)) {
		foreach ($images as $type => $tailles) {
			foreach ($tailles as $taille => $l) {
				$images[$type][$taille] = fabrique_restaurer_image($nom_de_base, $l, $taille);
			}
		}
		return $images;
	}
	return array();
}

// enregistrer dans local/ l'image reçue
function fabrique_restaurer_image($nom_de_base, $l, $taille = 0) {
	if ($l['contenu'] and $l['extension']) {
		$im_dest = _DIR_VAR . FABRIQUE_VAR_SOURCE . $nom_de_base . '_' . $taille . '.' . $l['extension'];
		$contenu = base64_decode($l['contenu']);
		ecrire_fichier($im_dest, $contenu);
		$l['fichier'] = $im_dest;
	}
	return $l;
}





/**
 * Outil de migration de données de sauvegardes 
 *
**/

function fabrique_migration($data) {
	$vdata = $data['fabrique']['version'];
	// versions < 4, la version est ailleurs !
	if (!$vdata) {
		$vdata = $data['fabricant']['version'];
	}
	if ($vdata < FABRIQUE_VERSION) {
		for ($i = $vdata; $i < FABRIQUE_VERSION; $i++) {
			$f = 'fabrique_migration_v' . ($i + 1);
			if (function_exists($f)) {
				$data = $f($data);
			}
		}
	}
	$data['fabrique']['version'] = FABRIQUE_VERSION;
	return $data;
}


// une petite migration pour l'exemple.
function fabrique_migration_v2($data) {
	// modification du tableau $data.
	// passage de certains fichiers dans un tableau 'fichiers'
	$data['paquet']['fichiers'] = array();
	foreach (array('autorisations', 'fonctions', 'pipelines', 'options') as $f) {
		if (isset($data['paquet'][$f]) and $data['paquet'][$f]) {
			$data['paquet']['fichiers'][] = $f;
			unset($data['paquet'][$f]);
		}
	}
	return $data;
}


// deplacer les logos dans un dossier
function fabrique_migration_v3($data) {
	$images = $data['images'];
	$paquet = $images['paquet'];

	// cas du paquet
	$new = array('logo' => array());
	if (isset($paquet['logo_extension'])) {
		$new['logo'][0] = array(
			'extension' => $paquet['logo_extension'],
			'contenu' => $paquet['logo_contenu']
		);
	}
	$images['paquet'] = $new;

	// cas des objets
	if (is_array($images['objets'])) {
		foreach ($images['objets'] as $c => $o) {
			$new = array('logo' => array());
			foreach (array('', '_32', '_24', '_16') as $taille) {
				if (isset($o["logo{$taille}_extension"])) {
					$new['logo'][ (integer)str_replace('_', '', $taille) ] = array(
						'extension' => $o["logo{$taille}_extension"],
						'contenu' => $o["logo{$taille}_contenu"]
					);
				}
			}
			$images['objets'][$c] = $new;
		}
	}

	// on remplace par notre nouveau stockage.
	$data['images'] = $images;

	return $data;
}

// renommer le fabricant en fabrique
function fabrique_migration_v4($data) {
	$data['fabrique'] = $data['fabricant'];
	unset($data['fabricant']);
	return $data;
}

// echafaudage n'a qu'un F !
function fabrique_migration_v5($data) {
	if (is_array($data['objets'])) {
		foreach ($data['objets'] as $c => $o) {
			if (isset($o['echaffaudages'])) {
				$data['objets'][$c]['echafaudages'] = $data['objets'][$c]['echaffaudages'];
				unset($data['objets'][$c]['echaffaudages']);
			}
		}
	}
	return $data;
}

?>
