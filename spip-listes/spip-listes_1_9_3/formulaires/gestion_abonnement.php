<?php
/**
 * @since SPIP 2.0
 * @see http://www.spip.net/fr_article3800.html (les formulaires CVT)
 * @see http://www.spip.net/fr_article3796.html (CVT par l'exemple)
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('inc/spiplistes_api');
include_spip('inc/spiplistes_api_globales');

function formulaires_gestion_abonnement_charger_dist($id_liste=''){
	//spiplistes_debug_log ('formulaires_gestion_abonnement_charger_dist()');
	
	$d = _request('d');
	$stop = intval(_request('stop'));
	$contexte = array();
	$contexte['id_liste'] = $id_liste;
	$contexte['d'] = $d;
	$contexte['editable'] = false;
	
	if($auteur = spiplistes_auteur_cookie_ou_session($d))
	{
		$id_auteur = $auteur['id_auteur'];
		$contexte['id_auteur'] = intval($id_auteur);
		$contexte['format'] = spiplistes_format_abo_demande($id_auteur);
		$contexte['editable'] = true;
		
		/**
		 * Recupere la liste des abonnements en cours
		 * pour cet auteur (avec titre des  listes)
		 */
		$mes_abos = spiplistes_abonnements_listes_auteur ($id_auteur, true);
		
		/**
		 * Si c'est un desabonnement a une liste
		 * affiche juste la demande de confirmation
		 */
		if ($stop > 0)
		{
			if ($id_auteur > 0)
			{
				$id_liste = $stop;
				
				// verifier qu'il est encore abonne' a cette liste
				if (
					$mes_abos
					&& isset($mes_abos[$id_liste])
				)
				{
					$row = spiplistes_listes_liste_fetsel ($id_liste, 'titre,descriptif');
					$contexte['titre_liste'] = $row['titre'];
					$contexte['descriptif'] = $row['descriptif'];
					$contexte['stop'] = $stop;
				}
				else
				{
					$contexte['errormsg'] = _T('spiplistes:pas_abonne_liste');
				}
			}
			else
			{
				unset ($contexte['d']);
				unset ($contexte['editable']);
			}
		}
	}
	else
	{
		spiplistes_log ('ERR: UNSUBSCRIBE id_auteur #'.$id_auteur.' id_liste #'.$id_liste);
		$contexte['errormsg'] = _T('spiplistes:action_interdite');
	}
	
	$contexte['nb_abos'] = count($mes_abos);
	
	return ($contexte);
}

function formulaires_gestion_abonnement_verifier($id_liste='') {
	//spiplistes_debug_log('formulaires_gestion_abonnement_verifier()');
	
	$erreurs = array();
	return $erreurs;
}

function formulaires_gestion_abonnement_traiter_dist($id_liste='') {
	//spiplistes_debug_log('formulaires_gestion_abonnement_traiter_dist()');
	
	$d = _request('d');
	$listes = _request('listes');
	$format = _request('suppl_abo');
	$stop = intval(_request('stop'));
	
	if ($auteur = spiplistes_auteur_cookie_ou_session($d))
	{
		$id_auteur = $auteur['id_auteur'];
		$email = $auteur['email'];
		
		// la liste des abonnements en cours
		// pour cet auteur
		$mes_abos = spiplistes_abonnements_listes_auteur ($id_auteur, FALSE);
							  
		// demander de stopper une inscription ?
		if ($stop > 0)
		{
			$id_liste = $stop;
			
			if (isset ($mes_abos[$id_liste]))
			{
				spiplistes_abonnements_auteur_desabonner ($id_auteur, $id_liste);
				$message_ok = _T('spiplistes:abonnement_modifie').'.'
					. '<br />' . PHP_EOL
					. _T('spiplistes:vous_n_etes_plus_abonne_aux_listes')
					;
			}
		}
		else
		{
			$prev_format = spiplistes_format_abo_demande($id_auteur);
		
			$listes_souhaitees =
				(is_array($listes) && count($listes))
				? $listes
				: array()
				;
			
			/**
			 * supprime d'abord tous les abonnements
			 */
			spiplistes_abonnements_auteur_desabonner ($id_auteur, 'toutes');
			$message_ok = _T('spiplistes:abonnement_modifie').'.';
			
			/**
			 * Abonne aux listes sélectionnées
			 */
			if (count ($listes_souhaitees))
			{
									  
				// les cles sont les id_listes
				$listes_souhaitees = array_flip ($listes_souhaitees);
				
				spiplistes_abonnements_ajouter ($id_auteur
												, array_keys($listes_souhaitees)
												);
				$nb = count ($listes_souhaitees);
				if ($nb >= 1)
				{
					$message_ok .= '<br />' . PHP_EOL;
					$message_ok .=
						($nb > 1)
						? _T('spiplistes:vous_etes_abonne_aux_listes_selectionnees')
						: _T('spiplistes:vous_etes_abonne_a_la_liste_selectionnee')
						;
				}
			}
			else if (count($mes_abos))
			{
				$message_ok .= '<br />' . PHP_EOL
					. _T('spiplistes:vous_n_etes_plus_abonne_aux_listes')
					;
			}
			
			if($format != $prev_format)
			{
				if ($format == 'non')
				{
					if (count ($mes_abos))
					{
						spiplistes_abonnements_auteur_desabonner ($id_auteur, 'toutes');
					}
					
					$message_ok = _T('spiplistes:abonnement_modifie').'.'
						. '<br />' . PHP_EOL
						. _T('spiplistes:vous_n_etes_plus_abonne_aux_listes')
						; 
				}
				else {
					spiplistes_format_abo_modifier($id_auteur, $format);
					$message_ok = _T('spiplistes:abonnement_modifie');
					$message_ok .= '<br />'._T('spiplistes:abonnement_nouveau_format').$format;
				}
			}
			
			spiplistes_auteurs_cookie_oubli_updateq ('', $d, $true);
		
			$contexte = array(
				'editable' => true,
				'message_ok' => $message_ok,
				'format' => $format,
				'id_auteur' => $id_auteur
			);
		}
	}
	
	return ($contexte);
}

/**
 * Recuperer id_auteur, statut, nom et email pour :
 * - l'auteur associé au cookie de l'environnement
 * - ou l'auteur de la session en cours
 * @return array
 */
function spiplistes_auteur_cookie_ou_session ($d)
{
	//spiplistes_debug_log ("spiplistes_auteur_cookie_ou_session($d)");
	$return = array();
	// si pas de cookie on chope l'auteur de la session
	if(empty($d)) {
		if($id_auteur=$GLOBALS['visiteur_session']['id_auteur']) {
			$return['id_auteur'] = intval($id_auteur);
			$row = sql_fetsel(
				'id_auteur,statut,nom,email',
				'spip_auteurs',
				'id_auteur='.sql_quote($id_auteur)
			);
			if($row) {
				$return['id_auteur'] = $row['id_auteur'];
				$return['statut'] = $row['statut'];
				$return['nom'] = $row['nom'];
				$return['email'] = $row['email'];
			}
		}
	}
	// recuperer les donnes de l'auteur associe au cookie
	if(!empty($d))
	{
		$row = sql_fetsel(
			'id_auteur,statut,nom,email',
			'spip_auteurs',
			'cookie_oubli='.sql_quote($d).' AND statut<>'.sql_quote('5poubelle')
		);
		if($row)
		{
			$return['id_auteur'] = $row['id_auteur'];
			$return['statut'] = $row['statut'];
			$return['nom'] = $row['nom'];
			$return['email'] = $row['email'];
		}
		else {
			spiplistes_debug_log ("spiplistes_auteur_cookie_ou_session ni cookie, ni id ?");
		}
	}
	return $return;
}