
<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

    include_spip('inc/presentation');
    include_spip('inc/gestion_base');
    include_spip('inc/version');

function exec_genespip() {
	global $connect_statut, $connect_toutes_rubriques;

	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques or $connect_statut == '1comite'))	{
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		echo fin_page(true);
		exit;
	}

	genespip_verifier_base();

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('genespip:genealogie'), "naviguer", "genealogie");

	$url_action_accueil=generer_url_ecrire('genespip');
	$url_choix_nom = generer_url_ecrire('fiche_detail');
	$nom_select=explode(":",$_POST['individu']);
	$nom_select=$nom_select[0];

	echo debut_gauche('',true);
	
	echo debut_boite_info(true);
	//Mettre à la poubelle
	if ($_POST['edit']=='poubelle'){
		genespip_poubelle_fiche($_POST['id_individu']);
	}
	//Vider ou Restaurer poubelle
	if ($_POST['actionpoubelle']<>NULL){
		genespip_supp_fiche($_POST['actionpoubelle']);
	}
	echo propre(_T('genespip:info_doc'));
	echo fin_boite_info(true);
	
	echo genespip_nouvelle_fiche($url_action_accueil);
	
	include_spip('inc/raccourcis_intro');
	
	echo debut_droite('',true);	
	$inventaire = spip_query("SELECT id_individu FROM spip_genespip_individu");
	$compteinventaire = mysql_num_rows($inventaire);
	spip_mysql_free($inventaire);
	if ($compteinventaire==0){
		echo debut_cadre_relief(true);
		echo gros_titre(_T('genespip:gedcom'), '', false);
		echo "<br />";
		if ($_POST['edit']=='gedcom'){
			include_spip('inc/gedcom_fonctions');
			$date_upload=date("dmY");
			$chemin = _DIR_PLUGIN_GENESPIP."gedcom/";
			$fic=$chemin.$date_upload."-".$_FILES['gedcomfic']['name'];
			if (is_uploaded_file($_FILES['gedcomfic']['tmp_name'])) {
			   move_uploaded_file ( $_FILES['gedcomfic']['tmp_name'],$fic);
			}
			echo "<u>debut gedcom ".$fic."</u><br />";
			genespip_gedcom($fic);
			echo "<a href='".$url_action_accueil."'>&raquo;&nbsp;Fermer</a>";
		}else{
			echo _T('genespip:info_gedcom_etape1');
			$ret .= "<FORM ACTION='".$url_action_accueil."' method='POST' ENCTYPE='multipart/form-data'>";
			$ret .= "<input type='hidden' name='action' value='gedcom'>";
			$ret .= "<input type='hidden' name='max_file_size' value='5000000'>";
			$ret .= "Fichier Gedcom : <input type='file' name='gedcomfic' size='15'><br />";
			$ret .= "<INPUT TYPE='submit' name='telecharger' value='Charger' class='fondo'>";
			$ret .= "</form>";
			echo $ret;
		}
		echo fin_cadre_relief(true);	
	}
	echo debut_cadre_formulaire('',true);
	
	$result = spip_query("SELECT id_individu FROM spip_genespip_individu where poubelle <> '1'");
	$compte = mysql_num_rows($result);
	spip_mysql_free($result);
	$result = spip_query("SELECT nom, count(id_individu) as compte2 FROM spip_genespip_individu where poubelle <> '1' group by nom");
	$comptenom = mysql_num_rows($result);
    
	echo gros_titre(_T('genespip:Base GeneSPIP'), '', false);
    echo "<table border='0' width='100%'>";
    echo "<tr><td width='50%'>";
    echo "<p>La base contient:<br/> - <font color='#6A0000'><b>".$compte."</b></font> fiches<br />",
         " - <font color='#6A0000'><b>".$comptenom."</b></font> patronymes</p><br />";
    echo "</td><td style='vertical-align:right;text-align:top'>";
    echo "<form action='$url_action_accueil' method='post'>";
    echo "<select name='individu'>";
    while (list ($nom, $compte2) = mysql_fetch_array($result)) {
        echo "<option>$nom</a>:[$compte2]</option>";
    }
    echo "</select>";
    echo "<br /><input type='submit' value='Valider ...' />";
    echo "</form>";
    echo "</td></tr></table>";
	spip_mysql_free($result);
	echo fin_cadre_formulaire(true);

	echo '<br />';
	//Test nouvelle fiche si pas de doublon avant validation.
	
	if ($_POST['edit']=="nouvellefiche"){
		$result = spip_query("SELECT id_individu, nom, prenom FROM spip_genespip_individu where nom = '".$_POST['nom']."' and prenom = '".$_POST['prenom']."'");
		$compte = mysql_num_rows($result);
		
		echo debut_cadre_relief(true);
		echo gros_titre(_T('genespip:nouvelle fiche'), '', false);
		echo "<br />";
		echo "<form action='".$url_choix_nom."' method='post'>";
		if ($compte>0){
			//Si doublon, il faut confirmer
			while (list ($id_individu, $nom, $prenom) = mysql_fetch_array($result)) {
				$resultN = spip_query("SELECT date_evenement FROM spip_genespip_evenements where id_type_evenement='1' and id_individu = ".$id_individu);
				$naissance=NULL;
				while (list ($date_evenement) = mysql_fetch_array($resultN)) {
					$naissance=genespip_datefr($date_evenement);
				}
				$resultD = spip_query("SELECT date_evenement FROM spip_genespip_evenements where id_type_evenement='2' and id_individu = ".$id_individu);
				$deces=NULL;
				while (list ($date_evenement) = mysql_fetch_array($resultD)) {
					$deces=genespip_datefr($date_evenement);
				}
				echo $nom." ".$prenom."(&deg;".$naissance." - &dagger;".$deces.") est pr&eacute;sent dans la base.<br />";
			}
			echo "<br /><input type='submit' name='actionnew' value='Confirmer' class='fondo' />",
			 "&nbsp;&nbsp;<input type='submit' name='actionnew' value='Annuler' class='fondo' />";
		}else{
			//Si pas de doublon, on envoi sur detail_fiche
			echo "Cr&eacute;ation de la fiche ".$_POST['nom']." ".$_POST['prenom'];
			$url = $url_choix_nom."&nom=".$_POST['nom']."&prenom=".$_POST['prenom']."&sexe=".$_POST['sexe']."&actionnew=Confirmer";
			genespip_rediriger_javascript($url);
		}
		echo "<input type='hidden' name='nom' value='".$_POST['nom']."' />";
		echo "<input type='hidden' name='prenom' value='".$_POST['prenom']."' />";
		echo "</form>";
		echo fin_cadre_relief(true);
	}

	//Affichage des personnes en fonction du nom sélectionné.
	if ($_POST['individu']<>""){
		$result = spip_query('SELECT id_individu, nom, prenom, sexe FROM spip_genespip_individu where poubelle<>1 and nom = "'.$nom_select.'" order by prenom');
		
		echo debut_cadre_relief(true);
		echo gros_titre(_T('genespip:'.$nom_select), '', false);
		echo '<br />';
		while (list ($id_individu, $nom, $prenom, $sexe) = mysql_fetch_array($result)) {
			$resultN = spip_query("SELECT date_evenement FROM spip_genespip_evenements where id_type_evenement='1' and id_individu = ".$id_individu);
			$naissance=NULL;
			while (list ($date_evenement) = mysql_fetch_array($resultN)) {
				$naissance=genespip_datefr($date_evenement);
			}
			if ($sexe==0){
				$sexe=_DIR_PLUGIN_GENESPIP.'img_pack/garcon.gif';
			}else{
				$sexe=_DIR_PLUGIN_GENESPIP.'img_pack/fille.gif';
			}
			echo "&nbsp;-&nbsp;<img src='".$sexe."' /> <a href='".$url_choix_nom."&id_individu=".$id_individu."'>".$nom." ".$prenom."</a> (&deg;".$naissance.")<br />";
		}
		
		echo fin_cadre_relief(true);
		//spip_free_result($result);
	}

	//Affichage de la Corbeille
	if ($_GET['poubelle']=="1"){
		$result = spip_query("SELECT id_individu, nom, prenom, sexe FROM spip_genespip_individu where poubelle=1");
		echo debut_cadre_relief(true);
		echo "<table width='100%'>";
		echo '<form action="'.$PHP_SELF.'" method="post">';
		echo "<tr><td>";
		echo gros_titre(_T('genespip:poubelle'), '', false);
		echo "</td>";
		echo "<td style='vertical-align:right'><input name='submit' type='image' src='"._DIR_PLUGIN_GENESPIP."img_pack/poubelle.gif' class='fondo'></td></tr>";
		echo "</table>";
		echo "<table width='70%'>";
		echo '<form action="'.$PHP_SELF.'" method="post">';
		while (list ($id_individu, $nom, $prenom, $sexe) = mysql_fetch_array($result)) {
			$resultN = spip_query("SELECT date_evenement FROM spip_genespip_evenements where id_type_evenement='1' and id_individu = ".$id_individu);
			$naissance=NULL;
			while (list ($date_evenement) = mysql_fetch_array($resultN)) {
				$naissance=genespip_datefr($date_evenement);
			}
			$resultD = spip_query("SELECT date_evenement FROM spip_genespip_evenements where id_type_evenement='2' and id_individu = ".$id_individu);
			$deces=NULL;
			while (list ($date_evenement) = mysql_fetch_array($resultD)) {
				$deces=genespip_datefr($date_evenement);
			}
			echo "<tr><td>".$nom." ".$prenom."</a> (&deg;".$naissance." - &dagger;".$deces.")",
			"<td><input type='checkbox' name='action_fiche[]' value='".$id_individu."' /></td></tr>";
		}
		//echo "<input name='action' type='hidden' value='videpoubelle' />";
		echo "<tr><td colspan='2'><input name='actionpoubelle' type='submit' value='Supprimer' class='fondo' />",
			 "&nbsp;&nbsp;<input name='actionpoubelle' type='submit' value='Restaurer' class='fondo' />",
			 "</td></tr>";
		echo '</form></table>';
		echo fin_cadre_relief(true);
		spip_free_result($result);
	}
	echo fin_page();
}
?>