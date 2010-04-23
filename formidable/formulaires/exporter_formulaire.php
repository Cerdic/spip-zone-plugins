<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/formidable');

function formulaires_exporter_formulaire_charger($id_formulaire){
	
	$contexte = array();
	
	// On va chercher toutes les fonctions d'exportation existantes
	$liste = find_all_in_path('echanger_formulaire/', '.+[.]php$');
	$types_echange = array();
	if (count($liste)){
		foreach ($liste as $fichier=>$chemin){
			$type_echange = preg_replace(',[.]php$,i', '', $fichier);
			$dossier = str_replace($fichier, '', $chemin);
			// On ne garde que les traitements qui ont bien la fonction
			if ($f = charger_fonction('exporter', "echanger_formulaire/$type_echange", true)){
				$types_echange[$type_echange] = $type_echange;
			}
		}
	}
	$contexte['id'] = $id_formulaire;
	$contexte['types_echange'] = $types_echange;
	
	return $contexte;
}

function formulaires_exporter_formulaire_verifier($id_formulaire){
	$erreurs = array();
	
	return $erreurs;
}

function formulaires_exporter_formulaire_traiter($id_formulaire){
	$retours = array();
	
	$type_export = _request('type_export');
	$exporter = charger_fonction('exporter', "echanger_formulaire/$type_export", true);
	$exporter($id_formulaire);
	
	return $retours;
}

?>
