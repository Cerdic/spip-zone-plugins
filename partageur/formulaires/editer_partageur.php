<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// IN CVT WE TRUST

//
//  Etape 1: CHARGER
//
function formulaires_editer_partageur_charger_dist() {
	$contexte = array(
        'titre' => '',
        'url_site' => 'http://'
	);
	return $contexte;
}

//
//  Etape 2: VERIFIER
//
function formulaires_editer_partageur_verifier_dist() {
  $erreurs = array();
  
  if (!_request('titre')) 
                         $erreurs['titre'] = _T('info_obligatoire_02');
  
  if ((!_request('url_site')) OR (_request('url_site')=="http://")) {
                         $erreurs['url_site'] = _T('entree_adresse_site');
  } else {
      // "ping" si flux distant disponible
      include_spip('inc/distant');     
      $url = _request('url_site')."/spip.php?page=backend-partageur&id_article=1";
    	$ping = recuperer_lapage($url);  
    	if (!$ping) {    		
    		 $erreurs['url_site'] = _T('partageur:flux_inconnu')."<br /><a href='$url'>$url</a>";
    	} else if ($row_site = sql_fetsel("url_site","spip_partageurs",'url_site='.sql_quote(_request('url_site'))))   
    	   $erreurs['url_site'] = _T('partageur:flux_doublon');
     
    

  }	                        
                         	    
  

	return $erreurs;
}

//
//  Etape 3: TRAITER
//
function formulaires_editer_partageur_traiter_dist() {
    include_spip('base/abstract_sql');
    include_spip('inc/headers');
    include_spip('inc/utils');
    
    $data_sql = array (		          
			     "titre"	=> _request('titre'),			
			     "url_site"	=> _request('url_site')
    );
      
   $id_partage= sql_insertq('spip_partageurs',$data_sql);
   
   
   redirige_par_entete('./?exec=partageurs');   
}


?>