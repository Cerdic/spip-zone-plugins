<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/noizetier');

function formulaires_configurer_bloc_charger($bloc,$page,$infos_bloc){
	$contexte = array();
	
	$contexte['bloc'] = $bloc;
	$contexte['page'] = $page;
	$type_compo = explode ('-',$page,2);
	$contexte['type'] = $type_compo[0];
	$contexte['composition'] = $type_compo[1];
	$contexte['bloc_page'] = $bloc.'-'.$page;
	$contexte['_infos_bloc'] = $infos_bloc;
	
	// On sait toujours où va la noisette
	$contexte['_hidden'] .= '<input type="hidden" name="type" value="'.$contexte['type'].'" />';
	$contexte['_hidden'] .= '<input type="hidden" name="composition" value="'.$contexte['composition'].'" />';
	$contexte['_hidden'] .= '<input type="hidden" name="bloc" value="'.$contexte['bloc'].'" />';
	
	// Si on demande une nouvelle noisette pour un bloc --------------------------
	if ($bloc_page = _request('demander_nouvelle_noisette')){
		// S'il n'y a pas encore de noisette de choisie
		if (!($noisette = _request('noisette'))){
			$contexte['bloc_page_nouvelle_noisette'] = $bloc_page;
			// On charge les différentes noisettes du type, celles de la composition et celles pour toutes les pages
			$contexte['_noisettes_type'] = noizetier_lister_noisettes($contexte['type']);
			if ($contexte['composition']!='')
				$contexte['_noisettes_composition'] = noizetier_lister_noisettes($contexte['type'].'-'.$contexte['composition']);
			$contexte['_noisettes_toutes_pages'] = noizetier_lister_noisettes('');
		}
		// Si on a choisi une noisette
		else{
			$contexte['bloc_page_nouvelle_noisette'] = $bloc_page;
			$contexte['noisette'] = $noisette;
			// On charge les infos de la noisette choisie
			$noisettes = noizetier_lister_noisettes();
			$contexte['_infos_'.$noisette] = $noisettes[$noisette];
			$contexte['_infos_'.$noisette]['parametres'][] = array(
				'saisie' => 'input',
				'options' => array(
					'nom' => 'noizetier_css',
					'label' => _T('noizetier:label_noizetier_css'),
					'explication' => _T('noizetier:explication_noizetier_css')
				)
			);
		}
	}
	
	// Si on veut modifier une noisette ------------------------------------------
	if ($id_noisette = intval(_request('modifier_noisette'))){
		// On va chercher l'existant de cette noisette
		$entree = sql_fetsel(
			'noisette, parametres, css',
			'spip_noisettes',
			'id_noisette = '.$id_noisette
		);
		$noisette = $entree['noisette'];
		$parametres = unserialize($entree['parametres']);
		$css = $entree['css'];
		
		if (is_array($parametres))
			$contexte = array_merge($contexte, $parametres);
		$contexte['id_noisette'] = $id_noisette;
		$contexte['noisette'] = $noisette;
		// On charge les infos de la noisette choisie
		$noisettes = noizetier_lister_noisettes();
		$contexte['_infos_'.$noisette] = $noisettes[$noisette];
		$contexte['_infos_'.$noisette]['parametres'][] = array(
				'saisie' => 'input',
				'options' => array(
					'nom' => 'noizetier_css',
					'label' => _T('noizetier:label_noizetier_css'),
					'explication' => _T('noizetier:explication_noizetier_css'),
					'defaut' => $css
				)
			);
	}
	
	// Si on a validé une noisette et qu'il y a une erreur -------------------------------
	if (($bloc_page = _request('bloc_page_nouvelle_noisette') or $id_noisette = intval(_request('id_noisette_modifiee'))) and _request('enregistrer')!=''){
		$erreurs = formulaires_configurer_bloc_verifier($bloc,$page);
		// S'il y a des erreurs
		if(count($erreurs)>0){
			$noisette = _request('noisette');
			$infos_param = noizetier_charger_parametres_noisette($noisette);
			// On récupère les paramètres transmis
			foreach ($infos_param as $nom=>$parametre)
				$contexte[$nom] = _request($nom);
			// On transmets les autres éléments du contexte
			$noisettes = noizetier_lister_noisettes();
			$contexte['_infos_'.$noisette] = $noisettes[$noisette];
			$contexte['noisette'] = $noisette;
			if ($id_noisette>0)
				$contexte['id_noisette'] = $id_noisette;
			if ($bloc_page)
				$contexte['bloc_page_nouvelle_noisette'] = $bloc_page;
		}
	}

	return $contexte;
}

function formulaires_configurer_bloc_verifier($bloc,$page){
	$erreurs = array();
	
	// Si on demande une nouvelle noisette pour un bloc --------------------------
	
	if ($bloc_page = _request('demander_nouvelle_noisette')){
		// S'il n'y a pas encore de noisette de choisie alors qu'on a demandé la suite
		if (!($noisette = _request('noisette')))
			if (_request('suivant'))
				$erreurs['erreur_noisette'] = _T('noizetier:erreur_doit_choisir_noisette');
	}
	
	// Si on valide une noisette pour un bloc ------------------------------------
	
	if (($bloc_page = _request('bloc_page_nouvelle_noisette') or $id_noisette = intval(_request('id_noisette_modifiee',$_POST))) and _request('enregistrer')){
		$noisette = _request('noisette');
		$infos_param = noizetier_charger_parametres_noisette($noisette);
		// Si le plugin verifier est actif
		if (defined('_DIR_PLUGIN_VERIFIER')){
			$verifier = charger_fonction('verifier','inc',true);
			foreach ($infos_param as $nom=>$parametre){
				if (isset($parametre['verifier'])){
					if (!isset($parametre['verifier']['options']))
						$parametre['verifier']['options']=array();
					if (($erreur = $verifier(_request($nom),$parametre['verifier']['type'],$parametre['verifier']['options'])) != '')
						$erreurs[$nom] = $erreur;
				}
			}
		}
		// On teste que chaque paramètre obligatoire est bien renseigné
		foreach ($infos_param as $nom=>$parametre){
			if ($parametre['obligatoire']=='oui'){
				if (_request($nom)=='')
					$erreurs[$nom] = _T('info_obligatoire');
			}
		}
	}
	return $erreurs;
}

function formulaires_configurer_bloc_traiter($bloc,$page){
	$retours = array();
	
	// Si on valide une noisette pour un bloc ------------------------------------
	
	if (($bloc_page = _request('bloc_page_nouvelle_noisette') or $id_noisette_modifiee = intval(_request('id_noisette_modifiee',$_POST))) and _request('enregistrer')!=''){
		$rang = intval(_request('rang'));
		$noisette = _request('noisette');
		$css = _request('noizetier_css');
		$infos_param = noizetier_charger_parametres_noisette($noisette);
		$parametres_envoyes = array();
		foreach ($infos_param as $nom=>$parametre)
			$parametres_envoyes[$nom] = _request($nom);
		spip_desinfecte($parametres_envoyes);
		
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
					'parametres' => serialize($parametres_envoyes),
					'css' => $css
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
		if ($id_noisette_modifiee) {
			$id_noisette = sql_updateq(
				'spip_noisettes',
				array(
					'parametres' => serialize($parametres_envoyes),
					'css' => $css
				),
				'id_noisette='.$id_noisette_modifiee
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
	
	// Si on demande à déplacer une noisette avec dragndrop -------------------------------------

	if ($params = _request('dragndrop_noisette')){
		$rang_noisettes = _request('rang_noisettes');
		
		foreach ($rang_noisettes as $rang=>$id_noisette){
			$rang = $rang + 1;
			$ok = sql_updateq('spip_noisettes',array('rang' => intval($rang)),"id_noisette = $id_noisette");
		}
		
		// On invalide le cache
		include_spip('inc/invalideur');
		// necessaire tout ça ?
		$cle_invalidation = _request('bloc').'/'._request('type');
		if (_request('composition') != '')
			$cle_invalidation .= '-'._request('composition');
		// doit suffire '1' 
		suivre_invalideur($cle_invalidation);
		
		if (!$ok) $retours['message_erreur'] = _T('noizetier:erreur_mise_a_jour');
	}

	// Si on demande à déplacer une noisette sans dragndrop -------------------------------------
	
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
