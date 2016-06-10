<?php
/**
 * Plugin spip2spip
 * 
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_spip2spip_identifier_dist($id_spip2spip='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_spip2spip)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_spip2spip_charger_dist($id_spip2spip='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('spip2spip',$id_spip2spip,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_spip2spip_verifier_dist($id_spip2spip='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	// version de base fabrique
  //return formulaires_editer_objet_verifier('spip2spip',$id_spip2spip);
  
  $erreurs = formulaires_editer_objet_verifier('spip2spip',$id_spip2spip);
  
  // verification supplementaires 
  if (!_request('site_titre')) 
                         $erreurs['site_titre'] = _T('spip2spip:erreur_obligatoire');
  
  if ((!_request('site_rss')) OR (_request('site_rss')=="http://www")) {
                         $erreurs['site_rss'] = _T('spip2spip:erreur_obligatoire');
  } else {
      // "ping" si flux distant disponible
      include_spip('inc/distant'); 
    	$ping = recuperer_lapage(_request('site_rss'));  
    	if (!$ping) {    		
    		 $erreurs['site_rss'] = _T('spip2spip:erreur_flux_inconnu');
    	}  

  }	
  
  return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_spip2spip_traiter_dist($id_spip2spip='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
  return formulaires_editer_objet_traiter('spip2spip',$id_spip2spip,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>