<?php

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/

include_spip('inc/spiplistes_api');


function spip_listes_onglets($rubrique, $onglet){
	global $id_auteur, $connect_id_auteur, $connect_statut, $statut_auteur, $options;
	
	echo debut_onglet();
	if ($rubrique == "messagerie"){
		echo onglet(_T('spiplistes:Historique_des_envois'), generer_url_ecrire("spip_listes"), "messagerie", $onglet, _DIR_PLUGIN_SPIPLISTES."img_pack/stock_hyperlink-mail-and-news-24.gif");
		echo onglet(_T('spiplistes:Listes_de_diffusion'), generer_url_ecrire("listes_toutes"), "messagerie", $onglet, _DIR_PLUGIN_SPIPLISTES."img_pack/reply-to-all-24.gif");
		echo onglet(_T('spiplistes:Suivi_des_abonnements'), generer_url_ecrire("abonnes_tous"), "messagerie", $onglet, _DIR_PLUGIN_SPIPLISTES."img_pack/addressbook-24.gif");
	}
	echo fin_onglet();
}


function boite_autocron(){
	@define('_SPIP_LISTE_SEND_THREADS',1);
	include_spip('inc/spiplistes_cron');
	if (cron_spiplistes_cron(0)) return; // rien a faire
	
	$res = spip_query("SELECT COUNT(a.id_auteur) AS n FROM spip_auteurs_courriers AS a JOIN spip_courriers AS c ON c.id_courrier=a.id_courrier WHERE c.statut='encour'");
	$n = 0;
	if ($row = spip_fetch_array($res))
		$n = $row['n'];

	if(true or $n > 0 ){
		echo "<br />";
		echo debut_boite_info();
		//echo "<script type='text/javascript' src='".find_in_path('javascript/autocron.js')."'></script>";
		
		echo "<div style='font-weight:bold;text-align:center'>"._T('spiplistes:envoi_en_cours')."</div>";
		echo "<div style='padding : 10px;text-align:center'><img src='"._DIR_PLUGIN_SPIPLISTES."img_pack/48_import.gif'></div>";
		
		$total = $n;
		$res2 = spip_query("SELECT SUM(total_abonnes) AS total FROM spip_courriers WHERE statut='encour'");
		$row2 = spip_fetch_array($res2);
		$total = $row2['total'];
		echo "<div id='meleuse'>";
		echo "<p align='center' id='envoi_statut'>"._T('spiplistes:envoi_en_cours')." "
		  . "<strong id='envois_restants'>$n</strong>/<span id='envois_total'>$total</span> (<span id='envois_restant_pourcent'>"
		  . round($n/$total*100)."</span>%)</p>";
		 
		$href = generer_action_auteur('spiplistes_envoi_lot','envoyer');
		
		for ($i=0;$i<_SPIP_LISTE_SEND_THREADS;$i++)
			echo "<span id='proc$i' class='processus' name='$href'></span>";
		if (_request('exec')=='spip_listes')
			echo "<a href='".generer_url_ecrire('spip_listes')."' id='redirect_after'></a>";
		echo "</div>";
		
		echo "<script><!--
		var target = $('#envois_restants');
		var total = $('#envois_total').html();
		var target_pc = $('#envois_restant_pourcent');
		function redirect_fin(){
			redirect = $('#redirect_after');
			if (redirect.length>0){
				href = redirect.attr('href');
				setTimeout('document.location.href = \"'+href+'\"',0);
			}
		}
		jQuery.fn.runProcessus = function(url) {
			var proc=this;
			var href=url;
			$(target).load(url,function(data){
				restant = $(target).html();
				pourcent=Math.round(restant/total*100);
				$(target_pc).html(pourcent);
				if (Math.round(restant)>0)
					$(proc).runProcessus(href);
				else
					redirect_fin();
			});
		}
		$('span.processus').each(function(){
			var href = $(this).attr('name');
			$(this).html(ajax_image_searching).runProcessus(href);
			//run_processus($(this).attr('id'));
		});
		//--></script>";
		echo "<p>"._T('spiplistes:texte_boite_en_cours')."</p>" ;
		echo "<p align='center'><a href='".generer_url_ecrire('gerer_courrier','change_statut=publie&id_message='.$id_mess)."'>["._T('annuler')."]</a></p>";
		echo fin_boite_info();
	}
	//echo ' <div style="background-image: url(\''. generer_url_action('cron','&var='.time()).'\');"> </div> ';
	//spip_log("spip_listes :  autocron");	
}

function spip_listes_raccourcis(){
	global  $connect_statut;
	
	// debut des racourcis
	echo debut_raccourcis(_DIR_PLUGIN_SPIPLISTES."img_pack/mailer_config.gif");
	
	if ($connect_statut == "0minirezo") {
		icone_horizontale(_T('spiplistes:Nouveau_courrier'), generer_url_ecrire("sl_courrier_rediger","new=oui&type=nl"), _DIR_PLUGIN_SPIPLISTES."img_pack/stock_mail_send.gif");
// 		echo "</a>"; // bug icone_horizontale()
		echo "<br />" ;
		echo "<br />" ;
		
		icone_horizontale(_T('spiplistes:Nouvelle_liste_de_diffusion'), generer_url_ecrire("liste_edit","new=oui"), _DIR_PLUGIN_SPIPLISTES."img_pack/reply-to-all-24.gif");
// 		echo "</a>"; // bug icone_horizontale()
		icone_horizontale(_T('spiplistes:import_export'), generer_url_ecrire("import_export"), _DIR_PLUGIN_SPIPLISTES."img_pack/listes_inout.png");
// 		echo "</a>"; // bug icone_horizontale()
		
		icone_horizontale(_T('spiplistes:Configuration'), generer_url_ecrire("config"),_DIR_PLUGIN_SPIPLISTES."img_pack/mailer_config.gif");
// 		echo "</a>"; // bug icone_horizontale()
	}
	echo fin_raccourcis();

	//Afficher la console d'envoi ?
	boite_autocron();
	
	// colonne gauche boite info
	echo "<br />" ;
	echo debut_boite_info();
	echo _T('spiplistes:_aide');
	echo fin_boite_info();
}

/**
* spiplistes_afficher_en_liste
*
* affiche des listes d'éléments
*
* @param string titre
* @param string image
* @param string statut
* @param string recherche
* @param string nom_position
* @return string la liste des lettres pour le statut demandé @author BoOz / Pierre Basson
**/
function spiplistes_afficher_en_liste($titre, $image, $element='listes', $statut, $recherche='', $nom_position='position') {
	
	global $pas, $id_auteur;
	$position = intval($_GET[$nom_position]);
	
	$clause_where = '';
	if (!empty($recherche)) {
		$clause_where.= ' AND ( titre LIKE '._q("%$recherche%").' OR descriptif LIKE '._q("%$recherche%").' OR  texte LIKE '._q("%$recherche%").' )';
	}
	
	$lettres = '';
	
	if(!$pas) $pas=10 ;
	if(!$position) $position=0 ;
	
	if($element == 'listes'){
		$requete_listes = 'SELECT id_liste,
		titre,
		date
		FROM spip_listes
		WHERE statut='._q($statut).' '.$clause_where.'
		ORDER BY date DESC
		LIMIT '.intval($position).','.intval($pas).'';
	}
	
	if($element == 'messages'){
		$type='nl' ;
		$type2="";
		$statut2="";
		if($statut=='redac')
			$statut2=" OR statut='ready'";
		if($statut=='auto'){
			$type='auto';
			$statut='publie';
		}
		if($statut=='encour')
			$type2=" OR type='auto'";
	
		$requete_listes = 'SELECT id_courrier,
			titre,
			date, nb_emails_envoyes
			FROM spip_courriers
			WHERE (type='._q($type).$type2.') AND (statut='._q($statut).$statut2.') '.$clause_where.'
			ORDER BY date DESC
			LIMIT '.intval($position).','.intval($pas).'';
	}
	
	if($element == 'abonnements'){
		if($statut=='')
			$requete_listes = 'SELECT listes.id_liste, listes.titre, listes.statut, listes.date,lien.id_auteur,lien.id_liste 
			  FROM spip_auteurs_listes AS lien 
			  LEFT JOIN spip_listes AS listes ON lien.id_liste=listes.id_liste 
			  WHERE lien.id_auteur='._q($id_auteur).' AND (listes.statut="liste" OR listes.statut="inact") 
			  ORDER BY listes.date DESC LIMIT '.intval($position).','.intval($pas).'';
		else{
			$requete_listes = 'SELECT id_courrier,
			titre,
			date, nb_emails_envoyes
			FROM spip_courriers
			WHERE type='._q($type).' AND statut='._q($statut).' '.$clause_where.'
			ORDER BY date DESC
			LIMIT '.intval($position).','.intval($pas).'';
		}
	}
	
	//echo "$requete_listes";
	$resultat_aff = spip_query($requete_listes);

	
	if (@spip_num_rows($resultat_aff) > 0) {
	
	$en_liste.= "<div class='liste'>\n";
	$en_liste.= "<div style='position: relative;'>\n";
	$en_liste.= "<div style='position: absolute; top: -12px; left: 3px;'>\n";
	$en_liste.= "<img src='".$image."'  />\n";
	$en_liste.= "</div>\n";
	$en_liste.= "<div style='background-color: white; color: black; padding: 3px; padding-left: 30px; border-bottom: 1px solid #444444;' class='verdana2'>\n";
	$en_liste.= "<b>\n";
	$en_liste.= $titre;
	$en_liste.= "</b>\n";
	$en_liste.= "</div>\n";
	$en_liste.= "</div>\n";
	$en_liste.= "<table width='100%' cellpadding='2' cellspacing='0' border='0'>\n";
	
	while ($row = spip_fetch_array($resultat_aff)) {
		$titre		= $row['titre'];
		$date		= affdate($row['date']);				
		
		$retour = _DIR_RESTREINT_ABS.self();
		switch ($element){
			case "abonnements":
				$id_row = $row['id_liste'];
				$url_row	= generer_url_ecrire('listes', 'id_liste='.$id_row);
				$url_desabo = generer_action_auteur('spiplistes_changer_statut_abonne', $row['id_auteur']."-listedesabo-$id_row", $retour);
				break;
			case "listes":
				$id_row = $row['id_liste'];
				$url_row	= generer_url_ecrire('listes', 'id_liste='.$id_row);
				break;
			default:
				$id_row	= $row['id_courrier'];			
				$nb_emails_envoyes	= $row['nb_emails_envoyes'];
				$url_row	= generer_url_ecrire('gerer_courrier', 'id_message='.$id_row);
		}
		
		$en_liste.= "<tr class='tr_liste'>\n";
		$en_liste.= "<td width='11'>";
		switch ($statut) {
			case 'brouillon':
				$en_liste.= "<img src='"._DIR_IMG_PACK."puce-blanche.gif' alt='puce-blanche' border='0' style='margin: 1px;' />";
				break;
			case 'publie':
				$en_liste.= "<img src='"._DIR_IMG_PACK."puce-verte.gif' alt='puce-verte' border='0' style='margin: 1px;' />";
				break;
			case 'envoi_en_cours':
				$en_liste.= "<img src='"._DIR_IMG_PACK."puce-orange.gif' alt='puce-orange' border='0' style='margin: 1px;' />";
				break;
		}
		$en_liste.= "</td>";
		$en_liste.= "<td class='arial2'>\n";
		$en_liste.= "<div>\n";
		$en_liste.= "<a href=\"".$url_row."\" dir='ltr' style='display:block;'>\n";
		$en_liste.= $titre;
		
		if ($element == 'listes') {
			$nb_abo= spip_num_rows(spip_query("SELECT id_auteur FROM spip_auteurs_listes WHERE id_liste="._q($id_row)));
			$nb_abo = ($nb_abo>1)? $nb_abo._T('spiplistes:nb_abonnes_plur') : $nb_abo._T('spiplistes:nb_abonnes_sing');
			
			$en_liste.= " <span style='font-size:100%;color:#666666' dir='ltr'>\n";
			$en_liste.= "(".$nb_abo.")\n";
			$en_liste.= "</span>\n";
		}
		
		if($nb_emails_envoyes>0){
			$en_liste.= "<span style='font-size:100%;color:#666666' dir='ltr'>\n";
			$en_liste.= "(".$nb_emails_envoyes.")\n";
			$en_liste.= "</span>\n";
		}
		
		$en_liste.= "</a>\n";
		$en_liste.= "</div>\n";
		$en_liste.= "</td>\n";
		
		switch ($element){
			case "abonnements":
				$en_liste.= "<td width='120' class='arial1'><a href=\"".$url_desabo."\" dir='ltr' style='display:block;'>"._T('spiplistes:desabonnement')."</a></td>\n";
				break;
			default:
				$en_liste.= "<td width='120' class='arial1'>".$date."</td>\n";
		}
		
		$en_liste.= "<td width='50' class='arial1'><b>"._T('spiplistes:numero').$id_row."</b></td>\n";
		$en_liste.= "</tr>\n";
	
	}
	$en_liste.= "</table>\n";
	
	switch ($element){
		case "listes":
			$requete_total = 'SELECT id_liste
			FROM spip_listes
			WHERE statut='._q($statut).' '.$clause_where.'
			ORDER BY date DESC';
			$retour = 'listes_toutes';
			break;
		case "messages":
			$requete_total = 'SELECT id_courrier
			FROM spip_courriers
			WHERE type='._q($type).' AND statut='._q($statut);
			$retour = 'spip_listes';
			break;
		case "abonnements":
			$requete_total = 'SELECT listes.id_liste, listes.titre, listes.statut, listes.date, lien.id_auteur,lien.id_liste 
			 FROM  spip_auteurs_listes AS lien 
			 LEFT JOIN spip_listes AS listes 
			 ON lien.id_liste=listes.id_liste 
			 WHERE lien.id_auteur='._q($id_auteur).' AND (listes.statut ="liste" OR listes.statut ="inact") 
			 ORDER BY listes.date DESC';
			$retour = 'abonne_edit';
			$param = '&id_auteur='.$id_auteur;
			break;
	}
	
	$resultat_total = spip_query($requete_total);
	$total = spip_num_rows($resultat_total);
	
	$en_liste.= spiplistes_afficher_pagination($retour, $param, $total, $position, $nom_position);
	$en_liste.= "</div>\n";
	$en_liste.= "<br />\n";
	}

	return $en_liste;

}

// adapté de abomailman ()
// MaZiaR - NetAktiv
// tech@netaktiv.com
 

// Afficher l'arbo
function  spiplistes_arbo_rubriques($id_rubrique,  $rslt_id_rubrique="") {
	global $ran;
	$ran ++;
	
	$marge="&nbsp;&nbsp;&nbsp;|";
	for ($g=0;$g<$ran;$g++) {
		if (($ran-1)==0) {
			$marge="&bull;";
		}
		else {
			$marge .="-"; 
		}
	}
	$marge .="&nbsp;";

	$rqt_rubriques = spip_query ("SELECT id_rubrique, id_parent, titre FROM spip_rubriques WHERE id_parent='".$id_rubrique."'");
	while ($row = spip_fetch_array($rqt_rubriques)) {
		$id_rubrique = $row['id_rubrique'];
		$id_parent = $row['id_parent'];
		$titre = $row['titre'];
		$arbo .="<option value='".$id_rubrique."'>" . $marge  . supprimer_numero (typo($titre)) . "</option>";
		$arbo .= spiplistes_arbo_rubriques($id_rubrique,   $rslt_id_parent);
	}
	
	return $arbo;
	
}



/**
* adapte de lettres_afficher_pagination
*
* @param string fond
* @param string arguments
* @param int total
* @param int position
* @author Pierre Basson
**/
function spiplistes_afficher_pagination($fond, $arguments, $total, $position, $nom) {
	global $pas;
	$pagination = '';
	$i = 0;

	$nombre_pages = floor(($total-1)/$pas)+1;

	if($nombre_pages>1) {
	
		$pagination.= "<div style='background-color: white; color: black; padding: 3px; padding-left: 30px;  padding-right: 40px; text-align: right;' class='verdana2'>\n";
		while($i<$nombre_pages) {
			$url = generer_url_ecrire($fond, $nom.'='.strval($i*$pas).$arguments, '&');
			$item = strval($i+1);
			if(($i*$pas) != $position) {
				$pagination.= '&nbsp;&nbsp;&nbsp;<a href="'.$url.'">'.$item.'</a>'."\n";
			} else {
				$pagination.= '&nbsp;&nbsp;&nbsp;<i>'.$item.'</i>'."\n";
			}
			$i++;
		}
		
		$pagination.= "</ul>\n";
		$pagination.= "</div>\n";
	}

	return $pagination;
}


function spiplistes_cherche_auteur(){
	if (!$cherche_auteur = _request('cherche_auteur')) return;
	
	echo "<p align='left'>";
	$col = strpos($cherche_auteur, '@') !== false ? 'email' : 'nom';
	$like = '';
	if (strpos($cherche_auteur, '%') !== false) {
		$like = " WHERE $col LIKE '" . $cherche_auteur . "'";
		$cherche_auteur = str_replace('%', ' ', $cherche_auteur);
	}
	$result = spip_query("SELECT id_auteur, $col FROM spip_auteurs$like");
	
	while ($row = spip_fetch_array($result, SPIP_NUM)) {
		$table_auteurs[] = $row[1];
		$table_ids[] = $row[0];
	}
	
	$resultat = mots_ressemblants($cherche_auteur, $table_auteurs, $table_ids);
	echo debut_boite_info();
	if (!$resultat)
		echo "<b>"._T('texte_aucun_resultat_auteur', array('cherche_auteur' => $cherche_auteur)).".</b><br />";
	elseif (count($resultat) == 1) {
		list(, $nouv_auteur) = each($resultat);
		echo "<b>"._T('spiplistes:une_inscription')."</b><br />";
		$result = spip_query("SELECT * FROM spip_auteurs WHERE id_auteur="._q($nouv_auteur));
		echo "<ul>";
		while ($row = spip_fetch_array($result)) {
			$id_auteur = $row['id_auteur'];
			$nom_auteur = $row['nom'];
			$email_auteur = $row['email'];
			$bio_auteur = $row['bio'];
			
			echo "<li><font face='Verdana,Arial,Sans,sans-serif' size=2><b><font size=3><a href=\"?exec=abonne_edit&id_auteur=$id_auteur\">".typo($nom_auteur)."</a></font></b>";
			echo " | $email_auteur";
			echo "</font>\n";
		}
		echo "</ul>";
	}
	elseif (count($resultat) < 16) {
		reset($resultat);
		unset($les_auteurs);
		while (list(, $id_auteur) = each($resultat))
			$les_auteurs[] = $id_auteur;
		if ($les_auteurs) {
			$les_auteurs = join(',', $les_auteurs);
			echo "<b>"._T('texte_plusieurs_articles', array('cherche_auteur' => $cherche_auteur))."</b><br />";
			$result = spip_query("SELECT * FROM spip_auteurs WHERE id_auteur IN ($les_auteurs) ORDER BY nom");
			echo "<ul>";
			while ($row = spip_fetch_array($result)) {
				$id_auteur = $row['id_auteur'];
				$nom_auteur = $row['nom'];
				$email_auteur = $row['email'];
				$bio_auteur = $row['bio'];
				
				echo "<li><font face='Verdana,Arial,Sans,sans-serif' size=2><b><font size=3>".typo($nom_auteur)."</font></b>";
				if ($email_auteur)
					echo " ($email_auteur)";
				echo " | <a href=\"".generer_url_ecrire("abonne_edit","id_auteur=$id_auteur")."\">"._T('spiplistes:choisir')."</a>";
				if (trim($bio_auteur))
					echo "<br /><font size=1>".couper(propre($bio_auteur), 100)."</font>\n";
				echo "</font><p>\n";
			}
			echo "</ul>";
		}
	}
	else
		echo "<b>"._T('texte_trop_resultats_auteurs', array('cherche_auteur' => $cherche_auteur))."</b><br />";

	echo fin_boite_info();
	echo "<p>";
}

function spiplistes_afficher_auteurs($query, $url){
	$tri = _request('tri') ? _request('tri') : 'nom';

	$t = spip_query('SELECT COUNT(*) FROM spip_auteurs');
	$nombre_auteurs = spip_fetch_array($t, SPIP_NUM);
	$nombre_auteurs = intval($nombre_auteurs[0]);
	
	// reglage du debut
	$max_par_page = 30;
	$debut = intval(_request('debut'));
	if ($debut > $nombre_auteurs - $max_par_page) {
		$debut = max(0,$nombre_auteurs - $max_par_page);
	}
	
	$t = spip_query($query . ' LIMIT ' . $debut . ',' . $max_par_page);
	
	$auteurs=array();
	$les_auteurs = array();
	while ($auteur = spip_fetch_array($t)) {
		if ($auteur['statut'] == '0minirezo') {
		$auteur['restreint'] = spip_num_rows(spip_query(
		  "SELECT * FROM spip_auteurs_rubriques WHERE id_auteur="._q($auteur['id_auteur'])));
		}
		$auteurs[] = $auteur;
		$les_auteurs[] = $auteur['id_auteur'];
	}
		
	$lettre = array();
	if (($tri == 'nom') AND $GLOBALS['options'] == 'avancees') {
		$qlettre = spip_query(
	'select distinct upper(left(nom,1)) l, count(*) from spip_auteurs group by l order by l');
		$count = 0;
		while ($rlettre = spip_fetch_array($qlettre, SPIP_NUM)) {
			$lettre[$rlettre[0]] = $count;
			$count += intval($rlettre[1]);
		}
	}
	
	//
	// Affichage
	//
	
	// ici commence la vraie boucle
	echo debut_cadre_relief('redacteurs-24.gif');
	echo "<table border='0' cellpadding=3 cellspacing=0 width='100%' class='arial2'>\n";
	
	if ($nombre_auteurs > $max_par_page) {
		echo "<tr bgcolor='white'><td colspan='6'>";
		echo "<font face='Verdana,Arial,Sans,sans-serif' size='1'>";
		for ($j=0; $j < $nombre_auteurs; $j+=$max_par_page) {
			if ($j > 0) echo " | ";
			
			if ($j == $debut)
				echo "<b>$j</b>";
			elseif ($j > 0)
				echo "<a href='".parametre_url($url,'debut',$j)."'>$j</a>";
			else
				echo " <a href='".parametre_url($url,'debut',0)."'>0</a>";
			
			if ($debut > $j  AND $debut < $j+$max_par_page)
				echo " | <b>$debut</b>";
		}
		echo "</font>";
		echo "</td></tr>\n";
		
		if (($tri == 'nom') AND $GLOBALS['options'] == 'avancees') {
			// affichage des lettres
			echo "<tr bgcolor='white'><td colspan='6'>";
			echo "<font face='Verdana,Arial,Sans,sans-serif' size=2>";
			foreach ($lettre as $key => $val) {
				if ($val == $debut)
					echo "<b>$key</b> ";
				else
					echo "<a href='".parametre_url($url,'debut',$val)."'>$key</a> ";
			}
			echo "</font>";
			echo "</td></tr>\n";
		}
		echo "<tr height='5'></tr>";
	}
	
	echo "<tr bgcolor='#DBE1C5'>";
	echo "<td width='20'>";
	$img = "<img src='"._DIR_IMG_PACK."/admin-12.gif' alt='' border='0'>";
	if ($tri=='statut')
		echo $img;
	else
		echo "<a href='".parametre_url($url,'tri','statut')."' title='"._T('lien_trier_statut')."'>$img</a>";
	
	echo "</td><td>";
	if ($tri == '' OR $tri=='nom')
		echo '<b>'._T('info_nom').'</b>';
	else
		echo "<a href='".parametre_url($url,'tri','nom')."' title='"._T('lien_trier_nom')."'><b>"._T('info_nom')."</b></a>";
	
	echo "</td><td colspan='2'><b>"._T('info_site')."</b>";
		echo "</td><td>";
	if ($visiteurs != 'oui') {
		if ($tri=='nombre')
			echo "<b>"._T('spiplistes:format')."</b>";
		else
			echo "<b>"._T('spiplistes:format')."</b>"; 
	}
	echo "</td><td>";
	echo "<b>"._T('spiplistes:modifier')."</b>";
	
	echo "</td></tr>\n";
	
	//translate extra field data
	list(,,,$trad,$val) = explode("|",_T("spiplistes:options")); 
	$trad = explode(",",$trad);
	$val = explode(",",$val);
	$trad_map = Array();
	for($index_map=0;$index_map<count($val);$index_map++) {
		$trad_map[$val[$index_map]] = $trad[$index_map];
	}
	$i=0;
	foreach ($auteurs as $row) {
		// couleur de ligne
		$couleur = ($i % 2) ? '#FFFFFF' : $couleur_claire;
		$i++;
		echo "<tr bgcolor='$couleur'>";
		

		// statut auteur
		echo "<td>";
		echo bonhomme_statut($row);
		
		// nom
		echo '</td><td>';
		echo "<a href='?exec=abonne_edit&id_auteur=".$row['id_auteur']."'>".typo($row['nom']).'</a>';
		
		if ($connect_statut == '0minirezo' AND $row['restreint'])
		echo " &nbsp;<small>"._T('statut_admin_restreint')."</small>";
		
		// contact
		if ($GLOBALS['options'] == 'avancees') {
			echo '</td><td>';
			if ($row['messagerie'] == 'oui' AND $row['login']
			  AND $activer_messagerie != "non" AND $connect_activer_messagerie != "non" AND $messagerie != "non")
				echo _T('spiplistes:erreur'); // bouton_imessage($row['id_auteur'],"force")."&nbsp;";
			if ($connect_statut=="0minirezo"){
				if (strlen($row['email'])>3)
					echo "<a href='mailto:".$row['email']."'>"._T('lien_email')."</a>";
				else
					echo "&nbsp;";
			}
			
			if (strlen($row['url_site'])>3)
				echo "</td><td><a href='".$row['url_site']."'>"._T('lien_site')."</a>";
			else
				echo "</td><td>&nbsp;";
		}
		
		// Abonne ou pas ?
		echo '</td><td>';
		$id_auteur=$row['id_auteur'] ;
		$abo = spip_fetch_array(spip_query("SELECT `spip_listes_format` FROM `spip_auteurs_elargis` WHERE `id_auteur`=$id_auteur")) ;		
		//var_dump($abo);die("coucou");
		$abo = $abo["spip_listes_format"];
		//var_dump($abo);
		if($abo == "non")
			echo "-";
		else
			echo "&nbsp;".$trad_map[$abo];
		
		// Modifier l'abonnement
		echo '</td><td>';
		echo "<a name='abo".$row['id_auteur']."'></a>";

		$retour = parametre_url($url,'debut',$debut);
			$u = generer_action_auteur('spiplistes_changer_statut_abonne', $row['id_auteur']."-format", $retour);
			if($abo == 'html'){
				$option_abo = "<a href='".parametre_url($u,'statut','non')."'>"._T('spiplistes:desabo')
				 . "</a> | <a href='".parametre_url($u,'statut','texte')."'>"._T('spiplistes:texte')."</a>";
			}
			elseif ($abo == 'texte') 
				$option_abo = "<a href='".parametre_url($u,'statut','non')."'>"._T('spiplistes:desabo')
				 . "</a> | <a href='".parametre_url($u,'statut','html')."'>"._T('spiplistes:html')."</a>";
			elseif(($abo == 'non')OR (!$abo)) 
				$option_abo = "<a href='".parametre_url($u,'statut','texte')."'>"._T('spiplistes:texte')
				 . "</a> | <a href='".parametre_url($u,'statut','html')."'>"._T('spiplistes:html')."</a>";
			echo "&nbsp;".$option_abo;
		
		echo "</td></tr>\n";
	}
	
	echo "</table>\n";
	
	echo "<a name='bas'>";
	echo "<table width='100%' border='0'>";
	
	$debut_suivant = $debut + $max_par_page;
	if ($debut_suivant < $nombre_auteurs OR $debut > 0) {
		echo "<tr height='10'></tr>";
		echo "<tr bgcolor='white'><td align='left'>";
		if ($debut > 0) {
			$debut_prec = strval(max($debut - $max_par_page, 0));
			echo '<form method="post" action="'.parametre_url($url,'debut',$debut_prec).'">';
			echo "<input type='submit' name='submit' value='&lt;&lt;&lt;' class='fondo' />";
			echo "</form>";
		}
		echo "</td><td align='right'>";
		if ($debut_suivant < $nombre_auteurs) {
			echo '<form method="post" action="'.parametre_url($url,'debut',$debut_suivant).'">';
			echo "<input type='submit' name='submit' value='&gt;&gt;&gt;' class='fondo' />";
			echo "</form>";
		}
		echo "</td></tr>\n";
	}
	
	echo "</table>\n";
	echo fin_cadre_relief();
	return join(',', $les_auteurs);
}

/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
?>
