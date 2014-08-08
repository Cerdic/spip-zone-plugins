<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Générer le traitement des formulaires de recherche
 *
 * @pipeline formulaire_charger
 * @param array $flux Données du pipeline
 * @return array Retourne les données du pipeline modifiées
 */
function cvt_rechercher_formulaire_charger($flux){
	if (
		$form = $flux['args']['form']
		and strncmp($form, 'rechercher_', 11) == 0 // un #FORMULAIRE_RECHERCHER_XXX
	) {
		// On modifie le texte du bouton de validation de Saisies s'il n'est pas déjà défini
		if (!isset($flux['data']['saisies_texte_submit'])) {
			$flux['data']['saisies_texte_submit'] = _T('info_rechercher');
		}
	}
	
	return $flux;
}

/**
 * Générer le traitement des formulaires de recherche
 *
 * @pipeline formulaire_traiter
 * @param array $flux Données du pipeline
 * @return array Retourne les données du pipeline modifiées
 */
function cvt_rechercher_formulaire_traiter($flux){
	if (
		$form = $flux['args']['form']
		and strncmp($form, 'rechercher_', 11) == 0 // un #FORMULAIRE_RECHERCHER_XXX
		and $args = $flux['args']['args']
	) {
		include_spip('inc/filtres');
		
		// On va chercher le contexte de base du formulaire
		$contexte = array();
		if ($fonction_charger = charger_fonction("charger", "formulaires/$form/", true)) {
			$contexte = call_user_func_array($fonction_charger, $args);
		}
		$contexte = pipeline(
			'formulaire_charger',
			array(
				'args' => array('form'=>$form, 'args'=>$args, 'je_suis_poste'=>false),
				'data' => $contexte)
		);
		
		// Il faut une configuration pour les champs de recherche
		if (isset($contexte['_rechercher_champs']) and $rechercher_champs = $contexte['_rechercher_champs']) {
			// Le premier argument doit être un URL de destination
			$redirect = $args[0];
			
			foreach ($rechercher_champs as $champ=>$config) {
				// On netttoie l'ancienne valeur
				if (isset($config['multiple']) and $config['multiple']) {
					$redirect = parametre_url($redirect, $champ.'[]', '');
				}
				else {
					$redirect = parametre_url($redirect, $champ, '');
				}
				
				// Si une nouvelle valeur existe, on l'ajoute
				if ($nouvelle = _request($champ)) {
					$redirect = parametre_url($redirect, $champ, $nouvelle);
				}
			}
		
			// Si on a configuré une ancre
			if (isset($contexte['_rechercher_ancre']) and $ancre = $contexte['_rechercher_ancre']) {
				$redirect = ancre_url($redirect, $ancre);
			}
		
			$flux['data']['redirect'] = $redirect;
		}
	}
	
	return $flux;
}

