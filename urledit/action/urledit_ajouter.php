<?php


if (!defined("_ECRIRE_INC_VERSION")) return;


//
// créer un nouvelle URL
//
function action_urledit_ajouter_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	list($type_objet, $id_objet) = preg_split('/\W/', $arg);
	$id_objet = intval($id_objet);
	/*$url = pipeline('creer_chaine_url',
			array(
				'data' => _request('urlpropre'),  // le vieux url_propre
				'objet' => array('type' => $type, 'id_objet' => $id_objet, 'titre'=>_request('urlpropre'))
			)
		);
		*/
	$url =  _request('urlpropre');

  // nettoyage URLs
	if (!defined('_URLS_ARBO_MAX')) define('_URLS_ARBO_MAX', 35);
	if (!defined('_URLS_ARBO_MIN')) define('_URLS_ARBO_MIN', 3);

	include_spip('action/editer_url');
	
  if (!$url = url_nettoyer($url,_URLS_ARBO_MAX,_URLS_ARBO_MIN,'-',''))   // ici possible d'ajouter des arguments l'argument filtre (url en minuscule)
		return;

	
	$set = array('url' => $url, 'type' => $type_objet, 'id_objet' => $id_objet, 'date' => 'NOW()');
  $c = @sql_insertq('spip_urls', $set);  

			//retour erreur duplicite  
      $redirect = _request('redirect');
			$redirect = parametre_url($redirect,'erreur_urledit',"1-$c",'&');
			include_spip('inc/headers');
			redirige_par_entete($redirect); 
  
    		
	
}

?>