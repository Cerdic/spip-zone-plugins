<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_generer_formulaire_charger($contenu, $traitements){
	$contexte = array();
	
	// On cherche les noms des champs dans toutes les saisies et on ajoute au contexte
	$saisies = formidable_chercher_saisies($contenu);
	foreach ($saisies as $saisie){
		$contexte[$saisie['options']['nom']] = '';
	}
	
	// On ajoute aussi le contenu complet
	$contexte['_contenu'] = $contenu;
	
	return $contexte;
}

function formulaires_generer_formulaire_verifier($contenu, $traitements){
	$erreurs = array();
	
	$saisies = formidable_chercher_saisies($contenu);
	foreach ($saisies as $saisie){
		$obligatoire = $saisie['options']['obligatoire'];
		$champ = $saisie['options']['nom'];
		$verifier = $saisie['verifier'];
		
		// On regarde d'abord si le champ est obligatoire
		if ($obligatoire and $obligatoire != 'non' and ($valeur=_request($champ)) == '')
			$erreurs[$champ] = _T('info_obligatoire');
		
		// On continue seulement si ya pas d'erreur d'obligation et qu'il y a une demande de verif
		if (!$erreurs[$champ] and is_array($verifier)){
			include_spip('inc/verifier');
			// Si le champ n'est pas valide par rapport au test demandé, on ajoute l'erreur
			if ($erreur_eventuelle = verifier($valeur, $verifier['type'], $verifier['options']))
				$erreurs[$champ] = $erreur_eventuelle;
		}
	}
	
	return $erreurs;
}

function formulaires_generer_formulaire_traiter($contenu, $traitements){
	$retours = array();
	$saisies = formidable_chercher_saisies($contenu);
	
	if (is_array($traitements) and !empty($traitements))
		foreach($traitements as $traitement=>$options){
			if ($appliquer_traitement = charger_fonction($traitement, 'traitement/'))
				$retours = array_merge($retours, $appliquer_traitement($contenu, $options, $retours));
		}
	else{
		$retours['message_ok'] = _T('formidable:retour_aucun_traitement');
	}
	
	return $retours;
}


// Cherche uniquement les saisies dans un tableau de description de formulaire
function formidable_chercher_saisies($contenu){
	$saisies = array();
	
	if (is_array($contenu)){
		foreach ($contenu as $ligne){
			if (is_array($ligne)){
				if (array_key_exists('saisie', $ligne)){
					$saisies[$ligne['options']['nom']] = $ligne;
				}
				elseif (array_key_exists('groupe', $ligne)){
					$saisies = array_merge($saisies, formidable_chercher_saisies($ligne['contenu']));
				}
			}
		}
	}
	
	return $saisies;	
}

?>
