<?php
include_spip('inc/date');

function PIMAgenda_install(){
	PIMAgenda_verifier_base();
}

function PIMAgenda_uninstall(){
	include_spip('base/pim_agenda');
	include_spip('base/abstract_sql');

	// suppression du champ pim_agenda a la table spip_groupe_mots
	$query = "ALTER TABLE `spip_groupes_mots` DROP `pim_agenda`";
	spip_query($query);
	
}

function PIMAgenda_verifier_base(){
	$version_base = 0.10;
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta']['pim_agenda_base_version']) )
			|| (($current_version = $GLOBALS['meta']['pim_agenda_base_version'])!=$version_base)){
		include_spip('base/pim_agenda');
		if ($current_version==0.0){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			// ajout du champ pim_agenda a la table spip_groupe_mots
			// si pas deja existant
			$desc = spip_abstract_showtable("spip_groupes_mots",'',true);
			if (!isset($desc['field']['pim_agenda'])){
				spip_query("ALTER TABLE spip_groupes_mots ADD `pim_agenda` VARCHAR(3) NOT NULL AFTER `syndic`");
			}
			ecrire_meta('pim_agenda_base_version',$current_version=$version_base);
		}
		
		ecrire_metas();
	}
	
	if (isset($GLOBALS['meta']['INDEX_elements_objet'])){
		$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
		if (!isset($INDEX_elements_objet['spip_pim_agenda'])){
			$INDEX_elements_objet['spip_pim_agenda'] = array('titre'=>8,'descriptif'=>4,'lieu'=>3);
			ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
			ecrire_metas();
		}
	}
	if (isset($GLOBALS['meta']['INDEX_objet_associes'])){
		$INDEX_objet_associes = unserialize($GLOBALS['meta']['INDEX_objet_associes']);
		if (!isset($INDEX_objet_associes['spip_pim_agenda']['spip_articles'])){
			$INDEX_objet_associes['spip_pim_agenda']['spip_articles'] = 1;
			ecrire_meta('INDEX_objet_associes',serialize($INDEX_objet_associes));
			ecrire_metas();
		}
	}
	if (isset($GLOBALS['meta']['INDEX_elements_associes'])){
		$INDEX_elements_associes = unserialize($GLOBALS['meta']['INDEX_elements_associes']);
		if (!isset($INDEX_elements_associes['spip_articles'])){
			$INDEX_elements_associes['spip_articles'] = array('titre'=>2,'descriptif'=>1);
			ecrire_meta('INDEX_elements_associes',serialize($INDEX_elements_associes));
			ecrire_metas();
		}
	}
}


//
// Liste des evenements agenda de l'article
//

function PIMAgenda_formulaire_edition_evenement($id_agenda, $neweven, $ndate=""){
	global $spip_lang_right;
	$out = "";

	// inits
	$ftitre='';
	$flieu='';
	$fdescriptif='';
	$ftype='reunion';
	$fprive='non';
	$fcrayon='non';
	$fdescriptif='';
	$fstdatedeb=time();

	if (($neweven)&&($ndate)){
		$newdate=urldecode($ndate);
		$test=strtotime($newdate);
		if ($test>0)
			$fstdatedeb=$test;
	}
	$fstdatefin=$fstdatedeb+60*60;

	if ($id_agenda!=NULL){
		$query = "SELECT spip_pim_agenda.* FROM spip_pim_agenda WHERE spip_pim_agenda.id_agenda='$id_agenda';";
		$res = spip_query($query);
		if ($row = spip_fetch_array($res)){
			if (!$neweven){
				$fid_agenda=$row['id_agenda'];
				$ftitre=attribut_html($row['titre']);
				$ftype=$row['type'];
				$fprive=$row['prive'];
				$fcrayon=$row['crayon'];
				$flieu=attribut_html($row['lieu']);
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
	$url=parametre_url($url,'id_agenda','');

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
  $out .=	"<a href='$url'><img src='"._DIR_PLUGIN_PIM_AGENDA."/img_pack/croix.png' width='12' height='12' style='border:none;'></a>";
  $out .= "</div>\n";
  $out .=  "<form name='edition_evenement' action='$url' method='post'>";
  #$out .=  "<input type='hidden' name='redirect' value='$url' />\n";
	if (!$neweven){
	  $out .=  "<input type='hidden' name='id_agenda' value='$fid_agenda' />\n";
	  $out .=  "<input type='hidden' name='evenement_modif' value='1' />\n";
	}
	else {
	  $out .=  "<input type='hidden' name='evenement_insert' value='1' />\n";
	}
	
	// TITRE
	$out .=  "<div class='titre-titre'>"._T('agenda:evenement_titre')."</div>\n";
	$out .=  "<div class='titre-visu'>";
	$ftitre = entites_html($ftitre);
	$out .=  "<input type='text' name='evenement_titre' value=\"$ftitre\" style='width:100%;' />";
	$out .=  "</div>\n";

	// LIEU
	$out .=  "<div class='lieu-titre'>"._T('agenda:evenement_lieu')."</div>";
	$out .=  "<div class='lieu-visu'>";
	$flieu = entites_html($flieu);
	$out .=  "<input type='text' name='evenement_lieu' value=\"$flieu\" style='width:100%;' />";
	$out .=  "</div>\n";

	// Horaire
	/*$out .=  "<div class='horaire-titre'>";
	$out .=  "<input type='checkbox' name='evenement_horaire' value='oui' ";
	$out .= ($fhoraire=='oui'?"checked='checked' ":"");
	$out .= " onClick=\"var element =  findObj('evenement_horaire');var choix = element.checked;
	if (choix==true){	setvisibility('afficher_horaire_debut_evenement', 'visible');setvisibility('afficher_horaire_fin_evenement', 'visible');}
	else{setvisibility('afficher_horaire_debut_evenement', 'hidden');setvisibility('afficher_horaire_fin_evenement', 'hidden');}\"";
	$out .= "/>";
	$out .= _T('agenda:evenement_horaire')."</div>";*/

	// DATES
	$out .=  "<div class='date-titre'>"._T('agenda:evenement_date')."</div>";
	$out .=  "<div class='date-visu'>";
	$out .=  _T('agenda:evenement_date_de');
	$out .= WCalendar_controller(date('Y-m-d H:i:s',$fstdatedeb),"_evenement_debut");
	$out .= "<span class='agenda_".($fhoraire=='oui'?"":"in")."visible_au_chargement' id='afficher_horaire_debut_evenement'>";
	$out .=  _T('agenda:evenement_date_a');
	$out .= PIMAgenda_heure_selector(date('H',$fstdatedeb),date('i',$fstdatedeb),"_debut");
	$out .=	"</span>";
	$out .=  "<br/>";
	$out .=  _T('agenda:evenement_date_au');
	$out .= WCalendar_controller(date('Y-m-d H:i:s',$fstdatefin),"_evenement_fin");
	$out .= "<span class='agenda_".($fhoraire=='oui'?"":"in")."visible_au_chargement' id='afficher_horaire_fin_evenement'>";
	$out .=  _T('agenda:evenement_date_a');
	$out .= PIMAgenda_heure_selector(date('H',$fstdatefin),date('i',$fstdatefin),"_fin");
	$out .=	"</span>";
	$out .=  "</div>\n";
	
	// DESCRIPTIF
	$out .=  "<div class='descriptif-titre'>"._T('agenda:evenement_descriptif')."</div>";
	$out .=  "<div class='descriptif-visu'>";
	$out .=  "<textarea name='evenement_descriptif' style='width:100%;' rows='3'>";
	$out .=  entites_html($fdescriptif);
	$out .=  "</textarea>\n";
	$out .=  "</div>\n";

	// MOTS CLES : chaque groupe de mot cle attribuable a un evenement agenda
	// donne un select
	$out .=  "<div class='agenda_mots_cles'>";
	$query = "SELECT * FROM spip_groupes_mots WHERE pim_agenda='oui' ORDER BY titre";
	$res = spip_query($query);
	while ($row = spip_fetch_array($res,SPIP_ASSOC)){
		$id_groupe = $row['id_groupe'];
		$multiple = ($row['unseul']=='oui')?"size='4'":"multiple='multiple' size='4'";
		
		$query = "SELECT mots_pim_agenda.id_mot FROM spip_mots_pim_agenda AS mots_pim_agenda
							LEFT JOIN spip_mots AS mots ON mots.id_mot=mots_pim_agenda.id_mot 
							WHERE mots.id_groupe=$id_groupe AND mots_pim_agenda.id_agenda=$id_agenda";
		$res2 = spip_query($query);
		$id_mot_select = array();
		while ($row2 = spip_fetch_array($res2))
			$id_mot_select[] = $row2['id_mot'];

			
		$out .= "<select name='evenement_groupe_mot_select_{$id_groupe}[]' class='fondl verdana1 agenda_mot_cle_select' $multiple>\n";
		$out .= "\n<option value='x' style='font-variant: small-caps;' disabled='disabled'>".supprimer_numero($row['titre'])."</option>";

		$res2= spip_query("SELECT * FROM spip_mots WHERE id_groupe=$id_groupe ORDER BY titre");
		while ($row2 = spip_fetch_array($res2,SPIP_ASSOC)){
			$id_mot = $row2['id_mot'];
			$titre = $row2['titre'];
			$out .= my_sel($id_mot, "&nbsp;&nbsp;&nbsp;$titre", in_array($id_mot,$id_mot_select)?$id_mot:0);
		}
		$out .= "</select>\n";
	}
	$out .=  "</div>";
	
  $out .=  "<div class='edition-bouton'>";
  #echo "<input type='submit' name='submit' value='Annuler' />";
	if ($neweven==1){
		$out .=	"<div style='text-align:$spip_lang_right'><input type='submit' name='ajouter' value='"._T('bouton_ajouter')."' class='fondo'></div>";
	}
	else{
		$out .=	"<div style='text-align:$spip_lang_right'><input type='submit' name='ajouter' value='"._T('bouton_enregistrer')."' class='fondo'></div>";
	}
	$out .=  "</div>\n";

	// feature desactivee pour le moment
	// $out .= "<script type='text/javascript' src='"._DIR_PLUGIN_PIM_AGENDA."/img_pack/multiselect.js'></script>";

  $out .=  "</div>";

	$out .=  "</form>";
	$out .=  "</div>\n";
	return $out;
}


?>