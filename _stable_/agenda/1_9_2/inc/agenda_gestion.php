<?php

include_spip('inc/texte');
include_spip('inc/date');
include_spip('inc/layer');
include_spip('inc/presentation');
include_spip('inc/minipres');
include_spip('inc/calendar');
include_spip('agenda_mes_fonctions');

function Agenda_install(){
	Agenda_verifier_base();
}

function Agenda_uninstall(){
	include_spip('base/agenda_evenements');
	include_spip('base/abstract_sql');

	// suppression du champ evenements a la table spip_groupe_mots
	spip_query("ALTER TABLE `spip_groupes_mots` DROP `evenements`");
	
}

function Agenda_verifier_base(){
	$version_base = 0.12;
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta']['agenda_base_version']) )
			|| (($current_version = $GLOBALS['meta']['agenda_base_version'])!=$version_base)){
		include_spip('base/agenda_evenements');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			// ajout du champ evenements a la table spip_groupe_mots
			// si pas deja existant
			$desc = spip_abstract_showtable("spip_groupes_mots", '', true);
			if (!isset($desc['field']['evenements'])){
				spip_query("ALTER TABLE spip_groupes_mots ADD `evenements` VARCHAR(3) NOT NULL AFTER `syndic`");
			}
			ecrire_meta('agenda_base_version',$current_version=$version_base);
		}
		if ($current_version<0.11){
			spip_query("ALTER TABLE spip_evenements ADD `horaire` ENUM('oui','non') DEFAULT 'oui' NOT NULL AFTER `lieu`");
			ecrire_meta('agenda_base_version',$current_version=0.11);
		}
		if ($current_version<0.12){
			spip_query("ALTER TABLE spip_evenements ADD `id_article` bigint(21) DEFAULT '0' NOT NULL AFTER `id_evenement`");
			spip_query("ALTER TABLE spip_evenements ADD INDEX ( `id_article` )");
			$res = spip_query ("SELECT * FROM spip_evenements_articles");
			while ($row = spip_fetch_array($res)){
				$id_article = $row['id_article'];
				$id_evenement = $row['id_evenement'];
				spip_query("UPDATE spip_evenements SET id_article=$id_article WHERE id_evenement=$id_evenement");
			}
			spip_query("DROP TABLE spip_evenements_articles");
			ecrire_meta('agenda_base_version',$current_version=0.12);
		}
		
		ecrire_metas();
	}
	
	if (isset($GLOBALS['meta']['INDEX_elements_objet'])){
		$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
		if (!isset($INDEX_elements_objet['spip_evenements'])){
			$INDEX_elements_objet['spip_evenements'] = array('titre'=>8,'descriptif'=>4,'lieu'=>3);
			ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
			ecrire_metas();
		}
	}
	if (isset($GLOBALS['meta']['INDEX_objet_associes'])){
		$INDEX_objet_associes = unserialize($GLOBALS['meta']['INDEX_objet_associes']);
		if (!isset($INDEX_objet_associes['spip_articles']['spip_evenements'])){
			$INDEX_objet_associes['spip_articles']['spip_evenements'] = 1;
			ecrire_meta('INDEX_objet_associes',serialize($INDEX_objet_associes));
			ecrire_metas();
		}
	}
	if (isset($GLOBALS['meta']['INDEX_elements_associes'])){
		$INDEX_elements_associes = unserialize($GLOBALS['meta']['INDEX_elements_associes']);
		if (!isset($INDEX_elements_associes['spip_evenements'])){
			$INDEX_elements_associes['spip_evenements'] = array('titre'=>2,'descriptif'=>1);
			ecrire_meta('INDEX_elements_associes',serialize($INDEX_elements_associes));
			ecrire_metas();
		}
	}
}

function article_editable($id_article){
	$flag_editable = false;
	global $connect_id_auteur, $id_secteur; 

 	$id_parent = intval($id_parent);
 	if (!($id_article=intval($id_article)))
 		return false;

	if ($row = spip_fetch_array(spip_query("SELECT statut, titre, id_rubrique FROM spip_articles WHERE id_article="._q($id_article)))) {
		$statut_article = $row['statut'];
		$titre_article = $row['titre'];
		$id_rubrique = $row['id_rubrique'];
		$statut_rubrique = acces_rubrique($id_rubrique);
		if ($titre_article=='') $titre_article = _T('info_sans_titre');
	}
	else {
		$statut_article = '';
		$statut_rubrique = false;
		$id_rubrique = '0';
		if ($titre=='') $titre = _T('info_sans_titre');
	}

	$flag_auteur = spip_num_rows(spip_query("SELECT id_auteur FROM spip_auteurs_articles WHERE id_article="._q($id_article)." AND id_auteur="._q($connect_id_auteur)." LIMIT 1"));

	$ok_nouveau_statut = false;
	$flag_editable = ($statut_rubrique
		OR ($flag_auteur
			AND ($statut_article == 'prepa'
				OR $statut_article == 'prop' 
				OR $statut_article == 'poubelle')));
	return $flag_editable;
}

function Agenda_afficher_date_evenement($date_debut, $date_fin, $horaire){
	$s = "";
	if (($d=date("Y-m-d",$date_debut))==date("Y-m-d",$date_fin))
	{ // meme jour
		$s = affdate_jourcourt($d);
		if ($horaire=='oui'){
			$s .= " ".($hd=date("H:i",$date_debut));
			if ($hd!=($hf=date("H:i",$date_fin)))
				$s .= "-$hf";
		}
	}
	else if ((date("Y-m",$date_debut))==date("Y-m",$date_fin))
	{ // meme annee et mois, jours differents
		$d=date("Y-m-d",$date_debut);
		$s = affdate_jourcourt($d);
		if ($horaire=='oui')
			$s .= " ".($hd=date("H:i",$date_debut));
		$s .= "<br/>"._T('agenda:evenement_date_au').date(($horaire=='oui')?"d  H:i ":"d ",$date_fin);
	}
	else if ((date("Y",$date_debut))==date("Y",$date_fin))
	{ // meme annee, mois et jours differents
		$d=date("Y-m-d",$date_debut);
		$s = affdate_jourcourt($d);
		if ($horaire=='oui')
			$s .= " ".date("H:i",$date_debut);
		$d = date("Y-m-d",$date_fin);
		$s .= "<br/>"._T('agenda:evenement_date_au').affdate_jourcourt($d);
		if ($horaire=='oui')
			$s .= " ".date("H:i",$date_fin);
	}
	else
	{ // tout different
		$s = affdate($d);
		if ($horaire=='oui')
			$s .= " ".date("(H:i)",$date_debut);
		$d = date("Y-m-d",$date_fin);
		$s .= "<br/>"._T('agenda:evenement_date_au').affdate($d);
		if ($horaire=='oui')
			$s .= " ".date("(H:i)",$date_fin);
	}
	return $s;	
}

function Agenda_formulaire_article_afficher_evenements($id_article, $flag_editable)
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
				$s .= "$count_rep ". _T('agenda:evenement_repetitions');
				$s .= debut_block_invisible("repetitions_evenement_$id_evenement");
				$s .= $s_rep;
				$s .= fin_block();
			}

			$vals[] = $s;

			
			if ($flag_editable) {
				$url = self();
				$url = parametre_url($url,'id_article',$id_article);
				$url = parametre_url($url,'id_evenement',$id_evenement);
				$url = parametre_url($url,'edit',1);
				$s = "<a href='$url'>".($titre ? $titre : '<em>('._T('info_sans_titre').')</em>')."</a>";
				$vals[] = $s;
			}
			else{
				$vals[] = $titre;
			}
			$vals[] = propre($descriptif);
		
			if ($flag_editable) {
				$vals[] =  "<a href='" . generer_url_ecrire("articles","id_article=$id_article&supp_evenement=$id_evenement#agenda") . "'>"._T('agenda:lien_retirer_evenement')."&nbsp;". http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' align='middle'") . "</a>";
			} else {
				$vals[] = "";
			}
			
			$table[] = $vals;
		}
	
		$largeurs = array('', '', '', '', '');
		$styles = array('arial11', 'arial11', 'arial2', 'arial11', 'arial11');
		$out .= afficher_liste($largeurs, $table, $styles, false);
	
		$out .= "</table></div>\n";
	
		$les_evenements = join(',', $les_evenements);
	}
	return array($out,$les_evenements) ;
}


//
// Liste des evenements agenda de l'article
//

function Agenda_formulaire_article_ajouter_evenement($id_article, $les_evenements, $flag_editable){
  global $spip_lang_left, $spip_lang_right, $options;
	global $connect_statut, $options,$connect_id_auteur, $couleur_claire ;
	$id_evenement = intval(_request('id_evenement'));
	$edit = _request('edit');

	$out = "";
	$out .= "<div style='clear: both;'></div>";
	if ($flag_editable){
		if ((in_array($id_evenement,explode(",",$les_evenements)) && $edit==1)||_request('neweven'))
			$out .=  debut_block_visible("evenementsarticle");
		else
			$out .=  debut_block_invisible("evenementsarticle");
		
		$out .=  "<div style='width:100%;'>";
		$out .=  "<table width='100%'>";
		$out .=  "<tr>";
		$out .=  "<td>";
	
		if (in_array($id_evenement,explode(",",$les_evenements)) && $edit==1){
			$out .=  "<span class='verdana1'><strong>"._T('agenda:titre_cadre_modifier_evenement')."&nbsp; </strong></span>\n";
		} else {
			$out .=  "<span class='verdana1'><strong>"._T('agenda:titre_cadre_ajouter_evenement')."&nbsp; </strong></span>\n";
		}
		$out .=  "<div><input type='hidden' name='id_article' value=\"$id_article\">";

		$bouton_ajout = false;
		if (in_array($id_evenement,explode(",",$les_evenements)) && $edit==1){
			$form .= Agenda_formulaire_edition_evenement($id_evenement, false);
			$bouton_ajout = true;
		}
		else{
			// recuperer le titre de l'article pour le mettre par defaut sur l'evenement
			$titre_defaut = "";
			$res = spip_query("SELECT titre FROM spip_articles where id_article="._q($id_article));
			if ($row = spip_fetch_array($res))
				$titre_defaut = $row['titre'];
			
			$form .= Agenda_formulaire_edition_evenement(NULL, true, '', $titre_defaut);
			$id_evenement = 0;
		}
		$out .= ajax_action_auteur('editer_evenement',"$id_article-$id_evenement",'articles', "id_article=$id_article", $form);
			
		$out .= "</div>";
		$out .=  "</td></tr></table>";
		$out .= "<div style='clear: both;'></div>";

		if ($bouton_ajout){
			$url = parametre_url(self(),'edit','');
			$url = parametre_url($url,'neweven','1');
			$url = parametre_url($url,'id_evenement','');
			$out .= icone_horizontale(_T("agenda:icone_creer_evenement"),$url , "../"._DIR_PLUGIN_AGENDA."/img_pack/agenda-24.png", "creer.gif",false);
		}

		$out .= "</div>";
		$out .=  fin_block();
	}
	return $out;
}

function Agenda_formulaire_article($id_article, $flag_editable){

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

	list($s,$les_evenements) = Agenda_formulaire_article_afficher_evenements($id_article, $flag_editable);
	$out .= $s;
	//
	// Ajouter un evenements
	//

	if ($flag_editable)
		$out .= Agenda_formulaire_article_ajouter_evenement($id_article, $les_evenements, $flag_editable);


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
  $out .=	"<a href='$url'><img src='"._DIR_PLUGIN_AGENDA."/img_pack/croix.png' width='12' height='12' style='border:none;'></a>";
  $out .= "</div>\n";
  //$out .=  "<form name='edition_evenement' action='$url' method='post'>";
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

	//$out .=  "</form>";
	$out .=  "</div>\n";
	return $out;
}


?>
