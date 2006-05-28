<?php
include_spip('inc/date');

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

	if ($row = spip_fetch_array(spip_query("SELECT statut, titre, id_rubrique FROM spip_articles WHERE id_article=".spip_abstract_quote($id_article)))) {
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

	$flag_auteur = spip_num_rows(spip_query("SELECT id_auteur FROM spip_auteurs_articles WHERE id_article=".spip_abstract_quote($id_article)." AND id_auteur=".spip_abstract_quote($connect_id_auteur)." LIMIT 1"));

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
	. "WHERE evenements.id_article=".spip_abstract_quote($id_article)
	. " AND evenements.id_evenement_source=0"
	. " GROUP BY evenements.id_evenement ORDER BY evenements.date_debut");

	if (spip_num_rows($result)) {
		$out .= "<div class='liste liste-evenements'>";
		$out .= "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>";
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

			$s = Agenda_afficher_date_evenement($date_debut,$date_fin, $horaire);
			$s_rep = "";
			$count_rep = 0;
			$res2 = spip_query("SELECT * FROM spip_evenements WHERE id_evenement_source=".spip_abstract_quote($id_evenement)." ORDER BY date_debut");
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
	
		$out .=  generer_url_post_ecrire("articles", "id_article=$id_article");
		$out .=  "<span class='verdana1'><strong>"._T('agenda:titre_cadre_ajouter_evenement')."&nbsp; </strong></span>\n";
		$out .=  "<div><input type='hidden' name='id_article' value=\"$id_article\">";

		if (in_array($id_evenement,explode(",",$les_evenements)) && $edit==1){
			$out .= Agenda_formulaire_edition_evenement($id_evenement, false);
			$out .= "</div>";
			$out .=  "</td></tr></table>";
			$out .= "<div style='clear: both;'></div>";
			$url = parametre_url(self(),'edit','');
			$url = parametre_url($url,'neweven','1');
			$url = parametre_url($url,'id_evenement','');
			$out .= icone_horizontale(_T("agenda:icone_creer_evenement"),$url , "../"._DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/agenda-24.png", "creer.gif",false);
		}
		else{
			// recuperer le titre de l'article pour le mettre par defaut sur l'evenement
			$titre_defaut = "";
			$res = spip_query("SELECT titre FROM spip_articles where id_article=".spip_abstract_quote($id_article));
			if ($row = spip_fetch_array($res))
				$titre_defaut = $row['titre'];
			
			$out .= Agenda_formulaire_edition_evenement(NULL, true, '', $titre_defaut);
			$out .= "</div>";
			$out .=  "</td></tr></table>";
		}

		$out .= "</div>";
		$out .=  fin_block();
	}
	return $out;
}

function Agenda_formulaire_article($id_article, $flag_editable){

  global $spip_lang_left, $spip_lang_right, $options;
	global $connect_statut, $options,$connect_id_auteur, $couleur_claire ;
	
	$out = "";
	$out .= "<a name='agenda'></a>";
	if ($flag_editable) {
		$out .= Agenda_action_formulaire_article();
		if (_request('edit')||_request('neweven'))
			$bouton = bouton_block_visible("evenementsarticle");
		else
			$bouton = bouton_block_invisible("evenementsarticle");
	}

	$out .= debut_cadre_enfonce("../"._DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/agenda-24.png", true, "", $bouton._T('agenda:texte_agenda')
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
	return $out;
}


function Agenda_action_update_repetitions($id_evenement,$repetitions,$liste_mots){
	// evenement source
	$res = spip_query("SELECT * FROM spip_evenements WHERE id_evenement=".spip_abstract_quote($id_evenement));
	if ($row = spip_fetch_array(($res))){
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$horaire = $row['horaire'];
		$lieu = $row['lieu'];
		$date_debut = strtotime($row['date_debut']);
		$date_fin = strtotime($row['date_fin']);
		$duree = $date_fin - $date_debut;
		$id_evenement_source = $row['id_evenement_source'];
		$id_article = $row['id_article'];
		if ($id_evenement_source!=0)
			return; // pas un evenement source donc rien a faire ici

		$repetitions_updated = array();
		// mettre a jour toutes les repetitions deja existantes ou les supprimer si plus lieu
		$res = spip_query("SELECT id_evenement FROM spip_evenements WHERE id_evenement_source=".spip_abstract_quote($id_evenement));
		while ($row = spip_fetch_array($res)){
			$date = strtotime(date('Y-m-d',$row['date_debut']));
			if (in_array($date,$repetitions)){
				// il est maintenu, on l'update
				$repetitions_updated[] = $date;
				$update_date_debut = date('Y-m-d',$date)." ".date('H:i:s',$date_debut);
				$update_date_fin = date('Y-m-d H:i:s',strtotime($update_date_debut)+$duree);
				
				// TODO : prendre en charge la mise a jour uniquement si conforme a l'original
				$update_titre = $titre;
				$update_descriptif = $descriptif;
				$update_lieu = $lieu;
				
				// mettre a jour l'evenement
				$res=spip_query("UPDATE spip_evenements SET `titre`=".spip_abstract_quote($update_titre)
				. ",`descriptif`=".spip_abstract_quote($update_descriptif)
				. ",`lieu`=".spip_abstract_quote($update_lieu)
				. ",`horaire`=".spip_abstract_quote($horaire)
				. ",`date_debut`=".spip_abstract_quote($update_date_debut)
				. ",`date_fin`=".spip_abstract_quote($update_date_fin)
				. ",`id_article`=".spip_abstract_quote($id_article)
				. " WHERE `id_evenement` =".spip_abstract_quote($row['id_evenement']));
				Agenda_action_update_liste_mots($row['id_evenement'],$liste_mots);
			}
			else {
				// il est supprime
				spip_query("DELETE FROM spip_mots_evenements WHERE id_evenement=".$row['id_evenement']);
				spip_query("DELETE FROM spip_evenements WHERE id_evenement=".$row['id_evenement']);
			}
			
		}
		// regarder les repetitions a ajouter
		foreach($repetitions as $date){
			if (!in_array($date,$repetitions_updated)){
				$update_date_debut = date('Y-m-d',$date)." ".date('H:i:s',$date_debut);
				$update_date_fin = date('Y-m-d H:i:s',strtotime($update_date_debut)+$duree);
				$update_titre = $titre;
				$update_descriptif = $descriptif;
				$update_lieu = $lieu;

				$id_evenement_new = spip_abstract_insert("spip_evenements",
					"(id_evenement_source,maj)",
					"(".spip_abstract_quote($id_evenement).",NOW())");
				if ($id_evenement_new==0)
					spip_log("agenda action formulaire article : impossible d'ajouter un evenement repete");
				else {
					// mettre a jour l'evenement
					$res=spip_query("UPDATE spip_evenements SET `titre`=".spip_abstract_quote($update_titre)
					. ",`descriptif`=".spip_abstract_quote($update_descriptif)
					. ",`lieu`=".spip_abstract_quote($update_lieu)
					. ",`horaire`=".spip_abstract_quote($horaire)
					. ",`date_debut`=".spip_abstract_quote($update_date_debut)
					. ",`date_fin`=".spip_abstract_quote($update_date_fin)
					. ",`id_article`=".spip_abstract_quote($id_article)
					. " WHERE `id_evenement` =".spip_abstract_quote($id_evenement_new));
					
					Agenda_action_update_liste_mots($id_evenement_new,$liste_mots);
				}
			}
		}
	}
}

function Agenda_action_update_liste_mots($id_evenement,$liste_mots){
	// suppression des mots obsoletes
	$cond_in = "";
	if (count($liste_mots))
		$cond_in = "AND" . calcul_mysql_in('id_mot', implode(",",$liste_mots), 'NOT');
	spip_query("DELETE FROM spip_mots_evenements WHERE id_evenement=".spip_abstract_quote($id_evenement)." ".$cond_in);
	// ajout/maj des nouveaux mots
	foreach($liste_mots as $id_mot){
		if (!spip_fetch_array(spip_query("SELECT * FROM spip_mots_evenements WHERE id_evenement=".spip_abstract_quote($id_evenement)." AND id_mot=".spip_abstract_quote($id_mot))))
			spip_query("INSERT INTO spip_mots_evenements (id_mot,id_evenement) VALUES (".spip_abstract_quote($id_mot).",".spip_abstract_quote($id_evenement).")");
	}
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
			$id_article = intval(_request('ajouter_id_article'));
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
			spip_query("UPDATE spip_evenements SET id_article=".spip_abstract_quote($id_article)." WHERE id_evenement=".spip_abstract_quote($id_evenement));
	 	}
		$titre = _request('evenement_titre');
		$descriptif = _request('evenement_descriptif');
		$lieu = _request('evenement_lieu');
		$horaire = _request('evenement_horaire');
		if ($horaire!='oui') $horaire='non';
	
		// pour les cas ou l'utilisateur a saisi 29-30-31 un mois ou ca n'existait pas
		$maxiter=4;
		$st_date_deb=FALSE;
		$jour_debut=_request('jour_evenement_debut');
		// test <= car retour strtotime retourne -1 ou FALSE en cas d'echec suivant les versions
		while(($st_date_deb<=FALSE)&&($maxiter-->0)) {
			$date_deb=_request('annee_evenement_debut')."-"._request('mois_evenement_debut')."-".($jour_debut--)." "._request('heure_evenement_debut').":"._request('minute_evenement_debut');
			$st_date_deb=strtotime($date_deb);
		}
		$date_deb=format_mysql_date(date("Y",$st_date_deb),date("m",$st_date_deb),date("d",$st_date_deb),date("H",$st_date_deb),date("i",$st_date_deb), $s=0);
	
		// pour les cas ou l'utilisateur a saisi 29-30-31 un mois ou ca n'existait pas
		$maxiter=4;
		$st_date_fin=FALSE;
		$jour_fin=_request('jour_evenement_fin');
		// test <= car retour strtotime retourne -1 ou FALSE en cas d'echec suivant les versions
		while(($st_date_fin<=FALSE)&&($maxiter-->0)) {
			$st_date_fin=_request('annee_evenement_fin')."-"._request('mois_evenement_fin')."-".($jour_fin--)." "._request('heure_evenement_fin').":"._request('minute_evenement_fin');
			$st_date_fin=strtotime($st_date_fin);
		}
		$st_date_fin = max($st_date_deb,$st_date_fin);
		$date_fin=format_mysql_date(date("Y",$st_date_fin),date("m",$st_date_fin),date("d",$st_date_fin),date("H",$st_date_fin),date("i",$st_date_fin), $s=0);
	
		// mettre a jour l'evenement
		$res=spip_query("UPDATE spip_evenements SET `titre`=".spip_abstract_quote($titre)
		. ",`descriptif`=".spip_abstract_quote($descriptif)
		. ",`lieu`=".spip_abstract_quote($lieu)
		. ",`horaire`=".spip_abstract_quote($horaire)
		. ",`date_debut`=".spip_abstract_quote($date_deb)
		. ",`date_fin`=".spip_abstract_quote($date_fin)
		. " WHERE `id_evenement` =".spip_abstract_quote($id_evenement));

		// les mots cles : par groupes
		$res = spip_query("SELECT * FROM spip_groupes_mots WHERE evenements='oui' ORDER BY titre");
		$liste_mots = array();
		while ($row = spip_fetch_array($res,SPIP_ASSOC)){
			$id_groupe = $row['id_groupe'];
			$id_mot_a = _request("evenement_groupe_mot_select_$id_groupe"); // un array
			if (is_array($id_mot_a) && count($id_mot_a)){
				if ($row['unseul']=='oui')
					$liste_mots[] = intval(reset($id_mot_a));
				else 
					foreach($id_mot_a as $id_mot)
						$liste_mots[] = intval($id_mot);
			}				
		}

		Agenda_action_update_liste_mots($id_evenement,$liste_mots);
				
		// gestion des repetitions
		$repetitions = _request('selected_date_repetitions');
		$repetitions = explode(',',$repetitions);
		foreach($repetitions as $key=>$date){
			$date = preg_replace(",[0-2][0-9]:[0-6][0-9]:[0-6][0-9]\s*(UTC|GMT)(\+|\-)[0-9]{4},","",$date);
			$date = explode(' ',$date);
			$date = strtotime($date[2]." ".$date[1]." ".$date[3]);
			$repetitions[$key] = $date;
		}
		Agenda_action_update_repetitions($id_evenement, $repetitions, $liste_mots);
	}
	else if ($supp_evenement){
		$id_article = intval(_request('id_article'));
		if (!$id_article)
			$id_article = intval(_request('ajouter_id_article'));
		$res = spip_query("SELECT * FROM spip_evenements WHERE id_article=".spip_abstract_quote($id_article)." AND id_evenement=".spip_abstract_quote($supp_evenement));
		if ($row = spip_fetch_array($res)){
			spip_query("DELETE FROM spip_mots_evenements WHERE id_evenement=".spip_abstract_quote($supp_evenement));
			spip_query("DELETE FROM spip_evenements WHERE id_evenement=".spip_abstract_quote($supp_evenement));
		}
		Agenda_action_supprime_repetitions($id_evenement);
	}
	return "";
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
		$res = spip_query("SELECT evenements.* FROM spip_evenements AS evenements WHERE evenements.id_evenement=".spip_abstract_quote($id_evenement));
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
		$res2 = spip_query("SELECT * FROM spip_articles AS articles WHERE id_article=".spip_abstract_quote($ajouter_id_article));
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
	$out .= WCalendar_controller(date('Y-m-d H:i:s',$fstdatedeb),"_evenement_debut");
	$out .= "<span class='agenda_".($fhoraire=='oui'?"":"in")."visible_au_chargement' id='afficher_horaire_debut_evenement'>";
	$out .=  _T('agenda:evenement_date_a');
	$out .= Agenda_heure_selector(date('H',$fstdatedeb),date('i',$fstdatedeb),"_debut");
	$out .=	"</span>";
	$out .=  "<br/>";
	$out .=  _T('agenda:evenement_date_au');
	$out .= WCalendar_controller(date('Y-m-d H:i:s',$fstdatefin),"_evenement_fin");
	$out .= "<span class='agenda_".($fhoraire=='oui'?"":"in")."visible_au_chargement' id='afficher_horaire_fin_evenement'>";
	$out .=  _T('agenda:evenement_date_a');
	$out .= Agenda_heure_selector(date('H',$fstdatefin),date('i',$fstdatefin),"_fin");
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
								WHERE mots.id_groupe=".spip_abstract_quote($id_groupe)." AND mots_evenements.id_evenement=".spip_abstract_quote($id_evenement));
			while ($row2 = spip_fetch_array($res2))
				$id_mot_select[] = $row2['id_mot'];
		}

		$nb_mots = 0;
		$select = "";	
		$select .= "<select name='evenement_groupe_mot_select_{$id_groupe}[]' class='fondl verdana1 agenda_mot_cle_select' $multiple>\n";
		$select .= "\n<option value='x' style='font-variant: small-caps;' disabled='disabled'>".supprimer_numero($row['titre'])."</option>";

		$res2= spip_query("SELECT * FROM spip_mots WHERE id_groupe=".spip_abstract_quote($id_groupe)." ORDER BY titre");
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
		$res = spip_query("SELECT date_debut FROM spip_evenements WHERE id_evenement_source=".spip_abstract_quote($id_evenement));
		while ($row=spip_fetch_array($res)){
			$dates[] = date('m/d/Y',strtotime($row['date_debut']));
		}
		$dates = implode(",",$dates);
	}
	$out .= WCalendar_statique_point_entree('_repetitions',$dates);
	
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
	// $out .= "<script type='text/javascript' src='"._DIR_PLUGIN_AGENDA_EVENEMENTS."/img_pack/multiselect.js'></script>";

  $out .=  "</div>";

	$out .=  "</form>";
	$out .=  "</div>\n";
	return $out;
}

// Pre traitements -----------------------------------------------------------------------

function Agenda_heure_selector($heure,$minute,$suffixe){
	return
		afficher_heure($heure, "name='heure_evenement$suffixe' size='1' class='fondl'") .
  	afficher_minute($minute, "name='minute_evenement$suffixe' size='1' class='fondl'");
}
?>