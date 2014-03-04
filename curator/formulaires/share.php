<? 
	function formulaires_share_charger_dist($titre,$nom_site,$url_site,$texte,$id_parent,$date,$auteur,$ps,$lang){
		$valeurs = array('titre'=>$titre,'nom_site'=>$nom_site,'url_site'=>$url_site,'texte'=>$texte,'id_parent'=>'1',$date=>'',$auteur=>'',$ps=>'',$lang=>'');
		
		return $valeurs;
	}

	function formulaires_share_verifier_dist(){
		
	}

	function formulaires_share_traiter_dist($titre,$nom_site,$url_site,$texte,$id_parent,$date,$auteur,$ps,$lang){
		$res = array();
		$titre = _request('titre');
		$nom_site = _request('nom_site');
		$id_parent = _request('id_parent');
		$url_site = _request('url_site');
		$texte = _request('texte');
		$date = _request('date');
		$auteur = _request('auteur');
		$ps = _request('ps');
		$lang = _request('lang');
		// creation de l'article
		$id_article = sql_insertq( 'spip_articles', array(
		                    'titre'=>$titre, 'id_rubrique'=>$id_parent,
		                    'texte'=>$texte, 'statut'=>'publie', 'id_secteur'=>$id_parent,
		                    'nom_site'=>$nom_site, 'url_site'=>$url_site, 'date'=>$date,'ps'=>$ps,'lang'=>$lang));
		// gestion auteur            
		sql_insertq( 'spip_auteurs_liens', array('id_auteur'=>$auteur, 'id_objet'=>$id_article, 'objet'=>'article')); 
		echo '<script language="JavaScript">self.close();</script>';
		exit;               
	}

 ?>
 
 