<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/menus');
include_spip('inc/editer');
include_spip('inc/actions');

function formulaires_editer_menus_entree_charger($id_menu,$id_menus_entree='new'){
	/**
	 * On vérifie si nous ne sommes pas dans une modification
	 * Sinon c'est une création
	 */
	$id_menus_entree = intval(_request('modifier_entree')) ? _request('modifier_entree') : $id_menus_entree;

	$valeurs = formulaires_editer_objet_charger('menus_entree',$id_menus_entree,0,0,'', '', '', '');

	$valeurs['id_menu'] = $id_menu;

	// Les champs pour les entrées

	$valeurs['parametres'] = array();

	// Des champs pour controler le formulaire
	$valeurs['demander_nouvelle_entree'] = '';
	$valeurs['id_menu_nouvelle_entree'] = '';
	$valeurs['enregistrer'] = '';

	// On a en permanence accès aux infos des types
	$valeurs['types_entrees'] = menus_lister_disponibles();

	// On sait toujours si on est sur un menu déjà créé ou pas
	$valeurs['_hidden'] .= '<input type="hidden" name="id_menu" value="'.$id_menu.'" />';

	// les valeurs retournées par _verifier
	$valeurs['entrees'] = '';
	$valeurs['donnees'] = '';
	$valeurs['id_menus_entree'] = '';
	$valeurs['type'] = '';
	$valeurs['type_entree'] = '';
	if( $type_entree = _request('type_entree') ) {
		$valeurs['infos_' . $type_entree] = '';
	}


	return $valeurs;
}

function formulaires_editer_menus_entree_verifier($id_menu,$id_menus_entree='new'){
	$erreurs = formulaires_editer_objet_verifier('menus_entree',$id_menus_entree,array());

	// Si on demande une nouvelle entree pour un menu --------------------------

	if ($id_menu = intval(_request('demander_nouvelle_entree'))){
		// S'il n'y a pas encore de type d'entree de choisi
		if (!($type_entree = _request('type_entree'))){
			include_spip('inc/config');
			set_request('id_menu_nouvelle_entree', $id_menu);
			// On charge les différents types d'entrées disponibles
			$masque = array_flip(lire_config('menus/entrees_masquees', array()));
			set_request('entrees', array_diff_key(menus_lister_disponibles(), $masque));
			if (_request('suivant'))
				$erreur['type'] = _T('menus:erreur_type_menu');
		}
		// Si on a choisi un type d'entree
		else{
			set_request('id_menu_nouvelle_entree', $id_menu);
			set_request('type_entree', $type_entree);
			// On charge les infos du type choisi
			$entrees = menus_lister_disponibles();
			set_request('infos_'.$type_entree, $entrees[$type_entree]);
		}
	}

	// Si on veut modifier une entrée ------------------------------------------

	if ($id_menus_entree = intval(_request('modifier_entree'))){
		// On va chercher l'existant de cette entrée
		$entree = sql_fetsel(
			'type_entree, parametres',
			'spip_menus_entrees',
			'id_menus_entree = '.$id_menus_entree
		);
		$type_entree = $entree['type_entree'];
		$parametres = unserialize($entree['parametres']);

		set_request('donnees', $parametres);
		set_request('id_menus_entree', $id_menus_entree);
		set_request('type_entree', $type_entree);
		// On charge les infos du type choisi
		$entrees = menus_lister_disponibles();
		set_request('infos_'.$type_entree, $entrees[$type_entree]);
	}

	// Si on valide une entree pour un menu ------------------------------------

	if (($id_menu = intval(_request('id_menu_nouvelle_entree')) or $id_menus_entree = intval(_request('id_menus_entree'))) and _request('enregistrer')){
		$type_entree = _request('type_entree');
		$parametres_envoyes = _request('parametres');
		$entrees = menus_lister_disponibles();
		$infos = $entrees[$type_entree];
		// On teste que chaque paramètre obligatoire est bien renseigné
		foreach ($infos['parametres'] as $nom=>$parametre){
			if ($parametre['obligatoire']){
				if (!$parametres_envoyes[$nom]){
					if ($id_menu)
						set_request('id_menu_nouvelle_entree', $id_menu);
					if ($id_menus_entree)
						set_request('id_menus_entree', $id_menus_entree);
					set_request('type_entree', $type_entree);
					set_request('infos_'.$type_entree, $infos);
					$erreurs['parametres'][$nom] = _T('info_obligatoire');
				}
			}
		}
	}

	return $erreurs;
}

function formulaires_editer_menus_entree_traiter($id_menu,$id_menus_entree='new'){
	$retours = array();

	// Si on valide une entree pour un menu ------------------------------------

	if (($id_menu = intval(_request('id_menu_nouvelle_entree')) or $id_menus_entree = intval(_request('id_menus_entree'))) and _request('enregistrer')){
		$res = formulaires_editer_objet_traiter('menus_entree', $id_menus_entree, 0, 0, '', '', '', '');
		set_request('id_menu_nouvelle_entree', '');
		set_request('id_menus_entree', '');
		if (!$res['id_menus_entree'])
			$retours['message_erreur'] = _T('menus:erreur_mise_a_jour');
	}

	// Si on demande la supression d'une entrée --------------------------------

	if ($id_menus_entree = intval(_request('supprimer_entree'))){
		$ok = menus_supprimer_entree($id_menus_entree);
		if (!$ok)
			$retours['message_erreur'] = _T('menus:erreur_mise_a_jour');
	}

	// Si on demande à déplacer une entrée -------------------------------------

	if ($params = _request('deplacer_entree')){
		preg_match('/^([\d]+)-(bas|haut)$/', $params, $params);
		array_shift($params);
		list($id_menus_entree, $sens) = $params;
		$id_menus_entree = intval($id_menus_entree);

		// On récupère des infos sur le placement actuel
		$entree = sql_fetsel(
			'id_menu, rang',
			'spip_menus_entrees',
			'id_menus_entree = '.$id_menus_entree
		);
		$id_menu = intval($entree['id_menu']);
		$rang_actuel = intval($entree['rang']);

		// On teste si ya une entrée suivante
		$dernier_rang = intval(sql_getfetsel(
			'rang',
			'spip_menus_entrees',
			'id_menu = '.$id_menu,
			'',
			'rang desc',
			'0,1'
		));

		// Tant qu'on ne veut pas faire de tour complet
		if (!($sens == 'bas' and $rang_actuel == $dernier_rang) and !($sens == 'haut' and $rang_actuel == 1)){
			// Alors on ne fait qu'échanger deux entrées
			$rang_echange = ($sens == 'bas') ? ($rang_actuel + 1) : ($rang_actuel - 1);
			$ok = sql_updateq(
				'spip_menus_entrees',
				array(
					'rang' => $rang_actuel
				),
				'id_menu = '.$id_menu.' and rang = '.$rang_echange
			);
			if ($ok)
				$ok = sql_updateq(
					'spip_menus_entrees',
					array(
						'rang' => $rang_echange
					),
					'id_menus_entree = '.$id_menus_entree
				);
		}
		// Sinon on fait un tour complet en déplaçant tout
		else{
			if ($sens == 'bas'){
				// Tout le monde descend d'un rang
				$ok = sql_update(
					'spip_menus_entrees',
					array(
						'rang' => 'rang + 1'
					),
					'id_menu = '.$id_menu
				);
				// L'entrée passe tout en haut
				if ($ok)
					$ok = sql_updateq(
						'spip_menus_entrees',
						array(
							'rang' => 1
						),
						'id_menus_entree = '.$id_menus_entree
					);
			}
			else{
				// Tout le monde monte d'un rang
				$ok = sql_update(
					'spip_menus_entrees',
					array(
						'rang' => 'rang - 1'
					),
					'id_menu = '.$id_menu
				);
				// L'entrée passe tout en bas
				if ($ok)
					$ok = sql_updateq(
						'spip_menus_entrees',
						array(
							'rang' => $dernier_rang
						),
						'id_menus_entree = '.$id_menus_entree
					);
			}
		}
		if (!$ok) $retours['message_erreur'] = _T('menus:erreur_mise_a_jour');
	}

	// Si on veut faire un sous-menu -------------------------------------------

	if ($id_menus_entree = intval(_request('demander_sous_menu'))){
		$id_menu = sql_insertq(
			'spip_menus',
			array(
				'id_menus_entree' => $id_menus_entree
			)
		);
		if (!$id_menu) $retours['message_erreur'] = _T('menus:erreur_mise_a_jour');
	}

	// Si on veut supprimer un menu --------------------------------------------

	if ($id_menu = intval(_request('supprimer_menu'))){
		// Est-ce un menu ou un sous-menu ?
		$sous_menu = intval(sql_getfetsel(
			'id_menus_entree',
			'spip_menus',
			'id_menu = '.$id_menu
		));
		$ok = menus_supprimer_menu($id_menu);
		if (!$ok) $retours['message_erreur'] = _T('menus:erreur_mise_a_jour');
		if ($ok and !$sous_menu) $retours['redirect'] = generer_url_ecrire('menus_tous');
	}

	// Bouton annuler ---------------------------------------------------------
	
	if(_request('annuler')){
		set_request('id_menus_entree', '');
	}
	
	$retours['editable'] = true;

	return $retours;
}

?>
