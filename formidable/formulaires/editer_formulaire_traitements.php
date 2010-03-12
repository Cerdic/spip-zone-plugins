<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_editer_formulaire_traitements_charger($id_formulaire){
	$contexte = array();
	$id_formulaire = intval($id_formulaire);
	
	// On teste si le formulaire existe
	if ($id_formulaire
		and $formulaire = sql_fetsel('*', 'spip_formulaires', 'id_formulaire = '.$id_formulaire)
		and autoriser('editer', 'formulaire', $id_formulaire)
	){
		$traitements = unserialize($formulaire['traitements']);
		$contenu = unserialize($formulaire['contenu']);
		if (!is_array($traitements)) $traitements = array();
		if (!is_array($contenu)) $contenu = array();
		$contexte['traitements'] = $traitements;
		$contexte['formulaire'] = _T_ou_typo($contenu, 'multi');
		$contexte['id'] = $id_formulaire;
		
		$traitements_disponibles = traitements_lister_disponibles();
		$configurer_traitements = array();
		foreach ($traitements_disponibles as $type_traitements => $traitement){
			$configurer_traitements[] = array(
				'saisie' => 'case',
				'options' => array(
					'nom' => 'traitements_choisis[]',
					'label' => $traitement['titre'],
					'label_case' => $traitement['description']
				)
			);
			$configurer_traitements[] = array(
				'saisie' => 'fieldset',
				'options' => array(
					'nom' => 'options',
					'label' => $traitement['titre'],
					'li_class' => "$type_traitement options_traiter"
				),
				'saisies' => $traitement['options']
			);
		}
		$contexte['_configurer_traitements'] = $configurer_traitements;
	}
	
	return $contexte;
}

function formulaires_editer_formulaire_traitements_verifier($id_formulaire){
	$erreurs = array();
	
	return $erreurs;
}

function formulaires_editer_formulaire_traitements_traiter($id_formulaire){
	$retours = array();
	
	return $retours;
}

/*
 * Liste toutes les saisies configurables (ayant une description)
 *
 * @return array Un tableau listant des saisies et leurs options
 */
function traitements_lister_disponibles(){
	static $traitements = null;
	
	if (is_null($traitements)){
		$traitements = array();
		$liste = find_all_in_path('traiter/', '.+[.]yaml$');
		
		if (count($liste)){
			foreach ($liste as $fichier=>$chemin){
				$type_traitement = preg_replace(',[.]yaml$,i', '', $fichier);
				$dossier = str_replace($fichier, '', $chemin);
				// On ne garde que les traitements qui ont bien la fonction
				if (charger_fonction($type_traitement, 'traiter', true)
					and (
						is_array($traitement = traitements_charger_infos($type_traitement))
					)
				){
					$traitements[$type_traitement] = $traitement;
				}
			}
		}
	}
	
	return $traitements;
}

/**
 * Charger les informations contenues dans le yaml d'une saisie
 *
 * @param string $type_saisie Le type de la saisie
 * @return array Un tableau contenant le YAML décodé
 */
function traitements_charger_infos($type_traitement){
	include_spip('inc/yaml');
	$fichier = find_in_path("traiter/$type_traitement.yaml");
	$traitement = yaml_decode_file($fichier);
	if (is_array($traitement)){
		$traitement['titre'] = $traitement['titre'] ? _T_ou_typo($traitement['titre']) : $type_traitement;
		$traitement['description'] = $traitement['description'] ? _T_ou_typo($traitement['description']) : '';
		$traitement['icone'] = $traitement['icone'] ? find_in_path($traitement['icone']) : '';
	}
	return $traitement;
}

?>
