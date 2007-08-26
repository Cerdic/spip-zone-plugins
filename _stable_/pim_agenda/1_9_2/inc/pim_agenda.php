<?php

/*
 * P.I.M Agenda
 * Gestion d'un agenda collaboratif
 *
 * Auteur :
 * Cedric Morin, Notre-ville.net
 * (c) 2005,2007 - Distribue sous licence GNU/GPL
 *
 */

function PIMAgenda_detailler_agenda($id_agenda, $complet= false ){
	$res = spip_query("SELECT * FROM spip_pim_agenda WHERE id_agenda="._q($id_agenda));
	if (!$row = spip_fetch_array($res)) return false;
	if ($complet){
		$res = spip_query("SELECT id_mot FROM spip_mots_pim_agenda WHERE id_agenda="._q($id_agenda));
		while ($row2 = spip_fetch_array($res))
			$row['mots'][] = $row2['id_mot'];

		$res = spip_query("SELECT id_auteur FROM spip_pim_agenda_auteurs WHERE id_agenda="._q($id_agenda));
		while ($row2 = spip_fetch_array($res))
			$row['auteurs'][] = $row2['id_auteur'];
			
		$res = spip_query("SELECT id_auteur FROM spip_pim_agenda_invites WHERE id_agenda="._q($id_agenda));
		while ($row2 = spip_fetch_array($res))
			$row['invites'][] = $row2['id_auteur'];
		
		$res = spip_query("SELECT id_groupe FROM spip_pim_agenda_groupes_invites WHERE id_agenda="._q($id_agenda));
		while ($row2 = spip_fetch_array($res))
			$row['groupes_invites'][] = $row2['id_groupe'];
			
		$res = spip_query("SELECT id_donnee FROM spip_forms_donnees_pim_agenda WHERE id_agenda="._q($id_agenda));
		while ($row2 = spip_fetch_array($res))
			$row['donnees'][] = $row2['id_donnee'];
	}

	return $row;
}

function PIMAgenda_supprimer_agenda($id_agenda){
	spip_log("suppression de l'agenda $id_agenda par ".$GLOBALS['auteur_session']['id_auteur'],'pimagenda');
	if ($row = detailler_agenda($id_agenda, true)){
		spip_query("DELETE FROM spip_mots_pim_agenda WHERE id_agenda="._q($id_agenda));
		spip_query("DELETE FROM spip_pim_agenda_auteurs WHERE id_agenda="._q($id_agenda));
		spip_query("DELETE FROM spip_pim_agenda_invites WHERE id_agenda="._q($id_agenda));
		spip_query("DELETE FROM spip_pim_agenda_groupes_invites WHERE id_agenda="._q($id_agenda));
		spip_query("DELETE FROM spip_forms_donnees_pim_agenda WHERE id_agenda="._q($id_agenda));
		spip_query("DELETE FROM spip_pim_agenda WHERE id_agenda="._q($id_agenda));
	}
	$notifier_pim_agenda = charger_fonction('notifier_pim_agenda','inc');
	$notifier_pim_agenda('supprimer',$id_agenda,$row, "");
}

function PIMAgenda_cree_groupe(){
	$titre = _q(_request('titre'));
	$descriptif = _q(_request('descriptif'));
	if (strlen($titre)>0){
		$id_groupe = spip_abstract_insert('spip_groupes', "(titre,descriptif,maj)", "($titre,$descriptif,NOW())");
		/*if ($id_groupe && _request('auto_attribue_droits')=='oui'){
			global $connect_id_auteur;
			if (autoriser('modifier','groupe'))
				spip_abstract_insert('spip_auteurs_groupes', "(id_groupe,id_auteur)", "($id_groupe,$connect_id_auteur)");
		}*/
		return $id_groupe;
	} 
	return 0;
}
function PIMAgenda_supprimer_groupe(){
	$id_groupe = intval(_request('supp_groupe'));
	if ($id_groupe){
		spip_query("DELETE FROM spip_groupes WHERE id_groupe='$id_groupe'");
		spip_query("DELETE FROM spip_auteurs_groupes WHERE id_groupe='$id_groupe'");
	}
	return 0;
}

function PIMAgenda_enregistrer_groupe(){
	$titre = _q(_request('titre'));
	$descriptif = _q(_request('descriptif'));
	$id_groupe = intval(_request('id_groupe'));
	if (strlen($titre)>0 && $id_groupe){
		spip_query("UPDATE spip_groupes SET titre=$titre, descriptif=$descriptif, WHERE id_groupe=$id_groupe");
		/*if (is_array($_POST['restrict'])){
			foreach(array_keys($_POST['restrict']) as $id){
				$id = intval($id);
				spip_abstract_insert('spip_groupes_rubriques', "(id_groupe,id_rubrique)", "('$id_groupe','$id')");
			}
		}*/
	}
	return 0;
}

// liste des auteurs contenus dans un groupe
function PIMAgenda_liste_contenu_groupe_auteur($id_groupe) {
	$liste_auteurs=array();
	if (is_array($id_groupe)){
		$in_groupe = calcul_mysql_in('id_groupe',join(',',array_map('intval',$id_groupe)));
	}
	else $in_groupe = "id_groupe="._q($id_groupe);
	$id_groupe = intval($id_groupe);
	// liste des rubriques directement liees a la groupe
	$s = spip_query("SELECT id_auteur FROM spip_auteurs_groupes WHERE $in_groupe");
	while ($row=spip_fetch_array($s))
		$liste_auteurs[] = $row['id_auteur'];
	return $liste_auteurs;
}

/*
 * Affiche la liste des groupes associee a l'objet
 * specifie, plus le formulaire d'ajout de groupe
 */
	
function PIMAgenda_formulaire_groupes($table, $id_objet, $nouv_groupe, $supp_groupe, $flag_editable, $retour) {
  global $connect_statut, $connect_toutes_rubriques, $options, $connect_id_auteur, $id_auteur;
	global $spip_lang_rtl, $spip_lang_right;
	$out = "";

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
		$url_base = ($GLOBALS['spip_version_code']>1.92)?"auteur_infos":"auteurs_edit";
	}

	list($nombre_groupes) = spip_fetch_array(spip_query("SELECT COUNT(*) FROM spip_groupes AS groupes, spip_{$table}_groupes AS lien WHERE lien.$id_table=$id_objet AND groupes.id_groupe=lien.id_groupe"),SPIP_NUM);

	$out .= "<a name='groupes'></a>";
	if ($flag_editable){
		if ($nouv_groupe||$supp_groupe)
			$bouton = bouton_block_visible("lesgroupes");
		else
			$bouton =  bouton_block_invisible("lesgroupes");
	}
	$out .= debut_cadre_enfonce(_DIR_PLUGIN_PIMAGENDA."img_pack/groupes-24.gif", true, "", $bouton._T('pimagenda:titre_groupes_acces'));

	//////////////////////////////////////////////////////
	// Recherche de groupes d'acces
	//

	if ($nouv_groupe)
		$nouveaux_groupes = array($nouv_groupe);

	//////////////////////////////////////////////////////
	// Appliquer les modifications sur les groupes d'acces
	//
	if ($nouveaux_groupes && $flag_editable) {
		while ((list(,$nouv_groupe) = each($nouveaux_groupes)) AND $nouv_groupe!='x') {
			$query = "SELECT * FROM spip_{$table}_groupes WHERE id_groupe=$nouv_groupe AND $id_table=$id_objet";
			$result = spip_query($query);
			if (!spip_num_rows($result)) {
				$query = "INSERT INTO spip_{$table}_groupes (id_groupe,$id_table) VALUES ($nouv_groupe, $id_objet)";
				$result = spip_query($query);
			}
		}
		$reindexer = true;
	}

	if ($supp_groupe && $flag_editable) {
		if ($supp_groupe == -1)
			$groupes_supp = "";
		else
			$groupes_supp = " AND id_groupe=$supp_groupe";
		$query = "DELETE FROM spip_{$table}_groupes WHERE $id_table=$id_objet $groupes_supp";

		$result = spip_query($query);
		$reindexer = true;
	}
	
	//
	// Afficher les groupes d'acces
	//

	unset($les_groupes);

	$query = "SELECT groupes.* FROM spip_groupes AS groupes, spip_{$table}_groupes AS lien WHERE lien.$id_table=$id_objet AND groupes.id_groupe=lien.id_groupe ORDER BY groupes.titre";
	$result = spip_query($query);

	if (spip_num_rows($result) > 0) {
		$out .= "<div class='liste'>";
		$out .= "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>";
	
		$ifond=0;
			
		$tableau= '';
		while ($row = spip_fetch_array($result)) {
			$vals = '';
		
			$id_groupe = $row['id_groupe'];
			$titre_groupe = $row['titre'];
			$descriptif_groupe = $row['descriptif'];

			if ($ifond==0){
				$ifond=1;
				$couleur="#FFFFFF";
			}else{
				$ifond=0;
				$couleur="#EDF3FE";
			}
	
			$url = "href='" . generer_url_ecrire('auteurs_groupe_edit', "id_groupe=$id_groupe&retour=".rawurlencode(generer_url_ecrire($url_base, "$id_table=$id_objet#groupes"))) . "'";

			$vals[] = "<a $url>" . http_img_pack(_DIR_PLUGIN_PIMAGENDA.'img_pack/groupes-16.gif', "", "width='16' height='16' border='0'") ."</a>";

			$s = "<a $url>".typo($titre_groupe)."</a>";
			$vals[] = $s;
	
			$vals[] = "";
	
			// Un admin restreint ne peut agir que sur les groupes auxquelles il appartient (excepté sur les admins) et excepté lui-même pour éviter de se retirer d'une groupe par erreur
			if($flag_editable && ($connect_toutes_rubriques || (AccesRestreint_test_appartenance_groupe_auteur($id_groupe, $connect_id_auteur) && autoriser('modifier', 'auteur', $id_auteur ) && $id_connect_auteur!=$id_auteur))){
			  $s = "<a href='" . generer_url_ecrire($url_base, "$id_table=$id_objet&supp_groupe=$id_groupe#groupes") . "'>"._T('pimagenda:info_retirer_groupe')."&nbsp;" . http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' align='middle'") ."</a>";
				$vals[] = $s;
			}
			else $vals[]= "";

			$tableau[] = $vals;
	
			$les_groupes[] = $id_groupe;
		}
	
		$largeurs = array('25', '', '', '');
		$styles = array('arial11', 'arial2', 'arial2', 'arial1');
		$out .= afficher_liste($largeurs, $tableau, $styles);
	
		$out .= "</table></div>";
	}

	if ($les_groupes) {
		$nombre_groupes_associes = count($les_groupes);
		$les_groupes = join($les_groupes, ",");
	} else {
		$les_groupes = "0";
	}

	//
	// Afficher le formulaire d'ajout de groupes d'acces
	//
	if ($flag_editable) {
		if ($nouveaux_groupes | $supp_groupe)
			$out .= debut_block_visible("lesgroupes");
		/*else if ($nb_groupes > 0) {
			$out .= debut_block_visible("lesgroupes");
			// vilain hack pour redresser un triangle
			$couche_a_redresser = $GLOBALS['numero_block']['lesgroupes'];
			if ($GLOBALS['browser_layer']) $out .= http_script("
				triangle = findObj('triangle' + $couche_a_redresser);
				if (triangle) triangle.src = '" . _DIR_IMG_PACK . "deplierbas$spip_lang_rtl.gif';");
		}*/
		else
			$out .= debut_block_invisible("lesgroupes");

		if ($nombre_groupes_associes > 3) {
			$out .= "<div align='right' class='arial1'>";
			$out .= "<a href='". generer_url_ecrire($url_base, "$id_table=$id_objet&supp_groupe=-1#groupes"). "'>"._T('pimagenda:info_retirer_groupes')."</a>";
			$out .= "</div><br />\n";
		}

		// il faudrait rajouter STYLE='margin:1px;' qq part

		$form_groupe = generer_url_post_ecrire($url_base,"$id_table=$id_objet", '', "#groupes");

		if ($table == 'rubriques') $form_groupe .= "<input type='hidden' name='id_rubrique' value='$id_objet' />";

		$message_ajouter_groupe = "<span class='verdana1'><B>"._T('pimagenda:titre_ajouter_groupe')."</B></span> &nbsp;\n";

		$out .= "<table border='0' width='100%' style='text-align: $spip_lang_right'>";

		// Un admin restreint ne peut ajouter à un auteur que les groupes auxquelles il appartient
		if($connect_toutes_rubriques ){
			$query = "SELECT * FROM spip_groupes AS z WHERE z.id_groupe NOT IN ($les_groupes) ORDER BY z.titre";
		} else {
			$query = "SELECT * FROM spip_groupes AS z JOIN spip_auteurs_groupes AS za ON z.id_groupe=za.id_groupe WHERE za.id_auteur=$connect_id_auteur AND z.id_groupe NOT IN ($les_groupes) ORDER BY titre";
		}

		$result = spip_query($query);

		if (spip_num_rows($result) > 0) {
			$out .= "\n<tr>";
			$out .= $form_groupe;
			$out .= "\n<td>";
			$out .= $message_ajouter_groupe;
			$message_ajouter_groupe = "";
			$out .= "</td>\n<td>";

			$out .= "<select name='nouv_groupe' size='1' onChange=\"setvisibility('valider_groupe_$id_objet', 'visible');\" style='width: 180px; ' class='fondl'>";

			$out .= "\n<option value='x' style='font-variant: small-caps;'>"._T("pimagenda:selectionner_un_groupe")."</option>";
			while($row = spip_fetch_array($result)) {
				$id_groupe = $row['id_groupe'];
				$titre_groupe = $row['titre'];
				$texte_option = entites_html(textebrut(typo($titre_groupe)));
				$out .= "\n<option value=\"$id_groupe\">";
				$out .= "&nbsp;&nbsp;&nbsp;";
				$out .= "$texte_option</option>";
			}
			$out .= "</select>";
			$out .= "</td>\n<td>";
			$out .= "<span class='visible_au_chargement' id='valider_groupe_$id_objet'>";
			$out .= " &nbsp; <input type='submit' name='Choisir' value='"._T('bouton_choisir')."' class='fondo'>";
			$out .= "</span>";
			$out .= "</td></form>";
			$out .= "</tr>";
		}
		
		/*if (autoriser('modifier','groupe') AND $flag_editable) {
			$out .= "<tr><td></td><td colspan='2'>";
			$out .= "<div style='width: 200px;'>";
			icone_horizontale(_T('pimagenda:icone_creer_groupe'), generer_url_ecrire("mots_edit","new=oui&ajouter_id_article=$id_objet&table=$table&id_table=$id_table&redirect=$retour"), "img_pack/groupes-acces-24.gif", "creer.gif");
			$out .= "</div> ";
			$out .= "</td></tr>";
		}*/
		
		$out .= "</table>";
		$out .= fin_block();
	}

	$out .= fin_cadre_enfonce(true);
	return $out;
}


?>