<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');
function formulaires_editer_flux_charger_dist($id_flux='nouveau'){

        /***  NOUVEAU  ***/
        if($id_flux=='nouveau'){
	        $valeurs = array(
		        "id_flux" => $id_flux,
		        "url_flux" => "http://",
		        "nom_flux" => ""
		    );
        }
        
        /***  UPDATE  ***/
        else{
      		$valeurs = array();				
      		$mappage_tab = array();			//Table de Mappage
      		$liste_chps_update = array();	//Liste des champs a mettre ajour
      		
	        $map = sql_fetsel('*', 'spip_tourinfrance_flux', 'id_flux='.$id_flux);
	        
	        //Liste des elements du flux
	        $liste_elements_tab = liste_elements($map['url_flux']);
	        
	        //Pour chaque colonne de la ligne : on mappe dans un tableau
	        $i=0;
	        foreach( $map as $cle => $valeur ){
	        	if($i>3){
		        	$mappage_tab[$cle] = $valeur;
		        	$liste_chps_update[] = $cle;
	        	}
	        	if($i==1){
		        	$liste_chps_update[] = $cle;
	        	}
		        $valeurs[$cle] = $valeur;
	        	$i++;
	        }
			$valeurs["mappage"] = $mappage_tab;
			$valeurs["liste_chps_update"] = $liste_chps_update;
			$valeurs["liste_elements"] = $liste_elements_tab;
        }
        
	    return $valeurs;
        
}
function formulaires_editer_flux_verifier_dist($id_flux='nouveau'){
        
        $erreurs = array();
        
        /***  NOUVEAU  ***/
        if($id_flux=='nouveau'){
	    	foreach(array('url_flux','nom_flux') as $champ) {
		        if (!$_REQUEST[$champ] || ($champ=='url_flux' && $_REQUEST[$champ]=='http://')) {
		            $erreurs[$champ] = "Cette information est obligatoire !";
		        }
		    }
        }
        
        /***  UPDATE  ***/
        else{
        	foreach(array('nom_flux') as $champ) {
		        if (!$_REQUEST[$champ]) {
		            $erreurs[$champ] = "Cette information est obligatoire !";
		        }
		    }
		    
	    	$type_flux = trouver_type_flux($_REQUEST["id_flux"], $_REQUEST['id_type']);
	    	if(!$type_flux) $champs['vide'] = $erreurs['message_erreur'] .= "Le flux propos&eacute; est vide ou ne correspond pas aux crit&egrave;res Tourinfrance.<br />";
        }
        
	    if (count($erreurs)) {
	        $erreurs['message_erreur'] .= "Une erreur est pr&eacute;sente dans votre saisie.";
	        //$erreurs['id_flux'] = $id_flux;
	    }
   		return $erreurs;
   		
}
function formulaires_editer_flux_traiter_dist($id_flux='nouveau'){
        //return formulaires_editer_objet_traiter('flux', $id_flux, '', '', $retour, '');
    
        /***  NOUVEAU  ***/
        if ($id_flux=='nouveau'){
        	
        
	        if ($id_flux = sql_insertq('spip_tourinfrance_flux', array(
	        		'nom_flux'=>$_REQUEST['nom_flux'],
	        		'url_flux'=>$_REQUEST['url_flux']
	        	))) {
				//$retour['liste_elements'] = $liste_elements;
				//$retour['id_flux'] = $id_flux;
	   			//$retour['editable'] = true;
	   			
		    	$retour['redirect'] = "?exec=flux_edit&id_flux=" . $id_flux;
	   			//$retour['message_ok'] = "Flux ajout&eacute; avec succ&egrave;s.<br />Proc&eacute;dez au mappage avec pr&eacute;caution et enregistrer.";
		    } else {
		    	$retour['message_erreur'] = "Une erreur s'est produite lors de l'ajout.";
		    }
		  
		    
		    //$pop = array('nom_flux'=>_request('nom_flux'),'url_flux'=>_request('url_flux'));
		    
	   		//$retour['editable'] = true;
		    //$retour['redirect'] = "?exec=flux_edit&id_flux=" . 1;
	        //$retour['message_ok'] = "Flux ajout&eacute; avec succ&egrave;s.";
	    }
	    
        /***  UPDATE  ***/
	    else{ 
	    	//Liste des champs a mettre ˆ jour.
	    	$liste_chps_update = unserialize($_REQUEST["chps_updates"]);
	    	
	    	$type_flux = trouver_type_flux($_REQUEST["id_flux"], $_REQUEST['id_type']);
	    	
	    	$champs = array();
	    	
	    	//Pour chaque champs, on l'ajoute au tableau des modifications.
	    	foreach( $liste_chps_update as $cle => $valeur ){
	    		$champs[$valeur] = $_REQUEST[$valeur];
	        }
	        
	        $champs['type_flux'] = $type_flux;
	        //$champs['urlll_flux'] = $url_flux;
	        //$champs['typeid_flux'] = $_REQUEST['id_type'];
	        
			//UPDATE : Mise a jour
			sql_updateq('spip_tourinfrance_flux', $champs, 'id_flux=' . $_REQUEST["id_flux"]);


	   		$retour['editable'] = true;
	   		$retour['message_ok'] = "Flux modifi&eacute; avec succ&egrave;s.";
	   		//$retour['redirect'] = "?exec=flux_voir&id_flux=" . $_REQUEST["id_flux"];
	    }
	    
	    return $retour;
}

function liste_elements($url) {

		//FAIRE une COPIE LOCALE ????
		
		$xml = simplexml_load_file($url);
		
		$liste_elements_tab = array();
			
		//  Liste des ELEMENTS dŽclarŽs
		$listeElements = $xml->xpath("//*[name()='xs:element']");
		foreach ($listeElements as $element) {
		
			$element_att = $element->attributes();
			$element_name = (string)$element_att["name"];
			if($element_name!="Listing" && $element_name!="LISTING"){
				$liste_elements_tab[$element_name] = $element_name;
			}
		}
		
		return $liste_elements_tab;
}
function trouver_type_flux($id_flux, $label_type) {

		$url = sql_getfetsel('url_flux', 'spip_tourinfrance_flux', 'id_flux=' . $id_flux);

		//FAIRE une COPIE LOCALE ????
		
		$xml = simplexml_load_file($url);
		
		$chemin = '//child::LISTING/' . $label_type;
		
		if($noeud = $xml->xpath($chemin)){
	 
			$type_flux = trim(strip_tags($noeud[0]->asXML()));
			
			return strtolower($type_flux);
		}
		return false;
}
?>