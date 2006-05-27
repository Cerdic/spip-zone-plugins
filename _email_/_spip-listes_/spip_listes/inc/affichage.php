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


 
 
function spip_listes_onglets($rubrique, $onglet){
	global $id_auteur, $connect_id_auteur, $connect_statut, $statut_auteur, $options;

	debut_onglet();


		if ($rubrique == "messagerie"){
		onglet(_T('spiplistes:Historique_des_envois'), "?exec=spip_listes", "messagerie", $onglet, "../"._DIR_PLUGIN_SPIPLISTES."/img_pack/stock_hyperlink-mail-and-news-24.gif");
		onglet(_T('spiplistes:Listes_de_diffusion'), "?exec=listes_toutes", "messagerie", $onglet, "../"._DIR_PLUGIN_SPIPLISTES."/img_pack/reply-to-all-24.gif");
		onglet(_T('spiplistes:Suivi_des_abonnements'), "?exec=abonnes_tous", "messagerie", $onglet,  "../"._DIR_PLUGIN_SPIPLISTES."/img_pack/addressbook-24.gif");
		}

	
	

	fin_onglet();
}


function spip_listes_raccourcis(){
	global  $connect_statut;

	// debut des racourcis
debut_raccourcis("../"._DIR_PLUGIN_LETTRE_INFORMATION."/img_pack/mailer_config.gif");

if ($connect_statut == "0minirezo") {
  icone_horizontale(_T('spiplistes:Nouveau_courrier'), "?exec=courrier_edit&new=oui&type=nl", "../"._DIR_PLUGIN_SPIPLISTES."/img_pack/stock_mail_send.gif");
  

  echo "<br />" ;
  echo "<br />" ;
  icone_horizontale(_T('spiplistes:Nouvelle_liste_de_diffusion'), "?exec=liste_edit&new=oui", "../"._DIR_PLUGIN_SPIPLISTES."/img_pack/reply-to-all-24.gif");
  icone_horizontale(_T('spiplistes:import_export'), "?exec=import_export", "../"._DIR_PLUGIN_SPIPLISTES."/img_pack/listes_inout.png");
  icone_horizontale(_T('spiplistes:Configuration'), "?exec=config","../"._DIR_PLUGIN_SPIPLISTES."/img_pack/mailer_config.gif");
}
fin_raccourcis();
//



//Afficher la console d'envoi ?

global $table_prefix;
$qery_message = "SELECT * FROM ".$table_prefix."_messages AS messages WHERE statut='encour' AND (type='auto' OR type='nl') LIMIT 0,1";
$rsult_pile = spip_query($qery_message);
$mssage_pile = spip_num_rows($rsult_pile);
		

//initialiser le nombre total d'abonnes		
$extra_meta = get_extra(1,"auteur");
if(!$extra_meta["total_auteurs"]){
  $extra_meta["total_auteurs"] = "0";
  set_extra(1,$extra_meta,"auteur");
  $extra_meta = get_extra(1,"auteur");
}


if($mssage_pile > 0 ){
	// Les valeurs sont deja initialisés
	// Compter le nombre de mails à envoyer

	$nb_inscrits = $extra_meta["total_auteurs"];
	echo "<br />";
	debut_boite_info();

	echo "<div style='font-weight:bold;text-align:center'>"._T('spiplistes:message_en_cours')."</div>";
	echo "<div style='padding : 10px;text-align:center'><img src='../"._DIR_PLUGIN_SPIPLISTES."/img_pack/48_import.gif'></div>";
	if($nb_inscrits > 0){
		echo "<p align='center'><b>".round($extra_meta["debut"]/$nb_inscrits *100)." %</b></p>";
	}
	echo "<p>"._T('spiplistes:texte_boite_en_cours')."</p>" ;
	echo "<p align='center'><a href='$url_site/spip-listes/spip-meleuse.php3'>["._T('spiplistes:suivi_envois')."]</a></p>";
	
	echo "<p align='center'><a href='".$PHP_SELF."?envoi_lot=oui'><tt>["._T('spiplistes:lot_suivant')."]</tt></a></p>";
  echo "<p align='center'><a href='".$PHP_SELF."'><tt>["._T('spiplistes:actualiser')."]</tt></a></p>";
	if($envoi_lot == "oui"){
	// echo"<iframe src='../spip-meleuse.php3' height='1' width='1' frameborder='0' >"._T('spiplistes:desole')."</iframe>";
  }
	
	fin_boite_info();
 } elseif ($extra_meta["debut"] != 0){
	$extra_meta["debut"] = 0; // initialiser le compteur a zero pour etre sur
	set_extra(1,$extra_meta,"auteur");
}


// colonne gauche boite info
echo "<br />" ;
debut_boite_info();
echo _T('spiplistes:_aide');
fin_boite_info();


}

/**
	 * afficher_en_liste
	 *
	 * affiche des listes d'éléments
	 *
	 * @param string titre
	 * @param string image
	 * @param string statut
	 * @param string recherche
	 * @param string nom_position
	 * @return string la liste des lettres pour le statut demandé
	 * @author BoOz / Pierre Basson
	 **/
	function afficher_en_liste($titre, $image, $element='articles', $statut, $recherche='', $nom_position='position') {
		
		global $pas, $id_auteur;
		$position = intval($_GET[$nom_position]);

		$clause_where = '';
		if (!empty($recherche)) {
			$recherche = addslashes($recherche);
			$clause_where.= ' AND ( titre LIKE "%'.$recherche.'%"  OR  descriptif LIKE "%'.$recherche.'%"  OR  texte LIKE "%'.$recherche.'%" )';
		}
	
		$lettres = '';
		
		if(!$pas) $pas=10 ;
		if(!$position) $position=0 ;
		
		if($element == 'articles'){
		$requete_listes = 'SELECT id_article,
								titre,
								date
							FROM spip_articles
							WHERE statut="'.$statut.'" '.$clause_where.'
							ORDER BY date DESC
							LIMIT '.$position.','.$pas.'';
		
		}
		
		if($element == 'messages'){
		$type='nl' ;
		if($statut=='auto'){
		$type='auto';
		$statut='publie';
		}
		$requete_listes = 'SELECT id_message,
								titre,
								date_heure
							FROM spip_messages
							WHERE type="'.$type.'" AND statut="'.$statut.'" '.$clause_where.'
							ORDER BY date_heure DESC
							LIMIT '.$position.','.$pas.'';
		
		}
		
		if($element == 'abonnements'){
		
	
		if($statut==''){
		
$requete_listes = 'SELECT articles.id_article, articles.titre, articles.statut, articles.date, lien.id_auteur,lien.id_article FROM  spip_auteurs_articles AS lien LEFT JOIN spip_articles AS articles  ON lien.id_article=articles.id_article WHERE lien.id_auteur="'.$id_auteur.'" AND (articles.statut ="liste" OR articles.statut ="inact") ORDER BY articles.date DESC LIMIT '.$position.','.$pas.'';
		
		}else{
		$requete_listes = 'SELECT id_message,
								titre,
								date_heure
							FROM spip_messages
							WHERE type="'.$type.'" AND statut="'.$statut.'" '.$clause_where.'
							ORDER BY date_heure DESC
							LIMIT '.$position.','.$pas.'';
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
			
				$id_row	= $row['id_message'];
				if(!intval($id_row)) $id_row = $row['id_article'];
				$titre		= $row['titre'];
				$date		= affdate($row['date']);
				$nom_langue	= traduire_nom_langue($row['lang']);
				
				
					switch ($element){
				case "abonnements":
				$url_row	= generer_url_ecrire('gerer_liste', 'id_article='.$id_row);
				$url_desabo	= generer_url_ecrire('abonne_edit', 'id_article='.$id_row.'&id_auteur='.$id_auteur.'&suppr_auteur='.$id_auteur);
				break;
				
				case "articles":
				$url_row	= generer_url_ecrire('gerer_liste', 'id_article='.$id_row);
				break;
				default:
				
				$url_row	= generer_url_ecrire('gerer_courrier', 'id_message='.$id_row);
				}
				
				$en_liste.= "<tr class='tr_liste'>\n";
				$en_liste.= "<td width='11'>";
				switch ($statut) {
					case 'brouillon':
						$en_liste.= "<img src='img_pack/puce-blanche.gif' alt='puce-blanche' border='0' style='margin: 1px;' />";
						break;
					case 'publie':
						$en_liste.= "<img src='img_pack/puce-verte.gif' alt='puce-verte' border='0' style='margin: 1px;' />";
						break;
					case 'envoi_en_cours':
						$en_liste.= "<img src='img_pack/puce-orange.gif' alt='puce-orange' border='0' style='margin: 1px;' />";
						break;
				}
				$en_liste.= "</td>";
				$en_liste.= "<td class='arial2'>\n";
				$en_liste.= "<div>\n";
				$en_liste.= "<a href=\"".$url_row."\" dir='ltr' style='display:block;'>\n";
				$en_liste.= $titre;
				if ($GLOBALS['langue_site'] != $row['lang']) {
					$en_liste.= " <font size='1' color='#666666' dir='ltr'>\n";
					$en_liste.= "(".$nom_langue.")\n";
					$en_liste.= "</font>\n";
				}
				$en_liste.= "</a>\n";
				$en_liste.= "</div>\n";
				$en_liste.= "</td>\n";
				
				switch ($element){
				case "abonnements":
				$en_liste.= "<td width='120' class='arial1'><a href=\"".$url_desabo."\" dir='ltr' style='display:block;'>Désabonnement</a></td>\n";
				break;
				default:
				$en_liste.= "<td width='120' class='arial1'>".$date."</td>\n";
				}
				
				$en_liste.= "<td width='50' class='arial1'><b>N&deg;&nbsp;".$id_row."</b></td>\n";
				$en_liste.= "</tr>\n";

			}
			$en_liste.= "</table>\n";
			$requete_total = 'SELECT id_articles,
								FROM spip_articles
								WHERE statut="'.$statut.'" '.$clause_where.'
								ORDER BY date DESC';
			$resultat_total = spip_query($requete_total);
			$total = spip_num_rows($resultat_total);
			$en_liste.= lettres_afficher_pagination('lettres', '', $total, $position, $nom_position);
			$en_liste.= "</div>\n";
			$en_liste.= "<br />\n";
		}
		
		echo $en_liste;

	}



/**
	 * lettres_afficher_pagination
	 *
	 * @param string fond
	 * @param string arguments
	 * @param int total
	 * @param int position
	 * @author Pierre Basson
	 **/
	function lettres_afficher_pagination($fond, $arguments, $total, $position, $nom) {
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
?>
