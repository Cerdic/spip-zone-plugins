<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/noizetier');

function formulaires_configurer_bloc_charger($bloc,$page){
	$contexte = array();
	
	$contexte['bloc'] = $bloc;
	$type_compo = explode ('-',$page,2);
	$contexte['type'] = $type_compo[0];
	$contexte['composition'] = $type_compo[1];
	$contexte['bloc_page'] = $bloc.'-'.$page;
	
	// Les champs pour les noisettes
	$contexte['id_noisette'] = 0;
	$contexte['rang'] = 0;
	$contexte['parametres'] = array();
	
	// Des champs pour controler le formulaire
	$contexte['demander_nouvelle_entree'] = '';
	$contexte['id_menu_nouvelle_entree'] = '';
	$contexte['enregistrer'] = '';
	
	// On a en permanence accès aux infos des noisettes
	$contexte['liste_noisettes'] = noizetier_lister_noisettes();
	
	// On sait toujours où va la noisette
	$contexte['_hidden'] .= '<input type="hidden" name="type" value="'.$contexte['type'].'" />';
	$contexte['_hidden'] .= '<input type="hidden" name="composition" value="'.$composition.'" />';
	$contexte['_hidden'] .= '<input type="hidden" name="bloc" value="'.$contexte['bloc'].'" />';
	
	return $contexte;
}

function formulaires_configurer_bloc_verifier($bloc,$page){
	$erreurs = array();
	
	// Si on demande une nouvelle noisette pour un bloc --------------------------
	
	if ($bloc_page = _request('demander_nouvelle_noisette')){
		$t_bloc_page = explode ('-',$bloc_page,3);
		$bloc = $t_bloc_page[0];
		$type = $t_bloc_page[1];
		$composition = $t_bloc_page[2];
		$erreurs['bloc'] = $bloc;
		$erreurs['type'] = $type;
		$erreurs['composition'] = $composition;
		// S'il n'y a pas encore de noisette de choisie
		if (!($noisette = _request('noisette'))){
			$erreurs['bloc_page_nouvelle_noisette'] = $bloc_page;
			// On charge les différents noisettes du type et celle pour toutes les pages
			$erreurs['noisettes_type'] = noizetier_lister_noisettes($type);
			$erreurs['noisettes_page'] = noizetier_lister_noisettes('page');
			if (_request('suivant'))
				$erreurs['erreur_noisette'] = _T('noizetier:erreur_doit_choisir_noisette');
		}
		// Si on a choisi une noisette
		else{
			$erreurs['bloc_page_nouvelle_noisette'] = $bloc_page;
			$erreurs['noisette'] = $noisette;
			// On charge les infos de la noisette choisie
			$noisettes = noizetier_lister_noisettes();
			$erreurs['infos_'.$noisette] = $noisettes[$noisette];
			// On charge les valeurs par défaut des paramètres
			$valeurs_defaut = array();
			foreach ($noisettes[$noisette]['parametres'] as $cle => $valeur)
				$valeurs_defaut[$cle] = $valeur['defaut'];
			$erreurs = array_merge($erreurs, $valeurs_defaut);
		}
	}
	
	// Si on veut modifier une noisette ------------------------------------------
	
	if ($id_noisette = intval(_request('modifier_noisette'))){
		// On va chercher l'existant de cette noisette
		$entree = sql_fetsel(
			'noisette, parametres',
			'spip_noisettes',
			'id_noisette = '.$id_noisette
		);
		$noisette = $entree['noisette'];
		$parametres = unserialize($entree['parametres']);
		
		if (is_array($parametres))
			$erreurs = array_merge($erreurs, $parametres);
		$erreurs['id_noisette'] = $id_noisette;
		$erreurs['noisette'] = $noisette;
		// On charge les infos de la noisette choisie
		$noisettes = noizetier_lister_noisettes();
		$erreurs['infos_'.$noisette] = $noisettes[$noisette];
	}
	
	// Si on valide une noisette pour un bloc ------------------------------------
	
	if (($bloc_page = _request('bloc_page_nouvelle_noisette') or $id_noisette = intval(_request('id_noisette'))) and _request('enregistrer')){
		$noisette = _request('noisette');
		$parametres_envoyes = _request('parametres');
		$noisettes = noizetier_lister_noisettes();
		$infos = $noisettes[$noisette];
		// On teste que chaque paramètre obligatoire est bien renseigné
		foreach ($infos['parametres'] as $nom=>$parametre){
			if ($parametre['obligatoire']){
				if (!$parametres_envoyes[$nom]){
					if ($bloc_page)
						$erreurs['bloc_page'] = $bloc_page;
					if ($id_noisette)
						$erreurs['id_noisette'] = $id_noisette;
					$erreurs['noisette'] = $noisette;
					$erreurs['infos_'.$noisette] = $infos;
					$erreurs['parametres'][$nom] = _T('info_obligatoire');
				}
			}
		}
	}
	
	return $erreurs;
}

function formulaires_configurer_bloc_traiter($bloc,$page){
	$retours = array();
	
	// Si on valide une noisette pour un bloc ------------------------------------
	
	if (($bloc_page = _request('bloc_page_nouvelle_noisette') or $id_noisette = intval(_request('id_noisette'))) and _request('enregistrer')){
		$rang = intval(_request('rang'));
		$noisette = _request('noisette');
		if ($parametres_envoyes = _request('parametres'))
			spip_desinfecte($parametres_envoyes);
		$noisettes = noizetier_lister_noisettes();
		$infos = $noisettes[$noisette];
		
		// Enregistrement de la noisette
		if ($bloc_page) {
			$t_bloc_page = explode ('-',$bloc_page,3);
			$bloc = $t_bloc_page[0];
			$type = $t_bloc_page[1];
			$composition = $t_bloc_page[2];
			$id_noisette = sql_insertq(
				'spip_noisettes',
				array(
					'bloc' => $bloc,
					'type' => $type,
					'composition' => $composition,
					'rang' => $rang,
					'noisette' => $noisette,
					'parametres' => serialize($parametres_envoyes)
				)
			);
			// On invalide le cache
			include_spip('inc/invalideur');
			$cle_invalidation = $bloc.'/'.$type;
			if ($composition != '')
				$cle_invalidation .= '-'.composition;
			suivre_invalideur($cle_invalidation);
		}
		// Mise à jour de la noisette
		if ($id_noisette) {
			$id_noisette = sql_updateq(
				'spip_noisettes',
				array(
					'parametres' => serialize($parametres_envoyes)
				),
				'id_noisette='.$id_noisette
			);
			// On invalide le cache
			include_spip('inc/invalideur');
			$cle_invalidation = _request('bloc').'/'._request('type');
			if (_request('composition') != '')
				$cle_invalidation .= '-'._request('composition');
			suivre_invalideur($cle_invalidation);
		}
		
		if (!$id_noisette)
			$retours['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
	}
	
	// Si on demande la supression d'une noisette --------------------------------
	
	if ($id_noisette = intval(_request('supprimer_noisette'))){
		$ok = sql_delete(
			'spip_noisettes',
			'id_noisette = '.$id_noisette
		);
		// On invalide le cache
		include_spip('inc/invalideur');
		$cle_invalidation = _request('bloc').'/'._request('type');
		if (_request('composition') != '')
			$cle_invalidation .= '-'._request('composition');
		suivre_invalideur($cle_invalidation);
		
		if (!$ok) $retours['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
	}
	
	// Si on demande à déplacer une noisette -------------------------------------
	
	if ($params = _request('deplacer_noisette')){
		preg_match('/^([\d]+)-(bas|haut)$/', $params, $params);
		array_shift($params);
		list($id_noisette, $sens) = $params;
		$id_noisette = intval($id_noisette);
		
		// On récupère des infos sur le placement actuel
		$noisette = sql_fetsel(
			'bloc, type, composition, rang',
			'spip_noisettes',
			'id_noisette = '.$id_noisette
		);
		$bloc = $noisette['bloc'];
		$type = $noisette['type'];
		$composition = $noisette['composition'];
		$rang_actuel = intval($noisette['rang']);
		
		// On teste si ya une noisette suivante
		$dernier_rang = intval(sql_getfetsel(
			'rang',
			'spip_noisettes',
			'bloc = '.sql_quote($bloc).' and type='.sql_quote($type).'and composition='.sql_quote($composition),
			'',
			'rang desc',
			'0,1'
		));
		
		// Tant qu'on ne veut pas faire de tour complet
		if (!($sens == 'bas' and $rang_actuel == $dernier_rang) and !($sens == 'haut' and $rang_actuel == 1)){
			// Alors on ne fait qu'échanger deux noisettes
			$rang_echange = ($sens == 'bas') ? ($rang_actuel + 1) : ($rang_actuel - 1);
			$ok = sql_updateq(
				'spip_noisettes',
				array(
					'rang' => $rang_actuel
				),
				'bloc = '.sql_quote($bloc).' and type='.sql_quote($type).'and composition='.sql_quote($composition).' and rang = '.$rang_echange
			);
			if ($ok)
				$ok = sql_updateq(
					'spip_noisettes',
					array(
						'rang' => $rang_echange
					),
					'id_noisette = '.$id_noisette
				);
		}
		// Sinon on fait un tour complet en déplaçant tout
		else{
			if ($sens == 'bas'){
				// Tout le monde descend d'un rang
				$ok = sql_update(
					'spip_noisettes',
					array(
						'rang' => 'rang + 1'
					),
					'bloc = '.sql_quote($bloc).' and type='.sql_quote($type).'and composition='.sql_quote($composition)
				);
				// La noisette passe tout en haut
				if ($ok)
					$ok = sql_updateq(
						'spip_noisettes',
						array(
							'rang' => 1
						),
						'id_noisette = '.$id_noisette
					);
			}
			else{
				// Tout le monde monte d'un rang
				$ok = sql_update(
					'spip_noisettes',
					array(
						'rang' => 'rang - 1'
					),
					'bloc = '.sql_quote($bloc).' and type='.sql_quote($type).'and composition='.sql_quote($composition)
				);
				// La noisette passe tout en bas
				if ($ok)
					$ok = sql_updateq(
						'spip_noisettes',
						array(
							'rang' => $dernier_rang
						),
						'id_noisette = '.$id_noisette
					);
			}
		}
		// On invalide le cache
		include_spip('inc/invalideur');
		$cle_invalidation = _request('bloc').'/'._request('type');
		if (_request('composition') != '')
			$cle_invalidation .= '-'._request('composition');
		suivre_invalideur($cle_invalidation);
		
		if (!$ok) $retours['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
	}
	
	
	$retours['editable'] = true;
	
	return $retours;
}

?>
