<?php

// ******************************************************************
//            Fonctions calculant le nombre d'éléments liés à un attribut
// ******************************************************************

function attributs_nb_articles($id_attribut) {
	$nb = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_attributs_articles WHERE id_attribut=$id_attribut"));
	$nb = $nb['n'];
	return($nb);
}

function attributs_nb_rubriques($id_attribut) {
	$nb = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_attributs_rubriques WHERE id_attribut=$id_attribut"));
	$nb = $nb['n'];
	return($nb);
}

function attributs_nb_breves($id_attribut) {
	$nb = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_attributs_breves WHERE id_attribut=$id_attribut"));
	$nb = $nb['n'];
	return($nb);
}

function attributs_nb_syndic($id_attribut) {
	$nb = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_attributs_syndic WHERE id_attribut=$id_attribut"));
	$nb = $nb['n'];
	return($nb);
}

function attributs_nb_auteurs($id_attribut) {
	$nb = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_attributs_auteurs WHERE id_attribut=$id_attribut"));
	$nb = $nb['n'];
	return($nb);
}

function attributs_nb_groupes($id_attribut) {
	$nb = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_attributs_groupes_mots WHERE id_attribut=$id_attribut"));
	$nb = $nb['n'];
	return($nb);
}

function attributs_nb_mots($id_attribut) {
	$nb = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_attributs_mots WHERE id_attribut=$id_attribut"));
	$nb = $nb['n'];
	return($nb);
}

//Affichage de la liste des auteurs

function attributs_afficher_auteurs($titre_table, $requete)
{
	global $couleur_claire;

	$tmp_var = 't_' . substr(md5(join(' ',$requete)), 0, 4);

	return affiche_tranche_bandeau($requete, "auteur-24.gif", $couleur_claire, "black", $tmp_var, $titre_table, false,  array(''), array('arial2'), 'attributs_afficher_auteurs_boucle');
}

function attributs_afficher_auteurs_boucle($row, &$tous_id, $voir_logo, $bof)
{
	global $spip_lang_right;

	$vals = '';
	$id_auteur=$row["id_auteur"];
	if (autoriser('voir','auteur',$id_auteur)){
		$nom=typo($row["nom"]);
		$statut=$row["statut"];
		
		$tous_id[] = $id_auteur;

		switch ($statut) {
			case '0minirezo':
					$puce='admin-12.gif';
					$title = _T('info_administrateur');
					break;
				case '1comite':
					$puce='redac-12.gif';
					$title = _T('info_redacteur_1');
					break;
				case '6forum':
					$puce='visit-12.gif';
					$title = _T('info_visiteur_1');
					break;
		}

		$s = "<a href=\"".generer_url_ecrire("auteurs_info","id_auteur=$id_auteur")."\" title=\"$nom\">";
	
		if ($voir_logo) {
			$chercher_logo = charger_fonction('chercher_logo', 'inc');
			if ($logo = $chercher_logo($id_auteur, 'id_auteur', 'on'))  {
				list($fid, $dir, $nom, $format) = $logo;
				include_spip('inc/filtres_images');
				$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
				if ($logo)
					$s .= "<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>$logo</span>";
			}
		}
	
		$s .= http_img_pack($puce, $statut, "") ."&nbsp;&nbsp;";
				
		$s .= typo($nom);
		
		$s .= "</a> &nbsp;&nbsp;";
		$vals[] = $s;

	}

	return $vals;
}

function attributs_afficher_mots($titre_table, $requete)
{
	global $couleur_claire;

	$tmp_var = 't_' . substr(md5(join(' ',$requete)), 0, 4);

	return affiche_tranche_bandeau($requete, "mot-cle-24.gif", $couleur_claire, "black", $tmp_var, $titre_table, false,  array('',''), array('arial2','arial2'), 'attributs_afficher_mots_boucle');
}

function attributs_afficher_mots_boucle($row, &$tous_id, $voir_logo, $bof)
{
	global $spip_lang_right;

	$vals = '';
	$id_mot=$row["id_mot"];
	if (autoriser('voir','mot',$id_auteur)){
		$titre=typo($row["titre"]);
		$descriptif=typo($row["descriptif"]);

		$tous_id[] = $id_mot;

		$puce='petite-cle.gif';
		$title = '';

		$s = "<a href=\"".generer_url_ecrire("mots_edit","id_mot=$id_mot")."\" title=\"$titre\">";

		if ($voir_logo) {
			$chercher_logo = charger_fonction('chercher_logo', 'inc');
			if ($logo = $chercher_logo($id_mot, 'id_mot', 'on'))  {
				list($fid, $dir, $nom, $format) = $logo;
				include_spip('inc/filtres_images');
				$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
				if ($logo)
					$s .= "<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>$logo</span>";
			}
		}
	
		$s .= http_img_pack($puce, $statut, "") ."&nbsp;&nbsp;";
				
		$s .= typo($titre);
		
		$s .= "</a> &nbsp;&nbsp;";
		$vals[] = $s;
		
		$s = typo($descriptif);
		$vals[] = $s;
		

	}

	return $vals;
}

function attributs_afficher_groupes_mots($titre_table, $requete)
{
	global $couleur_claire;

	$tmp_var = 't_' . substr(md5(join(' ',$requete)), 0, 4);

	return affiche_tranche_bandeau($requete, "groupe-mot-24.gif", $couleur_claire, "black", $tmp_var, $titre_table, false,  array('',''), array('arial2','arial2'), 'attributs_afficher_groupes_mots_boucle');
}

function attributs_afficher_groupes_mots_boucle($row, &$tous_id, $voir_logo, $bof)
{
	global $spip_lang_right;

	$vals = '';
	$id_groupe=$row["id_groupe"];
	if (autoriser('voir','groupemots',$id_auteur)){
		$titre=typo($row["titre"]);
		$descriptif=typo($row["descriptif"]);

		$tous_id[] = $id_groupe;

		$puce='';
		$title = '';

		$s = "<a href=\"".generer_url_ecrire("mots_type","id_groupe=$id_groupe")."\" title=\"$titre\">";

		/*if ($voir_logo) {
			$chercher_logo = charger_fonction('chercher_logo', 'inc');
			if ($logo = $chercher_logo($id_mot, 'id_mot', 'on'))  {
				list($fid, $dir, $nom, $format) = $logo;
				include_spip('inc/filtres_images');
				$logo = image_reduire("<img src='$fid' alt='' />", 26, 20);
				if ($logo)
					$s .= "<span style='float: $spip_lang_right; margin-top: -2px; margin-bottom: -2px;'>$logo</span>";
			}
		}*/
	
		//$s .= http_img_pack($puce, $statut, "") ."&nbsp;&nbsp;";
				
		$s .= typo($titre);
		
		$s .= "</a> &nbsp;&nbsp;";
		$vals[] = $s;
		
		$s = typo($descriptif);
		$vals[] = $s;
		

	}

	return $vals;
}

// Fonctions de gestion

function attributs_supprimer_attribut($id_attribut)
{
	global $connect_statut;
	global $connect_toutes_rubriques;

	$id_attribut = intval($id_attribut);
	if ($id_attribut) {
		spip_query("DELETE FROM spip_attributs WHERE id_attribut='$id_attribut'");
		spip_query("DELETE FROM spip_attributs_articles WHERE id_attribut='$id_attribut'");
		spip_query("DELETE FROM spip_attributs_rubriques WHERE id_attribut='$id_attribut'");
		spip_query("DELETE FROM spip_attributs_breves WHERE id_attribut='$id_attribut'");
		spip_query("DELETE FROM spip_attributs_auteurs WHERE id_attribut='$id_attribut'");
		spip_query("DELETE FROM spip_attributs_syndic WHERE id_attribut='$id_attribut'");
	}
	return 0;
}

	function attributs_formulaire($table, $id_objet, $nouv_attribut, $supp_attribut, $flag_editable, $retour) {
	  global $connect_statut, $connect_toutes_rubriques, $attributs, $connect_id_auteur;
		global $spip_lang_rtl, $spip_lang_right;
		$out = "";
	
		$retour = urlencode($retour);
		$select_groupe = $GLOBALS['select_groupe'];

		if ($table == 'articles') {
			$id_table = 'id_article';
			$objet = 'article';
			$url_base = 'articles';
		}
		else if ($table == 'rubriques') {
			$id_table = 'id_rubrique';
			$objet = 'rubrique';
			$url_base = 'naviguer';
		}
		else if ($table == 'breves') {
			$id_table = 'id_breve';
			$objet = 'breve';
			$url_base = 'breves_voir';
		}
		else if ($table == 'auteurs') {
			$id_table = 'id_auteur';
			$objet = 'auteur';
			$url_base = 'auteur_infos';
		}
		else if ($table == 'syndic') {
			$id_table = 'id_syndic';
			$objet = 'site';
			$url_base = 'sites';
		}
		else if ($table == 'groupes_mots') {
			$id_table = 'id_groupe';
			$objet = 'groupe_mot';
			$url_base = 'mots_type';
		}
		else if ($table == 'mots') {
			$id_table = 'id_mot';
			$objet = 'mot';
			$url_base = 'mots_edit';
		}

		list($nombre_attributs) = spip_fetch_array(spip_query("SELECT COUNT(*) FROM spip_attributs WHERE $table='oui'"),SPIP_NUM);
		if($nombre_attributs==0) return '';

		$out .= "<a name='attributs'></a>";
		if ($flag_editable){
			if ($nouv_attribut||$supp_attribut)
				$bouton = bouton_block_visible("lesattributs");
			else
				$bouton =  bouton_block_invisible("lesattributs");
		}
		$out .= debut_cadre_enfonce("../"._DIR_PLUGIN_ATTRIBUTS."/img_pack/attribut-24.png", true, "", $bouton._T('attributs:titre_attributs'));

		// Appliquer les modifications
		if ($nouv_attribut && $flag_editable && $nouv_attribut!='x' && autoriser('associer','attribut',$nouv_attribut)) {
			$query = "SELECT * FROM spip_attributs_$table WHERE id_attribut=$nouv_attribut AND $id_table=$id_objet";
			$result = spip_query($query);
			if (!spip_num_rows($result)) {
				$query = "INSERT INTO spip_attributs_$table (id_attribut,$id_table) VALUES ($nouv_attribut, $id_objet)";
				$result = spip_query($query);
				$reindexer = true;
			}
		}

		if ($supp_attribut && $flag_editable && autoriser('associer','attribut',$supp_attribut)) {
			$attributs_supp = " AND id_attribut=$supp_attribut";
			$query = "DELETE FROM spip_attributs_$table WHERE $id_table=$id_objet $attributs_supp";
			$result = spip_query($query);
			$reindexer = true;
		}
		
		//
		// Afficher les attributs
		//
	
		unset($les_attributs);

		$query = "SELECT attributs.* FROM spip_attributs AS attributs, spip_attributs_$table AS lien WHERE lien.$id_table=$id_objet AND attributs.id_attribut=lien.id_attribut ORDER BY attributs.titre";
		$result = spip_query($query);
	
		if (spip_num_rows($result) > 0) {
			$out .= "<div class='liste'>";
			$out .= "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>";
		
			$ifond=0;
				
			$tableau= '';
			while ($row = spip_fetch_array($result)) {
				$vals = '';
			
				$id_attribut = $row['id_attribut'];
				$titre_attribut = $row['titre'];
				$descriptif_attribut = $row['descriptif'];

				if ($ifond==0){
					$ifond=1;
					$couleur="#FFFFFF";
				}else{
					$ifond=0;
					$couleur="#EDF3FE";
				}
		
				$url = "href='" . generer_url_ecrire('attribut_edit', "id_attribut=$id_attribut&retour=".rawurlencode(generer_url_ecrire($url_base, "$id_table=$id_objet#attributs"))) . "'";
	
				$vals[] = "<a $url>" . http_img_pack("../"._DIR_PLUGIN_ATTRIBUTS.'/img_pack/attribut-16.png', "", "width='16' height='16' border='0'") ."</a>";

				$s = "<a $url>".typo($titre_attribut)."</a>";
				$vals[] = $s;

				$s = propre($row['descriptif']);
				$vals[] = $s;

				$vals[] = "";

				if($flag_editable && autoriser('associer','attribut',$id_attribut)){
					$s = "<a href='" . generer_url_ecrire($url_base, "$id_table=$id_objet&supp_attribut=$id_attribut#attributs") . "'>"._T('attributs:retirer')."&nbsp;" . http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' align='middle'") ."</a>";
					$vals[] = $s;
				}
				else $vals[]= "";

				$tableau[] = $vals;

				$les_attributs[] = $id_attribut;
			}

			$largeurs = array('', '', '', '', '');
			$styles = array('arial11', 'arial2', 'arial2', 'arial2', 'arial1');
			$out .= afficher_liste($largeurs, $tableau, $styles);

			$out .= "</table></div>";
		}

		if ($les_attributs) {
			$les_attributs = join($les_attributs, ",");
		} else {
			$les_attributs = "0";
		}

		//
		// Afficher le formulaire d'ajout des attributs
		//
		if ($flag_editable) {
			if ($nouv_attribut | $supp_attribut)
				$out .= debut_block_visible("lesattributs");
			else
				$out .= debut_block_invisible("lesattributs");

			$form_attribut = generer_url_post_ecrire($url_base,"$id_table=$id_objet", '', "#attributs");

			$message_ajouter_attribut = "<span class='verdana1'><B>"._T('attributs:ajouter_attribut')."</B></span> &nbsp;\n";

			$out .= "<table border='0' width='100%' style='text-align: $spip_lang_right'>";

			$query = "SELECT * FROM spip_attributs WHERE $table='oui' AND id_attribut NOT IN ($les_attributs) ORDER BY titre";

			$result = spip_query($query);

			if (spip_num_rows($result) > 0) {
				$out .= "\n<tr>";
				$out .= $form_attribut;
				$out .= "\n<td>";
				$out .= $message_ajouter_attribut;
				$message_ajouter_attribut = "";
				$out .= "</td>\n<td>";

				$out .= "<select name='nouv_attribut' size='1' onChange=\"setvisibility('valider_bouton', 'visible');\" style='width: 180px; ' class='fondl'>";

				$out .= "\n<option value='x' style='font-variant: small-caps;'>"._T("attributs:selectionner_attribut")."</option>";
				while($row = spip_fetch_array($result)) {
					$id_attribut = $row['id_attribut'];
					$titre_attribut = $row['titre'];
					$texte_attribut = entites_html(textebrut(typo($titre_attribut)));
					if(autoriser('associer','attribut',$id_attribut)) {
						$out .= "\n<option value=\"$id_attribut\">";
						$out .= "&nbsp;&nbsp;&nbsp;";
						$out .= "$texte_attribut</option>";
					}
				}
				$out .= "</select>";
				$out .= "</td>\n<td>";
				$out .= "<span class='visible_au_chargement' id='valider_bouton'>";
				$out .= " &nbsp; <input type='submit' name='Choisir' value='"._T('bouton_choisir')."' class='fondo'>";
				$out .= "</span>";
				$out .= "</td></form>";
				$out .= "</tr>";
			}
			

			$out .= "</table>";
			$out .= fin_block();
		}
	
		$out .= fin_cadre_enfonce(true);
		return $out;
	}

?>