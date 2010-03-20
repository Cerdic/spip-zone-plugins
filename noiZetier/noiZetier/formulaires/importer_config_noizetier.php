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
		if (_request('import_compos'))
			$import_compos = _request('import_compos');
		else
			$import_compos = 'non';
		$code_yaml = _request('code_yaml');
		include_spip('inc/yaml');
		$yaml = yaml_decode($code_yaml);
		
		// On s'occupe déjà des noisettes
		$noisettes = $yaml['noisettes'];
		include_spip('base/abstract_sql');
		if (is_array($noisettes) AND count($noisettes)>0) {
			$noisettes_insert = array();
			$rang = 1;
			$page = '';
			if ($type_import=='remplacer')
				sql_delete('spip_noisettes','1');
			foreach($noisettes as $noisette) {
				$type = $noisette['type'];
				$composition = $noisette['composition'];
				if ($type.'-'.$composition!=$page) {
					$page = $type.'-'.$composition;
					$rang = 1;
					if ($type_import=='fusion')
						$rang = sql_getfetsel('rang','spip_noisettes','type='.sql_quote($type).' AND composition='.sql_quote($composition),'','rang DESC') + 1;
				}
				else {
					$rang = $rang + 1;
				}
				$noisette['rang']=$rang;
				$noisette['parametres'] = serialize($noisette['parametres']);
				$noisettes_insert[] = $noisette;
			}
			$ok = sql_insertq_multi('spip_noisettes',$noisettes_insert);
		}
		
		// On s'occupe des compositions du noizetier
		if ($import_compos=='oui') {
			include_spip('inc/meta');
			$compos_importees = $yaml['noizetier_compositions'];
			if (is_array($compos_importees) AND count($compos_importees)>0){
				if ($type_import=='remplacer')
					effacer_meta('noizetier_compositions');
				else 
					$noizetier_compositions = unserialize($GLOBALS['meta']['noizetier_compositions']);
				
				if (!is_array($noizetier_compositions))
					$noizetier_compositions = array();
				
				foreach($compos_importees as $type => $compos_type)
					foreach($compos_type as $composition => $info_compo)
						$noizetier_compositions[$type][$composition] = $info_compo;
				
				ecrire_meta('noizetier_compositions',serialize($noizetier_compositions));
			}
		}
		
		if ($ok)
			$retours['message_ok'] = _T('noizetier:formulaire_config_importee');
		// On invalide le cache
		include_spip('inc/invalideur');
		suivre_invalideur('noizetier-import-config');
	}
	
	$retours['editable'] = true;
	return $retours;
}

?>
