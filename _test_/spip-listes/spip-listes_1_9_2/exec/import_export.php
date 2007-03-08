<?php

/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'information par email pour SPIP*/
/* Copyright (C) 2004 Vincent CARONv.caron<at>laposte.net , http://bloog.net*/
/**/
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation*/
/* (version 2). */
/**/
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE, */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU*/
/* pour plus de d�ails.*/
/**/
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU*/
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la*/
/* Free Software Foundation,*/
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis. */
/******************************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/acces');
include_spip('inc/affichage');

function exec_import_export(){

	global $connect_statut;
	global $connect_toutes_rubriques;
	global $connect_id_auteur;
	global $type,$list_abo;
	global $new, $etape;

	$nomsite=lire_meta("nom_site"); 
	$urlsite=lire_meta("adresse_site"); 

	// generation du fichier export ?
	if (isset($_POST['export_txt']) && isset($_POST['export_id']) && $connect_statut == "0minirezo" ) {
		$export_id =$_POST['export_id'];
		if (intval($export_id)>0) {
			$abonnes = spip_query("SELECT id_auteur FROM spip_auteurs_listes WHERE id_liste="._q($export_id));
			$str_export= "# spip-listes\r\n";
			$str_export .= "# "._T('spiplistes:membres_liste')."\r\n";
			$str_export .= "# liste id: $export_id\r\n";
			$str_export .= "# date: ".date("Y-m-d")."\r\n\r\n";
			while($row = spip_fetch_array($abonnes)) {
				$abonne = $row['id_auteur'];
				$extras = get_extra($abonne,"auteur");
				if ($extras["abo"]=="html" || $extras["abo"]=="texte") {
					$subresult = spip_query("SELECT email FROM spip_auteurs WHERE statut!='5poubelle' AND statut!='nouveau' AND id_auteur="._q($abonne)." LIMIT 1");
					while ($subrow = spip_fetch_array($subresult)) {
						$str_export .= $subrow['email']."\r\n";
					}
 				}
			}
			header("Content-type: text/plain");
			header("Content-Disposition: attachment; filename=\"export_liste$export_id-".date("Y-m-d").".txt\"");
			echo $str_export;
			exit;
		}
	else{

		if($export_id == "abo_sans_liste"){
			$abonnes = spip_query("select a.id_auteur, count(d.id_liste) from spip_auteurs a left join spip_auteurs_listes d on a.id_auteur = d.id_auteur group by a.id_auteur having count(d.id_liste) = 0;");

			$str_export= "# spip-listes\r\n"; 
			$str_export .= "# "._T('spiplistes:membres_liste')."\r\n";
			$str_export .= "# liste id: $export_id\r\n";
			$str_export .= "# date: ".date("Y-m-d")."\r\n\r\n";
			while($row = spip_fetch_array($abonnes)) {
				$abonne = $row['id_auteur'];
				$extras = get_extra($abonne,"auteur");
				if ($extras["abo"]=="html" || $extras["abo"]=="texte") {
					$subresult = spip_query("SELECT email FROM spip_auteurs WHERE statut!='5poubelle' AND statut!='nouveau' AND id_auteur="._q($abonne)." LIMIT 1");
					while ($subrow = spip_fetch_array($subresult)) {
						$str_export .= $subrow['email']."\r\n";
 					}
 				}
			}
			header("Content-type: text/plain");
			header("Content-Disposition: attachment; filename=\"export_liste$export_id-".date("Y-m-d").".txt\"");
			echo $str_export;
			exit;
		}
 		if($export_id == "desabo"){
			$result = spip_query("SELECT id_auteur, nom, extra FROM spip_auteurs");
			$nb_inscrits = spip_num_rows($result);

			$str_export= "# spip-listes\r\n";
			$str_export .= "# "._T('spiplistes:membres_liste')."\r\n";
			$str_export .= "# liste id: $export_id\r\n";
			$str_export .= "# date: ".date("Y-m-d")."\r\n\r\n";
			while($row = spip_fetch_array($result)) {
				$abonne = $row['id_auteur'];
				$extras = get_extra($abonne,"auteur");	
				if ($extras["abo"]=="non" || !$extras["abo"]) {
					$subresult = spip_query("SELECT email FROM spip_auteurs WHERE statut!='5poubelle' AND statut!='nouveau' AND id_auteur="._q($abonne)." LIMIT 1");
					while ($subrow = spip_fetch_array($subresult)) {
						$str_export .= $subrow['email']."\r\n";
					}
				}
			}
			header("Content-type: text/plain");
			header("Content-Disposition: attachment; filename=\"export_liste$export_id-".date("Y-m-d").".txt\"");
			echo $str_export;
			exit;
		}
	}
	}

// generation du fichier export fin

// Admin SPIP-Listes
	echo debut_page("SPIP-listes", "redacteurs", "spiplistes");

// spip-listes bien installe?
	if (!function_exists(spip_listes_onglets)){
		echo("<h3>erreur: spip-listes est mal install&eacute;!</h3>"); 
		echo("<p>V&eacute;rifier les &eacute;tapes d'installation, notamment si vous avez bien renomm&eacute;<i>mes_options.txt</i> en <i>mes_options.php3</i>.</p>");
		echo fin_page();
		exit;
	}

	if ($connect_statut != "0minirezo" ) {
		echo "<p><b>"._T('spiplistes:acces_a_la_page')."</b></p>";
		echo fin_page();
		exit;
	}

	if (($connect_statut == "0minirezo") OR ($connect_id_auteur == $id_auteur)) {
		$statut_auteur=$statut;
		spip_listes_onglets("messagerie", "Spip listes");
	}

	debut_gauche();

	spip_listes_raccourcis();

	creer_colonne_droite();


	debut_droite("messagerie");

// import //

	function test_login2($mail) {
		if (strpos($mail, "@") > 0) $login_base = substr($mail, 0, strpos($mail, "@"));
		else $login_base = $mail;

		$login_base = strtolower($login_base);
		$login_base = ereg_replace("[^a-zA-Z0-9]", "", $login_base);
		if (!$login_base) $login_base = "user";

		for ($i = 0; ; $i++) {
			if ($i) $login = $login_base.$i;
			else $login = $login_base;
			$result = spip_query("SELECT id_auteur FROM spip_auteurs WHERE login="._q($login));
			if (!spip_num_rows($result)) break;
		}

	return $login;
	}

	$format = $GLOBALS['suppl_abo'];


	// import form
	echo debut_cadre_relief("redacteurs-24.gif", false, "", _T('spiplistes:importer'));

	switch ($etape) {
		case "2" :
	{

		if (!$insert_file) $insert_file = $_FILES["insert_file"]["tmp_name"] ;
		if ($insert_file && $insert_file != "none") {		
		  $import_file = _DIR_TMP."import_email.txt";		  
			if(move_uploaded_file($insert_file,$import_file)) {
				// if(ereg("^php[0-9A-Za-z_.-]+$", basename($insert_file)))
				if(!empty($insert_file) && $insert_file != "none" && ereg("^php[0-9A-Za-z_.-]+$", basename($insert_file)))
				$liste = fread(fopen($import_file, "r"), filesize($import_file)); //pour NS et IE

				$liste=ereg_replace("\n|\r|\n\r|\r\n|\n\n","\n",$liste);
				$liste = explode( "\n",$liste);
				$new_abonne = 0;

				for($i=0;$i<sizeof($liste); $i++) {
          $tmp_log = "\n<br style='clear:both'/>";				  

					/* Ajouter un nouvel enregistrement dans la table */
					$liste[$i]=trim($liste[$i]);
					$ligne_nb = ($i+1);
						if(!empty($liste[$i])){

						// Inscription
						// Ajouter un code pour retrouver l'abonne
						$mail_inscription = $liste[$i] ;
						
						if(email_valide($mail_inscription)){
						  $tmp_log .= "<div style='color:#090;margin-bottom:5px;width:220px;float:left;'>$mail_inscription</div> " ;

							$pass = creer_pass_aleatoire(8, $mail_inscription);
							$nom_inscription = test_login2($mail_inscription);
							$login = test_login2($mail_inscription);
							$mdpass = md5($pass);
							$htpass = generer_htpass($pass);
							$statut = "6forum" ;
							$cookie = creer_uniqid();
							$extras = bloog_extra_recup_saisie('auteurs');

							$resulta = spip_query("SELECT * FROM spip_auteurs WHERE email="._q($mail_inscription));

							if ($row = spip_fetch_array($resulta)) {
								$nom = $row['nom'] ;
								$mail = $row['email'] ;
								$id = $row['id_auteur'] ;
								$tmp_log .= _T('spiplistes:adresse_deja_inclus')." ";								
								$ok = spip_query("UPDATE spip_auteurs SET extra="._q($extras)." WHERE id_auteur="._q($id));
								if ($ok)  $tmp_log .=  "("._T('spiplistes:mis_a_jour').")";
							}
							else {
				 				$tmp_log .= "<strong>$format</strong>";
								spip_query("INSERT INTO spip_auteurs (nom, email, login, pass, statut, htpass, extra, cookie_oubli) ".
								"VALUES ("._q($nom_inscription).","._q($mail_inscription).","._q($login).","._q($mdpass).","._q($statut).","._q($htpass).","._q($extras).","._q($cookie).")");
							}

							// Inscription aux listes
							// abonnement aux listes http://www.phpfrance.com/tutorials/index.php?page=2&id=13
							$resu = spip_query("SELECT * FROM spip_auteurs WHERE email="._q($mail_inscription));

							// l'abonne existe deja.
							if ($row = spip_fetch_array($resu)) {
								$id_auteur = $row['id_auteur'];
								$statut = $row['statut'];
								$nom = $row['nom'];
								$mel = $row['email'];

							// on abonne l'auteur aux listes
								if(is_array($list_abo)){
									reset($list_abo);
									while( list(,$val) = each($list_abo) ){										 
										 //$tmp_log .= "liste $val ";
										 $result = spip_query("DELETE FROM spip_auteurs_listes WHERE id_auteur="._q($id_auteur)." AND id_liste="._q($val));

										 if($GLOBALS['suppl_abo'] !='non')
											           spip_query("INSERT INTO spip_auteurs_listes (id_auteur,id_liste) VALUES ("._q($id_auteur).","._q($val).")");										 	
										 
									}																				 
									$new_abonne++;
								}else{
								if($GLOBALS['suppl_abo'] =='non'){
									$result=spip_query("DELETE FROM spip_auteurs_mod_listes WHERE id_auteur="._q($id_auteur)); 
									$tmp_log .= "<strong>"._T('spiplistes:desabo')."</strong>";
								}
								}
							}
						} else {

							$tmp_log .= _T('spiplistes:erreur_import').$ligne_nb.": ";
							$tmp_log .= "<span style='color:red;margin-bottom:5px'>".$liste[$i]." : </span>";
						}//email valide
						
            echo $tmp_log;
					}//listei

				}// for

				unlink($import_file);				
				echo "<div style='margin:10px 0'><strong>"._T('spiplistes:adresses_importees').": </strong> $new_abonne</div>\n";
			}// move et file

		} // insert
		else echo "<br /><br /><center><strong>"._T('spiplistes:erreur')."</strong></center>";
		echo "<a href='?exec=import_export'>["._T('spiplistes:retour_link')."]</a>";
	}
	break ;

	default :
	if($spip_version < 1.8 ){
		echo "<h3>"._T('spiplistes:importer')."</h3>" ;
	}

	echo _T('spiplistes:importer_fichier_txt')."<div>";

	$list = spip_query ("SELECT * FROM spip_listes WHERE statut = 'liste' OR statut = 'inact' ");
	$nb_listes = spip_num_rows($list);
	if($nb_listes == 0){
		echo "<fieldset> ";
		echo "<legend>"._T('spiplistes:abonnement_newsletter')."</legend>";
		echo _T('spiplistes:importer_preciser');
		echo "<form action='$PHP_SELF?etape=2' method='post'enctype='multipart/form-data' name='importform'> ";
		bloog_extra_saisie('', 'auteurs', 'inscription');
	} else {
		echo "<fieldset> ";
		echo "<legend>"._T('spiplistes:abonnement_newsletter')."</legend>";
		echo _T('spiplistes:importer_preciser');
		echo "<div style='text-align:left'>" ;
		echo "<form action='$PHP_SELF?exec=import_export&etape=2' method='post' enctype='multipart/form-data'name='importform'> ";
		while($row = spip_fetch_array($list)) {
			$id_liste = $row['id_liste'] ;
			$titre = $row['titre'] ;
			if ($nb_listes = 1) $ischecked = "";
			else $ischecked = "checked='checked'";
			echo "<input type=\"checkbox\" name=\"list_abo[]\" $ischecked value=\"".$id_liste."\" />\n";
			echo "<a href='?exec=import_export&liste=$id_liste' title='informations sur cette liste'>$titre</a><br />" ;
		}
		echo "<br />";
		bloog_extra_saisie('', 'auteurs', 'inscription');
		echo "</div>";

	} // fin du test nb listes

	echo "<!-- <script language=\"javascript\">function Soumettre(){
			document.importform.fich.value=document.importform.insert_file.value;
			document.importform.submit();
		}
		</script>
		--> ";

	echo "<h5>"._T('spiplistes:importer_fichier')."</h5>";
	echo "<input type=file name=\"insert_file\" /><br /><br />";
	echo "<input type=\"hidden\" name=\"mode\" value=\"inout\" />";
	echo "<input type=\"hidden\" name=\"import\" value=\"oui\" />";
	echo "</div>" ;
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' onclick='Soumettre()' />";
	echo "</form>" ;

	} // switch

	/**************/

	echo "</fieldset></div>"; 
	echo fin_cadre_relief();

	// import form end

	// import end //

	// export //(added by erational.org)
	// formulaire d'export

	$list = spip_query ("SELECT * FROM spip_listes WHERE statut = 'liste' OR statut = 'inact' ");
	$nb_listes = spip_num_rows($list);
	if ($nb_listes > 0) {
		echo debut_cadre_relief("redacteurs-24.gif", false, "", _T('spiplistes:exporter'));
		echo "<form action='$PHP_SELF?exec=import_export' method='post'>\n";	 
		while($row = spip_fetch_array($list)) {
			$id_liste = $row['id_liste'] ;
			$titre = $row['titre'];
			if ($nb_listes==1) $checked = " checked='checked'";
			else $checked = "";
			echo "<input type=\"radio\" name=\"export_id\" value=\"".$id_liste."\"$checked />$titre <br />\n";
		}
		echo "<input type=\"radio\" name=\"export_id\"value=\"abo_sans_liste\"$checked /><strong>"._T('spiplistes:abonne_aucune_liste')."</strong> <br />\n";
		echo "<input type=\"radio\" name=\"export_id\"value=\"desabo\"$checked /><strong>"._T('spiplistes:desabonnes')."</strong> <br />\n"; 
		echo "<input type='submit' name='export_txt' class='fondo' value='"._T('bouton_valider')."' />\n";
		echo "</form>\n";
		echo fin_cadre_relief();
	}
	
	// export end //

	// MODE INOUT FIN --------------------------------------------------------------

	echo "<p style='font-family: Arial, Verdana,sans-serif;font-size:10px;font-weight:bold'>".$GLOBALS['spiplistes_version']."</p>" ;

	echo fin_gauche(), fin_page();

}

/******************************************************************************************/
/* SPIP-listes est un syst�e de gestion de listes d'abonn� et d'envoi d'information */
/* par emailpour SPIP.*/
/* Copyright (C) 2004 Vincent CARONv.caron<at>laposte.net , http://bloog.net*/
/**/
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique G��ale GNU publi� par la Free Software Foundation*/
/* (version 2). */
/**/
/* Ce programme est distribu�car potentiellement utile, mais SANS AUCUNE GARANTIE, */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou */
/* d'adaptation dans un but sp�ifique. Reportez-vous �la Licence Publique G��ale GNU*/
/* pour plus de d�ails.*/
/**/
/* Vous devez avoir re� une copie de la Licence Publique G��ale GNU*/
/* en m�e temps que ce programme ; si ce n'est pas le cas, �rivez �la*/
/* Free Software Foundation,*/
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, �ats-Unis. */
/******************************************************************************************/
?>
