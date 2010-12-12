
<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

    include_spip('inc/presentation');
    include_spip('base/gestion_base');
    include_spip('inc/version');
    include_spip('genespip_fonctions');

function exec_genespip() {
	global $connect_statut, $connect_toutes_rubriques;

	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques or $connect_statut == '1comite'))	{
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		echo fin_page(true);
		exit;
	}

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
	$inventaire = sql_select('id_individu', 'spip_genespip_individu');
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
			echo "<u>"._T('genespip:debut_gedcom')." ".$fic."</u><br />";
			genespip_gedcom($fic);
			echo "<a href='".$url_action_accueil."'>&raquo;&nbsp;"._T('genespip:fermer')."</a>";
		}else{
			echo _T('genespip:info_gedcom_etape1');
			$ret .= "<FORM ACTION='".$url_action_accueil."' method='POST' ENCTYPE='multipart/form-data'>";
			$ret .= "<input type='hidden' name='action' value='gedcom'>";
			$ret .= "<input type='hidden' name='max_file_size' value='1048576'>";
			$ret .= _T('genespip:fichier_gedcom')." : <input type='file' name='gedcomfic' size='15'><br />";
			$ret .= "<INPUT TYPE='submit' name='telecharger' value='"._T('genespip:charger')."' class='fondo'>";
			$ret .= "</form>";
			echo $ret;
		}
		echo fin_cadre_relief(true);
	}
	echo debut_cadre_formulaire('',true);

	$result = sql_select('id_individu', 'spip_genespip_individu', 'poubelle <> 1');
	$compte = mysql_num_rows($result);
	spip_mysql_free($result);
	$result = sql_select('nom, count(id_individu) as compte2', 'spip_genespip_individu', 'poubelle <> 1', 'nom');
	$comptenom = mysql_num_rows($result);

	echo gros_titre(_T('genespip:base_genespip'), '', false);
    echo "<table border='0' width='100%'>";
    echo "<tr><td width='50%'>";
    echo "<p>"._T('genespip:base_contient')." : <br/> - <font color='#6A0000'><b>".$compte."</b></font> "._T('genespip:fiches')."<br />",
         " - <font color='#6A0000'><b>".$comptenom."</b></font> "._T('genespip:patronymes')."</p><br />";
    echo "</td><td style='vertical-align:right;text-align:top'>";
    echo "<form action='$url_action_accueil' method='post'>";
    echo "<select name='individu'>";
    while (list ($nom, $compte2) = mysql_fetch_array($result)) {
        echo "<option>$nom</a>:[$compte2]</option>";
    }
    echo "</select>";
    echo "<br /><input type='submit' value='"._T('genespip:valider')." ...' />";
    echo "</form>";
    echo "</td></tr></table>";
	echo fin_cadre_formulaire(true);

	echo '<br />';
	//Test nouvelle fiche si pas de doublon avant validation.

	if ($_POST['edit']=="nouvellefiche"){
		$result = sql_select('id_individu, nom, prenom', 'spip_genespip_individu', 'nom='.sql_quote(_request('nom')).' AND prenom='.sql_quote(_request('prenom')));
		$compte = mysql_num_rows($result);

		echo debut_cadre_relief(true);
		echo gros_titre(_T('genespip:nouvelle_fiche'), '', false);
		echo "<br />";
		echo "<form action='".$url_choix_nom."' method='post'>";
		if ($compte>0){
			//Si doublon, il faut confirmer
			while (list ($id_individu, $nom, $prenom) = mysql_fetch_array($result)) {
				$resultN = sql_select('date_evenement', 'spip_genespip_evenements', 'id_type_evenement=1 and id_individu ='.$id_individu);
				$naissance=NULL;
				while (list ($date_evenement) = mysql_fetch_array($resultN)) {
					$naissance=genespip_datefr($date_evenement);
				}
				$resultD = sql_select('date_evenement', 'spip_genespip_evenements', 'id_type_evenement=2 and id_individu ='.$id_individu);
				$deces=NULL;
				while (list ($date_evenement) = mysql_fetch_array($resultD)) {
					$deces=genespip_datefr($date_evenement);
				}
				echo $nom." ".$prenom."(&deg;".$naissance." - &dagger;".$deces.") "._T('genespip:est_present_dans_base').".<br />";
			}
			echo "<br /><input type='submit' name='actionnew' value='"._T('genespip:confirmer')."' class='fondo' />",
			 "&nbsp;&nbsp;<input type='submit' name='actionnew' value='"._T('genespip:annuler')."' class='fondo' />";
		}else{
			//Si pas de doublon, on envoi sur detail_fiche
			echo _T('genespip:creation_fiche')." ".$_POST['nom']." ".$_POST['prenom'];
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
		$result = sql_select('id_individu, nom, prenom, sexe', 'spip_genespip_individu', 'poubelle<>1 and nom ='.$nom_select.'', 'prenom');

		echo debut_cadre_relief(true);
		echo gros_titre(_T('genespip:'.$nom_select), '', false);
		echo '<br />';
		while (list ($id_individu, $nom, $prenom, $sexe) = mysql_fetch_array($result)) {
			$resultN = sql_select('date_evenement', 'spip_genespip_evenements', 'id_type_evenement=1 and id_individu ='.$id_individu);
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
		$result = sql_select('id_individu, nom, prenom, sexe', 'spip_genespip_individu', 'poubelle=1');
		echo debut_cadre_relief(true);
		echo "<table width='100%'>";
		echo "<form action='".$PHP_SELF."' method='post'>";
		echo "<tr><td>";
		echo gros_titre(_T('genespip:poubelle'), '', false);
		echo "</td>";
		echo "<td style='vertical-align:right'><input name='submit' type='image' src='"._DIR_PLUGIN_GENESPIP."img_pack/poubelle.gif' class='fondo'></td></tr>";
		echo "</table>";
		echo "<table width='70%'>";
		echo "<form action='".$PHP_SELF."' method='post'>";
		while (list ($id_individu, $nom, $prenom, $sexe) = mysql_fetch_array($result)) {
			$resultN = sql_select('date_evenement', 'spip_genespip_evenements', 'id_type_evenement=1 and id_individu ='.$id_individu);
			$naissance=NULL;
			while (list ($date_evenement) = mysql_fetch_array($resultN)) {
				$naissance=genespip_datefr($date_evenement);
			}
			$resultD = sql_select('date_evenement', 'spip_genespip_evenements', 'id_type_evenement=2 and id_individu ='.$id_individu);
			$deces=NULL;
			while (list ($date_evenement) = mysql_fetch_array($resultD)) {
				$deces=genespip_datefr($date_evenement);
			}
			echo "<tr><td>".$nom." ".$prenom."</a> (&deg;".$naissance." - &dagger;".$deces.")",
			"<td><input type='checkbox' name='action_fiche[]' value='".$id_individu."' /></td></tr>";
		}
		//echo "<input name='action' type='hidden' value='videpoubelle' />";
		echo "<tr><td colspan='2'><input name='actionpoubelle' type='submit' value='"._T('genespip:supprimer')."' class='fondo' />",
			 "&nbsp;&nbsp;<input name='actionpoubelle' type='submit' value='actionpoubelle' class='fondo' />",
			 "</td></tr>";
		echo '</form></table>';
		echo fin_cadre_relief(true);
	}
	echo fin_page();
}
?>
