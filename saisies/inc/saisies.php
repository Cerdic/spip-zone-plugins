<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Une librairie pour manipuler ou obtenir des infos sur un tableau de saisies


/*
 * Quelles sont les saisies qui se débrouillent toutes seules, sans le _base commun
 *
 * @return array Retourne un tableau contenant les types de saisies qui ne doivent pas utiliser le _base.html commun
 */
function saisies_autonomes(){
	$saisies_autonomes = pipeline(
		'saisies_autonomes',
		array(
			'fieldset',
			'hidden',
			'destinataires'
		)
	);
	
	return $saisies_autonomes;
}


/*
 * Génère une saisie à partir d'un tableau la décrivant et de l'environnement
 * Le tableau doit être de la forme suivante :
 * array(
 *		'type_saisie' => 'input',
 *		'nom' => 'le_name',
 *		'label' => 'Un titre plus joli',
 *		'obligatoire' => 'oui',
 *		'explication' => 'Remplissez ce champ en utilisant votre clavier.',
 *		0 => array(une saisie enfant),
 *		1 => array(une autre saisie enfant),
 *		2 => array(etc)
 * )
 * Les options de la saisie ont une clé textuelle, tandis que les éventuelles saisies enfants ont des clés numériques indiquant leur rang.
 * 
 * @param array $saisie La description d'une saisie sous la forme d'un tableau
 * @param arary $env L'environnement dans lequel sera construit la saisie (sert notamment pour récupérer la valeur du champ)
 * @return string Retourne le HTML de la saisie
 */
function saisies_generer_html($saisie, $env=array()){
	
	// Si le parametre n'est pas bon, on genere du vide
	if (!is_array($saisie))
		return '';
	
	// On sépare les options et les saisies enfants
	$cles = array_keys($saisie);
	// Les saisies enfants ce sont les clés numériques
	$cles_enfants = array_filter($cles, 'is_int');
	// Les options ce sont les autres
	$cles_options = array_diff($cles, $cles_enfants);
	// On remet les clés en tant que clés
	$cles_enfants = array_flip($cles_enfants);
	$cles_options = array_flip($cles_options);
	// On récupère chaque morceaux
	$options = array_intersect_key($saisie, $cles_options);
	$enfants = array_intersect_key($saisie, $cles_enfants);
	
	// Le contexte c'est d'abord les options de la saisie
	// Peut-être des transformations à faire sur les options textuelles
	$contexte = _T_ou_typo($options, 'multi');
	
	// Si on ne trouve pas de type de saisie on met input par defaut
	if (!$contexte['type_saisie'])
		$contexte['type_saisie'] = 'input';
	
	// Si env est définie dans les options, on ajoute tout l'environnement
	if(isset($contexte['env'])){
		unset($contexte['env']);
		// Les options de la saisie sont prioritaires sur l'environnement
		$contexte = array_merge($env, $contexte);
	}
	// Sinon on ne sélectionne que quelques éléments importants
	else{
		// On récupère la liste des erreurs
		$contexte['erreurs'] = $env['erreurs'];
	}
	
	// Dans tous les cas on récupère de l'environnement la valeur actuelle du champ
	// Si le nom du champ est un tableau indexé, il faut parser !
	if (preg_match('/([\w]+)((\[[\w]+\])+)/', $contexte['nom'], $separe)){
		$contexte['valeur'] = $env[$separe[1]];
		preg_match_all('/\[([\w]+)\]/', $separe[2], $index);
		// On va chercher au fond du tableau
		foreach($index[1] as $cle){
			$contexte['valeur'] = $contexte['valeur'][$cle];
		}
	}
	// Sinon la valeur est juste celle du nom
	else
		$contexte['valeur'] = $env[$contexte['nom']];
	
	// S'il y a des saisies enfants, on les ajoute au contexte
	if ($enfants and is_array($enfants)){
		$contexte['saisies'] = $enfants;
	}
	
	// On génère la saisie
	return recuperer_fond(
		'saisies/_base',
		$contexte
	);
	
}

/*
 * Prend la description complète du contenu d'un formulaire et retourne
 * les saisies "à plat" classées par nom.
 *
 * @param array $contenu Le contenu d'un formulaire
 * @return array Un tableau avec uniquement les saisies
 */
function saisies_recuperer_saisies($contenu){
	$saisies = array();
	
	if (is_array($contenu)){
		foreach ($contenu as $ligne){
			if (is_array($ligne)){
				if (array_key_exists('saisie', $ligne)){
					$saisies[$ligne['options']['nom']] = $ligne;
				}
				if (is_array($ligne['saisies'])){
					$saisies = array_merge($saisies, saisies_recuperer_saisies($ligne['saisies']));
				}
			}
		}
	}
	
	return $saisies;
}

/*
 * Prend la description complète du contenu d'un formulaire et retourne
 * une liste des noms des champs du formulaire.
 *
 * @param array $contenu Le contenu d'un formulaire
 * @return array Un tableau listant les noms des champs
 */
function saisies_recuperer_champs($contenu){
	$saisies = saisies_recuperer_saisies($contenu);
	return array_keys($saisies);
}

/*
 * Vérifier tout un formulaire tel que décrit avec les Saisies
 * @param array $formulaire Le contenu d'un formulaire décrit dans un tableau de Saisies
 * @return array Retourne un tableau d'erreurs
 */
function saisies_verifier($formulaire){
	$erreurs = array();
	
	$saisies = saisies_recuperer_saisies($formulaire);
	foreach ($saisies as $saisie){
		$obligatoire = $saisie['options']['obligatoire'];
		$champ = $saisie['options']['nom'];
		$verifier = $saisie['verifier'];
		
		// Si le nom du champ est un tableau indexé, il faut parser !
		if (preg_match('/([\w]+)((\[[\w]+\])+)/', $champ, $separe)){
			$valeur = _request($separe[1]);
			preg_match_all('/\[([\w]+)\]/', $separe[2], $index);
			// On va chercher au fond du tableau
			foreach($index[1] as $cle){
				$valeur = $valeur[$cle];
			}
		}
		// Sinon la valeur est juste celle du nom
		else
			$valeur = _request($champ);
		
		// On regarde d'abord si le champ est obligatoire
		if ($obligatoire and $obligatoire != 'non' and ($valeur == ''))
			$erreurs[$champ] = _T('info_obligatoire');
		
		// On continue seulement si ya pas d'erreur d'obligation et qu'il y a une demande de verif
		if (!$erreurs[$champ] and is_array($verifier)){
			// Si le champ n'est pas valide par rapport au test demandé, on ajoute l'erreur
			if ($erreur_eventuelle = verifier($valeur, $verifier['type'], $verifier['options']))
				$erreurs[$champ] = $erreur_eventuelle;
		}
	}
	
	return $erreurs;
}

/*
 * Liste toutes les saisies configurables (ayant une description)
 *
 * @return array Un tableau listant des saisies et leurs options
 */
function saisies_lister_disponibles(){
	static $saisies = null;
	
	if (is_null($saisies)){
		$saisies = array();
		$liste = find_all_in_path('saisies/', '.+[.]yaml$');
		
		if (count($liste)){
			foreach ($liste as $fichier=>$chemin){
				$type_saisie = preg_replace(',[.]yaml$,i', '', $fichier);
				$dossier = str_replace($fichier, '', $chemin);
				// On ne garde que les saisies qui ont bien le HTML avec !
				if (file_exists("$dossier$type_saisie.html")
					and (
						is_array($saisie = saisies_charger_infos($type_saisie))
					)
				){
					$saisies[$type_saisie] = $saisie;
				}
			}
		}
	}
	
	return $saisies;
}

/**
 * Charger les informations contenues dans le yaml d'une saisie
 *
 * @param string $type_saisie Le type de la saisie
 * @return array Un tableau contenant le YAML décodé
 */
function saisies_charger_infos($type_saisie){
	include_spip('inc/yaml');
	$fichier = find_in_path("saisies/$type_saisie.yaml");
	$saisie = yaml_decode_file($fichier);
	if (is_array($saisie)){
		$saisie['titre'] = $saisie['titre'] ? _T_ou_typo($saisie['titre']) : $type_saisie;
		$saisie['description'] = $saisie['description'] ? _T_ou_typo($saisie['description']) : '';
		$saisie['icone'] = $saisie['icone'] ? find_in_path($saisie['icone']) : '';
	}
	return $saisie;
}

/**
 * Génère un nom unique pour un champ d'un formulaire donné
 *
 * @param array $formulaire Le formulaire à analyser 
 * @param string $type_saisie Le type de champ dont on veut un identifiant 
 * @return string Un nom unique par rapport aux autres champs du formulaire
 */
function saisies_generer_nom($formulaire, $type_saisie){
	$champs = saisies_recuperer_champs($formulaire);
	
	// Tant que type_numero existe, on incrémente le compteur
	$compteur = 1;
	while (array_search($type_saisie.'_'.$compteur, $champs) !== false)
		$compteur++;
	
	// On a alors un compteur unique pour ce formulaire
	return $type_saisie.'_'.$compteur;
}

/**
 * Insère du HTML au début ou à la fin d'une saisie
 *
 * @param array $saisie La description d'une seule saisie
 * @param string $insertion Du code HTML à insérer dans la saisie 
 * @param string $ou L'endroit où insérer le HTML : "debut" ou "fin"
 * @return array Retourne la description de la saisie modifiée
 */
function saisies_inserer_html($saisie, $insertion, $ou='fin'){
	if (!in_array($ou, array('debut', 'fin')))
		$ou = 'fin';
	
	if ($ou == 'debut')
		$saisie['options']['inserer_debut'] = $insertion.$saisie['options']['inserer_debut'];
	elseif ($ou == 'fin')
		$saisie['options']['inserer_fin'] = $saisie['options']['inserer_fin'].$insertion;
	
	return $saisie;
}

?>
