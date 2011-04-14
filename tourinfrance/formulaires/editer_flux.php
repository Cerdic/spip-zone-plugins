<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');
function formulaires_editer_flux_charger_dist($id_flux='nouveau', $nouveau='non'){

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
	        $i=0;	//Compteur pour EVITER les premiers champs de la table.
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
			$valeurs["nouveau"] = $nouveau;
			$valeurs["mappage"] = $mappage_tab;
			$valeurs["liste_chps_update"] = $liste_chps_update;
			$valeurs["liste_elements"] = $liste_elements_tab;
        }
        
	    return $valeurs;
        
}
function formulaires_editer_flux_verifier_dist($id_flux='nouveau', $nouveau='non'){
        
        $erreurs = array();
        
        /***  NOUVEAU  ***/
        if($id_flux=='nouveau'){
	    	foreach(array('url_flux','nom_flux') as $champ) {
		        if (!$_REQUEST[$champ] || ($champ=='url_flux' && $_REQUEST[$champ]=='http://')) {
		            $erreurs[$champ] = _T('tourinfrance:form_info_obligatoire');
		        }
		    }
        }
        
        /***  UPDATE  ***/
        else{
        
	    	$liste_chps_update = unserialize($_REQUEST["chps_updates"]);
	    	
        	foreach($liste_chps_update as $champ) {
		        if (!$_REQUEST[$champ]) {
		            $erreurs[$champ] =  _T('tourinfrance:form_info_obligatoire');
		        }
		    }
		    
	   		if (!count($erreurs)) {
	    		$type_flux = trouver_type_flux($_REQUEST["id_flux"], $_REQUEST['id_type']);
	    		if(!$type_flux) $erreurs['message_erreur'] .=  _T('tourinfrance:form_flux_vide') . "<br />";
	    	}
        }
        
	    if (count($erreurs)) {
	        $erreurs['message_erreur'] .= _T('tourinfrance:form_message_verifier_erreur');
	    }
   		return $erreurs;
   		
}
function formulaires_editer_flux_traiter_dist($id_flux='nouveau', $nouveau='non'){
    
        /***  NOUVEAU  ***/
        if ($id_flux=='nouveau'){
        
	        if ($id_flux = sql_insertq('spip_tourinfrance_flux', array(
	        		'nom_flux'=>$_REQUEST['nom_flux'],
	        		'url_flux'=>$_REQUEST['url_flux']
	        	))) {
	   			
		    	$retour['redirect'] = "?exec=flux_edit&nouveau=oui&id_flux=" . $id_flux;
		    }
		    else {
		    	$retour['message_erreur'] = _T('tourinfrance:form_message_traiter_erreur');
		    }
		  
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
	        
			//UPDATE : Mise a jour
			sql_updateq('spip_tourinfrance_flux', $champs, 'id_flux=' . $_REQUEST["id_flux"]);

	   		$retour['message_ok'] = _T('tourinfrance:form_message_traiter_ok');
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