<?php
/* csv2spip est un plugin pour cr�er/modifier les visiteurs, r�dacteurs et administrateurs restreints d'un SPIP � partir de fichiers CSV
*	 					VERSION : 3.1 => plugin pour spip 2.*
*
* Auteur : cy_altern
*  
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*  
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

define('_LOGO_CSV2SPIP', _DIR_PLUGIN_CSV2SPIP."/img_pack/csv2spip-24.gif");

include_spip('auth/sha256.inc');
include_spip('inc/csv2spip_import');

// a partir de SPIP 2.1 il faut crypter les pass en sha256 a l place du md5 des version precedentes
// commit de creation de la version 2.1: 14864 cf 
// https://core.spip.net/projects/spip/repository/revisions/14864

function csv2spip_crypt_pass($input) {			
	global $spip_version_code;
    if ($spip_version_code < 14864)
		return md5($input);
	else return sha256($input);
}


function exec_csv2spip() {
	global $spip_version_code;

	include_spip('inc/autoriser');
	if(!autoriser('webmestre')) {
		include_spip('inc/minipres');
		echo minipres();
	}
	else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
        echo $commencer_page(_T('csvspip:titre_page'));
		echo "\r\n<style type=\"text/css\">				 
			\r\n.Cerreur { background-color: #f33; display: block; padding: 10px; }
			\r\n.Cok { width: 47%; background-color: #ddd; display: block; padding: 10px; }
			\r\n.Tpetit { font-size: 75%; }
			\r\n.ss_cadre { background-color: #eee; margin: 5px; padding: 5px; }
			\r\n</style>";
			 
		echo gros_titre(_T('csvspip:titre_page'), '',false);
		echo debut_grand_cadre(true);

		echo debut_gauche('',true);
		echo debut_boite_info(true);
		echo "<strong>"._T('csvspip:titre_info')."</strong><br /><br />";
		echo "\r\n"._T('csvspip:help_info');

		if ($_FILES['userfile']['name'] != '') {  
			echo "<br /><br /><a href=\"".$PHP_SELF."?exec=csv2spip\"><img src=\"".find_in_path('images/cal-today.gif')."\"> "._T('csvspip:retour_saisie')."</a>";
		}				 
		echo fin_boite_info(true);
		echo debut_droite('',true);
					 
		if (empty($_FILES['userfile']['name']))
			csv2spip_formulaire();
		elseif ($_FILES['userfile']['error'] != 0) { 
			echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape1'));
				echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape1.1_debut').$_FILES['userfile']['tmp_name']._T('csvspip:err_etape1.1_fin').$_FILES['userfile']['error']."</span>";
			echo fin_cadre_couleur(true);
		}
		else {
// Etape 1 : analyser le fichier 
			echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape1'));
			echo "<br />"._T('csvspip:ok_etape1'). $_FILES['userfile']['name']."<br />";
			echo fin_cadre_couleur(true);
			$f = $_FILES['userfile']['tmp_name'];
			$res = csv2spip_analyse(csv2spip_normalise($f));

// Etape 2 : transfert des donn�es vers une table SQL temporaire
			if (is_string($res))
				echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape2')), "<br /><span class=\"Cerreur\">", $res, "</span>", fin_cadre_couleur(true);
			else {
			// creer la table temporaire avec les champs n'existant pas dans spip_auteurs
				$spip_tmp_csv2spip = array(
					"id" 	      => "INT(11) NOT NULL AUTO_INCREMENT", 
					"id_spip"     => "INT(11) NOT NULL",
					"groupe"      => "TEXT NOT NULL", 
					"ss_groupe"   => "TEXT NOT NULL",
					"prenom"      => "TEXT NOT NULL");
			// completer avec les champs de la table spip_auteurs a partir de la recuperation de sa structure
				$Tchamps_exclus = array('id_auteur', 'low_sec', 'maj', 'htpass', 'en_ligne', 'alea_actuel', 'alea_futur', 'cookie_oubli');
				$trouver_table = charger_fonction('trouver_table', 'base');
				$desc_auteurs = $trouver_table('spip_auteurs');
				$Tchamps = $desc_auteurs['field'];
				foreach ($Tchamps as $champ=>$desc) {
					if (!in_array($champ, $Tchamps_exclus))
						$spip_tmp_csv2spip[$champ] = str_ireplace('CHARACTER SET utf8 COLLATE utf8_bin', '', $desc);
				}
/*				// champs homonymes a la table auteur
				// a terme, prendre la declaration de celle-ci
				"login"		  => "TEXT NOT NULL",
				"nom" 	      => "TEXT NOT NULL", 
				"pass" 	      => "TEXT NOT NULL", 
				"email"	      => "TEXT NOT NULL", 
				"bio" 	      => "TEXT NOT NULL",
				"source"	  => "TEXT NOT NULL"
						 );
echo '<br>table tmp:<pre>';
	var_dump($spip_tmp_csv2spip);
echo '</pre>';
*/
				$spip_tmp_csv2spip_key = array(
							 "PRIMARY KEY" 	=> "id"
							 );
				$create = sql_create('spip_tmp_csv2spip', $spip_tmp_csv2spip, $spip_tmp_csv2spip_key, true, true);
				echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape2')), "<br />", _T('info_auteurs_nombre'), ' ', count($res) , fin_cadre_couleur(true);
				if (!$create) {
					echo _T('csvspip:err_etape2.2');
					exit;
				}

				foreach($res as $insert) 
				  // ignorer les lignes vides rencontrees
				  if (is_array($insert))
					sql_insertq('spip_tmp_csv2spip', $insert);

		// Passer aux etapes suivantes
				csv2spip_etapes();

		// Fin: suppresion de la table temporaire
	//			sql_drop_table('spip_tmp_csv2spip');
			}
		}

		echo fin_grand_cadre(true),fin_page();
	}
}

// Formulaire de saisie du fichier CSV et des options de config		
function csv2spip_formulaire() {
	echo "<script language=\"JavaScript\"> ";
	echo "	 function aff_masq(id_elem, vis) { ";
	echo "		vis == 0 ? s_vis = 'none' : s_vis = 'block'; ";
	echo "		document.getElementById(id_elem).style.display = s_vis; ";
	echo "		this.checked = 'checked'; ";
	echo "	}";
	echo "</script>";

	// debut_cadre_formulaire();
	echo "\r\n<form name=\"csv2spip\" enctype=\"multipart/form-data\" action=\"".$PHP_SELF."?exec=csv2spip\" method=\"post\" onsubmit=\"return (verifSaisie());\">";
	echo debut_cadre_couleur("cal-today.gif", true, "", _T('csvspip:titre_choix_fichier'));
	echo "<strong>"._T('csvspip:choix_fichier')."</strong><input name=\"userfile\" type=\"file\">";
	echo "<br><br /><strong>"._T('csvspip:nom_groupe_redac')."</strong><input type=\"text\" name=\"groupe_redacs\" value=\"REDACTEURS\">";
	echo "<br><br /><strong>"._T('csvspip:nom_groupe_admin')."</strong><input type=\"text\" name=\"groupe_admins\" value=\"ADMINS\">";
	echo "<br><br /><strong>"._T('csvspip:nom_groupe_visit')."</strong><input type=\"text\" name=\"groupe_visits\" value=\"VISITEURS\">";
	echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:help_nom_groupe_admin')."</span>";		
	echo fin_cadre_couleur(true);

	echo debut_cadre_couleur("mot-cle-24.gif", true, "", _T('csvspip:options_maj'));
	echo "<strong>"._T('csvspip:maj_utils')."</strong>";
	echo _T('csvspip:oui')."<input type=\"radio\" name=\"maj_gene\" value=\"1\"  checked=\"checked\" onClick=\"aff_masq('maj_avance', 1);\">"; 
	echo "<input type=\"radio\" name=\"maj_gene\" value=\"0\" onClick=\"aff_masq('maj_avance', 0);\">"._T('csvspip:non');
	echo "<div id=\"maj_avance\" class=\"ss_cadre\">";
	echo "<br /><strong>"._T('csvspip:maj_mdp')."</strong>"; 
	echo _T('csvspip:oui')."<input type=\"radio\" name=\"maj_mdp\" value=\"1\"  checked=\"checked\">"; 
	echo "<input type=\"radio\" name=\"maj_mdp\" value=\"0\">"._T('csvspip:non');

	echo "<br /><br /><img src=\"".find_in_path('images/admin-12.gif')."\" alt=\"admins uniquement\"> <strong>"._T('csvspip:maj_rub_adm')."</strong>";
	echo "<input type=\"radio\" name=\"maj_rub_adm\" value=\"1\" checked=\"checked\">"._T('csvspip:oui');   
	echo "<input type=\"radio\" name=\"maj_rub_adm\" value=\"0\">"._T('csvspip:non'); 
	echo "<br /><span style=\"font-size: 10px;\">"._T('csvspip:help_maj_rub_adm')."</span><br>"; 
	echo "</div>";				 
	echo fin_cadre_couleur(true);

	echo debut_cadre_couleur(_DIR_PLUGIN_CSV2SPIP."/img_pack/supprimer_utilisateurs-24.gif", true, "", _T('csvspip:suppr_absents'));
	echo "<strong>"._T('csvspip:suppr_utilis')."</strong><ul style=\"padding: 0px; margin: 0px 0px 0px 30px;\">";
	echo "<li style=\"list-style-image: url('".find_in_path('images/redac-12.gif')."');\">"._T('csvspip:suppr_redac')."";
	echo _T('csvspip:oui')."<input type=\"radio\" name=\"eff_redac\" value=\"1\" onClick=\"aff_masq('archi', 1);\">";
	echo "<input type=\"radio\" name=\"eff_redac\" value=\"0\" checked=\"checked\" onClick=\"if (document.csv2spip.eff_admin[1].checked == true) { aff_masq('archi', 0) };\">"._T('csvspip:non');
	echo "</li>";
	echo "<li style=\"list-style-image: url('".find_in_path('images/admin-12.gif')."');\">"._T('csvspip:suppr_admin');
	echo _T('csvspip:oui')."<input type=\"radio\" name=\"eff_admin\" value=\"1\" onClick=\"aff_masq('archi', 1);\">";
	echo "<input type=\"radio\" name=\"eff_admin\" value=\"0\" checked=\"checked\" onClick=\"if (document.csv2spip.eff_redac[1].checked == true) { aff_masq('archi', 0) };\">"._T('csvspip:non'); 
	echo "</li>";
	echo "<li style=\"list-style-image: url('".find_in_path('images/visit-12.gif')."');\">"._T('csvspip:suppr_visit')."";
	echo _T('csvspip:oui')."<input type=\"radio\" name=\"eff_visit\" value=\"1\" >";
	echo "<input type=\"radio\" name=\"eff_visit\" value=\"0\" checked=\"checked\" >"._T('csvspip:non');
	echo "</li>";				 
	echo "</ul><span style=\"font-size: 10px;\">"._T('csvspip:help_suppr_redac')."</span><br>"; 
	echo "<div style=\"display: none\" id=\"archi\" class=\"ss_cadre\"><br /><strong>"._T('csvspip:suprr_articles')."</strong>";
	echo _T('csvspip:oui')."<input type=\"radio\" name=\"supprimer_articles\" value=\"1\" onClick=\"aff_masq('transfert', 0);\">";   
	echo "<input type=\"radio\" name=\"supprimer_articles\" value=\"0\" checked=\"checked\" onClick=\"aff_masq('transfert', 1);\">"._T('csvspip:non'); 
	echo "<div id=\"transfert\" class=\"ss_cadre\"><br><strong>"._T('csvspip:transfert_archive')."</strong>";
	echo "<input type=\"radio\" name=\"archivage\" value=\"1\" checked=\"checked\" onClick=\"aff_masq('rub_transfert', 1);\">"._T('csvspip:oui');   
	echo "<input type=\"radio\" name=\"archivage\" value=\"0\" onClick=\"aff_masq('rub_transfert', 0);\">"._T('csvspip:non'); 
	echo "<div id=\"rub_transfert\" class=\"ss_cadre\"><br>";
	$sql9 = sql_query("SELECT COUNT(*) AS nb_rubriques FROM spip_rubriques");
	$data9 = sql_fetch($sql9);
	$nb_rubriques = $data9['nb_rubriques'];
	$annee = date("Y"); 
	echo "<strong>"._T('csvspip:nom_rubrique_archives')."</strong>";
	echo "<input type=\"text\" name=\"rub_archivage\" value=\"Archives annee ".($annee - 1).'-'.$annee."\" style=\"width: 200px;\">";
	echo "";
	if ($nb_rubriques > 0) {   		
		echo"<br><br><strong>"._T('csvspip:choix_parent_archive')."</strong>"; 
		$sql10 = sql_query("SELECT id_rubrique, titre, id_secteur FROM spip_rubriques ORDER BY titre");
		echo "<select name=\"rub_parent_archivage\">";
		echo "<option value=\"0,0\" selected=\"selected\">"._T('csvspip:racine_site')."</option>";
			
		while ($data10 = sql_fetch($sql10)) { 
			echo "<option value=\"".$data10['id_rubrique'].",".$data10['id_secteur']."\">".$data10['titre']."</option>";
		}				 						
		echo "</select><br>";
	}
	else { 
		echo "<br>"._T('csvspip:pas_de_rubriques')."<br>";
	}  		
	echo "</div></div>";
	echo "<br><br><strong>"._T('csvspip:traitement_supprimes')."</strong><br>";
	echo "<input type=\"radio\" name=\"auteurs_poubelle\" value=\"1\">"._T('csvspip:auteurs_poubelle')."  <br>"; 
	echo "<input type=\"radio\" name=\"auteurs_poubelle\" value=\"0\" checked=\"checked\">"._T('csvspip:attribuer_articles'); 
	echo "<input type=\"text\" name=\"nom_auteur_archives\" value=\"archives".($annee - 1)."-".$annee."\">"._T('csvspip:passe_egale_login');
	echo "</div>";
	echo fin_cadre_couleur(true);

	echo debut_cadre_couleur("rubrique-24.gif", true, "", _T('csvspip:creation_rubriques'));
	//			 echo "<h3>"._T('csvspip:creation_rubriques')."</h3>";
	echo "<strong>"._T('csvspip:rubrique_ss_groupes')."</strong>"; 
	echo _T('csvspip:oui')."<input type=\"radio\" name=\"rub_prof\" value=\"1\" checked=\"checked\" onClick=\"aff_masq('rub_adm', 1);\">";   
	echo "<input type=\"radio\" name=\"rub_prof\" value=\"0\" onClick=\"aff_masq('rub_adm', 0);\">"._T('csvspip:non');
	echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:profs_admins')."</span>";
	echo "<br /><div id=\"rub_adm\" class=\"ss_cadre\">";
	if ($nb_rubriques > 0) {   		
		echo "<br /><strong>"._T('csvspip:choix_parent_rubriques')."</strong>"; 
		echo "<select name=\"rub_parent\">";
		echo "<option value=\"0,0\" selected=\"selected\">"._T('csvspip:racine_site')."</option>";
		$sql10 = sql_query("SELECT id_rubrique, titre, id_secteur FROM spip_rubriques ORDER BY titre");
		while ($data10 = sql_fetch($sql10)) { 
			echo "<option value=\"".$data10['id_rubrique'].",".$data10['id_secteur']."\">".$data10['titre']."</option>";
		}
		echo "</select>";
	}
	else {  
		echo "<br>"._T('csvspip:pas_de_rubriques');
	} 		
	echo "<br /><br /><strong>"._T('csvspip:article_rubrique')."</strong>"; 
	echo _T('csvspip:oui')."<input type=\"radio\" name=\"art_rub\" value=\"1\">";   
	echo "<input type=\"radio\" name=\"art_rub\" value=\"0\" checked=\"checked\">"._T('csvspip:non');
	echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:help_articles')."</span>";
	echo "<br /></div>";
	echo "<br /><div id=\"rub_adm_defaut\">";
	echo "<strong>"._T('csvspip:choix_rub_admin_defaut')."</strong>";
	echo "<input type=\"text\" name=\"rub_admin_defaut\" value=\""._T('csvspip:nom_rub_admin_defaut')."\" style=\"width: 200px;\">";
	echo "<br><span style=\"font-size: 10px;\">"._T('csvspip:help_rub_admin_defaut')."</span>";
	if ($nb_rubriques > 0) {
		echo "<br/><br/><strong>"._T('csvspip:choix_parent_rub_admin_defaut')."</strong>"; 
		echo "<select name=\"rub_parent_admin_defaut\">";
		echo "<option value=\"0,0\" selected=\"selected\">"._T('csvspip:racine_site')."</option>";
		$sql108 = sql_query("SELECT id_rubrique, titre, id_secteur FROM spip_rubriques ORDER BY titre");
		while ($data108 = sql_fetch($sql108)) { 
			echo "<option value=\"".$data108['id_rubrique'].",".$data108['id_secteur']."\">".$data108['titre']."</option>";
		}  	
		echo "</select><br />";
	}
	else {  
		echo "<br>"._T('csvspip:pas_de_rubriques')."<br>";
	} 		
	echo "</div>"; 
	echo fin_cadre_couleur(true);

	echo "<input type=\"submit\" value=\""._T('csvspip:lancer')."\" style=\"background-color: #FF8000; font-weight: bold; font-size: 14px;\">";
	echo "</form><br><br />";

	echo debut_cadre_trait_couleur("fiche-perso-24.gif", true, "", _T('csvspip:titre_help')); 
	// inclure le fichier help de la langue
	$code_langue = (!$GLOBALS['spip_lang'] ? lire_meta("langue_site") : $GLOBALS['spip_lang']);
	if (!find_in_path(_DIR_PLUGIN_CSV2SPIP.'lang/csvspip_help_'.$code_langue.'.php')) $code_langue = 'fr';
	include(_DIR_PLUGIN_CSV2SPIP.'lang/csvspip_help_'.$code_langue.'.php');
	echo "<a href=\""._DIR_PLUGIN_CSV2SPIP."tests_csv2spip/csv2spip_modele.csv\">csv2spip_modele.csv</a>";
	echo fin_cadre_trait_couleur(true);
            
}
		 

// TRAITEMENT DES DONNEES ENVOYEES PAR LE FORMULAIRE DE SAISIE

function csv2spip_etapes()
{
	$Tauteurs_rubriques = 'spip_auteurs_rubriques';
	$Tarticles =  'spip_articles';
	$Tauteurs_articles = 'spip_auteurs_articles';
		
	$err_total = 0;

// �tape 3 : si n�cessaire cr�ation des rubriques pour les admins restreints et des groupes pour accesgroupes
	$_POST['groupe_admins'] != '' ? $groupe_admins = strtolower($_POST['groupe_admins']) : $groupe_admins = '-1';
	$_POST['groupe_visits'] != '' ? $groupe_visits = strtolower($_POST['groupe_visits']) : $groupe_visits = '-1';
	$_POST['groupe_redacs'] != '' ? $groupe_redacs = strtolower($_POST['groupe_redacs']) : $groupe_redacs = '-1';
	echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape3'));
	
// �tape 3.1 : cr�ation des rubriques pour les admins restreints
	if ($_POST['rub_prof'] == 1 AND $groupe_admins != '-1') {
		$Terr_rub = array();
		$Tres_rub = array();
		$date_rub_ec = date("Y-m-j H:i:s");
		$Tch_rub = explode(',', $_POST['rub_parent']);
		$rubrique_parent = $Tch_rub[0];
		$secteur = $Tch_rub[1];
		$sql8 = sql_select('ss_groupe', 'spip_tmp_csv2spip', "LOWER(groupe) = '$groupe_admins' AND ss_groupe != ''", "ss_groupe");
		if(true) {
			while ($data8 = sql_fetch($sql8)) {
				$rubrique_ec = $data8['ss_groupe']; 
				$sql7 = sql_query("SELECT COUNT(*) AS rub_existe FROM spip_rubriques WHERE titre = '$rubrique_ec' LIMIT 1");
				$data7 = sql_fetch($sql7);
				if ($data7['rub_existe'] > 0) {
//print '<br>etape3 : rubrique '.$rubrique_ec.' existe';
					continue;
				}
				sql_query("INSERT INTO spip_rubriques (id_rubrique, id_parent, titre, id_secteur, statut, date) VALUES ('', '$rubrique_parent', '$rubrique_ec', '$secteur', 'publie', '$date_rub_ec')" );
				if (mysql_error() != '') {
					$Terr_rub[] = array('ss_groupe' => $rubrique_ec, 'erreur' => mysql_error());
				}
				else {
					$Tres_rub[] = $rubrique_ec;
				}
			}
		}
	}		
	if (count($Terr_rub) > 0) {  
		echo "<br><span class=\"Cerreur\">"._T('csvspip:err_etape3.1');
		foreach ($Terr_rub as $er) {
			echo "<br>"._T('csvspip:rubrique').$er['ss_groupe']._T('csvspip:erreur').$er['erreur'];
		}
		echo "</span>";
		$err_total ++;
	}		
	else {
		echo "<br>"._T('csvspip:ok_etape3.1_debut').count($Tres_rub)._T('csvspip:ok_etape3.1_fin')."<br>";
	}
	
	// gestion de la rubrique par d�faut des admins restreints
	if ($groupe_admins != '-1') {
	   // cr�ation de la rubrique par d�faut au besoin
		$where = ($_POST['rub_prof'] == 0) ? '' : " AND ss_groupe = ''";
		if (sql_countsel('spip_tmp_csv2spip', "LOWER(groupe) = '$groupe_admins'$where")) {
			$date_rub_defaut = date("Y-m-j H:i:s");
			$Tch_rub_defaut = explode(',', $_POST['rub_parent_admin_defaut']);
			$rubrique_parent_defaut = $Tch_rub_defaut[0];
			$secteur_defaut = $Tch_rub_defaut[1];
			$rubrique_defaut = ($_POST['rub_admin_defaut'] != '' ? $_POST['rub_admin_defaut'] : _T('csvspip:nom_rub_admin_defaut') );
			$sq21 = sql_query("SELECT COUNT(*) AS rub_existe FROM spip_rubriques WHERE titre = '$rubrique_defaut' LIMIT 1");
			$rows21 = sql_fetch($sq21);
			if ($rows21['rub_existe'] < 1) {
				sql_query("INSERT INTO spip_rubriques (id_rubrique, id_parent, titre, id_secteur, statut, date) 
							VALUES ('', '$rubrique_parent_defaut', '$rubrique_defaut', '$secteur_defaut', 'prive', '$date_rub_defaut')" );
				if (mysql_error() != '') {
					echo "<br><span class=\"Cerreur\">"._T('csvspip:err_cree_rub_defaut').mysql_error()."</span>";
					$err_total ++;
				}
				else {
					echo "<br>"._T('csvspip:ok_cree_rub_defaut').$rubrique_defaut."<br />";
					$id_rub_admin_defaut = mysql_insert_id();
				}
			}
			else {
				$sql1001 = sql_query("SELECT id_rubrique FROM spip_rubriques WHERE titre = '$rubrique_defaut' LIMIT 1");
				$rows1001 = sql_fetch($sql1001);
				$id_rub_admin_defaut = $rows1001['id_rubrique'];
			}
		}
	}
	echo fin_cadre_couleur(true);
	
// �tape 4 : int�gration des r�dacteurs, des visiteurs et des administrateurs							
	// redacteurs
	$Tres_nvx = array();
	$Terr_nvx = array();
	$Tres_maj = array();
	$Terr_maj = array();
	$Tres_eff = array();
	$Terr_eff = array();
	$Tres_poub = array();
	$Terr_poub = array();
	$TresR_ss_grpe = array();
	$TerrR_ss_grpe = array();
	$TerrR_eff_accesgroupes = array();
	
	// admins
	$TresA_nvx = array();
	$TerrA_nvx = array();
	$TresA_maj = array();
	$TerrA_maj = array();
	$TresA_eff = array();
	$TerrA_eff = array();
	$TresA_ss_grpe = array();
	$TerrA_ss_grpe = array();
	$TerrA_eff_accesgroupes = array();
	$TerrA_eff_rub_admins = array();
	
	// visiteurs
	$TresV_nvx = array();
	$TerrV_nvx = array();
	$TresV_maj = array();
	$TerrV_maj = array();
	$TresV_eff = array();
	$TerrV_eff = array();
	$TresV_ss_grpe = array();
	$TerrV_ss_grpe = array();
	$TerrV_eff_accesgroupes = array();
	
	// communs
	$Tres_maj_grpacces = array();
	$Terr_maj_grpacces = array();
	$Tres_maj_rub_admin = array();
	$Terr_maj_rub_admin = array();
        
	include_spip('action/editer_auteur');

	// LA boucle : g�re 1 � 1 les utilisateurs de spip_tmp_csv2spip en fonction des options => TOUS !
	$sql157 = sql_select('*', "spip_tmp_csv2spip");
	while ($data157 = sql_fetch($sql157)) {
//		$champs = $data157;
		$champs = array();
echo '<br>data157: <pre>';
var_dump($data157);
echo '</pre>';
		$groupe = strtolower($data157['groupe']);
		$ss_groupe = $data157['ss_groupe'];
		$login = $data157['login'];

/*
		unset($champs['id']);
		unset($champs['id_spip']);
		unset($champs['nom']);
		unset($champs['ss_groupe']);
		unset($champs['groupe']);
		unset($champs['prenom']);
*/		
		$champs['pass'] = csv2spip_crypt_pass($data157['pass']);
        $champs['login'] = $data157['login'];
        $nom = ($data157['nom'] == '' ? $data157['login'] : $data157['nom']);
echo '<br>login: '.$login.' nom157: |'.$data157['nom'].'| nom: '.$nom;        
		$champs['nom'] = strtoupper($nom).' '.ucfirst(strtolower($data157['prenom']));
		$champs['statut'] = ($groupe == $groupe_admins)  ?'0minirezo' : (($groupe != $groupe_visits) ?  '1comite' : '6forum');
//		$champs['alea_actuel'] = '';
//		$champs['alea_futur'] = '';
		$champs['bio'] = $data157['bio'];

		$id_spip = sql_getfetsel('id_auteur', 'spip_auteurs', "LOWER(login) = " . sql_quote(strtolower($login)));
		
        // 4.1 : l'utilisateur n'est pas inscrit dans la base spip_auteurs
		if (!$id_spip) {
/*		    $id_spip = insert_auteur('spip_auteurs');
			auteurs_set($id_spip, $champs);
			if ($id_spip) {
*/
// KISS: inserer l'auteur directement
		    if ($id_spip = sql_insertq('spip_auteurs', $champs)) {
				// insertion de l'id_spip dans la base tmp
				sql_updateq('spip_tmp_csv2spip', array('id_spip' => $id_spip), "id=" . $data157['id']);
        		$groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Tres_nvx[] = $login: $TresV_nvx[] = $login) : $TresA_nvx[] = $login;
		    }
		    else {
				$groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Terr_nvx[] = array('login' => $login, 'erreur' => mysql_error()) : $TerrV_nvx[] = array('login' => $login, 'erreur' => mysql_error()) ) :  $TerrA_nvx[] = array('login' => $login, 'erreur' => mysql_error());
        	}
		}
		else {
			
// 4.2 : l'utilisateur est d�ja inscrit dans la base spip_auteurs
			sql_updateq('spip_tmp_csv2spip', array('id_spip' => $id_spip), "id=" . $data157['id']);

			// faut il faire la maj des existants ?
			if ($_POST['maj_gene'] == 1) {
				
// 4.2.1 faire la maj des infos perso si n�cessaire
				if ($_POST['maj_mdp'] == 1) {
					sql_updateq('spip_auteurs', $champs, "id_auteur = $id_spip");
					if (mysql_error() == '') {
						$groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Tres_maj[] = $login : $TresV_maj[] = $login) : $TresA_maj[] = $login;
					}
					else {
						$groupe != $groupe_admins ? ($groupe != $groupe_visits ? $Terr_maj[] = array('login' => $login, 'erreur' => mysql_error()) : $TerrV_maj[] = array('login' => $login, 'erreur' => mysql_error())) : $TerrA_maj[] = array('login' => $login, 'erreur' => mysql_error());
					}
				}

// 4.2.3 suppression des droits sur les rubriques administr�es si n�cessaire
				if ($_POST['maj_rub_adm'] == 1 AND $statut == '0minirezo') {
					sql_query("DELETE FROM $Tauteurs_rubriques WHERE id_auteur = $id_spip");
					if (mysql_error() == '') {
						$Tres_maj_rub_admin[] = $login;
					}
					else {
						$Terr_maj_rub_admin[] = array('login' => $login, 'erreur' => mysql_error());
					}
				}
			}
        }
	}  // fin du while traitant les comptes 1 � 1

	// Mettre a jour les fichiers .htpasswd et .htpasswd-admin
	include_spip("inc/acces");
	ecrire_acces();

// 4.4 : gestion des suppressions
    // VERSION 2.3 de effacer les absents
	$ch_maj = 0;
	$eff_absv = 0;
	$eff_absr = 0;
	$eff_absa = 0;
	if ($_POST['eff_visit'] == 1) {
	//					 $ch_maj = 1;
		$eff_absv = 1;
	}
	if ($_POST['eff_redac'] == 1) {
		$ch_maj = 1;
		$eff_absr = 1;
	}
	if ($_POST['eff_admin'] == 1) {
		$ch_maj = 1;
		$eff_absa = 1;
	}

    // param�trage auteur et dossier d'archive
	if ($ch_maj !== 0) {
		  // si auteurs supprim�s (pas de poubelle), r�cup�rer l'id du r�dacteur affect� aux archives + si n�cessaire, cr�er cet auteur (groupe = poubelle)
		if ($_POST['auteurs_poubelle'] != 1) {
			$nom_auteur_archives = $_POST['nom_auteur_archives'];
			$sql615 = sql_query("SELECT id_auteur FROM spip_auteurs WHERE login = '$nom_auteur_archives' LIMIT 1");
			if (sql_count($sql615) > 0) {
				$data615 = sql_fetch($sql615);
				$id_auteur_archives = $data615['id_auteur'];
			}
			else {
				sql_query("INSERT INTO spip_auteurs (id_auteur, nom, login, pass, statut) VALUES ('', '$nom_auteur_archives', '$nom_auteur_archives', '$nom_auteur_archives', '5poubelle')");
				$id_auteur_archives = mysql_insert_id();
			}
			$nom_rub_archivesR = $nom_auteur_archives;
			$id_rub_parent_archivesA = $nom_auteur_archives;
			$id_rub_parent_archivesR = $id_auteur_archives;
			$nom_rub_archivesA = $nom_auteur_archives;
			$id_auteur_archivesA = $id_auteur_archives;
			$nom_auteur_archivesR = $nom_auteur_archives;
			$id_auteur_archivesR = $id_auteur_archives;
				
		// si archivage, r�cup de l'id de la rubrique archive + si n�cessaire, cr�er la rubrique				 		
			if ($_POST['supprimer_articles'] != 1 AND $_POST['archivage'] != 0) {
				$supprimer_articlesr = 0;
				$supprimer_articlesa = 0;
				$archivager =1;
				$archivagea = 1;
					 
				$nom_rub_archives = $_POST['rub_archivage'];
			// $_POST['rub_parent_archivage'] de la forme : "id_rubrique,id_secteur"
				$Tids_parent_rub_archives = explode(',', $_POST['rub_parent_archivage']);
				$id_rub_parent_archives = $Tids_parent_rub_archives[0];
				$id_sect_parent_archives = $Tids_parent_rub_archives[1];
				$date_rub_archives = date("Y-m-j H:i:s");
				$sql613 = sql_query("SELECT id_rubrique, id_secteur FROM spip_rubriques WHERE titre = '$nom_rub_archives' AND id_parent = '$id_rub_parent_archives' LIMIT 1");
				if (sql_count($sql613) > 0) {
					$data613 = sql_fetch($sql613);
					$id_rub_archives = $data613['id_rubrique'];
				}
				else {
					sql_query("INSERT INTO spip_rubriques (id_rubrique, id_parent, titre, id_secteur, statut, date) VALUES ('', '$id_rub_parent_archives', '$nom_rub_archives', '$id_sect_parent_archives', 'publie', '$date_rub_archives')" );
					$id_rub_archives = mysql_insert_id();
				}
			}
		}
	}
    			        						
// 4.4.1 : traitement des visiteurs actuels de la base spip_auteurs => si effacer les absV = OK
	if ($eff_absv == 1) {
		$sql1471 = sql_query("SELECT COUNT(*) AS nb_redacsV FROM spip_auteurs WHERE statut = '6forum'");
		$data1471 = sql_fetch($sql1471);
		if ($data1471['nb_redacsV'] > 0) {
		  // pas de poubelle pour les visiteurs => suppression puisque pas d'articles
			$sql1591 = sql_query("SELECT id_auteur, login FROM spip_auteurs WHERE statut = '6forum'");
			while ($data1591 = sql_fetch($sql1591)) {
				$login_sp = strtolower($data1591['login']);
				$id_auteur_ec = $data1591['id_auteur'];
				if (!sql_countsel('spip_tmp_csv2spip',"LOWER(nom) = '$login_sp'")) {
	 // l'utilisateur n'est pas dans le fichier CSV import� => le supprimer
					sql_query("DELETE FROM spip_auteurs WHERE id_auteur = '$id_auteur_ec' AND statut = '6forum' LIMIT 1");
					if (mysql_error() == 0) {
						$TresV_eff[] = $login;
					}
					else {
						$TerrV_eff[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
					}
				}
			}
		  // optimisation de la table apr�s les effacements
			sql_query("OPTIMIZE TABLE spip_auteurs");
		}	
	}
        
// 4.4.2 : traitement des r�dacteurs actuels de la base spip_auteurs => si effacer les absents redac = OK
	if ($eff_absr == 1) {
		$sql147 = sql_query("SELECT COUNT(*) AS nb_redacsR FROM spip_auteurs WHERE statut = '1comite'");
		$data147 = sql_fetch($sql147);
		if ($data147['nb_redacsR'] > 0) {
		  // si archivage, r�cup de l'id de la rubrique archive + si n�cessaire, cr�er la rubrique				 		
			if ($supprimer_articlesr != 1 AND $archivager != 0) {
				$nom_rub_archivesR = $rub_archivager;
				$sql613 = sql_query("SELECT id_rubrique, id_secteur FROM spip_rubriques WHERE titre = '$nom_rub_archivesR' AND id_parent = '$id_rub_parent_archivesR' LIMIT 1");
				if (sql_count($sql613) > 0) {
					$data613 = sql_fetch($sql613);
					$id_rub_archivesR = $data613['id_rubrique'];
				}
				else {
					sql_query("INSERT INTO spip_rubriques (id_rubrique, id_parent, titre, id_secteur, statut, date) VALUES ('', '$id_rub_parent_archivesR', '$nom_rub_archivesR', '$id_sect_parent_archivesR', 'publie', '$date_rub_archivesR')" );
					$id_rub_archivesR = mysql_insert_id();
				}
			}
			$sql159 = sql_query("SELECT id_auteur, login FROM spip_auteurs WHERE statut = '1comite' AND bio != 'archive'");
			$cteur_articles_deplacesR = 0;
			$cteur_articles_supprimesR = 0;
			$cteur_articles_modif_auteurR = 0;
			while ($data159 = sql_fetch($sql159)) {
			$login_sp = strtolower($data159['login']);
				$id_auteur_ec = $data159['id_auteur'];
			  // l'utilisateur n'est pas dans le fichier CSV import� => le supprimer
			if (sql_countsel('spip_tmp_csv2spip', "nom = '$login_sp'")) {
				  // traitement �ventuel des articles de l'auteur � supprimer
					$sql757 = sql_query("SELECT COUNT(*) AS nb_articles_auteur FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
					$data757 = sql_fetch($sql757);
//print '<br><br>id_auteur = '.$id_auteur_ec;
//print '<br>nb_articles_auteur = '.$data757['nb_articles_auteur'];
//print '<br>$supprimer_articlesr = '.$supprimer_articlesr;
//print '<br>$archivager = '.$archivager;
					if ($data757['nb_articles_auteur'] > 0) {
						if ($supprimer_articlesr != 1) {
							if ($archivager != 0) {
								$sql612 = sql_query("SELECT id_article FROM $Tauteurs_articles WHERE id_auteur = $id_auteur_ec");
								if (sql_count($sql612) > 0) {
//print '<br>d�part UPDATE';
									while ($data612 = sql_fetch($sql612)) {
										$id_article_ec = $data612['id_article'];
										sql_updateq("$Tarticles", array("id_rubrique" => '$id_rub_archivesR', "id_secteur" => '$id_sect_parent_archivesR'), "id_article = '$id_article_ec' LIMIT 1");
										$cteur_articles_deplacesR ++;
									}
								} 
								if ($auteurs_poubeller != 1) {
									sql_updateq("$Tauteurs_articles", array("id_auteur" => '$id_auteur_archivesR'), "id_auteur = '$id_auteur_ec'");
								}	   														
							}
						}
						else {
							$sql756 = sql_query("SELECT id_article FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
//print '<br>d�part DELETE';
							while ($data756 = sql_fetch($sql756)) {
								$id_article_a_effac = $data756['id_article'];
								sql_query("DELETE FROM $Tarticles WHERE id_article = '$id_article_a_effac' LIMIT 1");
								$cteur_articles_supprimesR ++;
							}
							sql_query("DELETE FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
						}
					}
				  // traitement des auteurs � effacer												
					if ($auteurs_poubeller != 1) {
						sql_query("DELETE FROM spip_auteurs WHERE id_auteur = '$id_auteur_ec' AND statut = '1comite' LIMIT 1");
						if (mysql_error() == 0) {
							$TresR_eff[] = $login;
						}
						else {
							$TerrR_eff[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
						}
					}
					else {
						sql_updateq("spip_auteurs", array("statut" => '5poubelle'), "id_auteur = '$id_auteur_ec' LIMIT 1");
						if (mysql_error() == 0) {
							$TresR_poub[] = $id_auteur_ec;
						}
						else {
							$TerrR_poub[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
						}
					}
				}
			}
		  // optimisation de la table apr�s les effacements
			sql_query("OPTIMIZE TABLE spip_auteurs, $Tarticles, $Tauteurs_articles, $Taccesgroupes_auteurs");
		}		
	}
// 4.4.3 : traitement des administrateurs restreints actuels de la base spip_auteurs => si effacer les absA = OK
	if ($eff_absa == 1) {
		$sql1473 = sql_query("SELECT COUNT(*) AS nb_redacsA FROM spip_auteurs
								 LEFT JOIN $Tauteurs_rubriques
								 ON $Tauteurs_rubriques.id_auteur = spip_auteurs.id_auteur
								 WHERE statut = '0minirezo'");
	//echo '<br>mysql_error 1473 = '.mysql_error();
		$data1473 = sql_fetch($sql1473);
		if ($data1473['nb_redacsA'] > 0) {
			$sql1593 = sql_query("SELECT Tauteurs.id_auteur, Tauteurs.login FROM spip_auteurs AS Tauteurs, $Tauteurs_rubriques AS Tauteurs_rubriques WHERE statut = '0minirezo' AND Tauteurs.id_auteur = Tauteurs_rubriques.id_auteur");
			$cteur_articles_deplacesA = 0;
			$cteur_articles_supprimesA = 0;
			$cteur_articles_modif_auteurA = 0;
			while ($data1593 = sql_fetch($sql1593)) {
				$login_sp = strtolower($data1593['login']);
				$id_auteur_ec = $data1593['id_auteur'];
				
	// l'utilisateur n'est pas dans le fichier CSV import� => le supprimer
				if (!sql_countsel('spip_tmp_csv2spip', "nom = '$login_sp'")) {
				  // traitement �ventuel des articles de l'admin � supprimer
					$sql7573 = sql_query("SELECT COUNT(*) AS nb_articles_auteur FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
					$data7573 = sql_fetch($sql7573);
					if ($data7573['nb_articles_auteur'] > 0) {
						if ($supprimer_articlesa != 1) {
							if ($archivagea != 0) {
								$sql6123 = sql_query("SELECT id_article FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
								if (sql_count($sql6123) > 0) {
									while ($data6123 = sql_fetch($sql6123)) {
										$id_article_ec = $data6123['id_article'];
										sql_updateq("$Tarticles", array("id_rubrique" => '$id_rub_archivesA', "id_secteur" => '$id_sect_parent_archivesA'), "id_article = '$id_article_ec' LIMIT 1");
										$cteur_articles_deplacesA ++;
									}
								}
								if ($auteurs_poubellea != 1) {
									sql_updateq("$Tauteurs_articles", array("id_auteur" => '$id_auteur_archivesA'), "id_auteur = '$id_auteur_ec'");
								}	   														
							}
						}
						else {
							$sql7563 = sql_query("SELECT id_article FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
							while ($data7563 = sql_fetch($sql7563)) {
								$id_article_a_effac = $data7563['id_article'];
								sql_query("DELETE FROM $Tarticles WHERE id_article = '$id_article_a_effac' LIMIT 1");
								$cteur_articles_supprimesA ++;
							}
							sql_query("DELETE FROM $Tauteurs_articles WHERE id_auteur = '$id_auteur_ec'");
						}
					}
					// traitement des admins � effacer												
					if ($auteurs_poubellea != 1) {
						sql_query("DELETE FROM spip_auteurs WHERE id_auteur = '$id_auteur_ec' AND statut = '0minirezo' LIMIT 1");
						if (mysql_error() == 0) {
							$TresA_eff[] = $login;

						  // virer l'administation de toutes les rubriques pour cet admin
							sql_query("DELETE FROM $Tauteurs_rubriques WHERE id_auteur = $id_auteur_ec");
							if (mysql_error() != '') {
								$TerrA_eff_rub_admins[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
							}
						}
						else {
							$TerrA_eff[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
						}
					}
					else {
						sql_updateq("spip_auteurs", array("statut" => '5poubelle'), "id_auteur = '$id_auteur_ec' LIMIT 1");
						if (mysql_error() == 0) {
							$TresA_poub[] = $id_auteur_ec;
						}
						else {
							$TerrA_poub[] = array('id_auteur' => $id_auteur_ec, 'erreur' => mysql_error());
						}
					}
				}
			}
		  // optimisation de la table apr�s les effacements
			sql_query("OPTIMIZE TABLE spip_auteurs, $Tarticles, $Tauteurs_articles, $Taccesgroupes_auteurs");
		}
	}   //   fin effacer les abs (4.4)  V 2.3
	
// r�sultats �tape 4
	echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape4'));
	echo "<br>"._T('csvspip:etape4.1')."<br>";
	if (count($TerrV_nvx) > 0) {		
		echo "<span class=\"Cerreur\">"._T('csvspip:err_visit');
		foreach ($TerrV_nvx as $Ven) { 
			echo _T('csvspip:utilisateur').$Ven['login']._T('csvspip: erreur').$Ven['erreur']."<br>";
		}
		echo "</span>";
		$err_total ++;
	}
	else {
		echo "<br>"._T('csvspip:creation').count($TresV_nvx)._T('csvspip:comptes_visit_ok')."<br>";					 			
	}
	if (count($Terr_nvx) > 0) {		
		echo "<span class=\"Cerreur\">"._T('csvspip:err_redac');
		foreach ($Terr_nvx as $en) { 
			echo _T('csvspip:utilisateur').$en['login']._T('csvspip:erreur').$en['erreur']."<br>";
		}					
		echo "</span>";
		$err_total ++;
	}
	else {
		echo "<br>"._T('csvspip:creation').count($Tres_nvx)._T('csvspip:comptes_redac_ok')."<br>";					 
	}

	if (count($TerrA_nvx) > 0) {		
		echo "<span class=\"Cerreur\">"._T('csvspip:err_admin');
		foreach ($TerrA_nvx as $Pen) { 
			echo _T('csvspip:utilisateur').$Pen['login']._T('csvspip: erreur').$Pen['erreur']."<br>";
		}
		echo "</span>";
		$err_total ++;
	}
	else {
		echo "<br>"._T('csvspip:creation').count($TresA_nvx)._T('csvspip:comptes_admin_ok')."<br>";					 			
	}
	
// 4.2 r�sultats maj des existants
	if ($_POST['maj_gene'] == 1) {
		echo "<br>"._T('csvspip:etape4.2')."<br>";
		if ($_POST['maj_mdp'] == 1) { 					
			echo "<br>"._T('csvspip:etape4.2.1')."<br>";
			if (count($TerrV_maj) > 0) {
				echo "<span class=\"Cerreur\">"._T('csvspip:err_visit');
				foreach ($TerrV_maj as $Vem) { 
					echo _T('csvspip:visit').$Vem['login']._T('csvspip: erreur').$Vem['erreur']."<br>";
				}		
				echo "</span>";
				$err_total ++;
			}
			else {
				echo "<br />"._T('csvspip:ok_etape4.2.1').count($TresA_maj)._T('csvspip:comptes_visit_ok')."<br>";
			}  					
			if (count($Terr_maj) > 0) {		
				echo "<span class=\"Cerreur\">"._T('csvspip:err_redac');
				foreach ($Terr_maj as $em) { 
					echo _T('csvspip:redac').$em['login']._T('csvspip: erreur').$em['erreur']."<br>";
				}		 
				echo "</span>";
				$err_total ++;
			}
			else {
				echo "<br>"._T('csvspip:ok_etape4.2.1').count($Tres_maj)._T('csvspip:comptes_redac_ok')."<br>";							
			} 
			if (count($TerrA_maj) > 0) {
				echo "<span class=\"Cerreur\">"._T('csvspip:err_admin');
				foreach ($TerrA_maj as $Pem) { 
					echo _T('csvspip:admin').$Pem['login']._T('csvspip: erreur').$Pem['erreur']."<br>";
				}		
				echo "</span>";
				$err_total ++;
			}
			else {
				echo "<br />"._T('csvspip:ok_etape4.2.1').count($TresA_maj)._T('csvspip:comptes_admin_ok')."<br>";
			}  					
		}
		if ($_POST['maj_grpes_redac'] == 1 OR $_POST['maj_grpes_admin'] == 1 OR $_POST['maj_grpes_visit'] == 1) {
			echo "<br>"._T('csvspip:etape4.2.2')."<br>";
			if (count($Terr_maj_grpacces) > 0) {
				echo "<span class=\"Cerreur\">"._T('csvspip:err_maj_grpacces');
				foreach ($Terr_maj_grpacces as $Peg) { 
					echo _T('csvspip:utilisateur').$Peg['login']._T('csvspip: erreur').$Peg['erreur']."<br>";
				}		
				echo "</span>";
				$err_total ++;
			}
			else {
				echo "<br />"._T('csvspip:ok_maj_grpacces').count($Tres_maj_grpacces)._T('csvspip:utilisateurs')."<br>";
			}
		}
		if ($_POST['maj_rub_adm'] == 1) {
			echo "<br>"._T('csvspip:etape4.2.3')."<br>";
			if (count($Terr_maj_rub_admin) > 0) {
				echo "<span class=\"Cerreur\">"._T('csvspip:err_maj_rub_adm');
				foreach ($Terr_maj_rub_admin as $Pera) { 
					echo _T('csvspip:utilisateur').$Pera['login']._T('csvspip: erreur').$Pera['erreur']."<br>";
				}		
				echo "</span>";
				$err_total ++;
			}
			else {
				echo "<br />"._T('csvspip:ok_maj_rub_adm').count($Tres_maj_rub_admin)._T('csvspip:utilisateurs')."<br>";
			}
		}
	}

// 4.4 r�sultats effacer les absents
	if ($eff_absv == 1 OR $eff_absr == 1 OR $eff_absa == 1) {
		echo "<br />"._T('csvspip:etape4.4')."<br>";
	}
  
	// r�sultats effacer les visiteurs
	if ($eff_absv == 1) {  					
		echo "<br />"._T('csvspip:etape4.4.1')."<br>";
		if (count($TerrV_eff) > 0 OR count($TerrV_poub) > 0) {	
			echo "<span class=\"Cerreur\">"._T('csvspip:err_visit');
			foreach ($TerrV_eff as $Vee) {
				echo _T('csvspip:visit').$Vee['login']._T('csvspip: erreur').$Vee['erreur'];
			}	
			$err_total ++;
		}
		else { 
			echo "<br />"._T('csvspip:suppression_debut').count($TresV_eff)._T('csvspip:comptes_visit_ok')."<br>";
		}
	}  					

  // r�sultats effacer les redacteurs
	if ($eff_absr == 1) { 
		echo "<br />"._T('csvspip:etape4.4.2')."<br>";
		if (count($TerrR_eff) > 0 OR count($TerrR_poub) >0) {
			echo "<span class=\"Cerreur\">"._T('csvspip:suppr_redac');
			foreach ($TerrR_eff as $ee) { 
				echo '<br/>'._T('csvspip:redac').$ee['login']._T('csvspip: erreur').' '.$ee['erreur'];
			}
			echo "<span class=\"Cerreur\">"._T('csvspip:redac_poubelle');
			foreach ($TerrR_poub as $ep) { 
				echo '<br/>'._T('csvspip:redac').$ep['login']._T('csvspip: erreur').' '.$ep['erreur'];
			}
			$err_total ++;
		}
		else { 
			echo "<br />"._T('csvspip:suppression_debut').count($TresR_eff)._T('csvspip:comptes_redac_ok')."<br>";
			echo "<br />"._T('csvspip:poubelle_debut').count($TresR_poub)._T('csvspip:comptes_redac_ok')."<br>";
		}
		if ($archivager != 0) {
			echo "<br />"._T('csvspip:archivage_debut').$cteur_articles_deplacesR._T('csvspip:archivage_fin').$nom_rub_archivesR;
		}  
		if ($supprimer_articlesr == 1) {
			echo "<br />"._T('csvspip:suppression_debut').$cteur_articles_supprimesR._T('csvspip:suppression_fin')."<br>";
		}
	}  					

  // r�sultats effacer les admins
	if ($eff_absa == 1) { 			
		echo "<br />"._T('csvspip:etape4.4.3')."<br>";
		if (count($TerrA_eff) > 0 OR count($TerrA_poub) >0) {
			echo "<span class=\"Cerreur\">"._T('csvspip:suppr_redac');
			foreach ($TerrA_eff as $Aee) { 
				echo "<br />"._T('csvspip:admin').$Aee['login']._T('csvspip: erreur').' '.$Aee['erreur'];
			}
			echo "<span class=\"Cerreur\">"._T('csvspip:redac_poubelle');
			foreach ($TerrA_poub as $Aep) { 
				echo "<br />"._T('csvspip:admin').$Aep['login']._T('csvspip: erreur').' '.$Aep['erreur'];
			}
			$err_total ++;
		}
		else { 
			echo "<br />"._T('csvspip:suppression_debut').count($TresA_eff)._T('csvspip:comptes_admin_ok')."<br>";
			echo "<br />"._T('csvspip:poubelle_debut').count($TresA_poub)._T('csvspip:comptes_admin_ok')."<br>";
		}
		if ($archivagea != 0) {
			echo "<br />"._T('csvspip:archivage_debut').$cteur_articles_deplacesA._T('csvspip:archivage_fin').$nom_rub_archivesA;
		}  
		if ($supprimer_articlesa == 1) {
			echo "<br />"._T('csvspip:suppression_debut').$cteur_articles_supprimesA._T('csvspip:suppression_fin')."<br>";
		}

		if (count ($TerrA_eff_rub_admins) > 0) {
			echo "<span class=\"Cerreur\">"._T('csvspip:suppr_redac');
			foreach ($TerrA_eff_rub_admins as $Aer) { 
				echo _T('csvspip:err_eff_adm_rub').$Aer['login']._T('csvspip: erreur').$Aer['erreur'];
			}
			echo "</span>";
			$err_total ++;					 		
		}
	}   // fin effacer les absents V 2.3
	echo fin_cadre_couleur(true);			

// �tape 5 : si n�cessaire int�gration des admins comme administrateurs restreints de la rubrique de leur sous-groupe
	//$id_rub_admin_defaut
	if ($groupe_admins != '-1') {
		$Terr_adm_rub = array();
		$Tres_adm_rub = array();
		$sql54 = sql_select("ss_groupe, nom, id_spip", "spip_tmp_csv2spip", "LOWER(groupe) = '$groupe_admins'");
		while ($data54 = sql_fetch($sql54)) {
			$login_adm_ec = strtolower($data54['nom']);
			$id_adm_ec = $data54['id_spip'];
			if ($_POST['rub_prof'] == 1) {
				if ($data54['ss_groupe'] != '') {
					$ss_grpe_ec = $data54['ss_groupe'];
					$sql55 = sql_query("SELECT id_rubrique FROM spip_rubriques WHERE titre = '$ss_grpe_ec' LIMIT 1");
					$data55 = sql_fetch($sql55);
					$id_rubrique_adm_ec = $data55['id_rubrique'];									 		
				}
				else {
					$id_rubrique_adm_ec = $id_rub_admin_defaut;
					$ss_grpe_ec = '';
				}
			}
			$sql57 = sql_query("SELECT COUNT(*) AS existe_adm_rub FROM $Tauteurs_rubriques WHERE id_auteur = '$id_adm_ec' AND id_rubrique = '$id_rubrique_adm_ec' LIMIT 1");
			$data57 = sql_fetch($sql57);
			if ($data57['existe_adm_rub'] == 0) {
//print '<br>rubrique $ss_grpe_ec = '.$ss_grpe_ec.' $id_rubrique_adm_ec = '.$id_rubrique_adm_ec.'$id_adm_ec = '.$id_adm_ec;								 
				sql_query("INSERT INTO $Tauteurs_rubriques (id_auteur, id_rubrique) VALUES ('$id_adm_ec', '$id_rubrique_adm_ec')");
				if (mysql_error() != '') {
					$Terr_adm_rub[] = array('login' => $login_adm_ec, 'rubrique' => $ss_grpe_ec, 'erreur' => mysql_error());
				}
				else {
					$Tres_adm_rub[] = $login_adm_ec;
				}
			}
		}
		echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape5'));
//						 echo "<h2>"._T('csvspip:titre_etape5')."</h2>";
		if (count($Terr_adm_rub) > 0) {
			echo "<span class=\"Cerreur\">"._T('csvspip:err_admin_rubrique');
			foreach ($Terr_adm_rub as $ear) { 
				echo _T('csvspip:admin').$ear['login']._T('csvspip:rubrique_').$ear['rubrique']._T('csvspip: erreur').$ear['erreur']."<br>";
			}	
			echo "</span>";
			$err_total ++;
		}
		else {
			echo 'Attribution d\'une sous-rubrique pour '.count($Tres_adm_rub).' administrateurs restreints = OK<br>';
		}
		echo fin_cadre_couleur(true);
	}
	
// Etape 6 : si n�cessaire cr�ation d'un article par rubrique 					
	if ($_POST['art_rub'] == 1 AND $_POST['rub_prof'] == 1) {
		$Terr_art_rub = array();
		$Tres_art_rub = array();
		$sql57 = sql_select('ss_groupe, nom', "spip_tmp_csv2spip", "groupe = '$groupe_admins' AND ss_groupe != ''", "ss_groupe");
		while ($data57 = sql_fetch($sql57)) {
			$titre_rub_ec = $data57['ss_groupe'];
			$sql58 = sql_query("SELECT id_rubrique, id_parent, id_secteur FROM spip_rubriques WHERE titre = '$titre_rub_ec' AND id_parent = '$rubrique_parent' LIMIT 1");
			$data58 = sql_fetch($sql58);
			$id_rub_ec = $data58['id_rubrique'];
			$id_parent_ec = $data58['id_parent'];
			$id_sect_ec = $data58['id_secteur'];
			$date_ec = date("Y-m-d H:i:s");
			$titre_ec = 'Bienvenue dans la rubrique '.$titre_rub_ec;
			$sql432 = sql_query("SELECT id_article FROM $Tarticles WHERE id_rubrique = '$id_rub_ec' AND titre = '$titre_ec' LIMIT 1");
			if (sql_count($sql432) < 1) {
				$data432 = sql_fetch($sql432);
				sql_query("INSERT INTO $Tarticles (id_article, id_rubrique, id_secteur, titre, date, statut ) VALUES ('', '$id_rub_ec', '$id_sect_ec', '$titre_ec', '$date_ec', 'publie')");
				if (mysql_error() != '') {
					$Terr_art_rub[] = array('rubrique' => $titre_rub_ec, 'erreur' => mysql_error());
				}
				else {
					$Tres_art_rub[] = $titre_rub_ec;
				}
			}
		}
		echo debut_cadre_couleur(_LOGO_CSV2SPIP, true, "", _T('csvspip:titre_etape6'));
	//						 echo "<h3>"._T('csvspip:titre_etape6')."</h3>";
		if (count($Terr_art_rub) > 0) {
			echo "<span class=\"Cerreur\">"._T('csvspip:err_article');
			foreach ($Terr_art_rub as $eart) { 
				echo _T('csvspip:rubrique_').$eart['rubrique']._T('csvspip:erreur').$eart['erreur']."<br>";
			}	
			echo "</span>";
			$err_total ++;
		}
		else {
			echo _T('csvspip:ok_etape6_debut').count($Tres_art_rub)._T('csvspip:ok_etape6_fin')."<br>";
		}
		echo fin_cadre_couleur(true);
	}
}

?>
