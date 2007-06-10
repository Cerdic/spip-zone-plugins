<?php
//spip_log('inclusion accesgroupes_boucles = OK!');

// fonctions utilisées pour la surcharge des BOUCLES
//		merci à Cedric cedric.morin@yterium.com pour le code initial  (plugin acces_restreint)
include_spip('base/db_mysql');
include_spip('base/abstract_sql');
include_spip('inc/rubriques');


// inclure le fichier de redéfinitions des BOUCLES
include_spip('inc/accesgroupes_boucles');


// fct pour construire et renvoyer le tableau des rubriques à accès restreint dans la partie PUBLIQUE
// 		 boucler dans l'arborescence des rubriques en commençant par la racine (secteurs en 1ers)
// 		 pour remplir le tableau des rubriques restreintes en utilisant le principe d'héritage des restrictions
function accesgroupes_liste_rubriques_restreintes($id_parent = 0){
	//echo '<br>debut accesgroupes_liste_rubriques_restreintes';
	// Coyote - 20070406 - Vérif origine de l'accès
	$accesprive =((strpos($_SERVER['PHP_SELF'],"/ecrire/")===false)?"public":"prive");
	$id_parent = intval($id_parent); // securite
	static $Trub_restreintes; // nécessaire pour que la suite ne soit éxécutée qu'une fois par hit (même si on à n BOUCLES)
	if (!is_array($Trub_restreintes)) {
		$Trub_restreintes = array();
		// attaquer à la racine pour mettre tout de suite les éventuels secteurs restreints dans le tableau ce qui accélèrera la suite
		$sql1 = "SELECT id_rubrique, id_parent, id_secteur FROM spip_rubriques";
		$result1 = spip_query($sql1);
		//echo '<br>mysql_error $sql1 = '.mysql_error();
		while ($row1 = spip_fetch_array($result1)) {
			$rub_ec = $row1['id_rubrique'];
			$parent_ec = $row1['id_parent'];
			$sect_ec = $row1['id_secteur'];
			// si le parent ou le secteur est déja dans le tableau : vu le principe d'héritage pas la peine d'aller plus loin :)
			/*	if (in_array($parent_ec, $Trub_restreintes) OR in_array($sect_ec, $Trub_restreintes)) {
					$Trub_restreintes[] = $rub_ec;
				}
				// sinon c'est plus couteux : il faut faire le test complet de la restriction de la rubrique pour espace public
				else {  */
			if (accesgroupes_verif_acces($rub_ec, $accesprive) == 1 OR accesgroupes_verif_acces($rub_ec, $accesprive) == 2) {
				$Trub_restreintes[] = $rub_ec;
			}
			//	 }
		}
	}
	return $Trub_restreintes;
}

//fct pour renvoyer le tableau des articles appartenant aux rubriques à accès restreint
function accesgroupes_liste_articles_restreints() {
	static $Tart_restreints; // ainsi la suite ne sera effectuée qu'une fois par hit
	if (!is_array($Tart_restreints)) {
		$Tart_restreints = array();
		$Trub_rest = accesgroupes_liste_rubriques_restreintes();
		$result3 = spip_query("SELECT id_article, id_rubrique FROM spip_articles");
		while ($row3 = spip_fetch_array($result3)) {
			if (in_array($row3['id_rubrique'], $Trub_rest)) {
				$Tart_restreints[] = $row3['id_article'];
			}
		}
	}
	return $Tart_restreints;
}

// fct pour renvoyer le tableau des brèves appartenant à des rubriques à accès restreint
function accesgroupes_liste_breves_restreintes() {
	static $Tbrev_restreintes;
	if (!is_array($Tbrev_restreintes)) {
		$Tbrev_restreintes = array();
		$Trub_rest = accesgroupes_liste_rubriques_restreintes();
		$result4 = spip_query("SELECT id_breve, id_rubrique FROM spip_breves");
		while ($row4 = spip_fetch_array($result4)) {
			if (in_array($row4['id_rubrique'], $Trub_rest)) {
				$Tbrev_restreintes[] = $row4['id_breve'];
			}
		}
	}
	return $Tbrev_restreintes;
}

// fct pour renvoyer le tableau des forums liés à un élément appartenant à une rubrique restreinte
// 		 (puissant le Cedric pour maximiser ce qui est fait par la requete mySQL !)
function accesgroupes_liste_forums_restreints() {
	static $Tforum_restreints;
	if (!is_array($Tforum_restreints)) {
		$Tforum_restreints = array();
		// rattaches aux rubriques
		$liste_rub = accesgroupes_liste_rubriques_restreintes();
		$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
		// rattaches aux articles
		$liste_art = accesgroupes_liste_articles_restreints();
		$where .= " OR " . calcul_mysql_in('id_article', join(",",$liste_art));
		// rattaches aux breves
		$liste_breves = accesgroupes_liste_breves_restreintes();
		$where .= " OR " . calcul_mysql_in('id_breve', join(",",$liste_art));
		
		$s = spip_query("SELECT id_forum FROM spip_forum WHERE $where");
		while ($row = spip_fetch_array($s)){
			$Tforum_restreints[] = $row['id_forum'];
		}
	}
	return $Tforum_restreints;
}

// fct pour renvoyer le tableau des signatures des articles appartenant à une rubrique restreinte
function accesgroupes_liste_signatures_restreintes() {
	static $Tsignatures_restreintes;
	if (!is_array($Tsignatures_restreintes)){
		$Tsignatures_restreintes = array();
		// rattaches aux articles
		$liste_art = accesgroupes_liste_articles_restreints();
		$where = calcul_mysql_in('id_article', join(",",$liste_art));
		$s = spip_query("SELECT id_signature FROM spip_signatures WHERE $where");
		while ($row = spip_fetch_array($s)){
			$Tsignatures_restreintes[] = $row['id_signature'];
		}
	}
	return $Tsignatures_restreintes;
}

// fct pour renvoyer le tableau des documents liés à un élément appartenant à une rubrique restreinte
// 		 subtil la méthode de stocker les valeurs comme clés d'un tableau pour ne pas doublonner les valeurs
//		 avec le petit array_keys() qui va bien pour récupérer le tableau de valeurs à la fin !
function accesgroupes_liste_documents_restreints() {
	static $Tdocuments_restreints;
	if (!is_array($Tdocuments_restreints)){
		$Tdocuments_restreints = array();
		// rattaches aux articles
		$liste_art = accesgroupes_liste_articles_restreints();
		$where = calcul_mysql_in('id_article', join(",",$liste_art));
		$s = spip_query("SELECT id_document FROM spip_documents_articles WHERE $where");
		while ($row = spip_fetch_array($s)){
			$Tdocuments_restreints[$row['id_document']]=1;
		}
		// rattaches aux rubriques
		$liste_rub = accesgroupes_liste_rubriques_restreintes();
		$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
		$s = spip_query("SELECT id_document FROM spip_documents_rubriques WHERE $where");
		while ($row = spip_fetch_array($s)){
			$Tdocuments_restreints[$row['id_document']]=1;
		}
		// rattaches aux breves
		$liste_breves = accesgroupes_liste_breves_restreintes();
		$where = calcul_mysql_in('id_breve', join(",",$liste_breves));
		$s = spip_query("SELECT id_document FROM spip_documents_breves WHERE $where");
		while ($row = spip_fetch_array($s)){
			$Tdocuments_restreints[$row['id_document']]=1;
		}
		// rattaches aux syndic
		/*        			$liste_syn = accesgroupes_liste_syndics_restreints();
					$where = calcul_mysql_in('id_syndic', join(",",$liste_syn));
					$s = spip_query("SELECT id_document FROM spip_documents_syndic WHERE $where");
					while ($row = spip_fetch_array($s)){
								$Tdocuments_restreints[$row['id_document']]=1;
					}
*/							
		$Tdocuments_restreints = array_keys($Tdocuments_restreints);
	}
	return $Tdocuments_restreints;	
}

// fct pour renvoyer le tableau des rubriques syndiquées appartenants à une rubrique restreinte
function accesgroupes_liste_syndics_restreints() {
	static $Tsyndics_restreints;
	if (!is_array($Tsyndics_restreints)){
		$Tsyndics_restreints = array();
		$liste_rub = accesgroupes_liste_rubriques_restreintes();
		$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
		$s = spip_query("SELECT id_syndic FROM spip_syndic WHERE $where");
		while ($row = spip_fetch_array($s)){
			$Tsyndics_restreints[] = $row['id_syndic'];
		}
	}
	return $Tsyndics_restreints;
}	

// fct pour renvoyer le tableau des articles syndiqués appartenant à une rubrique restreinte
function accesgroupes_liste_syndic_articles_restreints(){
	static $Tsyndic_articles_restreints;
	if (!is_array($Tsyndic_articles_restreints)){
		$Tsyndic_articles_restreints = array();
		$liste_syn = accesgroupes_liste_syndics_restreints();
		$where = calcul_mysql_in('id_syndic', join(",",$liste_syn));
		$s = spip_query("SELECT id_syndic_article FROM spip_syndic_articles WHERE $where");
		while ($row = spip_fetch_array($s)){
			$Tsyndic_articles_restreints[] = $row['id_syndic_article'];
		}
	}
	return $Tsyndic_articles_restreints;
}


// fonctions de filtrage : 	plus performant à priori : liste des éléments restreints uniquement
// -> condition NOT IN

// filtrage RUBRIQUES
function accesgroupes_rubriques_accessibles_where($primary){
	$liste = accesgroupes_liste_rubriques_restreintes();
	return calcul_mysql_in($primary, join(",",$liste),"NOT");
}

// filtrage ARTICLES
function accesgroupes_articles_accessibles_where($primary){
	$liste = accesgroupes_liste_articles_restreints();
	return calcul_mysql_in($primary, join(",",$liste),"NOT");
}
/*	*/
// filtrage BREVES
function accesgroupes_breves_accessibles_where($primary){
	$liste = accesgroupes_liste_breves_restreintes();
	return calcul_mysql_in($primary, join(",",$liste),"NOT");
}

// filtrage FORUMS
function accesgroupes_forums_accessibles_where($primary){
	$liste = accesgroupes_liste_forums_restreints();
	return calcul_mysql_in($primary, join(",",$liste),"NOT");
}

// filtrage SIGNATURES
function accesgroupes_signatures_accessibles_where($primary){
	$liste = accesgroupes_liste_signatures_restreintes();
	return calcul_mysql_in($primary, join(",",$liste),"NOT");
}

// filtrage DOCUMENTS
function accesgroupes_documents_accessibles_where($primary){
	$liste = accesgroupes_liste_documents_restreints();
	return calcul_mysql_in($primary, join(",",$liste),"NOT");
}

// filtrage SYNDICS
function accesgroupes_syndics_accessibles_where($primary){
	$liste = accesgroupes_liste_syndics_restreints();
	return calcul_mysql_in($primary, join(",",$liste),"NOT");
}

// filtrage ARTICLES SYNDIQUES
function accesgroupes_syndic_articles_accessibles_where($primary){
	$liste = accesgroupes_liste_syndic_articles_restreints();
	return calcul_mysql_in($primary, join(",",$liste),"NOT");
}


// filtre de securisation des squelettes
// utilise avec [(#REM|accesgroupes_securise_squelette)]
// evite divulgation d'info si plugin desactive
// par erreur fatale
function accesgroupes_securise_squelette($letexte){
	return "!!! plantage du plugin accesgroupes !!!";
}


// filtre de test pour savoir si l'acces à un article est restreint
function accesgroupes_article_restreint($id_article) {
	if (@in_array($id_article, accesgroupes_liste_articles_restreints())) {
		return true; //; <INCLURE{fond=accesgroupes_login}{skel=#SQUELETTE}> '#LOGIN_PUBLIC '
	}
	else {
		return false;
	}
}

// filtre de test pour savoir si l'acces à une rubrique est restreint
function accesgroupes_rubrique_restreinte($id_rubrique) {
	if (@in_array($id_rubrique, accesgroupes_liste_rubriques_restreintes())) {
		return true; //;
	}
	else {
		return false;
	}
}

// filtre de test pour savoir si l'accès à une brève est restreint
function accesgroupes_breve_restreinte($id_breve) {
	if (@in_array($id_breve, accesgroupes_liste_breves_restreintes())) {
		return ' #LOGIN_PUBLIC '; //true;
	}
	else {
		return false;
	}
}

//fct pour renvoyer le tableau des evenements appartenant aux rubriques à accès restreint
function accesgroupes_liste_evenements_restreints() {

        static $Tevenements_restreints;
        if (!is_array($Tevenements_restreints)) {
                $Tevenements_restreints = array();
                // rattaches aux articles
                $liste_art = accesgroupes_liste_articles_restreints();
                $where = calcul_mysql_in('id_article', join(",",$liste_art));

                $s = spip_query("SELECT id_evenement FROM spip_evenements WHERE $where");
                while ($row = spip_fetch_array($s)){
                        $Tevenements_restreints[] = $row['id_evenement'];
                }
        }
        return $Tevenements_restreints;
}

// filtrage EVENEMENTS
function accesgroupes_evenements_accessibles_where($primary){
        $liste = accesgroupes_liste_evenements_restreints();
        return calcul_mysql_in($primary, join(",",$liste),"NOT");
}
?>