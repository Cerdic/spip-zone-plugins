<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

//ne pas mettre la possibilité de motclef unique si longue liste de mots

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/mots');

// http://doc.spip.org/@inc_editer_motdoc_dist
//voir les memes noms de fichier dans action et exec
function inc_editer_motdoc_dist($objet, $id_objet, $cherche_mot, $select_groupe, $flag) {
	global $id_article, $options, $connect_statut, $spip_lang_rtl, $spip_lang_right, $spip_lang;

	if (!($options == 'avancees' AND $GLOBALS['meta']["articles_mots"] != 'non'))
		return '';

	$visible = ($cherche_mot OR ($flag === 'ajax'));

	if ($objet == 'article') {
		$table_id = 'id_article';
		$table = 'articles';
		$url_base = "articles";
	}
	else if ($objet == 'breve') {
		$table_id = 'id_breve';
		$table = 'breves';
		$url_base = "breves_voir";
	}
	else if ($objet == 'rubrique') {
		$table_id = 'id_rubrique';
		$table = 'rubriques';
		$url_base = "naviguer";
	} 
	else if ($objet == 'syndic') {
		$table_id = 'id_syndic';
		$table = 'syndic';
		$url_base = "sites";
	}
	//ajout alm dec 2006
	 else if ($objet == 'document') {
		$table_id = 'id_document';
		$table = 'documents';
		$url_base = "articles&id_article=$id_article";
	}
	else {
	//erreur dans formulaire_mots(document, 545, , 8, ajax)
		spip_log("erreur dans formulaire_motsdoc_cles($objet, $id_objet, $cherche_mot, $select_groupe, $flag)");
		return '';
	}

	$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_mots AS mots, spip_mots_$table AS lien WHERE lien.$table_id=$id_objet AND mots.id_mot=lien.id_mot"));

	if (!($nombre_mots = $cpt['n'])) {
		if (!$flag) return;
		$cpt = spip_fetch_array(spip_query("SELECT COUNT(*) AS n FROM spip_groupes_mots WHERE $table = 'oui'	AND ".substr($connect_statut,1)." = 'oui'"));

		if (!$cpt['n']) return;
	}

	//
	// Preparer l'affichage
	//

	// La reponse
	$reponse = '';
	if ($flag AND $cherche_mot) {
		$reindexer = false;
		list($reponse, $nouveaux_mots) = inserer_motdoc_cle($cherche_mot, $select_groupe, $objet, $id_objet, $table, $table_id, $url_base);
		foreach($nouveaux_mots as $nouv_mot) {
			if ($nouv_mot!='x') {
				$reindexer |= inserer_motdoc("spip_mots_$table", $table_id, $id_objet, $nouv_mot);
			}
		}
		if ($reindexer AND ($GLOBALS['meta']['activer_moteur'] == 'oui')) {
			include_spip("inc/indexation");
			marquer_indexer("spip_$table", $id_objet);
		}
	}

	$form = afficher_motsdoc_cles($flag, $objet, $id_objet, $table, $table_id, $url_base, $visible);
//alm on vire le bouton trop lourd
	// Envoyer titre + div-id + formulaire + fin
	/*if ($flag){
		if ($visible)
			$bouton = bouton_block_visible("lesmots");
		else
			$bouton =  bouton_block_invisible("lesmots");
	} else $bouton = '';

	$bouton .= _T('titre_mots_cles').aide ("artmots");*/

	$res =  '<div style="height:0px">&nbsp;</div>' // place pour l'animation pendant Ajax
	//. debut_cadre_enfonce("mot-cle-24.gif", true, "", $bouton)
	  .'<div style="margin: 0 3px 3px 3px; text-align:left; padding:0 3px 0 3px; border: 1px solid'
	  . $GLOBALS['couleur_foncee']
	  . '; border-top:none; background-color:#dadada">'
	  . $reponse
	  . $form
	  .'</div>';
	 //. fin_cadre_enfonce(true);

	return ajax_action_greffe("editer_motdoc-$id_objet", $res);
}

// http://doc.spip.org/@inserer_motdoc
function inserer_motdoc($table, $table_id, $id_objet, $id_mot)
{
	$result = spip_num_rows(spip_query("SELECT id_mot FROM $table WHERE id_mot=$id_mot AND $table_id=$id_objet"));

	if (!$result) {
		spip_query("INSERT INTO $table (id_mot,$table_id) VALUES ($id_mot, $id_objet)");
	}
	return $result;
}


// http://doc.spip.org/@inserer_motdoc_cle
function inserer_motdoc_cle($cherche_mots, $id_groupe, $objet, $id_objet, $table, $table_id, $url_base)
{
	if ($table == 'articles') $ou = _T('info_l_article');
	else if ($table == 'breves') $ou = _T('info_la_breve');
	else if ($table == 'rubriques') $ou = _T('info_la_rubrique');
	//ajout alm dec 2006
	else if ($table == 'documents') $ou = _T('ce_document');

	$result = spip_query("SELECT id_mot, titre FROM spip_mots WHERE id_groupe=$id_groupe");

	$table_mots = array();
	$table_ids = array();
	while ($row = spip_fetch_array($result)) {
			$table_ids[] = $row['id_mot'];
			$table_mots[] = $row['titre'];
	}

	$nouveaux_mots = array();
	$res = '';

	foreach (split(" *[,;] *", $cherche_mots) as $cherche_mot) {
	  if  ($cherche_mot) {
		$resultat = mots_ressemblants($cherche_mot, $table_mots, $table_ids);
		$res .= "<p>" . debut_boite_info(true);
		if (!$resultat) {
			$res .= "<b>"._T('info_non_resultat', array('cherche_mot' => $cherche_mot))."</b><br /></p>";
		}
		else if (count($resultat) == 1) {
			$nouveaux_mots[] = $resultat[0];
			$row = spip_fetch_array(spip_query("SELECT titre FROM spip_mots WHERE id_mot=$resultat[0]"));
			$res .= "<b>"._T('info_mot_cle_ajoute')." $ou : </b><br /><ul>";
			$res .= "<li><font face='Verdana,Arial,Sans,sans-serif' size='2'><b><font size='3'>".typo($row['titre'])."</font></b></font></li>\n";
			$res .= "</ul>";
		}
		else $res .= affiche_mots_ressemblant($cherche_mot, $objet, $id_objet, $resultat, $table, $table_id, $url_base);

		$res .= fin_boite_info(true) . "<p>";
	  }
	}
	return array($res, $nouveaux_mots);
}

// http://doc.spip.org/@afficher_motsdoc_cles
//alm cette fonction marche mais il faudrait alléger carrément l'affichage, il ne faut pas mettre option mot unique
function afficher_motsdoc_cles($flag_editable, $objet, $id_objet, $table, $table_id, $url_base, $visible)
{
	global $spip_lang_rtl, $spip_lang, $spip_lang_right, $connect_statut, $connect_toutes_rubriques;

	$les_mots = array();
	$id_groupes_vus = array();
	$groupes_vus = array();
	$result = spip_query("SELECT mots.id_mot, mots.titre, mots.descriptif, mots.id_groupe FROM spip_mots AS mots, spip_mots_$table AS lien WHERE lien.$table_id=$id_objet AND mots.id_mot=lien.id_mot ORDER BY mots.type, mots.titre");
	if (spip_num_rows($result) > 0) {
	
		$tableau= array();
		//$cle = http_img_pack('petite-cle.gif', "", "width='23' height='12'");
		//alm on vire l'image trop grande
		$cle = '';
		//alm on doit revenir sur l'article si c'est un document
		//$ret = generer_url_retour($url_base, "$table_id=$id_objet#mots");
		$ret = generer_url_retour($url_base, "#editer_motdoc-$id_objet");
		while ($row = spip_fetch_array($result)) {

			$id_mot = $row['id_mot'];
			$titre_mot = $row['titre'];
// on raccourci le mot
		if (($n=strlen($titre_mot)) > 14) 
		$titre_mot = substr($titre_mot, 0, 10)."...";
			
			$descriptif_mot = $row['descriptif'];
			$id_groupe = $row['id_groupe'];

			$id_groupes_vus[] = $id_groupe;
			$url = generer_url_ecrire('mots_edit', "id_mot=$id_mot&redirect=$ret");
			//alm on a vire la clef trop grande
			$vals= array(" ");
			//$vals= array("<a href='$url'>$cle</a>");
			

			$row_groupe = spip_fetch_array(spip_query("SELECT titre, unseul, obligatoire, minirezo, comite FROM spip_groupes_mots WHERE id_groupe = $id_groupe"));
	// On recupere le typo_mot ici, et non dans le mot-cle lui-meme; sinon bug avec arabe

			$type_mot = typo($row_groupe['titre']);
			$flag_groupe = ($flag_editable AND
					((($connect_statut === '1comite') AND $row_groupe['comite'] === 'oui') OR (($connect_statut === '0minirezo') AND $row_groupe['minirezo'] === 'oui')));

			// Changer
			//modifier ici pour avoir le choix
			if ($row_groupe['unseul'] == "oui") /*(   AND $flag_groupe)*/ {
				$vals[]= formulaire_motdoc_remplace($id_groupe, $id_mot, $url_base, $table, $table_id, $objet, $id_objet);
			} else {
				$vals[]= "<a href='$url'>".typo($titre_mot)."</a>";
			}
			
// on raccourci le type de mot
		if (($n=strlen($type_mot)) > 11) 
		$type_mot = substr($type_mot, 0, 8)."...";
			if ($connect_toutes_rubriques)
				$vals[]= "<a href='" . generer_url_ecrire("mots_type","id_groupe=$id_groupe") . "'>$type_mot</a>";

			else	$vals[] = $type_mot;
	
			if ($flag_editable){
				if ($flag_groupe) {
					$s =  ''
					//$s =  _T('info_retirer_mot') //alm suppression deplacement sur alt
					//. "&nbsp;" //alm suppression
					. http_img_pack('croix-rouge.gif', _T('info_retirer_mot') , "width='7' height='7' ");
					//on supprime après url_base "$table_id=$id_objet"
					$s = ajax_action_auteur('editer_motdoc', "$id_objet,$id_mot,$table,$table_id,$objet", $url_base, "", array($s,''),"&id_objet=$id_objet&objet=$objet");
				} else $s = "&nbsp;";
				$vals[] = $s;
			} else $vals[]= "";

			$tableau[] = $vals;
	
			$les_mots[] = $id_mot;
		}
	//alm on supprime le 1er td pour le cadre quand les mots clefs sont choisis
		$largeurs = array('', '', '');
		$styles = array('arial2', 'arial2', 'arial1');

		$res = "\n<div class='liste' style='text-align:center; margin:0 auto; padding:0; '>"
		. "\n<table style='width:100%; border:0; margin:0 auto;' cellspacing='0' >"
		. afficher_liste($largeurs, $tableau, $styles)
		. "</table></div>";
	} else $res ='';

	if ($flag_editable)
	  $res .= formulaire_motsdoc_cles($id_groupes_vus, $id_objet, $les_mots, $table, $table_id, $url_base, $visible, $objet);

	return $res;
}

// http://doc.spip.org/@formulaire_motdoc_remplace
function formulaire_motdoc_remplace($id_groupe, $id_mot, $url_base, $table, $table_id, $objet, $id_objet)
{
	$result = spip_query("SELECT id_mot, titre FROM spip_mots WHERE id_groupe = $id_groupe ORDER by titre");

	$s = '';

	while ($row_autres = spip_fetch_array($result)) {
		$id = $row_autres['id_mot'];
		$le_titre_mot = supprimer_tags(typo($row_autres['titre']));
		$selected = ($id == $id_mot) ? " selected='selected'" : "";
		$s .= "<option value='$id'$selected> $le_titre_mot</option>";
	}

	$ancre = "valider_groupe_$id_groupe"; 
	// forcer le recalcul du noeud car on est en Ajax
	$jscript1 = "findObj_forcer('$ancre').style.visibility='visible';";

	return ajax_action_auteur('editer_motdoc', "$id_objet,$id_mot,$table,$table_id,$objet", $url_base, "$table_id=$id_objet", (
	"<select name='nouv_mot' onchange=\"$jscript1\""
	. " class='fondl' style=' width:90px;'>"
	. $s
	. "</select>"
	//ajout alm visibility:visible sur 3 occurences "visible_au_chargement" sinon marche pas
	. "<span class='visible_au_chargement' style='visibility:visible' id='$ancre'>"
	. "\n&nbsp; <input type='submit' value='"
	. _T('bouton_changer')
	. "' class='fondo' />"
	. "</span>"),"&id_objet=$id_objet&objet=$objet");
}


// http://doc.spip.org/@formulaire_motsdoc_cles
function formulaire_motsdoc_cles($id_groupes_vus, $id_objet, $les_mots, $table, $table_id, $url_base, $visible, $objet) {
	global $connect_statut, $spip_lang, $spip_lang_right, $spip_lang_rtl;

	if ($les_mots) {
		$nombre_mots_associes = count($les_mots);
		$les_mots = join($les_mots, ",");
	} else {
		$les_mots = "0";
		$nombre_mots_associes = 0;
	}
	$cond_id_groupes_vus = "0";
	if ($id_groupes_vus) $cond_id_groupes_vus = join(",",$id_groupes_vus);
	
	$nb_groupes = spip_num_rows(spip_query("SELECT * FROM spip_groupes_mots WHERE $table = 'oui' AND ".substr($connect_statut,1)." = 'oui' AND obligatoire = 'oui' AND id_groupe NOT IN ($cond_id_groupes_vus)"));

	if ($visible)
		$res = debut_block_visible("lesmots");
	else if ($nb_groupes > 0) {
		$res = debut_block_visible("lesmots");
			// vilain hack pour redresser un triangle
		$couche_a_redresser = $GLOBALS['numero_block']['lesmots'];
		if ($GLOBALS['browser_layer'])
			$res .= http_script("
				triangle = findObj('triangle' + $couche_a_redresser);
				if (triangle) triangle.src = '" . _DIR_IMG_PACK . "deplierbas$spip_lang_rtl.gif';");
	} else $res = debut_block_invisible("lesmots");

	if ($nombre_mots_associes > 3) {
		$res .= "<div align='right' class='arial1'>"
		//alm on vire $table_id=$id_objet
		  . ajax_action_auteur('editer_motdoc', "$id_objet,-1,$table,$table_id,$objet", $url_base, "", array(_T('info_retirer_mots'),''),"&id_objet=$id_objet&objet=$objet")
		. "</div><br />\n";
	}

	$result_groupes = spip_query("SELECT id_groupe,unseul,obligatoire,titre, ".creer_objet_multi ("titre", $spip_lang)." FROM spip_groupes_mots WHERE $table = 'oui' AND ".substr($connect_statut,1)." = 'oui' AND (unseul != 'oui'  OR (unseul = 'oui' AND id_groupe NOT IN ($cond_id_groupes_vus))) ORDER BY multi");

	// Afficher un menu par groupe de mots
	$ajouter ='';
	while ($row = spip_fetch_array($result_groupes)) {
		if ($menu = menu_motsdoc($row, $id_groupes_vus, $les_mots)) {
			$menu = ajax_action_auteur('editer_motdoc',
				"$id_objet,,$table,$table_id,$objet",
				$url_base,
				//"$table_id=$id_objet" retirer alm
				" ",
				$menu,
				"&id_objet=$id_objet&objet=$objet&select_groupe="
					.$row['id_groupe']
			);
			$ajouter .= "<div style='padding:3px;'>$menu</div>\n";
		}
	}
	if ($ajouter) {
		//alm viré $message = "<span class='verdana1'><b>"._T('titre_ajouter_mot_cle')."</b></span>\n";
		$res .= "<div style='float:$spip_lang_right; width:100%; padding:3px;'>"
			. $ajouter
			."</div>\n" ;
	}
//alm on supprime la possibilité de rajouter un mot clef au document sinon ç'est trop lourd
	/*if (acces_mots()) {
		$titre = _request('cherche_mot')
			? "&titre=".rawurlencode(_request('cherche_mot')) : '';
			//modif alm pour retour sur article et le doc après creation mot
		$bouton_ajouter = icone_horizontale(_T('icone_creer_mot_cle'), generer_url_ecrire("mots_edit","new=oui&ajouter_id_article=$id_objet&table=$table&table_id=$table_id$titre&redirect=" . generer_url_retour($url_base, "#editer_motdoc-$id_objet")), "mot-cle-24.gif", "creer.gif", false)
		. "\n";
	}

	if ($message OR $bouton_ajouter) {
		$res .= "<div style='width:170px;'>$message
			<br />$bouton_ajouter</div>\n";
	}*/

	return $res . fin_block();
}


// http://doc.spip.org/@menu_motsdoc
function menu_motsdoc($row, $id_groupes_vus, $les_mots)
{
	$rand = rand(0,10000); # pour antifocus & ajax

	$id_groupe = $row['id_groupe'];

	$result = spip_query("SELECT id_mot, type, titre FROM spip_mots WHERE id_groupe =$id_groupe " . ($les_mots ? "AND id_mot NOT IN ($les_mots) " : '') .  "ORDER BY type, titre");

	$n = spip_num_rows($result);
	if (!$n) return '';

	$titre = textebrut(typo($row['titre']));
	$titre_groupe = entites_html($titre);
	$unseul = $row['unseul'] == 'oui';
	$obligatoire = $row['obligatoire']=='oui' AND !in_array($id_groupe, $id_groupes_vus);

	$res = '';
	$ancre = "valider_groupe_$id_groupe"; 

	// forcer le recalcul du noeud car on est en Ajax
	$jscript1 = "findObj_forcer('$ancre').style.visibility='visible';";
	$jscript2 = "if(!antifocus_mots['$rand-$id_groupe']){this.value='';antifocus_mots['$rand-$id_groupe']=true;}";
//alm on reduit de 50 à 10 la liste affichable
	if ($n > 10) {
		$jscript = "onfocus=\"$jscript1 $jscript2\"";

		if ($obligatoire)
			$res .= "<input type='text' name='cherche_mot' class='fondl' style='padding:0; margin:0; background-color:#E86519;' value=\"$titre_groupe\" size='14' $jscript />";
		else if ($unseul)
			$res .= "<input type='text' name='cherche_mot' class='fondl' style='padding:0; margin:0; background-color:#cccccc;' value=\"$titre_groupe\" size='14' $jscript />";
		else
			$res .= "<input type='text' name='cherche_mot'  class='fondl' style='padding:0; margin:0;' value=\"$titre_groupe\" size='14' $jscript />";

		$res .= "<input type='hidden' name='select_groupe'  value='$id_groupe' />";
		$res .= "<span class='visible_au_chargement' style='visibility:visible' id='$ancre'>";
		$res .= " <input type='submit' value='"._T('bouton_chercher')."' class='fondo' style='padding:0; margin:0;' />";
		$res .= "</span>"; 
	} else {

		$jscript = "onchange=\"$jscript1\"";
	  
		if ($obligatoire)
			$res .= "<select name='nouv_mot' size='1' style=' width: 120px; background-color:#E86519;' class='fondl' $jscript>";
		else if ($unseul)
			$res .= "<select name='nouv_mot' size='1' style=' width: 120px; background-color:#cccccc;' class='fondl' $jscript>";
		else
			$res .= "<select name='nouv_mot' size='1' style=' width: 120px; ' class='fondl' $jscript>";

		$res .= "\n<option value='x' style='font-variant: small-caps;'>$titre</option>";
		while($row = spip_fetch_array($result)) {
			$res .= "\n<option value='" .$row['id_mot'] .
				"'>&nbsp;&nbsp;&nbsp;" .
				textebrut(typo($row['titre'])) .
				"</option>";
		}
		$res .= "</select>";
		$res .= "<span class='visible_au_chargement' style='visibility:visible' id='$ancre'>";
		$res .= "\n&nbsp;<input type='submit' value='"._T('bouton_choisir')."' class='fondo' />";
		$res .= "</span>";
	}

	return $res;
}
?>
