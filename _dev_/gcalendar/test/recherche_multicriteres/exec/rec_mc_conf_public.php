<?php
######################################################################
# RECHERCHE MULTI-CRITERES                                           #
# Auteur: Dominique (Dom) Lepaisant - Novembre 2007                  #
# Adaptation de la contrib de Paul Sanchez - Netdeveloppeur          #
# http://www.netdeveloppeur.com                                      #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################
function exec_rec_mc_conf_public_dist(){

include_spip('inc/presentation');

#plug(11-07) :   elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$nbrub,
		$couleur_claire, $couleur_foncee;

	$sql = "SELECT colonnes as nbcol_actu, 
				colonnes_rub as nbcol_rub_actu, 
				taille_police as taille_actu, 
				taille_police_rub as taille_rub_actu, 
				couleur_police as coul_actu, 
				couleur_police_rub as coul_rub_actu, 
				couleur_bordure as bord_actu, 
				couleur_bordure_rub as bord_rub_actu 
				FROM spip_rmc_rubs_groupes_conf";
	$result = spip_query($sql);
if (mysql_num_rows($result) == 0){
	$message="La configuration de base affiche les groupes de mots sur une colonne<br />";
	$nbcol_rub_actu=1;
	$nbcol_actu=1;
	$taille_actu=12;
	$taille_rub_actu=10;
	$coul_actu='#000';
	$coul_rub_actu='#000';
	$bord_actu='#000';
	$bord_rub_actu='#000';
	$first=spip_query("INSERT INTO spip_rmc_rubs_groupes_conf ( colonnes , colonnes_rub, taille_police, taille_police_rub, couleur_police, couleur_police_rub ) VALUES (1, 1, 12, 10, '#000', '#000' )");
	}else{
	$row = mysql_fetch_assoc($result);
	extract($row);
	$message="";
	}
#plug(11-07) :   construction des "blocs"	
include_spip("inc/rec_mc_inc_pres");
#plug(11-07) :   rec_mc_inc_fonct.php	
	debut_page(_T('rmc:titre_page_admin'), "suivi", "rec_mc");

		echo gros_titre(_T('rmc:titre_page_admin'));

	debut_gauche();
	#plug(11-07) :    TODO : Etat des lieux...
		menu_admin() ;

	creer_colonne_droite();

	#plug(11-07) :    vers popup aide 
	bloc_ico_aide_ligne();

	#plug(11-07) :    signature
	echo "<br />";
	debut_boite_info();
		echo _T('rmc:signature');
	fin_boite_info();
	echo "<br />";

	debut_droite();

	debut_cadre_relief( _DIR_IMG_REC_MC."cles24.png");

		echo gros_titre(_T('rmc:titre_config_public'));
		echo "<br />";
				echo $message;

#plug(11-07) :  recherche sur tout le site - 
		echo _T('rmc:text_conf_public');
		echo "<br />";
			debut_cadre_trait_couleur('_DIR_IMG_PACK."tableau-24.gif"', false, '', _T('rmc:recherche_site'));
//				echo _T('rmc:text_conf_site');

#plug(11-07) :  recherche sur tout le site - debut du formulaire
				echo '<form action="'.generer_url_action('rec_mc_ecrireconf').'" method="post">';
				echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("rec_mc_conf_public")."' />\n";
				echo "<input type='hidden' name='hash' value='".calculer_action_auteur("ecrireconf-rien")."' />\n";
				echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";
#plug(11-07) :  recherche sur tout le site - nb de colonnes
			debut_cadre_relief(_DIR_IMG_REC_MC."tableau-24.gif");
				debut_band_titre($couleur_foncee, "verdana3", "bold");
				echo _T('rmc:colonnes');
				fin_bloc();
				echo 	"<p>";
				echo "<select style=\"width:50px;\" name=\"nbcol\" onchange=\"nbcol=this.options[this.selectedIndex].value\">";	
				for($i=1; $i<6; $i++){
					if($nbcol_actu==$i){
						echo "<option value=\"".$i."\" selected>".$i."</option> ";
					} else {
						echo "<option value=\"".$i."\">".$i."</option> ";
					}
				}
				echo "</select> ";
 				echo "<label> : "._T('rmc:insert_nb_colonnes')."</label>";
				echo "</p>";
			fin_cadre_relief();
			#plug(11-07) :  recherche sur tout le site -  taille de police
			debut_cadre_relief(_DIR_IMG_REC_MC."taille-24.gif");
				debut_band_titre($couleur_foncee, "verdana3", "bold");
				echo _T('rmc:taille_police');
				fin_bloc();
				echo 	"<p>";
				echo "<select style=\"width:50px;\" name=\"taille\" onchange=\"taille=this.options[this.selectedIndex].value\">";	
				for($i=8; $i<15; $i=$i+2){
					if($taille_actu==$i){
						echo "<option value=\"".$i."\" selected>".$i."</option> ";
					} else {
						echo "<option value=\"".$i."\">".$i."</option> ";
					}
				}
				echo "</select> ";
 				echo "<label> : "._T('rmc:insert_taille')."</label>";
				echo "</p>";
			fin_cadre_relief();
			#plug(11-07) :  recherche sur tout le site - couleur de police
			debut_cadre_relief(_DIR_IMG_REC_MC."style-24.gif");
				debut_band_titre($couleur_foncee, "verdana3", "bold");
				echo _T('rmc:couleur_police');
				fin_bloc();
				echo "<p>";
				echo "<input type=text  style='width:80px; color:".$coul_actu.";' name='coul' value='".$coul_actu."'>";	
				echo "<span style='padding:4px 0;background-color:".$coul_actu.";'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
 				echo "<label> : "._T('rmc:insert_coul')."</label>";
				echo "</p>";
			fin_cadre_relief();
			#plug(11-07) :  recherche sur tout le site - couleur de bordure
			debut_cadre_relief(_DIR_IMG_REC_MC."bordure-24.gif");
				debut_band_titre($couleur_foncee, "verdana3", "bold");
				echo _T('rmc:couleur_bordure');
				fin_bloc();
				echo "<p>";
				echo "<input type=text  style='width:80px;' name='bord' value='".$bord_actu."'>";	
				echo "<span style='padding:2px 0;border:2px solid ".$bord_actu.";'>&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
 				echo "<label> : "._T('rmc:insert_bord')."</label>";
				echo "</p>";
			fin_cadre_relief();
			#plug(11-07) :  recherche sur tout le site - bouton submit + fin du form
				echo "<p style=\"text-align:center\">";
				echo "<input type=\"submit\" name=\"submit\" value=\"Valider\">";
				echo "</p>";
				echo "</form>";
			fin_cadre_trait_couleur();

		#plug(11-07) :  recherche par rubrique
			debut_cadre_trait_couleur('_DIR_IMG_PACK."tableau-24.gif"', false, '', _T('rmc:recherche_rubrique'));

				echo '<form action="'.generer_url_action('rec_mc_ecrireconf').'" method="post">';
				echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("rec_mc_conf_public")."' />\n";
				echo "<input type='hidden' name='hash' value='".calculer_action_auteur("ecrireconf-rien")."' />\n";
				echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";

			#plug(11-07) :  recherche par rubrique - nb de colonnes
			debut_cadre_relief(_DIR_IMG_REC_MC."tableau-24.gif");
				debut_band_titre($couleur_foncee, "verdana3", "bold");
				echo _T('rmc:colonnes');
				fin_bloc();
				echo 	"<p>";
				echo "<select style=\"width:50px;\" name=\"nbcol_rub\" onchange=\"nbcol_rub=this.options[this.selectedIndex].value\">";	
				for($j=1; $j<6; $j++){
					if($nbcol_rub_actu==$j){
						echo "<option value=\"".$j."\" selected>".$j."</option> ";
					} else {
						echo "<option value=\"".$j."\">".$j."</option> ";
					}
				}
				echo "</select> ";
 				echo "<label> : "._T('rmc:insert_nb_colonnes_rub')."</label>";
				echo "</p>";
			fin_cadre_relief();

			#plug(11-07) :  recherche par rubrique - taille de police
			debut_cadre_relief(_DIR_IMG_REC_MC."taille-24.gif");
				debut_band_titre($couleur_foncee, "verdana3", "bold");
				echo _T('rmc:taille_police');
				fin_bloc();
				echo 	"<p>";
				echo "<select style=\"width:50px;\" name=\"taille_rub\" onchange=\"taille_rub=this.options[this.selectedIndex].value\">";	
				for($j=8; $j<15; $j=$j+2){
					if($taille_rub_actu==$j){
						echo "<option value=\"".$j."\" selected>".$j."</option> ";
					} else {
						echo "<option value=\"".$j."\">".$j."</option> ";
					}
				}
				echo "</select> ";
 				echo "<label> : "._T('rmc:insert_taille')."</label>";
				echo "</p>";
			fin_cadre_relief();
			#plug(11-07) :  recherche  par rubrique - couleur de police
			debut_cadre_relief(_DIR_IMG_REC_MC."style-24.gif");
				debut_band_titre($couleur_foncee, "verdana3", "bold");
				echo _T('rmc:couleur_police');
				fin_bloc();
				echo "<p>";
				echo "<input type=text  style='width:80px; color:".$coul_rub_actu.";' name='coul_rub' value='".$coul_rub_actu."'>";	
				echo "<span style='padding:4px 0;background-color:".$coul_rub_actu.";'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
 				echo "<label> : "._T('rmc:insert_coul_rub')."</label>";
				echo "</p>";
			fin_cadre_relief();
			#plug(11-07) :  recherch par rubrique - couleur de bordure
			debut_cadre_relief(_DIR_IMG_REC_MC."bordure-24.gif");
				debut_band_titre($couleur_foncee, "verdana3", "bold");
				echo _T('rmc:couleur_bordure');
				fin_bloc();
				echo "<p>";
				echo "<input type=text  style='width:80px;' name='bord_rub' value='".$bord_rub_actu."'>";	
				echo "<span style='padding:2px 0;border:2px solid ".$bord_rub_actu.";'>&nbsp;&nbsp;&nbsp;&nbsp;</span>";	
 				echo "<label> : "._T('rmc:insert_bord_rub')."</label>";
				echo "</p>";
			fin_cadre_relief();
			#plug(11-07) :  recherche par rubrique - bouton submit + fin du form
				echo "<p style=\"text-align:center\">";
				echo "<input type=\"submit\" name=\"submit2\" value=\"Valider\">";
				echo "</p>";
				echo "</form>";

			fin_cadre_trait_couleur(true);
	fin_cadre_relief();
	fin_page(true);//fin page
}#plug(11-07) :   finexec
?>