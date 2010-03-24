<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Liste tous les traitements configurables (ayant une description)
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
 * Charger les informations contenues dans le yaml d'un traitement
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

/*
 * Génère le nom du cookie qui sera utilisé par le plugin lors d'une réponse
 * par un visiteur non-identifié.
 *
 * @param int $id_formulaire L'identifiant du formulaire
 * @return string Retourne le nom du cookie
 */
function formidable_generer_nom_cookie($id_formulaire){
	return $GLOBALS['cookie_prefix'].'cookie_formidable_'.$id_formulaire;
}

/*
 * Vérifie si le visiteur a déjà répondu à un formulaire
 *
 * @param int $id_formulaire L'identifiant du formulaire
 * @return unknown_type Retourne un tableau contenant les id des réponses si elles existent, sinon false
 */
function formidable_verifier_reponse_formulaire($id_formulaire){
	global $auteur_session;
	$id_auteur = $auteur_session ? intval($auteur_session['id_auteur']) : 0;
	$cookie = $_COOKIE[formidable_generer_nom_cookie($id_formulaire)];
	
	if ($cookie)
		$where = '(cookie='.sql_quote($cookie).($id_auteur ? ' OR id_auteur='.intval($id_auteur).')' : ')');
	elseif ($id_auteur)
		$where = 'id_auteur='.intval($id_auteur);
	else
		return false;
	
	$reponses = sql_allfetsel(
		'id_formulaires_reponse',
		'spip_formulaires_reponses',
		array(
			array('=', 'id_formulaire', intval($id_formulaire)),
			array('=', 'statut', sql_quote('publie')),
			$where
		),
		'',
		'date'
	);
	
	if (is_array($reponses))
		return array_map('reset', $reponses);
	else
		return false;
}

/*
 * Génère la vue d'analyse de toutes les réponses à une saisie
 *
 * @param array $saisie Un tableau décrivant une saisie
 * @param array $env L'environnement, contenant normalement la réponse à la saisie
 * @return string Retour le HTML des vues
 */
function formidable_analyser_saisie($saisie, $env=array()){
	// Si le paramètre n'est pas bon, on génère du vide
	if (!is_array($saisie))
		return '';
	
	$contexte = array();
	
	// On sélectionne le type de saisie
	$contexte['type_saisie'] = $saisie['saisie'];
	
	// Peut-être des transformations à faire sur les options textuelles
	$options = $saisie['options'];
	foreach ($options as $option => $valeur){
		$options[$option] = _T_ou_typo($valeur, 'multi');
	}
	
	// On ajoute les options propres à la saisie
	$contexte = array_merge($contexte, $options);
	
	// Si env est définie dans les options ou qu'il y a des enfants, on ajoute tout l'environnement
	if(isset($contexte['env']) or is_array($saisie['saisies'])){
		unset($contexte['env']);
		
		// À partir du moment où on passe tout l'environnement, il faut enlever certains éléments qui ne doivent absolument provenir que des options
		$saisies_disponibles = saisies_lister_disponibles();
		if (is_array($saisies_disponibles[$contexte['type_saisie']]['options'])){
			$options_a_supprimer = saisies_lister_champs($saisies_disponibles[$contexte['type_saisie']]['options']);
			foreach ($options_a_supprimer as $option_a_supprimer){
				unset($env[$option_a_supprimer]);
			}
		}
		
		$contexte = array_merge($env, $contexte);
	}
	
	// On récupère de l'environnement la valeur actuelle du champ
	
	// On regarde en priorité s'il y a un tableau listant toutes les valeurs
	if ($env['valeurs'] and is_array($env['valeurs']) and $env['valeurs'][$contexte['nom']]){
		$contexte['liste_valeurs'] = $env['valeurs'][$contexte['nom']];
	}
	
	
	// Si ya des enfants on les remonte dans le contexte
	if (is_array($saisie['saisies']))
		$contexte['saisies'] = $saisie['saisies'];
	
	// On génère la saisie
	return recuperer_fond(
		'saisies-analyses/_base',
		$contexte
	);
}

?>
