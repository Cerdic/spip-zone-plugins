<?php

// $LastChangedRevision: 45629 $
// $LastChangedBy: paladin@quesaco.org $
// $LastChangedDate: 2011-03-21 16:36:52 +0100 (Lun 21 mar 2011) $

/**
 * @since SPIP 2.0
 * @see http://www.spip.net/fr_article3800.html (les formulaires CVT)
 * @see http://www.spip.net/fr_article3796.html (CVT par l'exemple)
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('inc/spiplistes_api');
include_spip('inc/spiplistes_api_globales');

function formulaires_gestion_abonnement_charger_dist($id_liste=''){
	//spiplistes_debug_log ('formulaires_gestion_abonnement_charger_dist()');
	
	$d = _request('d');
	$stop = intval(_request('stop'));
	$valeurs = array();
	$valeurs['id_liste'] = $id_liste;
	$valeurs['d'] = $d;
	$valeurs['editable'] = false;
	
	if($auteur = auteur_cookie_ou_session($d))
	{
		$id_auteur = $auteur['id_auteur'];
		$valeurs['id_auteur'] = intval($id_auteur);
		$valeurs['format'] = spiplistes_format_abo_demande($id_auteur);
		$valeurs['editable'] = true;
		
		// la liste des abonnements en cours
		// pour cet auteur (avec titre des  listes)
		$mes_abos = spiplistes_abonnements_listes_auteur ($id_auteur, true);
		
		// si c'est un desabonnement a une liste
		// affiche juste la demande de confirmation
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
					$valeurs['titre_liste'] = $row['titre'];
					$valeurs['descriptif'] = $row['descriptif'];
					$valeurs['stop'] = $stop;
				}
				else
				{
					$valeurs['errormsg'] = _T('spiplistes:pas_abonne_liste');
				}
			}
			else
			{
				unset ($valeurs['d']);
				unset ($valeurs['editable']);
			}
		}
	}
	else
	{
		spiplistes_log ('ERR: UNSUBSCRIBE id_auteur #'.$id_auteur.' id_liste #'.$id_liste);
		$valeurs['errormsg'] = _T('spiplistes:action_interdite');
	}
	return $valeurs;
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
	
	if ($auteur = auteur_cookie_ou_session($d))
	{
		$id_auteur = $auteur['id_auteur'];
		$email = $auteur['email'];
		
		// la liste des abonnements en cours
		// pour cet auteur
		$mes_abos = spiplistes_abonnements_listes_auteur ($id_auteur, true);
		
		// demander de stopper une inscription ?
		if ($stop > 0)
		{
			$id_liste = $stop;
			
			if (isset ($mes_abos[$id_liste]))
			{
				spiplistes_abonnements_auteur_desabonner ($id_auteur, $id_liste);
				$contexte = array(
					'message_ok' => _T('spiplistes:vous_etes_desinscrit')
				);
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
			if (count ($listes_souhaitees))
			{
				$listes_souhaitees = array_flip ($listes_souhaitees);
				
				// abonner aux listes demandées
				foreach (array_keys($listes_souhaitees) as $id_liste)
				{
					if (!isset($mes_abos[$id_liste]))
					{
						spiplistes_abonnements_ajouter ($id_auteur, $id_liste);
						$mes_abos[$id_liste] = '';
					}
				}
				
				// désabonner les listes non souhaitées
				foreach (array_keys($mes_abos) as $id_liste)
				{
					if (!isset ($listes_souhaitees[$id_liste]))
					{
						spiplistes_abonnements_auteur_desabonner ($id_auteur, $id_liste);
					}
				}
				
				// abonner aux listes demandées,
				// si pas déjà abonné
				foreach (array_keys($listes_souhaitees) as $id_liste)
				{
					if (!isset ($mes_abos[$id_liste]))
					{
						if(spiplistes_abonnements_ajouter($id_auteur, $id_liste) !== false)
						{
							// @todo a ameliorer, style une ligne de confirmation par liste ?
							$message_ok = _T('spiplistes:abonnement_modifie');
						}
					}
				}
			}
			else if (count ($mes_abos))
			{
				// tout est déselectionné ?
				// supprimer tout les abonnements !
				spiplistes_abonnements_auteur_desabonner ($id_auteur, 'toutes');
				$mes_abos = array();
			}
			
			if($format != $prev_format)
			{
				if ($format == 'non')
				{
					if (count ($mes_abos))
					{
						spiplistes_abonnements_auteur_desabonner ($id_auteur, 'toutes');
					}
					
					$message_ok = _T('spiplistes:desabonnement_valid').' :&nbsp;'.$email;  
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
				'format' => $format
			);
		}
	}
	
	return ($contexte);
}

// recuperer id_auteur, statut, nom et email pour :
// -* l'auteur associé au cookie de l'environnement
// -* ou l'auteur de la session en cours
function auteur_cookie_ou_session($d)
{
	//spiplistes_debug_log ("auteur_cookie_ou_session($d)");
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
			spiplistes_debug_log ("auteur_cookie_ou_session ni cookie, ni id ?");
		}
	}
	return $return;
}

