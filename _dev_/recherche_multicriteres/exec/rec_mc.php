<?php
######################################################################
# RECHERCHE MULTI-CRITERES                                           #
# Auteur: Dominique (Dom) Lepaisant - Novembre 2007                   #
# Adaptation de la contrib de Paul Sanchez - Netdeveloppeur          #
# http://www.netdeveloppeur.com                                      #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################


include_spip('inc/presentation');

function exec_rec_mc_dist(){
// elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

//#plug(11-07) : fonctions requises...(construction de la liste des rubriques, ...)
	include_spip("inc/rec_mc_inc_fonct");

//#plug(11-07) : Initialisation de l'id rubrique . Mis a -1 pour le premier affichage
	$rub = -1;
//#plug(11-07) : Recupération de l'id rubrique si $_post
	if (isset($_POST['rub'])) 
		$rub = $_POST['rub'];
	elseif (isset($_GET['rub'])) 
		$rub = $_GET['rub'];
		
	$Refresh  = "";		
	if (isset($_POST['refresh'])) {
		$Refresh = $_POST['refresh'];
	}
	elseif (isset($_GET['refresh'])) {
		$Refresh = $_GET['refresh'];
	}

//#plug(11-07) : construction des "blocs"	
include_spip("inc/rec_mc_inc_pres");

	debut_page(_T('rmc:titre_page_admin'), "suivi", "rec_mc");
		echo "<a name='haut_page'></a><br />";
		echo gros_titre(_T('rmc:titre_page_admin'));

	debut_gauche();

	//#plug(11-07) :  TODO : Etat des lieux...
		menu_admin() ;

	creer_colonne_droite();

	//#plug(11-07) :  vers popup aide 
	bloc_ico_aide_ligne();

	//#plug(11-07) :  signature
	echo "<br />";
	debut_boite_info();
		echo _T('rmc:signature');
	fin_boite_info();
	echo "<br />";

	debut_droite();

	debut_cadre_relief( _DIR_IMG_REC_MC."rec_mc-24.png");

		echo gros_titre(_T('rmc:titre_config'));

		echo _T('rmc:info_config');
		echo "<br />";

		//#plug(11-07) :  verif de l'existence d'au moins une rubrique
		$r=mysql_query("SELECT * FROM spip_rubriques");
		$nb_r=mysql_num_rows($r);		
		//#plug(11-07) : verif de l'existence d'au moins un groupe de mots
		$g=mysql_query("SELECT * FROM spip_groupes_mots");
		$nb_g=mysql_num_rows($g);		
		if($nb_r==0){
			debut_boite_erreur(_T('rmc:erreur_rubrique')) ;

		}else if($nb_g==0){
			debut_boite_erreur(_T('rmc:erreur_groupes_mots')) ;

		}else{
			//#plug(11-07) : Selectionner une rubrique
			debut_cadre_trait_couleur('', false, '', _T('rmc:rubriques'));
			echo _T('rmc:text_select_rubrique')." <br /><br />";

			echo "<form action=\" ".$PHP_SELF." \" method=\"POST\">";
			//#plug(11-07) :  Commentaire Dom : ma ligne qui merde !!!! ne merde plus!!!!!!!!!!!!!!
			echo "<select style=\"width:100%;\" name=\"rub\" onchange=\"window.location=('".generer_url_ecrire("rec_mc","rub='+this.options[this.selectedIndex].value").")\">";	
			if ($rub == -1) echo "<option value=\"-1\" selected><b>"._T('rmc:select_rubrique')."</b>";
			else echo "<option value=\"-1\"><b>"._T('rmc:select_rubrique')."</b>";

	
			if ($rub == 0) echo "<option value=\"0\" selected style=\"font-weight:bold\">"._T('rmc:select_ttes_rubriques');
			else echo "<option value=\"0\" style=\"font-weight:bold\">"._T('rmc:select_ttes_rubriques');
	
			$query = "SELECT id_parent FROM spip_rubriques order by titre";
			$result=spip_query($query);

			while($row=spip_fetch_array($result)){
				$parent_parent=$row['id_parent'];
			}
		getenfant(0,$rub);
		echo "</select><BR>\n";
		echo "</form>";
		fin_cadre_trait_couleur();
		}


		$idgroupeprec = 0;
		
		if ($rub != -1) {

		debut_cadre_trait_couleur ('', false, '', _T('rmc:groupes_mots'));

//#plug(11-07) :  formulaire - envoi du traitement vers action/rec_mc_ajoutgroupes
			echo '<form action="'.generer_url_action('rec_mc_ajoutgroupes').'" method="post">';
			echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("rec_mc", "rub=".$rub)."' />\n";
			echo "<input type='hidden' name='hash' value='".calculer_action_auteur("ajoutgroupes-rien")."' />\n";
			echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";
			echo "<input type='hidden' name='rub' value='$rub'>";
	
		
			if ($rub > -1) {
				$sql = "SELECT spip_groupes_mots.titre, spip_groupes_mots.id_groupe, spip_mots.titre AS titremot, spip_mots.id_mot as id_mot, spip_rmc_rubs_groupes.id_rubrique AS idrub FROM spip_groupes_mots 
								INNER JOIN spip_mots ON spip_mots.id_groupe = spip_groupes_mots.id_groupe 
								INNER JOIN spip_rmc_rubs_groupes ON spip_rmc_rubs_groupes.id_groupe = spip_groupes_mots.id_groupe 
								WHERE id_rubrique = $rub GROUP BY spip_groupes_mots.titre, spip_groupes_mots.id_groupe, spip_mots.titre 
								ORDER BY spip_groupes_mots.id_groupe";
			}
		
			$result = spip_query($sql);

		
			$aff_groupes_select="";
			$listidgrp = "";
			$compt = 0;
			$nbr = 0;
			if ($result) {
				while ($row = mysql_fetch_assoc($result)) {
					extract($row);
				
					if ($idgroupeprec != $id_groupe) {
							$aff_groupes_select.=" <br /><div style=\"background-color:#ccc; border:1px solid ".$couleur_foncee."; font-size:1.2em; font-weight:bold\"><input type=checkbox name=\"idgrp[]\" value=\"$id_groupe\" checked />$titre</div>";
							$nbr = ++$nbr;
						if ($compt == 0) {
							$listidgrp .= "$id_groupe";
							$compt = 1;
							$nb_groupes = ++$ng;
						}
						else $listidgrp .= ",$id_groupe";
					}
					$s="SELECT id_mot_exclu FROM spip_rmc_mots_exclus WHERE id_mot_exclu=$id_mot AND id_rubrique=$rub";
					$r=spip_query($s);
					$rw=spip_fetch_array($r);
					if($rw['id_mot_exclu']==$id_mot){
					$aff_groupes_select.= "<div style=\"border:1px solid ".$couleur_foncee."; background-color:#eee; font-size:.9em;height:15px;\"  ><span style='float:right;color:red;font-weight:bold;'>"._T('rmc:mot_exclu')."<input type=checkbox name='motsexclus[]' value='$id_mot' style='margin:2px;padding:0;' checked='checked' /></span> $titremot</div>";
					}else{
					$aff_groupes_select.= "<div style=\"border:1px solid ".$couleur_foncee."; background-color:#eee; font-size:.9em;height:15px;\"  ><span style='float:right;color:red;'>"._T('rmc:mot_exclure')."<input type=checkbox name='motsexclus[]' value='$id_mot' style='margin:2px;padding:0;'/></span> $titremot</div>";
					}
					$idgroupeprec = $id_groupe;

				}
				mysql_free_result($result);
			}
			if ($rub > -1) {
				if ($listidgrp != "") 
					$sql = "SELECT spip_groupes_mots.titre, spip_groupes_mots.id_groupe, spip_mots.titre 
								AS titremot 
								FROM spip_groupes_mots 
								INNER JOIN spip_mots ON spip_mots.id_groupe = spip_groupes_mots.id_groupe 
								WHERE spip_groupes_mots.id_groupe not IN ($listidgrp) 
								GROUP BY spip_groupes_mots.titre, spip_groupes_mots.id_groupe, spip_mots.titre 
								ORDER BY spip_groupes_mots.id_groupe";
				else 
					$sql = "SELECT spip_groupes_mots.titre, spip_groupes_mots.id_groupe, spip_mots.titre 
								AS titremot 
								FROM spip_groupes_mots 
								INNER JOIN spip_mots ON spip_mots.id_groupe = spip_groupes_mots.id_groupe 
								GROUP BY spip_groupes_mots.titre, spip_groupes_mots.id_groupe, spip_mots.titre 
								ORDER BY spip_groupes_mots.id_groupe";
			}
			$result = spip_query($sql);

			$idgroupeprc = 0;	
			$aff_groupes_noselected = "";
			$j=0;
			if ($result) {
				while ($row = mysql_fetch_assoc($result)) {
					extract($row);
				
					if ($idgroupeprec != $id_groupe) {
						$aff_groupes_noselected.=" <br /><div style=\"background-color:#ccc; border:1px solid ".$couleur_foncee."; font-size:1.2em; font-weight:bold\"><input type=checkbox name=\"idgrp[]\" value=\"$id_groupe\">$titre</div>";
						$nb_groupes_at = ++$j;
					}
					$aff_groupes_noselected.= "<div style=\"border:1px solid ".$couleur_foncee."; background-color:#eee; font-size:.9em\"  > $titremot</div>";
					$idgroupeprec = $id_groupe;
			
				}
				mysql_free_result($result);
			}

//#plug(11-07) : Affichage
			//#plug(11-07) : Affichage des groupes attribues (deja selectionnes)

			if($nbr >0){
				debut_cadre_relief(_DIR_IMG_PACK."groupe-mot-24.gif");
				debut_band_titre($couleur_foncee, "verdana3", "bold");
						if($idrub==0 ){
				echo _T('rmc:groupes_toutes_rubriques');
				}else{
				echo _T('rmc:groupes_mots_attribues');
				}
				fin_bloc();
				echo "$aff_groupes_select";
				fin_cadre_relief();
			}
				
			//#plug(11-07) : Affichage des groupes non attribues
			debut_cadre_relief(_DIR_IMG_PACK."groupe-mot-24.gif");
			if($nb_groupes_at >0){
				debut_band_titre($couleur_foncee, "verdana3", "bold");
				echo _T('rmc:groupes_mots_non_attribues');
				fin_bloc();
			}else{
				echo _T('rmc:groupes_mots_tous_attribues');
			} 
			echo $aff_groupes_noselected;
			fin_cadre_relief();
		echo "<input type=\"submit\" name=\"refresh\" value=\"Valider\">";
		echo "</form>";
		fin_cadre_trait_couleur(true);
		}
	fin_cadre_relief();
	fin_page(true);//fin page
}//finexec
?>