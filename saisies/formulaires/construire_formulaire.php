<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_construire_formulaire_charger($identifiant, $formulaire_initial=array()){
	include_spip('inc/saisies');
	$contexte = array();
	
	// On ajoute un préfixe devant l'identifiant, pour être sûr
	$identifiant = 'constructeur_formulaire_'.$identifiant;
	
	// On vérifie ce qui a été passé en paramètre 
	if (!is_array($formulaire_initial)) $formulaire_initial = array();
	
	// On initialise la session si elle est vide
	if (is_null($formulaire_actuel = session_get($identifiant))){
		session_set($identifiant, $formulaire_initial);
		$formulaire_actuel = $formulaire_initial;
	}
	
	// Si le formulaire actuel est différent du formulaire initial on agite un drapeau pour le dire
	if ($formulaire_actuel != $formulaire_initial){
		$contexte['formulaire_modifie'] = true;
		$contexte['_message_attention'] = _T('saisies:construire_attention_modifie');
	}
	
	// On passe ça pour l'affichage
	$contexte['_contenu'] = $formulaire_actuel;
	// On passe ça pour la récup plus facile des champs
	$contexte['_saisies_par_nom'] = saisies_lister_par_nom($formulaire_actuel);
	// Pour déclarer les champs modifiables à CVT
	foreach(array_keys($contexte['_saisies_par_nom']) as $nom){
		$contexte["saisie_modifiee_$nom"] = array();
	}
	
	// La liste des saisies
	$saisies_disponibles = saisies_lister_disponibles();
	$contexte['_saisies_disponibles'] = $saisies_disponibles;
	
	$contexte['fond_generer'] = 'formulaires/inc-generer_saisies_configurables';
	
	return $contexte;
}

function formulaires_construire_formulaire_verifier($identifiant, $formulaire_initial=array()){
	include_spip('inc/saisies');
	$erreurs = array();
	// On ajoute un préfixe devant l'identifiant
	$identifiant = 'constructeur_formulaire_'.$identifiant;
	// On récupère le formulaire à son état actuel
	$formulaire_actuel = session_get($identifiant);
	// On récupère les saisies actuelles
	$saisies_actuelles = saisies_lister_par_nom($formulaire_actuel);
	$noms_autorises = array_keys($saisies_actuelles);
	// La liste des saisies
	$saisies_disponibles = saisies_lister_disponibles();
	
	if (
		($nom = $configurer_saisie =  _request('configurer_saisie') or $nom = $enregistrer_saisie = _request('enregistrer_saisie'))
		and
		in_array($nom, $noms_autorises)
	){
		$saisie = $saisies_actuelles[$nom];
		$formulaire_config = $saisies_disponibles[$saisie['saisie']]['options'];
		array_walk_recursive($formulaire_config, 'formidable_transformer_nom', "saisie_modifiee_${nom}[options][@valeur@]");
		
		// S'il y a un groupe "validation" alors on va construire le formulaire des vérifications
		if ($chemin_validation = saisies_chercher($formulaire_config, "saisie_modifiee_${nom}[options][validation]", true)){
			include_spip('inc/verifier');
			$liste_verifications = verifier_lister_disponibles();
			$chemin_validation[] = 'saisies';
			$chemin_validation[] = 1000000; // à la fin
			
			// On construit la saisie à insérer et les fieldset des options
			$saisie_liste_verif = array(
				'saisie' => 'selection',
				'options' => array(
					'nom' => "saisie_modifiee_${nom}[verifier][type]",
					'label' => _T('saisies:construire_verifications_label'),
					'option_intro' => _T('saisies:construire_verifications_aucune'),
					'li_class' => 'liste_verifications',
					'datas' => array()
				)
			);
			$verif_options = array();
			foreach ($liste_verifications as $type_verif => $verif){
				$saisie_liste_verif['options']['datas'][$type_verif] = $verif['titre'];
				// Si le type de vérif a des options, on ajoute un fieldset
				if ($verif['options'] and is_array($verif['options'])){
					$groupe = array(
						'saisie' => 'fieldset',
						'options' => array(
							'nom' => 'options',
							'label' => $verif['titre'],
							'li_class' => "$type_verif options_verifier"
						),
						'saisies' => $verif['options']
					);
					array_walk_recursive($groupe, 'formidable_transformer_nom', "saisie_modifiee_${nom}[verifier][$type_verif][@valeur@]");
					$verif_options[$type_verif] = $groupe;
				}
			}
			$verif_options = array_merge(array($saisie_liste_verif), $verif_options);
		}
		
		
		if ($enregistrer_saisie){
			// On cherche les erreurs de la configuration
			$vraies_erreurs = saisies_verifier($formulaire_config);
			// On regarde s'il a été demandé un type de vérif
			$saisie_modifiee = _request("saisie_modifiee_${nom}");
			if (($type_verif = $saisie_modifiee['verifier']['type']) != '' and $verif_options[$type_verif]){
				// On ne vérifie que les options du type demandé
				$vraies_erreurs = array_merge($vraies_erreurs, saisies_verifier($verif_options[$type_verif]['saisies']));
			}
		}
		
		// On insère chaque saisie des options de verification
		if ($verif_options){
			foreach ($verif_options as $saisie_verif){
				$formulaire_config = saisies_inserer($formulaire_config, $saisie_verif, $chemin_validation);
			}
		}
		$erreurs['configurer_'.$nom] = $formulaire_config;
		$erreurs['positionner'] = '#configurer_'.$nom;
		
		if ($enregistrer_saisie)
			if ($vraies_erreurs)
				$erreurs = array_merge($erreurs, $vraies_erreurs);
			else
				$erreurs = array();
	}
	
	return $erreurs;
}

function formulaires_construire_formulaire_traiter($identifiant, $formulaire_initial=array()){
	include_spip('inc/saisies');
	$retours = array();
	$saisies_disponibles = saisies_lister_disponibles();
	
	// On ajoute un préfixe devant l'identifiant
	$identifiant = 'constructeur_formulaire_'.$identifiant;
	// On récupère le formulaire à son état actuel
	$formulaire_actuel = session_get($identifiant);
	
	// Si on demande à ajouter une saisie
	if ($ajouter_saisie = _request('ajouter_saisie')){
		$nom = saisies_generer_nom($formulaire_actuel, $ajouter_saisie);
		$saisie = array(
			'saisie' => $ajouter_saisie,
			'options' => array(
				'nom' => $nom
			)
		);
		// S'il y a des valeurs par défaut pour ce type de saisie, on les ajoute
		if (($defaut = $saisies_disponibles[$ajouter_saisie]['defaut']) and is_array($defaut)){
			$defaut = _T_ou_typo($defaut, 'multi');
			$saisie = array_replace_recursive($saisie, $defaut);
		}
		$formulaire_actuel = saisies_inserer($formulaire_actuel, $saisie);
	}

	// Si on demande à dupliquer une saisie
	if ($dupliquer_saisie = _request('dupliquer_saisie')) {
		$formulaire_actuel = saisies_dupliquer($formulaire_actuel, $dupliquer_saisie);	
	}
	
	// Si on demande à supprimer une saisie
	if ($supprimer_saisie = _request('supprimer_saisie')){
		$formulaire_actuel = saisies_supprimer($formulaire_actuel, $supprimer_saisie);
	}
	
	// Si on enregistre la conf d'une saisie
	if ($nom = _request('enregistrer_saisie')){
		// On récupère ce qui a été modifié
		$saisie_modifiee = _request("saisie_modifiee_$nom");
		
		// On regarde s'il y a une position à modifier
		if (isset($saisie_modifiee['position'])){
			$position = $saisie_modifiee['position'];
			unset($saisie_modifiee['position']);
			// On ne déplace que si ce n'est pas la même chose
			if ($position != $nom)
				$formulaire_actuel = saisies_deplacer($formulaire_actuel, $nom, $position);
		}
		
		// On regarde s'il y a des options de vérification à modifier
		if (($type_verif = $saisie_modifiee['verifier']['type']) != ''){
			$saisie_modifiee['verifier'] = array(
				'type' => $type_verif,
				'options' => $saisie_modifiee['verifier'][$type_verif]
			);
		}
		else
			unset($saisie_modifiee['verifier']);
		
		// On récupère les options postées en enlevant les chaines vides
		$saisie_modifiee['options'] = array_filter($saisie_modifiee['options']);
		if ($saisie_modifiee['verifier']['options']) $saisie_modifiee['verifier']['options'] = array_filter($saisie_modifiee['verifier']['options']);
		
		// On désinfecte à la main
		if (is_array($saisie_modifiee['options']))
			spip_desinfecte($saisie_modifiee['options']);
		
		// On modifie enfin
		$formulaire_actuel = saisies_modifier($formulaire_actuel, $nom, $saisie_modifiee);
	}
	
	// Si on demande à réinitialiser
	if (_request('reinitialiser') == 'oui'){
		$formulaire_actuel = $formulaire_initial;
	}
	
	// On enregistre en session la nouvelle version du formulaire
	session_set($identifiant, $formulaire_actuel);
	
	// Le formulaire reste éditable
	$retours['editable'] = true;
	
	return $retours;
}

// À utiliser avec un array_walk_recursive()
// Applique une transformation à la @valeur@ de tous les champs "nom" d'un formulaire, y compris loin dans l'arbo
function formidable_transformer_nom(&$valeur, $cle, $transformation){
	if ($cle == 'nom' and is_string($valeur)){
		$valeur = str_replace('@valeur@', $valeur, $transformation);
	}
}

// Préparer une saisie pour la transformer en truc configurable
function formidable_generer_saisie_configurable($saisie, $env){
	// On récupère le nom
	$nom = $saisie['options']['nom'];
	// On cherche si ya un formulaire de config
	$formulaire_config = $env['erreurs']['configurer_'.$nom];
	// On ajoute une classe
	$saisie['options']['li_class'] .= ' configurable';
	// On ajoute l'option "tout_afficher"
	$saisie['options']['tout_afficher'] = 'oui';
	
	// On ajoute les boutons d'actions, mais seulement s'il n'y a pas de configuration de lancée
	if (!$env['erreurs'])
		$saisie = saisies_inserer_html(
			$saisie,
			recuperer_fond(
				'formulaires/inc-construire_formulaire-actions',
				array('nom' => $nom, 'formulaire_config' => $formulaire_config)
			),
			'debut'
		);
	
	// On ajoute une ancre pour s'y déplacer
	$saisie = saisies_inserer_html(
		$saisie,
		"\n<a id=\"configurer_$nom\"></a>\n",
		'debut'
	);
	
	// Si ya un form de config on l'ajoute à la fin
	if (is_array($formulaire_config)){
		// On double l'environnement
		$env2 = $env;
		// On ajoute une classe
		$saisie['options']['li_class'] .= ' en_configuration';
		
		// Si possible on met en readonly
		$saisie['options']['readonly'] = 'oui';
		
		// On va ajouter le champ pour la position
		if (!($chemin_description = saisies_chercher($formulaire_config, "saisie_modifiee_${nom}[options][description]", true))){
			$chemin_description = array(0);
			$formulaire_config = saisies_inserer(
				$formulaire_config,
				array(
					'saisie' => 'fieldset',
					'options' => array(
						'nom' => "saisie_modifiee_${nom}[options][description]",
						'label' => _T('saisies:option_groupe_description')
					),
					'saisies' => array()
				),
				0
			);
		}
		$chemin_description[] = 'saisies';
		$chemin_description[] = '0'; // tout au début
		$formulaire_config = saisies_inserer(
			$formulaire_config,
			array(
				'saisie' => 'position_construire_formulaire',
				'options' => array(
					'nom' => "saisie_modifiee_${nom}[position]",
					'label' => _T('saisies:construire_position_label'),
					'explication' => _T('saisies:construire_position_explication'),
					'formulaire' => $env['_contenu'],
					'saisie_a_positionner' => $nom
				)
			),
			$chemin_description
		);
		
		$env2['saisies'] = $formulaire_config;
		
		// Un test pour savoir si on prend le _request ou bien
		$erreurs_test = $env['erreurs'];
		unset($erreurs_test['configurer_'.$nom]);
		unset($erreurs_test['positionner']);
		if ($erreurs_test){
			// Là aussi on désinfecte à la main
			if (is_array($env2["saisie_modifiee_$nom"]['options']))
				spip_desinfecte($env2["saisie_modifiee_$nom"]['options']);
		}
		else{
			$env2["saisie_modifiee_$nom"] = $env2['_saisies_par_nom'][$nom];
			$env2["saisie_modifiee_$nom"]['verifier'][$env2["saisie_modifiee_$nom"]['verifier']['type']] = $env2["saisie_modifiee_$nom"]['verifier']['options'];
		}
		
		$env2['fond_generer'] = 'inclure/generer_saisies';
		$saisie = saisies_inserer_html(
			$saisie,
			'<div class="formulaire_configurer"><ul class="formulaire_configurer-contenus">'
			.recuperer_fond(
				'inclure/generer_saisies',
				$env2
			)
			.'<li class="boutons">
				<input type="hidden" name="enregistrer_saisie" value="'.$nom.'" />
				<button type="submit" class="submit link" name="enregistrer_saisie" value="">'._T('bouton_annuler').'</button>
				<input type="submit" class="submit" name="enregistrer" value="'._T('bouton_valider').'" />
			</li>'
			.'</ul></div>',
			'fin'
		);
	}
	
	// On génère le HTML de la saisie
	$html = saisies_generer_html($saisie, $env);
	
	return $html;
}

?>
