<?php
function traiter_donnees_tourinfrance($url_flux, $id_flux, $infos_flux) {

	if (file_exists($url_flux)) {

		$xml = simplexml_load_file($url_flux);
		
		$tab_infos_flux = unserialize($infos_flux);
		
		
		/*****  Liste des ELEMENTS déclarés   !!!IMPORTANT!!!   *****/
		/*
		$listeElements = $xml->xpath("//*[name()='xs:element']");
		foreach ($listeElements as $element) {
			$c .= ' - ' . $element->getName() . ' : ' . $element->attributes() . "<br />";
		}*/
		
		/*****  PARCOURS DES OFFRES : <LISTING>  *****/
		$offres = $xml->xpath('//child::LISTING');
		
		
		
		$exec = "<b>Flux proposé à la base de données !</b><br />";
		$exec .= "URL : <i>$url_flux</i><br /><ul>";
		
		$retour_exec = "";
		
		for($i=0; $i<count($offres); $i++){
			
			/*****  VALEURS des ELEMENTS DES OFFRES  *****/
			$commun = array();
			$extra = array();
			
			foreach( $offres[$i] as $element => $valeur ){
		
				$contenu = trim(strip_tags($valeur->asXML()));
				
				if(in_array($element, $tab_infos_flux)){
					$commun[array_search($element, $tab_infos_flux)] = $contenu;
				}
				else{
					$extra[$element] = $contenu;
				}
				
			}
			
			
			/*****  DONNEES FORMATEES  *****/
			$commun_srlz = serialize($commun);
			$extra_srlz = serialize($extra);
			
			
			/*****  INSERER LES DONNEES  *****/	
   			$retour_exec .= inserer_donnees_tourinfrance($id_flux, $commun_srlz, $extra_srlz);
			
		}
		
		if($retour_exec == ""){
			$retour_exec = "Aucune modification apportée.";
		}
		
		$exec .= $retour_exec . "</ul><br />";
				
	    return $exec;
	}
	else{
		return "$url_flux fichier non trouvé";
	}
	
}
function inserer_donnees_tourinfrance($id_flux, $commun_srlz, $extra_srlz) {

	$commun = unserialize($commun_srlz);
	$extra = unserialize($extra_srlz);
	
	$id_offre = $commun["id_offre"];
	$nom_offre = $commun["nom_offre"];
	$nom_commune = $commun["commune"];
	$datemaj = formater_date($commun["datemaj"]);
	$bordereau = strtolower($commun["id_type"]); //Bordereau

	//RUBRIQUES : Recupere le TYPE D'OFFRE / BORDEREAU
	if ($req = sql_select("id_rubrique", "spip_rubriques", "titre=" . sql_quote($bordereau))) {
	    while ($res = sql_fetch($req)) {
	        $id_rubrique = $res['id_rubrique'];
	    }
	}
	
	//GROUPES_MOTS : Recupere ID du GROUPE "COMMUNES"
	if ($req = sql_select("id_groupe", "spip_groupes_mots", "titre='communes'")) {
	    while ($res = sql_fetch($req)) {
	        $id_gp_mot_communes = $res['id_groupe'];
	    }
	}
	
	//MOTS_CLES : Test existance de la COMMUNE en MOT-CLE
	if ($req = sql_select("id_mot", "spip_mots", "titre=" . sql_quote($nom_commune))) {
	    if ($res = sql_fetch($req)) {
	        $id_mot = $res['id_mot'];
	    }
	    else{	//INSERTION SPIP_MOTS : Si la COMMUNE n'est pas en MOT CLE, on l'ajoute.
			$id_mot = sql_insertq("spip_mots", array(
				'titre'=>$nom_commune,
				'id_groupe'=>$id_gp_mot_communes,
				'type'=>'communes'
			));
	    }
	}
	
	//INSERER un ARTICLE dans la table SPIP_ARTICLES (bon id_rubrique)
	$champ_article = array(
		"id_rubrique" => $id_rubrique,
		"id_secteur" => $id_rubrique,
		"titre" => $commun["nom_offre"],
		"descriptif" => $commun["description_offre"],
		"texte" => $commun["description_offre"],
		"statut" => "publie"
		);
	
	//INSERER dans la bonne table SPIP_TOURINFRANCE
	$nom_table_tourinfrance = "spip_tourinfrance_" . $bordereau;
	
	$champ_tourinfrance_type = $commun;
	$champ_tourinfrance_type["id_flux"] = $id_flux;
	$champ_tourinfrance_type["extra"] = $extra_srlz;
	
	
	
	//Test l'EXISTANCE de l'offre, et si MISE A JOUR.
	$update = false;
	$exist = false;
	if ($req = sql_select("*", $nom_table_tourinfrance, "id_offre=" . sql_quote($id_offre))) {
	    while ($res = sql_fetch($req)) {
	        $exist = true;
	        $id_article = $res['id_article'];
	        
	        $update = comparer_date($datemaj, $res['datemaj']); //true si MAJ
	    }
	}
	
	/***********  IF !UPDATE  id_offre n'existe pas deja  ***************/
	if(!$exist){
	
		//MODIFICATION : Mise a jour statut de la rubrique si c'est le premier ajout.
		if (!sql_countsel('spip_articles', "id_rubrique=$id_rubrique")) {
			sql_updateq('spip_rubriques', array('statut'=>'publie'), "id_rubrique=$id_rubrique");
		}
	
		//INSERTION ARTICLE
		$id_article = sql_insertq("spip_articles", $champ_article);
		
		//INSERTION AUTEURS_ARTICLES (liaison)
		$liaison_auteur_article = sql_insertq("spip_auteurs_articles", array(
			'id_auteur'=>'1', 
			'id_article'=>$id_article
		));
		
		//INSERTION MOTS_ARTICLES (liaison)
		$liaison_mot_article = sql_insertq("spip_mots_articles", array(
			'id_mot'=>$id_mot, 
			'id_article'=>$id_article
		));
		
		$champ_tourinfrance_type["id_article"] = $id_article;
		
		//INSERTION TOURINFRANCE
		$id_tourinfrance = sql_insertq($nom_table_tourinfrance, $champ_tourinfrance_type);
		
		$retour = "<li>ADDED : <b>Article n°" . $id_article . "</b> : " . $id_offre . " - " . $nom_offre . "</li>";

	}
	
	/***********  IF UPDATE  id_offre existe deja  ***************/
	else if($update){
	
		//MODIFICATION ARTICLE
		sql_updateq("spip_articles", $champ_article, "id_article=" . intval($id_article));
		
		//MODIFICATION MOTS_ARTICLES (liaison)
		sql_updateq("spip_mots_articles", array('id_mot'=>$id_mot), "id_article='" . $id_article . "'");
		
		$champ_tourinfrance_type["id_article"] = $id_article;
		
		//MODIFICATION TOURINFRANCE
		sql_updateq($nom_table_tourinfrance, $champ_tourinfrance_type, "id_offre='" . $id_offre . "'");
		
		$retour = "<li>MODIFIED : <b>Article n°" . $id_article . "</b> : " . $id_offre . " - " . $nom_offre . "</li>";

	}

	return $retour;
	
}
function formater_date($date) {
	$date = str_replace('T', ' ', $date);
	return (substr($date,0,19));
}
function comparer_date($newdate, $exdate) {

	$search = array('-', ' ', ':');
	
 	$newdate = str_replace($search, '', $newdate);
 	$exdate = str_replace($search, '', $exdate);
 	
 	$up = false;
 	if($exdate!="00000000000000" && $newdate>$exdate){
 		$up = true;
 	}
 	
 	return $up;
}
?>