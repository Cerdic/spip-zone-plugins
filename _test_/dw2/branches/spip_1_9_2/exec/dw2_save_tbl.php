<?php 
/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Corps de page : Sauvegarde
+--------------------------------------------+
| VO . de M. ONFRAY 
| .. modifiee pour DW2 et spip 1.9
+--------------------------------------------+
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_dw2_save_tbl() {

// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

// page prim en cours
$page_affiche=_request('exec');

//
// requis
//

// verif admin .. verif install .. superglobal
include_spip("inc/dw2_inc_admin");

include_spip("inc/dw2_inc_func");
include_spip("inc/dw2_inc_pres");



//
// prepa
//

//
// reconstruire .. var=val des get et post
// var : force_email ; ponct_email ; gz ; flag_save_dw ;
// .. Option .. utiliser : $var = _request($var);
foreach($_GET as $k => $v) { $$k=$_GET[$k]; }
foreach($_POST as $k => $v) { $$k=$_POST[$k]; }


//
// parametre par defaut
//
######## param modifiable #########

$rep_bases = _DIR_DUMP;		// repertoire de stockage des fichiers - par defaut _DIR_DUMP (ecrire/data/)
$jours_obso = 15;				// fichier obsolete après n jours -> delete / Mettre '-1' pour desactiver

##################################

//
// email
$defaut_email = lire_meta('email_webmaster');
$destinataire_save = "";			// Email du destinataire
if($force_email==1) { $destinataire_save = $defaut_email; }
if($force_email==2 && !empty($ponct_email)) { $destinataire_save = $ponct_email; }

//
// Param. a laisser tel que ... pour DW2 !!
$base = $GLOBALS['spip_mysql_db'];			// Nom BASE
$prefixe_save = "dw2_";						// Prefixe du fichier de sauvegarde
$accepter = "dw2_";							// Sauver que les tables avec une chaine dans le nom :
$eviter = "_index;_temp;_cache;_triche";	// Tables ignorees si contient dans son nom les chaines specifiees.
											// Sauve la structure !
$structure = true;					// Sauvegarde la structure des tables -> true
$donnees = true;					// Sauvegarde les donnees des tables -> true
$insertComplet = true;				// clause INSERT avec nom des champs -> true
$frequence_maj = -1;				// Pour DW2, mis a '-1' --> autorise ainsi +ieurs save /24h.


//
// affichage page
//

debut_page(_T('dw:titre_page_admin'), "suivi", "dw2_admin");
echo "<a name='haut_page'></a><br />";

gros_titre(_T('dw:titre_page_admin'));


debut_gauche();

	menu_administration_telech();
	menu_voir_fiche_telech();
	menu_config_sauve_telech();
	
	// module outils
	bloc_popup_outils();

	// module delocaliser
	bloc_ico_page(_T('dw:acc_dw2_dd'), generer_url_ecrire("dw2_deloc"), _DIR_IMG_DW2."deloc.gif");


creer_colonne_droite();

	// vers popup aide 
	bloc_ico_aide_ligne();

	// signature
	echo "<br />";
	debut_boite_info();
		echo _T('dw:signature', array('version' => _DW2_VERS_LOC));
	fin_boite_info();
	echo "<br />";

debut_droite();


debut_cadre_trait_couleur(_DIR_IMG_PACK."base-24.gif", false, "", _T('dw:sauvegarde_tables_dw'));

//
// Aff. regalges config
//
	echo "<div style='float:left; width:60px'>";
	echo "<img src='"._DIR_IMG_PACK."warning.gif'>";
	echo "</div>";
	echo "<div class='arial2' style='margin:0px 20px 0px 60px;'>";
	echo _T('dw:info_save_fonction');
	echo "</div>";
	echo "<div style='clear:both;'><br></div>";

	debut_boite_filet("a");
	echo "<table align='center' border='0' cellpadding='2' cellspacing='1' width='100%' class='verdana3'>";
	echo "<tr><td width='50%'>"._T('dw:repert_stock_save')."</td>";
	echo "<td>".$rep_bases."</td></tr>";
	
	echo "<tr><td width='50%'>"._T('dw:sauvegardes_obsolete_delais')."</td>";
	echo "<td>";
	if($jours_obso=="-1") { echo _T('dw:desactive'); }
	else { echo $jours_obso." "._T('dw:jour_s'); }
	echo "</td></tr>"; 
	echo "</td></tr>";
	echo "</table>";
	fin_bloc();

		$invisible = $jours_obso;
		if ($invisible)
			echo bouton_block_invisible('param');
		else 
			echo bouton_block_visible('param');
		echo "<span class='verdana2'>&nbsp;&nbsp;"._T('dw:attention_info')."</span><br>";
		
		if ($invisible)
			echo debut_block_invisible('param');
		else
			echo debut_block_visible('param');

			echo "<div style='margin-left:60px;' class='verdana2'>"._T('dw:info_save_param')."</div>";
		echo fin_block();

	echo "<br>";


//
// Formulaire sauvegarde
//
	echo "<form action='".generer_url_ecrire("dw2_save_tbl")."' method='post' class='verdana3'>";
	
	// envoi fichier save par email ?
	debut_boite_filet("a");
	echo "<b>"._T('dw:envoi_sauvegarde_mail')."</b><br>";
	echo "<input name='force_email' type='radio' value='' checked>"._T('dw:non')."<br>";
	echo "<input name='force_email' type='radio' value='1'>"._T('dw:au_webmaster')." ".$defaut_email."<br>\n";
	echo "<input name='force_email' type='radio' value='2'>"._T('dw:cette_adresse')."&nbsp;";
	echo "<input type='text' name='ponct_email' value='$ponct_email' size='30' class='fondl'><br>\n";
	if(!empty($ponct_email) && !$ok_mail=email_valide($ponct_email)) {
		echo "<font color=red>"._T('dw:adresse_mail_bad')."</font>";
		// annuler la sauvegarde !
		$flag_save_dw='';
	}
	fin_bloc();
	
	echo "<br>";
	
	// compression gz ?
	debut_boite_filet("a");
	echo "<b>"._T('dw:compression_fichier')."</b><br />";
	echo "<input name='gz' type='radio' value='".true."' checked>".
				$rep_bases.$prefixe_save.$base."_".date("ymd")."<b>.gz</b>";
	echo "<br />";
	echo "<input name='gz' type='radio' value='".false."'>".
				$rep_bases.$prefixe_save.$base."_".date("ymd")."<b>.sql</b>";
	echo "<input type='hidden' name='flag_save_dw' value='1'>";
	if (!$flag_gz) {
		echo "<br /><font color=red>"._T('dw:info_save_non_compress')."</font>";
	}	
	fin_bloc();
		
	echo "<div align='right'><input type='submit' value='"._T('dw:sauvegarde')."' class='fondo'></div>";
	echo "</form>";
	

	// execute script sauvegarde
	if($flag_save_dw) {
		echo "<br />";
		debut_cadre_enfonce("", false, "", _T('dw:resultat_sauvegarde'));
			include(_DIR_PLUGIN_DW2."/inc/dw2_inc_save.php");
		fin_cadre_enfonce();
	}
fin_cadre_trait_couleur();


//
// Aff. les precedentes sauvegardes
//
debut_cadre_trait_couleur(_DIR_IMG_DW2."catalogue.gif", false, "", _T('dw:fichiers_save_dans_repert', array('rep_bases' => $rep_bases)));
   // Lister fichiers contenus
   $entree = array();
   $myDirectory = opendir($rep_bases);
   while($entryName = readdir($myDirectory)) {
      //uniquement les fichiers du type : prefixe_nom_de_la_base
      if (substr($entryName, 0, strlen($prefixe_save . $base)) == $prefixe_save . $base) $entree[] = $entryName;
   }
   closedir($myDirectory);
   //trie dans l'ordre décroissant les sauvegardes
   rsort($entree);

	echo "<table align='center' border='0' cellpadding='3' cellspacing='1' width='100%' class='verdana2'>";
	for ($i=0; $i<count($entree); $i++) {
      echo "<tr><td>".$entree[$i]."</td>";
      $temps = filemtime($rep_bases . $entree[$i]);
      $jour = date("d", $temps); //format : 01->31
      $annee = date("y", $temps); //format : 2 chiffres
      $mois = date("m", $temps);
      $heure = date("H", $temps);
      $minutes = date("i", $temps);
      $date = _T('dw:date_heure', array('jour' => $jour, 'mois' => $mois, 'annee' => $annee, 'heure' => $heure, 'minutes' => $minutes));
      echo "<td><div align='right'>". $date . "</div></td>";
	  echo "<td width='25%'><div align='right'>".taille_octets(filesize($rep_bases.$entree[$i]))."</div></td></tr>\n";
   }
   echo "</table>";
fin_cadre_trait_couleur();



//
	bloc_minibout_act(_T('dw:top'), "#haut_page", _DIR_IMG_PACK."spip_out.gif","","");
	echo "<div style='clear:both;'></div>";

	fin_page();
} // fin exec_
?>
