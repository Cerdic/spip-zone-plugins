<?php
include_spip('inc/date');

function Agenda_install(){
	Agenda_verifier_base();
}

function Agenda_uninstall(){
	include_spip('base/agenda_evenements');
	include_spip('base/abstract_sql');

	// suppression du champ evenements a la table spip_groupe_mots
	$query = "ALTER TABLE `spip_groupes_mots` DROP `evenements`";
	spip_query($query);
	
}

function Agenda_verifier_base(){
	$version_base = 0.11;
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
			$desc = spip_abstract_showtable("spip_groupes_mots");
			if (!isset($desc['field']['evenements'])){
				$query = "ALTER TABLE `spip_groupes_mots` ADD `evenements` VARCHAR(3) NOT NULL AFTER `syndic`";
				spip_query($query);
			}
			$current_version = $version_base;
		}
		if ($current_version<0.11){
			$query = "ALTER TABLE `spip_evenements` ADD `horaire` ENUM('oui','non') DEFAULT 'oui' NOT NULL AFTER `lieu`";
			spip_query($query);
		}
		
		ecrire_meta('agenda_base_version',$version_base);
		ecrire_metas();
	}
}


function Agenda_formulaire_article_afficher_evenements($id_article, $flag_editable)
{
	global $connect_statut, $options,$connect_id_auteur;

	$les_evenements = array();

	$query = "SELECT * FROM spip_evenements AS evenements, spip_evenements_articles AS lien ".
	"WHERE evenements.id_evenement=lien.id_evenement AND lien.id_article=$id_article ".
	"GROUP BY evenements.id_evenement ORDER BY evenements.date_debut";
	$result = spip_query($query);

	if (spip_num_rows($result)) {
		echo "<div class='liste'>";
		echo "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>";
		$table = array();
		while ($row = spip_fetch_array($result,SPIP_ASSOC)) {
			$vals = array();
			$id_evenement = $row['id_evenement'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
			$horaire = $row['horaire'];
			$date_debut = strtotime($row['date_debut']);
			$date_fin = strtotime($row['date_fin']);
			$id_evenement_source = $row['id_evenement_source'];
			$repetition = ($id_evenement_source!=0);
			
			$les_evenements[] = $id_evenement;

			$s = "<a href='".generer_url_ecrire('calendrier',"id_evenement=$id_evenement&ajouter_id_article=$id_article")."'>";
			$s .= http_img_pack("../"._DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/agenda-12.png",'', "border='0'", _T('agenda:titre_sur_l_agenda'));
			$s .= "</a>";
			$vals[] = $s;

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
			$vals[] = $s;

			
			if ($flag_editable) {
				$url = self();
				$url = parametre_url($url,'id_article',$id_article);
				$url = parametre_url($url,'id_rubrique',$id_rubrique);
				$url = parametre_url($url,'id_evenement',$id_evenement);
				$url = parametre_url($url,'edit',1);
				$s = "<a href='$url'>$titre</a>";
				$vals[] = $s;
			}
			else{
				$vals[] = $titre;
			}
			$vals[] = $descriptif;
		
			if ($flag_editable) {
				$vals[] =  "<a href='" . generer_url_ecrire("articles","id_article=$id_article&id_rubrique=$id_rubrique&supp_evenement=$id_evenement#agenda") . "'>"._T('agenda:lien_retirer_evenement')."&nbsp;". http_img_pack('croix-rouge.gif', "X", "width='7' height='7' border='0' align='middle'") . "</a>";
			} else {
				$vals[] = "";
			}
			
			$table[] = $vals;
		}
	
		$largeurs = array('', '', '', '', '');
		$styles = array('arial11', 'arial11', 'arial2', 'arial11', 'arial11');
		afficher_liste($largeurs, $table, $styles);
	
		echo "</table></div>\n";
	
		$les_evenements = join(',', $les_evenements);
	}
	return $les_evenements ;
}


//
// Liste des evenements agenda de l'article
//

function Agenda_formulaire_article_ajouter_evenement($id_article, $id_rubrique, $les_evenements, $flag_editable){
  global $spip_lang_left, $spip_lang_right, $options;
	global $connect_statut, $options,$connect_id_auteur, $couleur_claire ;
	$id_evenement = intval(_request('id_evenement'));
	$edit = _request('edit');

	$out = "";
	if ($flag_editable){
		if ((in_array($id_evenement,explode(",",$les_evenements)) && $edit==1)||_request('neweven'))
			$out .=  debut_block_visible("evenementsarticle");
		else
			$out .=  debut_block_invisible("evenementsarticle");
		
		$out .=  "<table width='100%'>";
		$out .=  "<tr>";
		$out .=  "<td>";
	
		$out .=  generer_url_post_ecrire("articles", "id_article=$id_article&id_rubrique=$id_rubrique");
		$out .=  "<span class='verdana1'><strong>"._T('agenda:titre_cadre_ajouter_evenement')."&nbsp; </strong></span>\n";
		$out .=  "<div><input type='hidden' name='id_article' value=\"$id_article\">";

		if (in_array($id_evenement,explode(",",$les_evenements)) && $edit==1){
			$out .= Agenda_formulaire_edition_evenement($id_evenement, false);
			$out .= "<div style='clear: both;'></div>";
			$url = parametre_url(self(),'edit','');
			$url = parametre_url($url,'neweven','1');
			$url = parametre_url($url,'id_evenement','');
			$out .= icone_horizontale(_T("agenda:icone_creer_evenement"),$url , "../"._DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/agenda-24.png", "creer.gif",false);
		}
		else
			$out .= Agenda_formulaire_edition_evenement(NULL, true);

		$out .=  "</td></tr></table>";
		$out .=  fin_block();
	}
	return $out;
}

function Agenda_formulaire_article($id_article, $id_rubrique, $flag_editable){

  global $spip_lang_left, $spip_lang_right, $options;
	global $connect_statut, $options,$connect_id_auteur, $couleur_claire ;
	
	echo "<a name='agenda'></a>";
	if ($flag_editable) {
		Agenda_action_formulaire_article();
		if (_request('edit')||_request('neweven'))
			$bouton = bouton_block_visible("evenementsarticle");
		else
			$bouton = bouton_block_invisible("evenementsarticle");
	}

	debut_cadre_enfonce("../"._DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/agenda-24.png", false, "", $bouton._T('agenda:texte_agenda')
	." <a href='".generer_url_ecrire('calendrier',"ajouter_id_article=$id_article")."'>"._T('icone_calendrier')."</a>");

	//
	// Afficher les evenements
	//
	
	$les_evenements = Agenda_formulaire_article_afficher_evenements($id_article, $flag_editable);

	//
	// Ajouter un evenements
	//

	if ($flag_editable)
		echo Agenda_formulaire_article_ajouter_evenement($id_article, $id_rubrique, $les_evenements, $flag_editable);

	fin_cadre_enfonce(false);
}

function Agenda_action_formulaire_article(){
	include_spip('base/abstract_sql');
	// s'assurer que les tables sont crees
	Agenda_install();

	// gestion des requetes de mises à jour dans la base
	$id_evenement = intval(_request('id_evenement'));
	$insert = _request('evenement_insert');
	$modif = _request('evenement_modif');
	$supp_evenement = intval(_request('supp_evenement'));
	if ($insert || $modif){
		$id_article = intval(_request('id_article'));
		if (!$id_article){
			$id_article = _request('ajouter_id_article');
		}
	
		if ( ($insert) && (!$id_evenement) ){
			$id_evenement = spip_abstract_insert("spip_evenements",
				"(id_evenement_source,maj)",
				"('0',NOW())");
			if ($id_evenement==0){
				spip_log("agenda action formulaire article : impossible d'ajouter un evenement");
				return;
			}
	 	}
	 	if ($id_article){
			// mettre a jour le lien evenement-article
			$query="SELECT COUNT(*) FROM spip_evenements_articles WHERE id_article=$id_article AND id_evenement=$id_evenement";
			$res=spip_query($query);
			$row = spip_fetch_array($res,SPIP_NUM);
			$nblink = $row['0'];
			if ($nblink==0){
				spip_abstract_insert("spip_evenements_articles",
					"(id_evenement,id_article)",
					"($id_evenement,$id_article)");
			}
	 	}
		$titre = addslashes(_request('evenement_titre'));
		$descriptif = addslashes(_request('evenement_descriptif'));
		$lieu = addslashes(_request('evenement_lieu'));
		$horaire = addslashes(_request('evenement_horaire'));
		if ($horaire!='oui') $horaire='non';
	
		// pour les cas ou l'utilisateur a saisi 29-30-31 un mois ou ca n'existait pas
		$maxiter=4;
		$st_date_deb=FALSE;
		$jour_debut=_request('evenement_jour_debut');
		// test <= car retour strtotime retourne -1 ou FALSE en cas d'echec suivant les versions
		while(($st_date_deb<=FALSE)&&($maxiter-->0)) {
			$date_deb=_request('evenement_annee_debut')."-"._request('evenement_mois_debut')."-".($jour_debut--)." "._request('evenement_heure_debut').":"._request('evenement_minute_debut');
			$st_date_deb=strtotime($date_deb);
		}
		$date_deb=format_mysql_date(date("Y",$st_date_deb),date("m",$st_date_deb),date("d",$st_date_deb),date("H",$st_date_deb),date("i",$st_date_deb), $s=0);
	
		// pour les cas ou l'utilisateur a saisi 29-30-31 un mois ou ca n'existait pas
		$maxiter=4;
		$st_date_fin=FALSE;
		$jour_fin=_request('evenement_jour_fin');
		// test <= car retour strtotime retourne -1 ou FALSE en cas d'echec suivant les versions
		while(($st_date_fin<=FALSE)&&($maxiter-->0)) {
			$st_date_fin=_request('evenement_annee_fin')."-"._request('evenement_mois_fin')."-".($jour_fin--)." "._request('evenement_heure_fin').":"._request('evenement_minute_fin');
			$st_date_fin=strtotime($st_date_fin);
		}
		$st_date_fin = max($st_date_deb,$st_date_fin);
		$date_fin=format_mysql_date(date("Y",$st_date_fin),date("m",$st_date_fin),date("d",$st_date_fin),date("H",$st_date_fin),date("i",$st_date_fin), $s=0);
	
		// mettre a jour l'evenement
		$query="UPDATE `spip_evenements` SET `titre`='$titre',`descriptif`='$descriptif',`lieu`='$lieu',`horaire`='$horaire',`date_debut`='$date_deb',`date_fin`='$date_fin' WHERE `id_evenement` = '$id_evenement';";
		$res=spip_query($query);

		// les mots cles : 1 maxi par groupe uniquement
		$query = "SELECT * FROM spip_groupes_mots WHERE evenements='oui' ORDER BY titre";
		$res = spip_query($query);
		$liste_mots = array();
		while ($row = spip_fetch_array($res,SPIP_ASSOC)){
			$id_groupe = $row['id_groupe'];
			$id_mot = intval(_request("evenement_groupe_mot_select_$id_groupe"));
			if ($id_mot)
				$liste_mots[] = $id_mot;
				
		}
		// suppression des mots obsoletes
		$cond_in = "";
		if (count($liste_mots))
			$cond_in = "AND" . calcul_mysql_in('id_mot', implode(",",$liste_mots), 'NOT');
		spip_query("DELETE FROM spip_mots_evenements WHERE id_evenement=$id_evenement $cond_in");
		// ajout/maj des nouveaux mots
		foreach($liste_mots as $id_mot){
			if (!spip_fetch_array(spip_query("SELECT FROM spip_mots_evenements WHERE id_evenement=$id_evenement AND id_mot=$id_mot")))
				spip_query("INSERT INTO spip_mots_evenements (id_mot,id_evenement) VALUES ($id_mot,$id_evenement)");
		}
	}
	else if ($supp_evenement){
		$id_article = intval(_request('id_article'));
		if (!$id_article)
			$id_article = intval(_request('ajouter_id_article'));
		$res = spip_query("SELECT * FROM spip_evenements_articles WHERE id_article=$id_article AND id_evenement=$supp_evenement");
		if ($row = spip_fetch_array($res)){
			spip_query("DELETE FROM spip_evenements_articles WHERE id_article=$id_article AND id_evenement=$supp_evenement");
			spip_query("DELETE FROM spip_mots_evenements WHERE id_evenement=$supp_evenement");
			spip_query("DELETE FROM spip_evenements WHERE id_evenement=$supp_evenement");
		}
	}

}


function Agenda_formulaire_edition_evenement($id_evenement, $neweven, $ndate=""){
	global $spip_lang_right;
	$out = "";

	// inits
	$ftitre='';
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
		$query = "SELECT spip_evenements.* FROM spip_evenements WHERE spip_evenements.id_evenement='$id_evenement';";
		$res = spip_query($query);
		if ($row = spip_fetch_array($res)){
			if (!$neweven){
				$fid_evenement=$row['id_evenement'];
				$ftitre=attribut_html($row['titre']);
				$flieu=attribut_html($row['lieu']);
				$fhoraire=attribut_html($row['horaire']);
				$fdescriptif=attribut_html($row['descriptif']);
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
		$query = "SELECT * FROM spip_articles AS articles WHERE id_article=$ajouter_id_article";
		$res2 = spip_query($query);
		if ($row2 = spip_fetch_array($res2)){
			$out .= "<div class='article-evenement'>";
			$out .= "<a href='".generer_url_ecrire('articles',"id_article=".$row2['id_article'])."'>";
			$out .= http_img_pack("article-24.gif", "", "width='24' height='24' border='0'");
			$out .= entites_html($row2['titre'])."</a>";
			$out .= "</div>\n";
		}
	}
	
	$out .= "<div class='agenda-visu-evenement-bouton-fermer'>";
  $out .=	"<a href='$url'><img src='"._DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/croix.png' width='12' height='12' style='border:none;'></a>";
  $out .= "</div>\n";
  $out .=  "<form name='edition_evenement' action='$url' method='post'>";
  #$out .=  "<input type='hidden' name='redirect' value='$url' />\n";
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
	$ftitre=htmlentities($ftitre,ENT_QUOTES);
	$out .=  "<input type='text' name='evenement_titre' value='$ftitre' style='width:100%;' />";
	$out .=  "</div>\n";

	// LIEU
	$out .=  "<div class='lieu-titre'>"._T('agenda:evenement_lieu')."</div>";
	$out .=  "<div class='lieu-visu'>";
	$flieu=htmlentities($flieu,ENT_QUOTES);
	$out .=  "<input type='text' name='evenement_lieu' value='$flieu' style='width:100%;' />";
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
	$out .= Agenda_date_insert_js_calendar("_debut");
	$out .=  _T('agenda:evenement_date_de');
	$out .= Agenda_date_selector(date('Y-m-d',$fstdatedeb),"_debut");
	$out .= "<span class='agenda_".($fhoraire=='oui'?"":"in")."visible_au_chargement' id='afficher_horaire_debut_evenement'>";
	$out .=  _T('agenda:evenement_date_a');
	$out .= Agenda_heure_selector(date('H',$fstdatedeb),date('i',$fstdatedeb),"_debut");
	$out .=	"</span>";
	$out .=  "<br/>";
	$out .= Agenda_date_insert_js_calendar("_fin");
	$out .=  _T('agenda:evenement_date_au');
	$out .= Agenda_date_selector(date('Y-m-d',$fstdatefin),"_fin");
	$out .= "<span class='agenda_".($fhoraire=='oui'?"":"in")."visible_au_chargement' id='afficher_horaire_fin_evenement'>";
	$out .=  _T('agenda:evenement_date_a');
	$out .= Agenda_heure_selector(date('H',$fstdatefin),date('i',$fstdatefin),"_fin");
	$out .=	"</span>";
	$out .=  "</div>\n";
	
	// DESCRIPTIF
	$out .=  "<div class='descriptif-titre'>"._T('agenda:evenement_descriptif')."</div>";
	$out .=  "<div class='descriptif-visu'>";
	$out .=  "<textarea name='evenement_descriptif' style='width:100%;' rows='3'>";
	$out .=  htmlentities($fdescriptif,ENT_QUOTES);
	$out .=  "</textarea>\n";
	$out .=  "</div>\n";

	// MOTS CLES : chaque groupe de mot cle attribuable a un evenement agenda
	// donne un select
	$out .=  "<div class='agenda_mots_cles'>";
	$query = "SELECT * FROM spip_groupes_mots WHERE evenements='oui' ORDER BY titre";
	$res = spip_query($query);
	while ($row = spip_fetch_array($res,SPIP_ASSOC)){
		$id_groupe = $row['id_groupe'];
		$query = "SELECT mots_evenements.id_mot FROM spip_mots_evenements AS mots_evenements
							LEFT JOIN spip_mots AS mots ON mots.id_mot=mots_evenements.id_mot 
							WHERE mots.id_groupe=$id_groupe AND mots_evenements.id_evenement=$id_evenement";
		$row2 = spip_fetch_array(spip_query($query));
		$id_mot_select = 0;
		if ($row2)
			$id_mot_select = $row2['id_mot'];

		$out .= "<select name='evenement_groupe_mot_select_$id_groupe' class='fondl verdana1 agenda_mot_cle_select'>\n";
		$out .= "\n<option value='x' style='font-variant: small-caps;'>".supprimer_numero($row['titre'])."</option>";
		$query = "SELECT * FROM spip_mots WHERE id_groupe=$id_groupe ORDER BY titre";
		$res2= spip_query($query);
		while ($row2 = spip_fetch_array($res2,SPIP_ASSOC)){
			$id_mot = $row2['id_mot'];
			$titre = $row2['titre'];
			$out .= my_sel($id_mot, "&nbsp;&nbsp;&nbsp;$titre", $id_mot_select);
			/*$out .= "<option value='$id_mot'";
			if ($id_mot_select && $id_mot_select==$id_mot)
				$out .= " selected='selected'";
			$out .= ">&nbsp;&nbsp;&nbsp;$titre</option>\n";*/
		}
		$out .= "</select>\n";
	}
	$out .=  "</div>";
	$out.=  Agenda_date_insert_js_calendar_placeholder("_debut");
	$out.=  Agenda_date_insert_js_calendar_placeholder("_fin");
	
  $out .=  "<div class='edition-bouton'>";
  #echo "<input type='submit' name='submit' value='Annuler' />";
	if ($neweven==1){
		$out .=	"<div style='text-align:$spip_lang_right'><input type='submit' name='ajouter' value='"._T('bouton_ajouter')."' class='fondo'></div>";
	}
	else{
		$out .=	"<div style='text-align:$spip_lang_right'><input type='submit' name='ajouter' value='"._T('bouton_enregistrer')."' class='fondo'></div>";
	}
	$out .=  "</div>\n";


  $out .=  "</div>";

	$out .=  "</form>";
	$out .=  "</div>\n";
	return $out;
}

// Pre traitements -----------------------------------------------------------------------
function Agenda_date_insert_js_calendar_placeholder($suffixe){
	return "<div id='container$suffixe' style='position:absolute;display:none'></div>";
}
function Agenda_date_insert_js_calendar($suffixe){
	return "<script type='text/javascript'>window.onload = init;</script>
	<a href='javascript:void(null)' onclick='showCalendar$suffixe()'>
	<img id='dateLink$suffixe' src='"._DIR_IMG_PACK."/cal-jour.gif' border='0' style='vertical-align:middle;margin:5px'/></a>
	";
}
function Agenda_date_selector($date,$suffixe){
	include_spip('inc/date');

	return 
    afficher_jour(jour($date), "id='evenement_jour$suffixe' name='evenement_jour$suffixe' size='1' class='fondl verdana1' onchange='changeDate$suffixe()'") .
    afficher_mois(mois($date), "id='evenement_mois$suffixe' name='evenement_mois$suffixe' size='1' class='fondl verdana1' onchange='changeDate$suffixe()'") .
    afficher_annee(annee($date), "id='evenement_annee$suffixe' name='evenement_annee$suffixe' size='1' class='fondl verdana1' onchange='changeDate$suffixe()'", date('Y')-1);
}

function Agenda_heure_selector($heure,$minute,$suffixe){
	return
		afficher_heure($heure, "name='evenement_heure$suffixe' size='1' class='fondl'") .
  	afficher_minute($minute, "name='evenement_minute$suffixe' size='1' class='fondl'");
}
?>