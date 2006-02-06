<?php
include_ecrire('inc_db_mysql');
include_ecrire('inc_abstract_sql');
include_ecrire('inc_rubriques');

Class AccesRestreint {
	/* public static */
	function ajouterBoutons($boutons_admin) {
		// si on est admin
		if ($GLOBALS['connect_statut'] == "0minirezo" && $GLOBALS["connect_toutes_rubriques"]
		AND $GLOBALS["options"]=="avancees") {
		  // on voit le bouton dans la barre "naviguer"
		  $boutons_admin['configuration']->sousmenu['acces_restreint']= new Bouton(
			"../"._DIR_PLUGIN_ACCES_RESTREINT."/zones-acces-24.png",  // icone
			_T('accesrestreint:icone_menu_config')	// titre
			);
		}
		return $boutons_admin;
	}

	/* public static */
	function ajouterOnglets($flux) {
		$rubrique = $flux['args'];
		return $flux;
	}

  function cree_zone(){
    $titre = addslashes($_POST['titre']);
    $descriptif = addslashes($_POST['descriptif']);
		if (strlen($titre)>0){
			$id_zone = spip_abstract_insert('spip_zones', "(titre,descriptif,maj)", "('$titre','$descriptif',NOW())");
			return $id_zone;
		} 
		return 0;
	}
  function supprimer_zone(){
  	$id_zone = intval($_GET['supp_zone']);
  	if ($id_zone){
			spip_query("DELETE FROM spip_zones WHERE id_zone='$id_zone'");
			spip_query("DELETE FROM spip_zones_rubriques WHERE id_zone='$id_zone'");
			spip_query("DELETE FROM spip_zones_auteurs WHERE id_zone='$id_zone'");
		}
		return 0;
	}

  function enregistrer_zone(){
    $titre = addslashes($_POST['titre']);
    $descriptif = addslashes($_POST['descriptif']);
    $id_zone = intval($_GET['id_zone']);
		if (strlen($titre)>0 && $id_zone){
			spip_query("UPDATE spip_zones SET titre='$titre', descriptif='$descriptif' WHERE id_zone=$id_zone");
			// suppression de tous les liens zone-rubriques
			spip_query("DELETE FROM spip_zones_rubriques WHERE id_zone='$id_zone'");
			if (is_array($_POST['restrict'])){
				foreach(array_keys($_POST['restrict']) as $id){
					$id = intval($id);
					spip_abstract_insert('spip_zones_rubriques', "(id_zone,id_rubrique)", "('$id_zone','$id')");
				}
			}
		} 
		return 0;
	}
	

	// Fonction de presentation
	function sous_menu_rubriques($id_zone, $root, $niv, &$data, &$enfants, &$liste_rub_dir, &$liste_rub, $type) {
		global $browser_name, $browser_version;
		static $decalage_secteur;
		global $couleur_claire;
	
		// Si on a demande l'exclusion ne pas descendre dans la rubrique courante
		if ($exclus > 0
		AND $root == $exclus) return '';
	
		// en fonction du niveau faire un affichage plus ou moins kikoo
		
		$class = "";
		if ($restric = in_array($root,$liste_rub_dir))	$class = " class='selec_rub'";
	
		// le style en fonction de la profondeur
		list($style,$espace) = style_menu_rubriques($niv);
		$style = "style='padding-left:".($niv*1)."em;";
		if ($restrictherit = in_array($root,$liste_rub))
			$style .= "background-color: $couleur_claire;";
		$style.= "'";

		// creer l'<option> pour la rubrique $root
		if (isset($data[$root])) # pas de racine sauf pour les rubriques
		{
			$r .= "<input type='checkbox' name='restrict[$root]' value='O' id='label_$root'";
			$r .= ($restric!==FALSE)?" checked='checked'":"";
			$r .= " />\n <label for='label_$root' >$espace".$data[$root]."</label>";
		}
			
		// et le sous-menu pour ses enfants
		$sous = '';
		if ($enfants[$root])
			foreach ($enfants[$root] as $sousrub)
				$sous .= AccesRestreint::sous_menu_rubriques($id_rubrique, $sousrub,
					$niv+1, $data, $enfants, $liste_rub_dir, $liste_rub, $type);

		if (strlen($sous)>0){
			$visible = (($restrictherit) OR (strpos($sous,"checked='checked'")!==FALSE));
			if ($visible)
				$r = bouton_block_visible("rub$root") . $r;
			else
				$r = bouton_block_invisible("rub$root") . $r;
			$r = "<div $class$style>" . $r;
			$r .= "</div>\n";

			if ($visible)
				$r .= debut_block_visible("rub$root");
			else
				$r .= debut_block_invisible("rub$root");
			$r .= $sous;
			$r .= fin_block();
		}
		else{
			$r = "<div $class$style>" . $r;
			$r .= "</div>\n";
		}
		
		// et voila le travail
		return $r;
	}

	// Le selecteur de rubriques en mode classique (menu)
	function selecteur_rubrique_html($id_zone) {
		$type = 'rubrique';
		$data = array();
		if ($type == 'rubrique')
			$data[0] = _T('info_racine_site');
		if ($type == 'auteur')
			$data[0] = '&nbsp;'; # premier choix = neant (rubriques restreintes)
	
		//
		// creer une structure contenant toute l'arborescence
		//
	
		# oblige les breves a resider a la racine
		if ($type == 'breve') $where = 'WHERE id_parent=0';
	
		$q = spip_query("SELECT
		id_rubrique, id_parent, titre, statut, lang, langue_choisie
		FROM spip_rubriques
		$where
		ORDER BY 0+titre,titre");
		while ($r = spip_fetch_array($q)) {
			// titre largeur maxi a 50
			$titre = couper(supprimer_tags(typo(extraire_multi(
				$r['titre']
			)))." ", 50);
			if ($GLOBALS['meta']['multi_rubriques'] == 'oui'
			AND ($r['langue_choisie'] == "oui" OR $r['id_parent'] == 0))
				$titre .= ' ['.traduire_nom_langue($r['lang']).']';
			$data[$r['id_rubrique']] = $titre;
			$enfants[$r['id_parent']][] = $r['id_rubrique'];
			if ($id_rubrique == $r['id_rubrique']) $id_parent = $r['id_parent'];
		}

		$liste_rub_dir = AccesRestreint::liste_contenu_zone_rub_direct($id_zone);
		$liste_rub = AccesRestreint::liste_contenu_zone_rub($id_zone);
	
		$r = "<div style='font-size: 90%; width: 99%;"
		."font-face: verdana,arial,helvetica,sans-serif;'>\n";
	
		$r .= AccesRestreint::sous_menu_rubriques($id_zone,0,
			0,$data,$enfants,$liste_rub_dir, $liste_rub, $type);
	
		$r .= "</div>\n";
	
		return $r;
	}

	/*
	 * Affiche la liste des zones d'acces associee a l'objet
	 * specifie, plus le formulaire d'ajout de mot-cle
	 */
	
	function formulaire_zones($table, $id_objet, $nouv_zone, $supp_zone, $flag_editable, $retour) {
	  global $connect_statut, $connect_toutes_rubriques, $options;
		global $spip_lang_rtl, $spip_lang_right;
	
		$retour = urlencode($retour);
		$select_groupe = $GLOBALS['select_groupe'];
	
		if ($table == 'rubriques') {
			$id_table = 'id_rubrique';
			$objet = 'rubrique';
			$url_base = "naviguer";
		}
		else if ($table == 'auteurs') {
			$id_table = 'id_auteur';
			$objet = 'auteur';
			$url_base = "auteurs_edit";
		}
	
		list($nombre_zones) = spip_fetch_array(spip_query("SELECT COUNT(*) FROM spip_zones AS zones, spip_zones_$table AS lien WHERE lien.$id_table=$id_objet AND zones.id_zone=lien.id_zone"));
	
		echo "<a name='zones'></a>";
		if ($flag_editable){
			if ($nouv_zone||$supp_zone)
				$bouton = bouton_block_visible("leszones");
			else
				$bouton =  bouton_block_invisible("leszones");
		}
		debut_cadre_enfonce("../"._DIR_PLUGIN_ACCES_RESTREINT."/zones-acces-24.png", false, "", $bouton._T('accesrestreint:titre_zones_acces'));
	
		//////////////////////////////////////////////////////
		// Recherche de zones d'acces
		//
	
		if ($nouv_zone)
			$nouveaux_zones = array($nouv_zone);
	
		//////////////////////////////////////////////////////
		// Appliquer les modifications sur les zones d'acces
		//
		if ($nouveaux_zones && $flag_editable) {
			while ((list(,$nouv_zone) = each($nouveaux_zones)) AND $nouv_zone!='x') {
				$query = "SELECT * FROM spip_zones_$table WHERE id_zone=$nouv_zone AND $id_table=$id_objet";
				$result = spip_query($query);
				if (!spip_num_rows($result)) {
					$query = "INSERT INTO spip_zones_$table (id_zone,$id_table) VALUES ($nouv_zone, $id_objet)";
					$result = spip_query($query);
				}
			}
			$reindexer = true;
		}

		if ($supp_zone && $flag_editable) {
			if ($supp_zone == -1)
				$zones_supp = "";
			else
				$zones_supp = " AND id_zone=$supp_zone";
			$query = "DELETE FROM spip_zones_$table WHERE $id_table=$id_objet $zones_supp";

			$result = spip_query($query);
			$reindexer = true;
		}
		
		//
		// Afficher les zones d'acces
		//
	
		unset($les_zones);
	
		$query = "SELECT zones.* FROM spip_zones AS zones, spip_zones_$table AS lien WHERE lien.$id_table=$id_objet AND zones.id_zone=lien.id_zone ORDER BY zones.titre";
		$result = spip_query($query);
	
		if (spip_num_rows($result) > 0) {
			echo "<div class='liste'>";
			echo "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>";
		
			$ifond=0;
				
			$tableau= '';
			while ($row = spip_fetch_array($result)) {
				$vals = '';
			
				$id_zone = $row['id_zone'];
				$titre_zone = $row['titre'];
				$descriptif_zone = $row['descriptif'];

				if ($ifond==0){
					$ifond=1;
					$couleur="#FFFFFF";
				}else{
					$ifond=0;
					$couleur="#EDF3FE";
				}
		
				$url = "href='" . generer_url_ecrire('acces_restreint_edit', "id_zone=$id_zone&retour=".rawurlencode(generer_url_ecrire($url_base, "$id_table=$id_objet#zones"))) . "'";
	
				$vals[] = "<a $url>" . http_img_pack("../"._DIR_PLUGIN_ACCES_RESTREINT.'/restreint-16.png', "", "width='16' height='16' border='0'") ."</a>";

				$s = "<a $url>".typo($titre_zone)."</a>";
				$vals[] = $s;
		
				$vals[] = "";
		
				if ($flag_editable){
				  $s = "<a href='" . generer_url_ecrire($url_base, "$id_table=$id_objet&supp_zone=$id_zone#zones") . "'>"._T('accesrestreint:info_retirer_zone')."&nbsp;" . http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' align='middle'") ."</a>";
					$vals[] = $s;
				}
				else $vals[]= "";

				$tableau[] = $vals;
		
				$les_zones[] = $id_zone;
			}
		
			$largeurs = array('25', '', '', '');
			$styles = array('arial11', 'arial2', 'arial2', 'arial1');
			afficher_liste($largeurs, $tableau, $styles);
		
			echo "</table></div>";
		}
	
		if ($les_zones) {
			$nombre_zones_associes = count($les_zones);
			$les_zones = join($les_zones, ",");
		} else {
			$les_zones = "0";
		}

		//
		// Afficher le formulaire d'ajout de zones d'acces
		//
		if ($flag_editable) {
			if ($nouveaux_zones.$supp_zone)
				echo debut_block_visible("leszones");
			/*else if ($nb_groupes > 0) {
				echo debut_block_visible("leszones");
				// vilain hack pour redresser un triangle
				$couche_a_redresser = $GLOBALS['numero_block']['leszones'];
				if ($GLOBALS['browser_layer']) echo http_script("
					triangle = findObj('triangle' + $couche_a_redresser);
					if (triangle) triangle.src = '" . _DIR_IMG_PACK . "deplierbas$spip_lang_rtl.gif';");
			}*/
			else
				echo debut_block_invisible("leszones");
	
			if ($nombre_zones_associes > 3) {
				echo "<div align='right' class='arial1'>";
				echo "<a href='", generer_url_ecrire($url_base, "$id_table=$id_objet&supp_zone=-1#zones"), "'>",_T('accesrestreint:info_retirer_zones'),"</a>";
				echo "</div><br />\n";
			}
	
			// il faudrait rajouter STYLE='margin:1px;' qq part
	
			$form_zone = generer_url_post_ecrire($url_base,"$id_table=$id_objet", '', "#zones");
	
			if ($table == 'rubriques') $form_zone .= "<input type='hidden' name='id_rubrique' value='$id_objet' />";
	
			$message_ajouter_zone = "<span class='verdana1'><B>"._T('accesrestreint:titre_ajouter_zone')."</B></span> &nbsp;\n";
	
			echo "<table border='0' width='100%' style='text-align: $spip_lang_right'>";
	
					
			$query = "SELECT * FROM spip_zones ";
			if ($les_zones) $query .= "WHERE id_zone NOT IN ($les_zones) ";
			$query .= "ORDER BY titre";

			$result = spip_query($query);

			if (spip_num_rows($result) > 0) {
				echo "\n<tr>";
				echo $form_zone;
				echo "\n<td>";
				echo $message_ajouter_zone;
				$message_ajouter_zone = "";
				echo "</td>\n<td>";

				echo "<select name='nouv_zone' size='1' onChange=\"setvisibility('valider_groupe_$id_groupe', 'visible');\" style='width: 180px; ' class='fondl'>";

				echo "\n<option value='x' style='font-variant: small-caps;'>"._T("accesrestreint:selectionner_une_zone")."</option>";
				while($row = spip_fetch_array($result)) {
					$id_zone = $row['id_zone'];
					$titre_zone = $row['titre'];
					$texte_option = entites_html(textebrut(typo($titre_zone)));
					echo "\n<option value=\"$id_zone\">";
					echo "&nbsp;&nbsp;&nbsp;";
					echo "$texte_option</option>";
				}
				echo "</select>";
				echo "</td>\n<td>";
				echo "<span class='visible_au_chargement' id='valider_groupe_$id_groupe'>";
				echo " &nbsp; <input type='submit' name='Choisir' value='"._T('bouton_choisir')."' class='fondo'>";
				echo "</span>";
				echo "</td></form>";
				echo "</tr>";
			}
			
			/*if ($connect_statut == '0minirezo' AND $flag_editable AND $options == "avancees" AND $connect_toutes_rubriques) {
				echo "<tr><td></td><td colspan='2'>";
				echo "<div style='width: 200px;'>";
				icone_horizontale(_T('accesrestreint:icone_creer_zone'), generer_url_ecrire("mots_edit","new=oui&ajouter_id_article=$id_objet&table=$table&id_table=$id_table&redirect=$retour"), "zones-acces-24.png", "creer.gif");
				echo "</div> ";
				echo "</td></tr>";
			}*/
			
			echo "</table>";
			echo fin_block();
		}
	
		fin_cadre_enfonce();
	}
	
	// ***********************************************************************************************
	// Fonctions de service
	// ***********************************************************************************************
	// liste des zones a laquelle appartient une rubrique
	function liste_zones_appartenance_rub($id_rubrique){
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
	function test_appartenance_zone_rub($id_zone,$id_rubrique){
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
	function liste_contenu_zone_rub_direct($id_zone){
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
	function liste_contenu_zone_rub($id_zone){
		$liste_rubriques=array();
		$liste_recherche=AccesRestreint::liste_contenu_zone_rub_direct($id_zone);
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
	function liste_zones_appartenance_auteur($id_auteur){
	  $liste_zones=array();
	  $id_auteur = intval($id_auteur); // securite
  	$s = spip_query("SELECT id_zone FROM spip_zones_auteurs WHERE id_auteur=$id_auteur");
  	while ($row = spip_fetch_array($s)){
			$liste_zones[]=$row['id_zone'];
		}
		return $liste_zones;
	}
	// test si un auteur appartient a une zone
	function test_appartenance_zone_auteur($id_zone,$id_auteur){
	  $id_auteur = intval($id_auteur); // securite
	  $id_zone = intval($id_zone);
  	$s = spip_query("SELECT id_zone FROM spip_zones_auteurs WHERE id_auteur=$id_auteur AND id_zone=$id_zone");
  	if ($row = spip_fetch_array($s))
			return true;
		return false;
	}

	// liste des auteurs contenus dans une zone
	function liste_contenu_zone_auteur($id_zone){
	  $liste_auteurs=array();
	  $id_zone = intval($id_zone);
	  // liste des rubriques directement liees a la zone
  	$s = spip_query("SELECT id_auteur FROM spip_zones_auteurs WHERE id_zone=$id_zone");
  	while ($row=spip_fetch_array($s))
  		$liste_auteurs[] = $row['id_auteur'];
		return $liste_auteurs;
	}

	function liste_rubriques_acces_proteges(){
		static $liste_acces_protege; // la calculer une seule fois par hit
		if (!is_array($liste_acces_protege)){
			$liste_acces_protege = AccesRestreint::liste_contenu_zone_rub(0);
		}
		return $liste_acces_protege;
	}
	function liste_rubriques_acces_libre(){
		static $liste_acces_libre; // la calculer une seule fois par hit
		if (!is_array($liste_acces_libre)){
			$liste_acces_libre = array();
			$liste_prot = join(",",AccesRestreint::liste_rubriques_acces_proteges());
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

	function liste_rubriques_accessibles(){
		global $auteur_session;
		$liste = AccesRestreint::liste_rubriques_acces_libre();
		if ($auteur_session['id_auteur']){
			$id_auteur = intval($auteur_session['id_auteur']);
			$s = spip_query("SELECT id_zone FROM spip_zones_auteurs WHERE id_auteur=$id_auteur");
			while ($row = spip_fetch_array($s)){
				$liste = array_merge($liste,AccesRestreint::liste_contenu_zone_rub($row['id_zone']));
			}
		}
		return $liste;
	}
	// fonctions de filtrage rubrique
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function liste_rubriques_exclues(){
		static $liste_rub_exclues;
		if (!is_array($liste_rub_exclues)){
			$liste_rub_exclues = array();
			global $auteur_session;
			$liste_rub_exclues = AccesRestreint::liste_rubriques_acces_proteges();
			if ($auteur_session['id_auteur']){
				$id_auteur = intval($auteur_session['id_auteur']);
				$s = spip_query("SELECT id_zone FROM spip_zones_auteurs WHERE id_auteur=$id_auteur");
				while ($row = spip_fetch_array($s)){
					$liste_rub_exclues = array_diff($liste_rub_exclues,AccesRestreint::liste_contenu_zone_rub($row['id_zone']));
				}
			}
		}
		return $liste_rub_exclues;
	}
	function rubriques_accessibles_where($primary){
		$liste = AccesRestreint::liste_rubriques_exclues();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage article
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function liste_articles_exclus(){
		static $liste_art_exclus;
		if (!is_array($liste_art_exclus)){
			$liste_art_exclus = array();
			$liste_rub = AccesRestreint::liste_rubriques_exclues();
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			$s = spip_query("SELECT id_article FROM spip_articles WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_art_exclus[] = $row['id_article'];
			}
		}
		return $liste_art_exclus;
	}
	function articles_accessibles_where($primary){
		$liste = AccesRestreint::liste_articles_exclus();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage breves
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function liste_breves_exclues(){
		static $liste_breves_exclues;
		if (!is_array($liste_breves_exclues)){
			$liste_breves_exclues = array();
			$liste_rub = AccesRestreint::liste_rubriques_exclues();
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			$s = spip_query("SELECT id_breve FROM spip_breves WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_breves_exclues[] = $row['id_breve'];
			}
		}
		return $liste_breves_exclues;
	}
	function breves_accessibles_where($primary){
		$liste = AccesRestreint::liste_breves_exclues();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage forums
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function liste_forum_exclus(){
		static $liste_forum_exclus;
		if (!is_array($liste_forum_exclus)){
			$liste_forum_exclus = array();
			// rattaches aux rubriques
			$liste_rub = AccesRestreint::liste_rubriques_exclues();
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			// rattaches aux articles
			$liste_art = AccesRestreint::liste_articles_exclus();
			$where .= " OR " . calcul_mysql_in('id_article', join(",",$liste_art));
			// rattaches aux breves
			$liste_breves = AccesRestreint::liste_breves_exclues();
			$where .= " OR " . calcul_mysql_in('id_breve', join(",",$liste_art));

			$s = spip_query("SELECT id_forum FROM spip_forum WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_forum_exclus[] = $row['id_forum'];
			}
		}
		return $liste_forum_exclus;
	}
	function forum_accessibles_where($primary){
		$liste = AccesRestreint::liste_forum_exclus();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage signatures
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function liste_signatures_exclues(){
		static $liste_signatures_exclues;
		if (!is_array($liste_signatures_exclues)){
			$liste_signatures_exclues = array();
			// rattaches aux articles
			$liste_art = AccesRestreint::liste_articles_exclus();
			$where = calcul_mysql_in('id_article', join(",",$liste_art));
			$s = spip_query("SELECT id_signature FROM spip_signatures WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_signatures_exclues[] = $row['id_signature'];
			}
		}
		return $liste_signatures_exclues;
	}
	function signatures_accessibles_where($primary){
		$liste = AccesRestreint::liste_signatures_exclues();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage documents
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function liste_documents_exclus(){
		static $liste_documents_exclus;
		if (!is_array($liste_documents_exclus)){
			$liste_documents_exclus = array();
			// rattaches aux articles
			$liste_art = AccesRestreint::liste_articles_exclus();
			$where = calcul_mysql_in('id_article', join(",",$liste_art));
			$s = spip_query("SELECT id_document FROM spip_documents_articles WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$row['id_document']]=1;
			}
			// rattaches aux rubriques
			$liste_rub = AccesRestreint::liste_rubriques_exclues();
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			$s = spip_query("SELECT id_document FROM spip_documents_rubriques WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$row['id_document']]=1;
			}
			// rattaches aux breves
			$liste_breves = AccesRestreint::liste_breves_exclues();
			$where = calcul_mysql_in('id_breve', join(",",$liste_breves));
			$s = spip_query("SELECT id_document FROM spip_documents_breves WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$row['id_document']]=1;
			}
			// rattaches aux syndic
			$liste_syn = AccesRestreint::liste_syndic_exclus();
			$where = calcul_mysql_in('id_syndic', join(",",$liste_syn));
			$s = spip_query("SELECT id_document FROM spip_documents_syndic WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_documents_exclus[$row['id_document']]=1;
			}
			$liste_documents_exclus = array_keys($liste_documents_exclus);
		}
		return $liste_documents_exclus;
	}
	function articles_documents_where($primary){
		$liste = AccesRestreint::liste_documents_exclus();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage syndic
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function liste_syndic_exclus(){
		static $liste_syndic_exclus;
		if (!is_array($liste_syndic_exclus)){
			$liste_syndic_exclus = array();
			$liste_rub = AccesRestreint::liste_rubriques_exclues();
			$where = calcul_mysql_in('id_rubrique', join(",",$liste_rub));
			$s = spip_query("SELECT id_syndic FROM spip_syndic WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_syndic_exclus[] = $row['id_syndic'];
			}
		}
		return $liste_syndic_exclus;
	}
	function syndic_accessibles_where($primary){
		$liste = AccesRestreint::liste_syndic_exclus();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// fonctions de filtrage syndic_articles
	// plus performant a priori : liste des rubriques exclues uniquement
	// -> condition NOT IN
	function liste_syndic_articles_exclus(){
		static $liste_syndic_articles_exclus;
		if (!is_array($liste_syndic_articles_exclus)){
			$liste_syndic_articles_exclus = array();
			$liste_syn = AccesRestreint::liste_syndic_exclus();
			$where = calcul_mysql_in('id_syndic', join(",",$liste_syn));
			$s = spip_query("SELECT id_syndic_article FROM spip_syndic_articles WHERE $where");
			while ($row = spip_fetch_array($s)){
				$liste_syndic_articles_exclus[] = $row['id_syndic_article'];
			}
		}
		return $liste_syndic_articles_exclus;
	}
	function syndic_articles_accessibles_where($primary){
		$liste = AccesRestreint::liste_syndic_articles_exclus();
		return calcul_mysql_in($primary, join(",",$liste),"NOT");
	}

	// filtre de securisation des squelettes
	// utilise avec [(#REM|AccesRestreint::securise_squelette)]
	// evite divulgation d'info si plugin desactive
	// par erreur fatale
	function securise_squelette($letexte){
		return "";
	}
}
?>