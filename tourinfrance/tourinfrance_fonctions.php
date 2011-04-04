<?php
function traiter_donnees_tourinfrance($url_flux, $id_flux, $infos_flux) {

	if (file_exists($url_flux)) {
		
		$xml = simplexml_load_file($url_flux);
		
		$tab_infos_flux = unserialize($infos_flux);
		
		
		/*****  Liste des ELEMENTS déclarés  *****/
		$listeElements = $xml->xpath("//*[name()='xs:element']");
		foreach ($listeElements as $element) {
			$c .= ' - ' . $element->getName() . ' : ' . $element->attributes() . "<br />";
		}
		
		/*****  PARCOURS DES OFFRES : <LISTING>  *****/
		$offres = $xml->xpath('//child::LISTING');
		
		for($i=0; $i<count($offres); $i++){
		
			
			/*****  VALEURS des ELEMENTS DES OFFRES  *****/
			$commun = array();
			$extra = array();
			
			foreach( $offres[$i] as $element => $valeur ){
		
				$contenu = strip_tags(trim($valeur->asXML()));
				//$contenus = $contenu->getElementsByTagName($element)->item(0)->textContent; 
				//$contenus = $contenu->getElementsByTagName($element)->item(0)->textContent; 
				
				if(in_array($element, $tab_infos_flux)){
					$commun[array_search($element, $tab_infos_flux)] = $contenu;
				}
				else{
					$extra[$element] = $contenu;
				}
				
			}
			
			/*foreach( $commun as $cle => $valeur ){
				$c .= "<br />COMMUN == $cle = $valeur<br />";
			}
			foreach( $extra as $cle => $valeur ){
				$c .= "EXTRA == $cle = $valeur<br />";
			}*/
			
			
			/*****  DONNEES FORMATEES  *****/
			$commun_srlz = serialize($commun);
			$extra_srlz = serialize($extra);
			
			
			/*****  INSERER LES DONNEES  *****/	
   			inserer_donnees_tourinfrance($id_flux, $commun_srlz, $extra_srlz);
			
		}
				
	    return "Flux ajout&eacute; !";
	}
	else{
		return "$url_flux fichier non trouvé";
	}
	
}

function inserer_donnees_tourinfrance($id_flux, $commun_srlz, $extra_srlz) {

	$commun = unserialize($commun_srlz);
	$extra = unserialize($extra_srlz);
	
	$type_offre = strtolower($commun["id_type"]);

	//Recupere le TYPE D'OFFRE
	if ($req = sql_select("id_rubrique", "spip_rubriques", "titre='" . $type_offre . "'")) {
	    while ($res = sql_fetch($req)) {
	        $id_rubrique = $res['id_rubrique'];
	    }
	}
	

	//INSERER un ARTICLE dans la table SPIP_ARTICLES (bon id_rubrique)
	$champ_article = array(
		"id_rubrique" => $id_rubrique, 
		"titre" => $commun["nom_offre"], 
		"descriptif" => $commun["description_offre"]
		);
	//INSERTION ARTICLE
	$id_article = sql_insertq("spip_articles", $champ_article);

	
	//INSERTION AUTEUR
	$liaison_auteur_article = sql_insertq("spip_auteurs_articles", array(
		'id_auteur'=>'1', 
		'id_article'=>$id_article
	));
	
	
	//INSERER dans la bonne table SPIP_TOURINFRANCE
	$nom_table_tourinfrance = "spip_tourinfrance_" . $type_offre;
	
	$champ_tourinfrance_type = $commun;
	$champ_tourinfrance_type["id_flux"] = $id_flux;
	$champ_tourinfrance_type["id_article"] = $id_article;
	$champ_tourinfrance_type["extra"] = $extra_srlz;
	
	//INSERTION TOURINFRANCE
	$id_tourinfrance = sql_insertq($nom_table_tourinfrance, $champ_tourinfrance_type);

	
	//return  print_r($champ_article) . "<br />" . $nom_table_tourinfrance;
	
}

?>
