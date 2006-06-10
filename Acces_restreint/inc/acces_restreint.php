<?php
include_spip('base/db_mysql');
include_spip('base/abstract_sql');
include_spip('inc/rubriques');
	

	// ***********************************************************************************************
	// Fonctions de service
	// ***********************************************************************************************
	// liste des zones a laquelle appartient une rubrique
	function AccesRestreint_liste_zones_appartenance_rub($id_rubrique){
	  $liste_zones=array();
	  $id_rubrique = intval($id_rubrique); // securite
		while ($id_rubrique!=0){
	  	$s = spip_query("SELECT id_zone FROM spip_zones_rubriques WHERE id_rubrique=$id_rubrique");
	  	while ($row = spip_fetch_array($s)){
				$liste_zones[]=$row['id_zone'];
			}

 			// on remonte l'arbre hierarchique
			$s = spip_query("SELECT id_parent FROM spip_rubriques WHERE id_rubrique=$id_rubrique");
			if ($row = spip_fetch_array($s))
				$id_rubrique = $row['id_parent'];
			else {
				spip_log('acces_restreint : rubrique $id_rubrique introuvable');
				$id_rubrique = 0;
			}
		}
		return $liste_zones;
	}
	
	// test si une rubrique appartient a une zone directement ou par heritage
	function AccesRestreint_test_appartenance_zone_rub($id_zone,$id_rubrique){
	  $id_rubrique = intval($id_rubrique); // securite
	  $id_zone = intval($id_zone);
		while ($id_rubrique!=0){
	  	$s = spip_query("SELECT id_zone FROM spip_zones_rubriques WHERE id_rubrique=$id_rubrique AND id_zone=$id_zone");
	  	if ($row = spip_fetch_array($s))
				return true;

			// on remonte l'arbre hierarchique
			$s = spip_query("SELECT id_parent FROM spip_rubriques WHERE id_rubrique=$id_rubrique");
			if ($row = spip_fetch_array($s))
				$id_rubrique = $row['id_parent'];
			else {
				spip_log('acces_restreint : rubrique $id_rubrique introuvable');
				$id_rubrique = 0;
			}
		}
		return false;
	}
	

	// liste des rubriques contenues dans une zone, directement
	// pour savoir quelles rubriques on peut decocher
	// si id_zone = 0 : toutes les rub en acces restreint
	function AccesRestreint_liste_contenu_zone_rub_direct($id_zone){
	  $liste_rubriques=array();
	  $id_zone = intval($id_zone);
	  // liste des rubriques directement liees a la zone
	  $query = "SELECT id_rubrique FROM spip_zones_rubriques";
	  if ($id_zone) $query.=" WHERE id_zone=$id_zone";
  	$s = spip_query($query);
  	while ($row=spip_fetch_array($s))
  		$liste_rubriques[$row['id_rubrique']]=1;
		return array_keys($liste_rubriques);
	}
	// liste des rubriques contenues dans une zone, directement ou par heritage
	function AccesRestreint_liste_contenu_zone_rub($id_zone){
		$liste_rubriques=array();
		$liste_recherche=AccesRestreint_liste_contenu_zone_rub_direct($id_zone);
		// pour chaque rubrique de la zone, on cherche les sous rubriques qui heritent de la propriete
		while (count($liste_recherche)){
			// elle est fouillee donc on la passe un rubrique listee
			$id_rubrique = array_shift($liste_recherche);
			$liste_rubriques[] = $id_rubrique;
			// on liste toutes les sous rubriques
			$s = spip_query("SELECT id_rubrique FROM spip_rubriques WHERE id_parent=$id_rubrique");
			$liste = array();
			while ($row=spip_fetch_array($s))
				if (!in_array($row['id_rubrique'],$liste_rubriques))
					$liste[] = $row['id_rubrique'];
			$liste_recherche = array_merge($liste_recherche, $liste);
		}
		return $liste_rubriques;
	}

	// liste des zones a laquelle appartient un auteur
	function AccesRestreint_liste_zones_appartenance_auteur($id_auteur){
	  $liste_zones=array();
	  $id_auteur = intval($id_auteur); // securite
  	$s = spip_query("SELECT id_zone FROM spip_zones_auteurs WHERE id_auteur=$id_auteur ORDER BY id_zone");
  	while ($row = spip_fetch_array($s)){
			$liste_zones[]=$row['id_zone'];
		}
		return $liste_zones;
	}
	// test si un auteur appartient a une zone
	function AccesRestreint_test_appartenance_zone_auteur($id_zone,$id_auteur){
	  $id_auteur = intval($id_auteur); // securite
	  $id_zone = intval($id_zone);
  	$s = spip_query("SELECT id_zone FROM spip_zones_auteurs WHERE id_auteur=$id_auteur AND id_zone=$id_zone");
  	if ($row = spip_fetch_array($s))
			return true;
		return false;
	}

	// liste des auteurs contenus dans une zone
	function AccesRestreint_liste_contenu_zone_auteur($id_zone){
	  $liste_auteurs=array();
	  $id_zone = intval($id_zone);
	  // liste des rubriques directement liees a la zone
  	$s = spip_query("SELECT id_auteur FROM spip_zones_auteurs WHERE id_zone=$id_zone");
  	while ($row=spip_fetch_array($s))
  		$liste_auteurs[] = $row['id_auteur'];
		return $liste_auteurs;
	}

	function AccesRestreint_liste_rubriques_acces_proteges(){
		static $liste_acces_protege; // la calculer une seule fois par hit
		if (!is_array($liste_acces_protege)){
			$liste_acces_protege = AccesRestreint_liste_contenu_zone_rub(0);
		}
		return $liste_acces_protege;
	}
	function AccesRestreint_liste_rubriques_acces_libre(){
		static $liste_acces_libre; // la calculer une seule fois par hit
		if (!is_array($liste_acces_libre)){
			$liste_acces_libre = array();
			$liste_prot = join(",",AccesRestreint_liste_rubriques_acces_proteges());
			if ($liste_prot)
		  	$s = spip_query("SELECT id_rubrique FROM spip_rubriques WHERE id_rubrique NOT IN ($liste_prot)");
		  else
		  	$s = spip_query("SELECT id_rubrique FROM spip_rubriques");
	  	while ($row = spip_fetch_array($s)){
	  		$liste_acces_libre[] = $row['id_rubrique'];
			}
		}
		return $liste_acces_libre;
	}

	function AccesRestreint_liste_rubriques_accessibles(){
		global $auteur_session;
		$liste = AccesRestreint_liste_rubriques_acces_libre();
		if ($auteur_session['id_auteur']){
			$id_auteur = intval($auteur_session['id_auteur']);
			$s = spip_query("SELECT id_zone FROM spip_zones_auteurs WHERE id_auteur=$id_auteur");
			while ($row = spip_fetch_array($s)){
				$liste = array_merge($liste,AccesRestreint_liste_contenu_zone_rub($row['id_zone']));
			}
		}
		return $liste;
	}
	// fonctions de filtrage rubrique
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_rubriques_exclues(){
		static $liste_rub_exclues;
		if (!is_array($liste_rub_exclues)){
			$liste_rub_exclues = array();
			global $auteur_session;
			$liste_rub_exclues = AccesRestreint_liste_rubriques_acces_proteges();
			if (isset($auteur_session['id_auteur'])){
				$id_auteur = intval($auteur_session['id_auteur']);
				$s = spip_query("SELECT id_zone FROM spip_zones_auteurs WHERE id_auteur=$id_auteur");
				while ($row = spip_fetch_array($s)){
					$liste_rub_exclues = array_diff($liste_rub_exclues,AccesRestreint_liste_contenu_zone_rub($row['id_zone']));
				}
			}
		}
		return $liste_rub_exclues;
	}
	function AccesRestreint_rubriques_accessibles_where($primary){
		$liste = AccesRestreint_liste_rubriques_exclues();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage article
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_articles_exclus(){
		static $liste_art_exclus;
		if (!is_array($liste_art_exclus)){
			$liste_art_exclus = array();
			$liste_rub = AccesRestreint_liste_rubriques_exclues();
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			$s = spip_query("SELECT id_article FROM spip_articles WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_art_exclus[] = $row['id_article'];
			}
		}
		return $liste_art_exclus;
	}
	function AccesRestreint_articles_accessibles_where($primary){
		$liste = AccesRestreint_liste_articles_exclus();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage breves
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_breves_exclues(){
		static $liste_breves_exclues;
		if (!is_array($liste_breves_exclues)){
			$liste_breves_exclues = array();
			$liste_rub = AccesRestreint_liste_rubriques_exclues();
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			$s = spip_query("SELECT id_breve FROM spip_breves WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_breves_exclues[] = $row['id_breve'];
			}
		}
		return $liste_breves_exclues;
	}
	function AccesRestreint_breves_accessibles_where($primary){
		$liste = AccesRestreint_liste_breves_exclues();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage forums
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_forum_exclus(){
		static $liste_forum_exclus;
		if (!is_array($liste_forum_exclus)){
			$liste_forum_exclus = array();
			// rattaches aux rubriques
			$liste_rub = AccesRestreint_liste_rubriques_exclues();
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			// rattaches aux articles
			$liste_art = AccesRestreint_liste_articles_exclus();
			$where .= " OR " . calcul_mysql_in('id_article', join(",",$liste_art));
			// rattaches aux breves
			$liste_breves = AccesRestreint_liste_breves_exclues();
			$where .= " OR " . calcul_mysql_in('id_breve', join(",",$liste_art));

			$s = spip_query("SELECT id_forum FROM spip_forum WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_forum_exclus[] = $row['id_forum'];
			}
		}
		return $liste_forum_exclus;
	}
	function AccesRestreint_forum_accessibles_where($primary){
		$liste = AccesRestreint_liste_forum_exclus();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage signatures
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_signatures_exclues(){
		static $liste_signatures_exclues;
		if (!is_array($liste_signatures_exclues)){
			$liste_signatures_exclues = array();
			// rattaches aux articles
			$liste_art = AccesRestreint_liste_articles_exclus();
			$where = calcul_mysql_in('id_article', join(",",$liste_art));
			$s = spip_query("SELECT id_signature FROM spip_signatures WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_signatures_exclues[] = $row['id_signature'];
			}
		}
		return $liste_signatures_exclues;
	}
	function AccesRestreint_signatures_accessibles_where($primary){
		$liste = AccesRestreint_liste_signatures_exclues();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage documents
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_documents_exclus(){
		static $liste_documents_exclus;
		if (!is_array($liste_documents_exclus)){
			$liste_documents_exclus = array();
			// rattaches aux articles
			$liste_art = AccesRestreint_liste_articles_exclus();
			$where = calcul_mysql_in('id_article', join(",",$liste_art));
			$s = spip_query("SELECT id_document FROM spip_documents_articles WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$row['id_document']]=1;
			}
			// rattaches aux rubriques
			$liste_rub = AccesRestreint_liste_rubriques_exclues();
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			$s = spip_query("SELECT id_document FROM spip_documents_rubriques WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$row['id_document']]=1;
			}
			// rattaches aux breves
			$liste_breves = AccesRestreint_liste_breves_exclues();
			$where = calcul_mysql_in('id_breve', join(",",$liste_breves));
			$s = spip_query("SELECT id_document FROM spip_documents_breves WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$row['id_document']]=1;
			}
			// rattaches aux syndic
			$liste_syn = AccesRestreint_liste_syndic_exclus();
			$where = calcul_mysql_in('id_syndic', join(",",$liste_syn));
			$s = spip_query("SELECT id_document FROM spip_documents_syndic WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$row['id_document']]=1;
			}
			$liste_documents_exclus = array_keys($liste_documents_exclus);
		}
		return $liste_documents_exclus;
	}
	function AccesRestreint_documents_accessibles_where($primary){
		$liste = AccesRestreint_liste_documents_exclus();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage syndic
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_syndic_exclus(){
		static $liste_syndic_exclus;
		if (!is_array($liste_syndic_exclus)){
			$liste_syndic_exclus = array();
			$liste_rub = AccesRestreint_liste_rubriques_exclues();
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			$s = spip_query("SELECT id_syndic FROM spip_syndic WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_syndic_exclus[] = $row['id_syndic'];
			}
		}
		return $liste_syndic_exclus;
	}
	function AccesRestreint_syndic_accessibles_where($primary){
		$liste = AccesRestreint_liste_syndic_exclus();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage syndic_articles
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function AccesRestreint_liste_syndic_articles_exclus(){
		static $liste_syndic_articles_exclus;
		if (!is_array($liste_syndic_articles_exclus)){
			$liste_syndic_articles_exclus = array();
			$liste_syn = AccesRestreint_liste_syndic_exclus();
			$where = calcul_mysql_in('id_syndic', join(",",$liste_syn));
			$s = spip_query("SELECT id_syndic_article FROM spip_syndic_articles WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_syndic_articles_exclus[] = $row['id_syndic_article'];
			}
		}
		return $liste_syndic_articles_exclus;
	}
	function AccesRestreint_syndic_articles_accessibles_where($primary){
		$liste = AccesRestreint_liste_syndic_articles_exclus();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// filtre de securisation des squelettes
	// utilise avec [(#REM|AccesRestreint_securise_squelette)]
	// evite divulgation d'info si plugin desactive
	// par erreur fatale
	function AccesRestreint_securise_squelette($letexte){
		return "";
	}

?>