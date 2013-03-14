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
  
  if ($GLOBALS['meta']['type_urls']=="arbo") {
      $pipeline = "arbo_creer_chaine_url"; 
      include_spip("urls/arbo");
  } else {
      $pipeline = "propres_creer_chaine_url";
      include_spip("urls/propres"); 
  }
  
	$url = pipeline($pipeline,
			array(
				'data' => _request('urlpropre'),  // le vieux url_propre        
				'objet' => array('type' => $type_objet, 'id_objet' => $id_objet, 'titre'=>trim(_request('urlpropre')))
			)
		);

  // nettoyage URLs   (chargement param de cgf)   
  /*  le code est deporté dans les urls/...       
  // section a effacer dans   les futurs commits
  //$url =  _request('urlpropre');
  $longueur_min = (int) lire_config('urledit/longueur_min'); 
  if ($longueur_min<3)    $longueur_min = 3;
  if ($longueur_min>250)  $longueur_min = 250;    
  $longueur_max = (int) lire_config('urledit/longueur_max'); 
  if ($longueur_max<35)    $longueur_max = 35;
  if ($longueur_max>255)  $longueur_max = 255;
  if  ($longueur_min>$longueur_max)
                              $longueur_max = $longueur_min+10;
  
  $separateur = "-";
  if (lire_config('urledit/separateur')!="")
                          $separateur = lire_config('urledit/separateur');    
  $filtre = "";                        
  if (lire_config('urledit/filtre')!=""  AND function_exists(lire_config('urledit/filtre')))
                          $filtre = lire_config('urledit/filtre');
  
  //die("$longueur_min / $longueur_max / $separateur / $filtre *****");
  
  include_spip('inc/filtres'); // pour fournir à url_nettoyer le filtre supprimer_tags
	include_spip('action/editer_url');
	if (!$url = url_nettoyer($url,$longueur_max,$longueur_min,$separateur,$filtre))  
		return;
  */
	
	$set = array('url' => $url, 'type' => $type_objet, 'id_objet' => $id_objet, 'date' => 'NOW()');
  $c = @sql_insertq('spip_urls', $set);  

			//retour erreur duplicite  
      $redirect = _request('redirect');
			$redirect = parametre_url($redirect,'erreur_urledit',"1-$c",'&');
			include_spip('inc/headers');
			redirige_par_entete($redirect); 
    		
	
}

?>