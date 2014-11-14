<?php
/**
 * Plugin Partageur
 * 
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_partageur_identifier_dist($id_partageur='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_partageur)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_partageur_charger_dist($id_partageur='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('partageur',$id_partageur,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_partageur_verifier_dist($id_partageur='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
  
  $erreurs = formulaires_editer_objet_verifier('partageur',$id_partageur,array('titre'));
  
  // verification supplementaires
  if (!_request('titre')) 
                         $erreurs['titre'] = _T('partageur:erreur_obligatoire');
  
  if ((!_request('url_site')) OR (_request('url_site')=="http://www")) {
                         $erreurs['url_site'] = _T('partageur:erreur_obligatoire');
  } else {
      // "ping" si flux distant disponible
      include_spip('inc/distant');     
      $url = _request('url_site')."/spip.php?page=backend-partageur&id_article=1";
    	$ping = recuperer_page($url);  
    	if (!$ping) {    		
    		 $erreurs['url_site'] = _T('partageur:erreur_flux_inconnu')."<br /><a href='$url'>$url</a>";
    	} else if ($row_site = sql_fetsel("url_site","spip_partageurs",'id_partageur!='.intval($id_partageur).' AND statut="publie" AND url_site='.sql_quote(_request('url_site'))))   
    	   $erreurs['url_site'] = _T('partageur:erreur_flux_doublon');
 

  }	

  
  return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_partageur_traiter_dist($id_partageur='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('partageur',$id_partageur,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>