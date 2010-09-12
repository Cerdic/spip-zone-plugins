<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/editer');

function formulaires_importer_config_noizetier_charger(){
	include_spip('inc/autoriser');
	$contexte = array();

	// Seulement si on a le droit de configurer le noizetier
	if (autoriser('configurer', 'noizetier'))
		$contexte['editable'] = true;
	else
		$contexte['editable'] = false;

	return $contexte;
}

function formulaires_importer_config_noizetier_verifier(){
	$erreurs = array();
	if (!_request('code_yaml')) {
		// On a rien transmis et pas de fichier local
		if (!_request('import_local') AND $_FILES['import']['name']=='')
			$erreurs['import'] = _T('noizetier:formulaire_fichier_import_manquant');
		// On a transmis un fichier
		elseif ($_FILES['import']['name']!='') {
			$fichier = $_FILES['import']['tmp_name'];
			$yaml = '';
			lire_fichier($fichier, $code_yaml);
			// Si on a bien recupere une chaine on tente de la decoder
			if ($code_yaml){
				include_spip('inc/yaml');
				$yaml = yaml_decode($code_yaml);
				// Si le decodage marche on ajoute le décodage au contexte
				if (is_array($yaml)){
					$erreurs['yaml'] = $yaml;
					$erreurs['code_yaml'] = $code_yaml;
					$erreurs['fichier'] = $_FILES['import']['name'];
				}
				else
					$erreurs['import'] = _T('noizetier:formulaire_fichier_vide');
			}
		}
		// On n'a pas transmis de fichier mais un fichier local est sélectionné
		else {
			lire_fichier(_request('import_local'), $code_yaml);
			// Si on a bien recupere une chaine on tente de la decoder
			if ($code_yaml){
				include_spip('inc/yaml');
				$yaml = yaml_decode($code_yaml);
				// Si le decodage marche on ajoute le décodage au contexte
				if (is_array($yaml)){
					$erreurs['yaml'] = $yaml;
					$erreurs['code_yaml'] = $code_yaml;
					$erreurs['fichier'] = _request('import_local');
				}
				else
					$erreurs['import_local'] = _T('noizetier:formulaire_fichier_vide');
			}
		}
	}
	return $erreurs;
}

function formulaires_importer_config_noizetier_traiter(){
	include_spip('inc/autoriser');
	$retours = array();
	$ok = false;
	if (autoriser('configurer', 'noizetier') AND _request('importer')){
		$type_import = _request('type_import');
		$import_compos = _request('import_compos');
		$code_yaml = _request('code_yaml');
		include_spip('inc/yaml');
		$yaml = yaml_decode($code_yaml);
		
		include_spip('inc/noizetier');
		if (noizetier_importer_configuration($type_import, $import_compos, $yaml))
			$retours['message_ok'] = _T('noizetier:formulaire_config_importee');
	}
	
	$retours['editable'] = true;
	return $retours;
}

?>
