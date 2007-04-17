<?php

include_spip('inc/texte');
include_spip('inc/date');
include_spip('inc/layer');
include_spip('inc/presentation');
include_spip('inc/minipres');
include_spip('inc/calendar');
include_spip('agenda_mes_fonctions');

function article_editable($id_article){
	return autoriser('modifier','article',$id_article);
}

function Agenda_afficher_date_evenement($date_debut, $date_fin, $horaire){
	$d = date("Y-m-d", $date_debut);
	$f = date("Y-m-d", $date_fin);
	$h = $horaire=='oui';
	$hd = $h?date("H:i", $date_debut):"";
	$hf = $h?date("H:i", $date_fin):"";
	$s = affdate_jourcourt($d) . " $hd";
	if ($d==$f)
	{ // meme jour
			if ($hd!=$hf) $s .= "-$hf";
	}
	else if ((date("Y-m",$date_debut))==date("Y-m",$date_fin))
	{ // meme annee et mois, jours differents
		$s = ($h?$s."<br/>"._T('agenda:evenement_date_au'):jour($d)." ".strtolower(_T('agenda:evenement_date_au')))
			. affdate_jourcourt($f)." $hf";
	}
	else if ((date("Y",$date_debut))==date("Y",$date_fin))
	{ // meme annee, mois et jours differents
		if ($h)
			$s .= " ".date("H:i",$date_debut);
		$s .= "<br/>"._T('agenda:evenement_date_au').affdate_jourcourt($f);
		if ($h)
			$s .= " ".date("H:i",$date_fin);
	}
	else
	{ // tout different
		$s = affdate($d);
		if ($h)
			$s .= " ".date("(H:i)",$date_debut);
		$s .= "<br/>"._T('agenda:evenement_date_au').affdate($f);
		if ($h)
			$s .= " ".date("(H:i)",$date_fin);
	}
	return $s;
}

function Agenda_formulaire_article_afficher_evenements($id_article, $flag_editable, $script)
{
	global $connect_statut, $options,$connect_id_auteur;
	$out = "";

	$les_evenements = array();

	$result = spip_query( "SELECT * FROM spip_evenements AS evenements "
	. "WHERE evenements.id_article="._q($id_article)
	. " AND evenements.id_evenement_source=0"
	. " GROUP BY evenements.id_evenement ORDER BY evenements.date_debut");

	if (spip_num_rows($result)) {
		$out .= "<div class='liste liste-evenements'>";
		$out .= "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>";
		$table = array();
		while ($row = spip_fetch_array($result,SPIP_ASSOC)) {
			$vals = array();
			$id_evenement = $row['id_evenement'];
			$titre = typo($row['titre']);
			$descriptif = typo($row['descriptif']);
			$horaire = $row['horaire'];
			$date_debut = strtotime($row['date_debut']);
			$date_fin = strtotime($row['date_fin']);
			$id_evenement_source = $row['id_evenement_source'];
			$repetition = ($id_evenement_source!=0);

			$les_evenements[] = $id_evenement;

			$s = "<a href='".generer_url_ecrire('calendrier',"id_evenement=$id_evenement&ajouter_id_article=$id_article")."'>";
			$s .= http_img_pack("../"._DIR_PLUGIN_AGENDA."/img_pack/agenda-12.png",'', "border='0'", _T('agenda:titre_sur_l_agenda'));
			$s .= "</a>";
			$vals[] = $s;

			$s = Agenda_afficher_date_evenement($date_debut,$date_fin, $horaire);
			$s_rep = "";
			$count_rep = 0;
			$res2 = spip_query("SELECT * FROM spip_evenements WHERE id_evenement_source="._q($id_evenement)." ORDER BY date_debut");
			while ($row2 = spip_fetch_array($res2)){
				$s_rep .= Agenda_afficher_date_evenement(strtotime($row2['date_debut']),strtotime($row2['date_fin']),$row2['horaire'])."<br/>";
				$count_rep++;
			}
			if (strlen($s_rep)){
				$s .= "<br/>".bouton_block_invisible("repetitions_evenement_$id_evenement");
				if ($count_rep>1) $s .= _T('agenda:nb_repetitions', array('nb' => $count_rep));
					else $s .= _T('agenda:une_repetition');
				$s .= debut_block_invisible("repetitions_evenement_$id_evenement");
				$s .= $s_rep;
				$s .= fin_block();
			}

			$vals[] = $s;

			if ($flag_editable) {
				$s = ajax_action_auteur('editer_evenement', "$id_article-editer-$id_evenement", $script, "id_article=$id_article&id_evenement=$id_evenement&edit=oui", array($titre ? $titre : '<em>('._T('info_sans_titre').')</em>',''),'','wc_init');
				$vals[] = $s;
			}
			else{
				$vals[] = $titre;
			}
			$vals[] = propre($descriptif);

			if ($flag_editable) {
				$vals[] = ajax_action_auteur('editer_evenement', "$id_article-supprimer-$id_evenement", $script, "id_article=$id_article", array(_T('agenda:lien_retirer_evenement')."&nbsp;". http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' align='middle'"),''),"&id_article=$id_article&supp_evenement=$id_evenement",'wc_init');
			} else {
				$vals[] = "";
			}

			$table[] = $vals;
		}

		$largeurs = array('', '', '', '', '');
		$styles = array('arial11', 'arial11', 'arial2', 'arial11', 'arial11');
		$out .= afficher_liste($largeurs, $table, $styles);

		$out .= "</table></div>\n";

		$les_evenements = join(',', $les_evenements);
	}
	return array($out,$les_evenements) ;
}


//
// Liste des evenements agenda de l'article
//

function Agenda_formulaire_article_ajouter_evenement($id_article, $les_evenements, $flag_editable, $script){
  global $spip_lang_left, $spip_lang_right, $options;
	global $connect_statut, $options,$connect_id_auteur, $couleur_claire ;
	$id_evenement = intval(_request('id_evenement'));
	$edit = _request('edit') && in_array($id_evenement,explode(",",$les_evenements));
	$saisie_rapide = _request('saisie_rapide')!==NULL;
	$deplie = $saisie_rapide || $edit || _request('neweven');

	$out = "";
	$out .= "<div style='clear: both;'></div>";
	if ($flag_editable){
		if ($deplie)
			$out .=  debut_block_visible("evenementsarticle");
		else
			$out .=  debut_block_invisible("evenementsarticle");

		$out .=  "<div style='width:100%;'>";
		$out .=  "<table width='100%'>";
		$out .=  "<tr>";
		$out .=  "<td>";

		if ($edit){
		} else {
		}

		$bouton_ajout = false;
		if ($edit){
			$out .=  "<span class='verdana1'><strong>"._T('agenda:titre_cadre_modifier_evenement')."&nbsp; </strong></span>\n";
			$form =  "<input type='hidden' name='id_article' value='$id_article' />";
			$form .= Agenda_formulaire_edition_evenement($id_evenement, false);
			$bouton_ajout = true;
			$out .= ajax_action_auteur('editer_evenement',"$id_article-modifier-$id_evenement", $script, "id_article=$id_article&edit=1", $form,'','wc_init');
		}
		else{
			if ($saisie_rapide){
				$out .=  "<span class='verdana1'><strong>"._T('saisierapide:titre_cadre_ajouter_liste_evenement')."&nbsp; </strong></span>\n";
				include_spip('inc/agenda_saisie_rapide');
				$form .= Agenda_formulaire_saisie_rapide_previsu();
				if (strlen($form)){
					$form .=  "<input type='hidden' name='id_article' value='$id_article' />";
					$out .= ajax_action_auteur('editer_evenement',"$id_article-saisierapidecreer-0", $script, "id_article=$id_article&saisie_rapide=1", $form);
				}
				$form =  "<input type='hidden' name='id_article' value='$id_article' />";
				$form .= Agenda_formulaire_saisie_rapide();
				$out .= ajax_action_auteur('editer_evenement',"$id_article-saisierapidecompiler-0", $script, "id_article=$id_article&saisie_rapide=1", $form);
				$bouton_ajout = true;
			}
			else {
				$out .=  "<span class='verdana1'><strong>"._T('agenda:titre_cadre_ajouter_evenement')."&nbsp; </strong></span>\n";
				// recuperer le titre de l'article pour le mettre par defaut sur l'evenement
				$titre_defaut = "";
				$res = spip_query("SELECT titre FROM spip_articles where id_article="._q($id_article));
				if ($row = spip_fetch_array($res))
					$titre_defaut = $row['titre'];

				$form = "<input type='hidden' name='id_article' value='$id_article' />";
				$form .= Agenda_formulaire_edition_evenement(NULL, true, '', $titre_defaut);
				$id_evenement = 0;
				$out .= ajax_action_auteur('editer_evenement',"$id_article-modifier-$id_evenement", $script, "id_article=$id_article&edit=1", $form,'','wc_init');
			}
		}

		$out .= "</div>";
		$out .=  "</td></tr></table>";
		$out .= "<div style='clear: both;'></div>";

		if ($bouton_ajout)
			$out .= ajax_action_auteur('editer_evenement',"$id_article-creer-0", $script, "id_article=$id_article&neweven=1", array(http_img_pack(_DIR_PLUGIN_AGENDA."/img_pack/agenda-24.png", _T("agenda:icone_creer_evenement"), "width='24' height='24' border='0' align='middle'")."&nbsp;"._T("agenda:icone_creer_evenement"),''),'','wc_init')
				. "&nbsp;";
		if (!$saisie_rapide)
			$out .= ajax_action_auteur('editer_evenement',"$id_article-creer-0", $script, "id_article=$id_article&saisie_rapide=1", array(http_img_pack(_DIR_PLUGIN_AGENDA."/img_pack/agenda-24.png", _T("saisierapide:icone_saisie_rapide"), "width='24' height='24' border='0' align='middle'")."&nbsp;"._T("saisierapide:icone_saisie_rapide"),''));

		$out .= "</div>";
		$out .=  fin_block();
	}
	return $out;
}

function Agenda_formulaire_article($id_article, $flag_editable, $script){

  global $spip_lang_left, $spip_lang_right, $options;
	global $connect_statut, $options,$connect_id_auteur, $couleur_claire ;

	$out = "<div id='editer_evenement-$id_article'>";
	$out .= "<a name='agenda'></a>";
	if ($flag_editable) {
		//$out .= Agenda_action_formulaire_article($id_article);
		if (_request('edit')||_request('neweven'))
			$bouton = bouton_block_visible("evenementsarticle");
		else
			$bouton = bouton_block_invisible("evenementsarticle");
	}

	$out .= debut_cadre_enfonce("../"._DIR_PLUGIN_AGENDA."/img_pack/agenda-24.png", true, "", $bouton._T('agenda:texte_agenda')
	." <a href='".generer_url_ecrire('calendrier',"ajouter_id_article=$id_article")."'>"._T('icone_calendrier')."</a>");

	//
	// Afficher les evenements
	//

	list($s,$les_evenements) = Agenda_formulaire_article_afficher_evenements($id_article, $flag_editable, $script);
	$out .= $s;
	//
	// Ajouter un evenements
	//

	if ($flag_editable)
		$out .= Agenda_formulaire_article_ajouter_evenement($id_article, $les_evenements, $flag_editable, $script);


	$out .= fin_cadre_enfonce(true);
	$out .= "</div>";
	return $out;
}

function Agenda_formulaire_edition_evenement($id_evenement, $neweven, $ndate="", $titre_defaut=""){
	global $spip_lang_right;
	$out = "";

	// inits
	$ftitre=$titre_defaut;
	$flieu='';
	$fdescriptif='';
	$fstdatedeb=time();
	$fhoraire = 'oui';
	if (($neweven)&&($ndate)){
		$newdate=urldecode($ndate);
		$test=strtotime($newdate);
		if ($test>0)
			$fstdatedeb=$test;
	}
	$fstdatefin=$fstdatedeb+60*60;

	if ($id_evenement!=NULL){
		$res = spip_query("SELECT evenements.* FROM spip_evenements AS evenements WHERE evenements.id_evenement="._q($id_evenement));
		if ($row = spip_fetch_array($res)){
			if (!$neweven){
				$fid_evenement=$row['id_evenement'];
				$ftitre=entites_html($row['titre']);
				$flieu=entites_html($row['lieu']);
				$fhoraire=entites_html($row['horaire']);
				$fdescriptif=entites_html($row['descriptif']);
				$fstdatedeb=strtotime($row['date_debut']);
				$fstdatefin=strtotime($row['date_fin']);
			}
	 	}
	}

	$url=self();
	$url=parametre_url($url,'edit','');
	$url=parametre_url($url,'neweven','');
	$url=parametre_url($url,'ndate','');
	$url=parametre_url($url,'id_evenement','');

	$out .= "<div class='agenda-visu-evenement'>";

	$ajouter_id_article = _request('ajouter_id_article');
	if ($ajouter_id_article && !_request('id_article')){
		$res2 = spip_query("SELECT * FROM spip_articles AS articles WHERE id_article="._q($ajouter_id_article));
		if ($row2 = spip_fetch_array($res2)){
			$out .= "<div class='article-evenement'>";
			$out .= "<a href='".generer_url_ecrire('articles',"id_article=".$row2['id_article'])."'>";
			$out .= http_img_pack("article-24.gif", "", "width='24' height='24' border='0'");
			$out .= entites_html($row2['titre'])."</a>";
			$out .= "</div>\n";
		}
	}

	$out .= "<div class='agenda-visu-evenement-bouton-fermer'>";
	$out .= "<a href='$url' onclick=\"$('#voir_evenement-0').html('');return false;\">";
	$out .= "<img src='"._DIR_PLUGIN_AGENDA."/img_pack/croix.png' width='12' height='12' style='border:none;'></a>";
	$out .= "</div>\n";

	if (!$neweven){
	  $out .=  "<input type='hidden' name='id_evenement' value='$fid_evenement' />\n";
	  $out .=  "<input type='hidden' name='evenement_modif' value='1' />\n";
	}
	else {
	  $out .=  "<input type='hidden' name='evenement_insert' value='1' />\n";
	}

	// TITRE
	$out .=  "<div class='titre-titre'>"._T('agenda:evenement_titre')."</div>\n";
	$out .=  "<div class='titre-visu'>";
	$out .=  "<input type='text' name='evenement_titre' value=\"$ftitre\" style='width:100%;' />";
	$out .=  "</div>\n";

	// LIEU
	$out .=  "<div class='lieu-titre'>"._T('agenda:evenement_lieu')."</div>";
	$out .=  "<div class='lieu-visu'>";
	$out .=  "<input type='text' name='evenement_lieu' value=\"$flieu\" style='width:100%;' />";
	$out .=  "</div>\n";

	// Horaire
	$out .=  "<div class='horaire-titre'>";
	$out .=  "<input type='checkbox' name='evenement_horaire' value='oui' ";
	$out .= ($fhoraire=='oui'?"checked='checked' ":"");
	$out .= " onClick=\"var element =  findObj('evenement_horaire');var choix = element.checked;
	if (choix==true){	setvisibility('afficher_horaire_debut_evenement', 'visible');setvisibility('afficher_horaire_fin_evenement', 'visible');}
	else{setvisibility('afficher_horaire_debut_evenement', 'hidden');setvisibility('afficher_horaire_fin_evenement', 'hidden');}\"";
	$out .= "/>";
	$out .= _T('agenda:evenement_horaire')."</div>";

	// DATES
	$out .=  "<div class='date-titre'>"._T('agenda:evenement_date')."</div>";
	$out .=  "<div class='date-visu'>";
	$out .=  _T('agenda:evenement_date_de');
	$out .= WCalendar_controller($d=date('Y-m-d H:i:s',$fstdatedeb),"_evenement_debut");
	$out .= "<span class='agenda_".($fhoraire=='oui'?"":"in")."visible_au_chargement' id='afficher_horaire_debut_evenement'>";
	$out .=  _T('agenda:evenement_date_a_immediat');
	$out .= Agenda_heure_selector($d,"_debut");
	$out .=	"</span>";
	$out .=  "<br/>";
	$out .=  _T('agenda:evenement_date_au');
	$out .= WCalendar_controller($d=date('Y-m-d H:i:s',$fstdatefin),"_evenement_fin");
	$out .= "<span class='agenda_".($fhoraire=='oui'?"":"in")."visible_au_chargement' id='afficher_horaire_fin_evenement'>";
	$out .=  _T('agenda:evenement_date_a_immediat');
	$out .= Agenda_heure_selector($d,"_fin");
	$out .=	"</span>";
	$out .=  "</div>\n";

	// DESCRIPTIF
	$out .=  "<div class='descriptif-titre'>"._T('agenda:evenement_descriptif')."</div>";
	$out .=  "<div class='descriptif-visu'>";
	$out .=  "<textarea name='evenement_descriptif' style='width:100%;' rows='3'>";
	$out .=  $fdescriptif;
	$out .=  "</textarea>\n";
	$out .=  "</div>\n";

	// MOTS CLES : chaque groupe de mot cle attribuable a un evenement agenda
	// donne un select
	$out .=  "<div class='agenda_mots_cles'>";
	$res = spip_query("SELECT * FROM spip_groupes_mots WHERE evenements='oui' ORDER BY titre");
	while ($row = spip_fetch_array($res,SPIP_ASSOC)){
		$id_groupe = $row['id_groupe'];
		$multiple = ($row['unseul']=='oui')?"size='4'":"multiple='multiple' size='4'";

		$id_mot_select = array();
		if ($id_evenement){
			$res2 = spip_query("SELECT mots_evenements.id_mot FROM spip_mots_evenements AS mots_evenements
								LEFT JOIN spip_mots AS mots ON mots.id_mot=mots_evenements.id_mot
								WHERE mots.id_groupe="._q($id_groupe)." AND mots_evenements.id_evenement="._q($id_evenement));
			while ($row2 = spip_fetch_array($res2))
				$id_mot_select[] = $row2['id_mot'];
		}

		$nb_mots = 0;
		$select = "";
		$select .= "<select name='evenement_groupe_mot_select_{$id_groupe}[]' class='fondl verdana1 agenda_mot_cle_select' $multiple>\n";
		$select .= "\n<option value='x' style='font-variant: small-caps;' >".supprimer_numero($row['titre'])."</option>";

		$res2= spip_query("SELECT * FROM spip_mots WHERE id_groupe="._q($id_groupe)." ORDER BY titre");
		while ($row2 = spip_fetch_array($res2,SPIP_ASSOC)){
			$id_mot = $row2['id_mot'];
			$titre = $row2['titre'];
			$select .= my_sel($id_mot, "&nbsp;&nbsp;&nbsp;$titre", in_array($id_mot,$id_mot_select)?$id_mot:0);
			$nb_mots++;
		}
		$select .= "</select>\n";
		if ($nb_mots)
			$out .= $select;
	}
	$out .=  "</div>";

	$dates = "";
	if ($id_evenement!=NULL){
		$dates = array();
		$res = spip_query("SELECT date_debut FROM spip_evenements WHERE id_evenement_source="._q($id_evenement));
		while ($row=spip_fetch_array($res)){
			$dates[] = date('m/d/Y',strtotime($row['date_debut']));
		}
		$dates = implode(",",$dates);
	}
	$out .= "<div class='repetitions-calendrier'>";
	$out .= WCalendar_statique_point_entree('_repetitions',$dates);
	$out .= "</div>";

  $out .=  "<div class='edition-bouton'>";
  #echo "<input type='submit' name='submit' value='Annuler' />";
	if ($neweven==1){
		$out .=	"<div style='text-align:$spip_lang_right'><input type='submit' name='ajouter' value='"._T('bouton_ajouter')."' class='fondo' onclick='javascript:getSelectedDate_repetitions()'></div>";
	}
	else{
		$out .=	"<div style='text-align:$spip_lang_right'><input type='submit' name='ajouter' value='"._T('bouton_enregistrer')."' class='fondo' onclick='javascript:getSelectedDate_repetitions()'></div>";
	}
	$out .=  "</div>\n";

	// feature desactivee pour le moment
	// $out .= "<script type='text/javascript' src='"._DIR_PLUGIN_AGENDA."/img_pack/multiselect.js'></script>";

  $out .=  "</div>";
	$out .=  "</div>\n";
	return $out;
}


?>
