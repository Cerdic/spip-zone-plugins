<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

include_spip('inc/presentation');
include_spip('exec/genespip_evt');

function exec_fiche_detail(){
	global $connect_statut, $connect_toutes_rubriques;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('genespip:fiche_detail'), "", "");
	
	$url_action_fiche=generer_url_ecrire('fiche_detail');
	$url_action_accueil=generer_url_ecrire('genespip');
	$url_retour = $_SERVER['HTTP_REFERER'];
	
	if ($_GET['id_individu']!=NULL){$id_individu = $_GET['id_individu'];}else{$id_individu=$_POST['id_individu'];}
	$actionnew = $_GET['actionnew'].$_POST['actionnew'];
	if ($actionnew=='Confirmer'){
		$id_individu=genespip_ajout_fiche();
		$url = $url_action_fiche."&id_individu=".$id_individu;
		genespip_rediriger_javascript($url);
	}elseif($actionnew=='Annuler'){
		echo "<img src='"._DIR_PLUGIN_GENESPIP."img_pack/loader.gif' />&nbsp;&nbsp;";
		echo _T('genespip:creation_annulee');
		genespip_rediriger_javascript($url_action_accueil);
	}

	//modification de la fiche
	if ($_POST['edit']=='modif'){
		genespip_modif_fiche($id_individu);
	}
	//Ajout d'une photo

	if ($_POST['edit']=='image'){
		$chemin=_DIR_IMG;
		$split = split('/',$_FILES['image']['type']);
		if (is_uploaded_file($_FILES['image']['tmp_name'])) {
			if ($_POST['media']=='photo'){
				move_uploaded_file ( $_FILES['image']['tmp_name'],$chemin."gene_portrait_".$_POST['id_individu'].".".$split[1]);
				genespip_modif_fiche_portrait(1,$_POST['id_individu'],$split[1]);
			}
			if ($_POST['media']=='signature'){
			   move_uploaded_file ( $_FILES['image']['tmp_name'],$chemin."gene_signature_".$_POST['id_individu'].".".$split[1]);
				genespip_modif_fiche_signature(1,$_POST['id_individu'],$split[1]);
			}
		}
	}
	if ($_GET['actionportrait']=='0'){
		genespip_modif_fiche_portrait(0,$id_individu,'');
	}
	if ($_GET['actionsignature']=='0'){
		genespip_modif_fiche_signature(0,$id_individu,'');
	}

	echo debut_gauche('',true);
	include_spip('inc/boite_info');
	
	//Formulaire photo
	$ret .= "<a name='images'></a>\n";
	$titre_cadre = _T('genespip:ajout_media');
	$ret .= debut_cadre_relief("image-24.gif", true, "creer.gif", $titre_cadre);
	$ret .= "<FORM ACTION='".$url_action_fiche."' method='POST' ENCTYPE='multipart/form-data'>";
	$ret .= "<input type='hidden' name='edit' value='image'>";
	$ret .= "<input name='id_individu' type='hidden' value='".$id_individu."'>";
	$ret .= "<input type='hidden' name='max_file_size' value='102400'>";
	$ret .= _T('genespip:media').":<input TYPE='file' NAME='image' size='10'><br />";
	$ret .= "<select name='media'>";
	$ret .= "<option value='photo'>"._T('genespip:photo')."</option>";
	$ret .= "<option value='signature'>"._T('genespip:signature')."</option>";
	$ret .= "</select>";
	$ret .= "<INPUT TYPE='submit' NAME='telecharger' VALUE='"._T('genespip:telecharger')."' class='fondo'>";
	$ret .=_T('genespip:indication_format_photo');
	$ret .= "</form>";
	$ret .= fin_cadre_relief(true);
	echo $ret;
	//fin formulaire photo

	include_spip('inc/raccourcis_fiche');
	

	echo debut_droite('',true); 

	echo debut_cadre_relief(  "", false, "", $titre = _T('genespip:detail_fiche'));


	$result = sql_select('*', 'GENESPIP_INDIVIDU', 'id_individu = '.$id_individu);
	while ($fiche = spip_fetch_array($result)) {
	echo "<table width='100%'>";
	echo '<form action="'.$url_action_accueil.'" method="post">';
	echo "<tr><td>";
	echo gros_titre(_T(stripslashes($fiche['nom'])." ".$fiche['prenom']), '', false);
	echo "</td>";
	echo "<td style='vertical-align:right'><input name='submit' type='image' src='"._DIR_PLUGIN_GENESPIP."img_pack/poubelle.gif' class='fondo'></td></tr>";
	echo "<input name='edit' type='hidden' value='poubelle'>";
	echo "<input name='poubelle' type='hidden' value='1'>";
	echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
	echo "</form></table>";
	if ($fiche['sexe']==0){$sexegar="checked";}else{$sexefille="checked";}

	echo "<br /><fieldset><legend>"._T('genespip:derniere_modification')." &ndash;&rsaquo;<i><b>".$fiche['date_update']."</b></legend>";
	echo "<table width='100%'>";
	echo "<form action=".$url_action_fiche." method=post>";

	//affichage de la fiche complète
	$nom=stripslashes($fiche["nom"]);
	$prenom=stripslashes($fiche["prenom"]);
	echo "<tr><td>"._T('genespip:nom')."</td>";
	echo '<td><input type="text" name="nom" value="'.$nom.'" size="20" /></td>';
	echo "<td rowspan='4'>";
	if ($fiche['portrait']==1){
	echo "<center><img src='"._DIR_IMG."/gene_portrait_".$id_individu.".".$fiche['format_portrait']."' alt=''><br />",
		 "<a href='".$url_action_fiche."&actionportrait=0&id_individu=".$id_individu."'>&lsaquo;"._T('genespip:supprimer')."&rsaquo;</a></center>";
	}
	echo "</td></tr>";
	echo "<tr><td>"._T('genespip:prenom')."</td>";
	echo '<td><input type="text" name="prenom" value="'.$prenom.'" size="20" /></td>';
	echo "<tr><td>"._T('genespip:sexe')."</td>";
	echo "<td>M&nbsp;<input type='radio' name='sexe' value='0' id='1' ".$sexegar." />",
		 "&nbsp;F&nbsp;<input type='radio' name='sexe' value='1' id='2' ".$sexefille." /></td></tr>";
	if ($fiche['enfant']==1){$check1="checked";}
	echo "<tr><td colspan='2'>"._T('genespip:enfant')."&nbsp;<input type='checkbox' name='enfant' ".$check1." value='1' /></td></tr>";
	if ($fiche['limitation']==1){
	$check2="checked";
	$texte_limitation="<font color='#710000'>"._T('genespip:limitation_oui')."</font>";
	}else{
	$texte_limitation="<font color='#710000'>"._T('genespip:limitation_non')."</font>";
	}
	echo "<tr><td colspan='2'>"._T('genespip:limitation')."&nbsp;<input type='checkbox' name='limitation' ".$check2." value='1' />$texte_limitation<hr /></td></tr>";
	$metier=stripslashes($fiche["metier"]);
	$adresse=stripslashes($fiche["adresse"]);
	$source=stripslashes($fiche["source"]);
	echo "<tr><td>"._T('genespip:metier')."</td>";
	echo "<td colspan='2'><input type='text' name='metier' value='".$metier."' size='40' /></td></tr>";
	echo "<tr><td>"._T('genespip:adresse')."</td>";
	echo "<td colspan='2'><input type='text' name='adresse' value='".$adresse."' size='40' /></td></tr>";
	echo "<tr><td style='vertical-align:top'>"._T('genespip:note')."</td><td colspan='2'><textarea name='note' rows='10' cols='45'>".stripslashes($fiche['note'])."</textarea></td></tr>";

	echo "<tr><td>"._T('genespip:source')."</td>";
	echo "<td colspan='2'><input type='text' name='source' value='".$source."' size='40' /></td></tr>";
	echo "<tr><td colspan='3'>";
	if ($fiche['signature']==1){
		echo "<center><img src='"._DIR_IMG."/gene_signature_".$id_individu.".".$fiche['format_signature']."' alt=><br />",
		 "<a href='".$url_action_fiche."&actionsignature=0&id_individu=".$id_individu."'>&lsaquo;"._T('genespip:supprimer')."&rsaquo;</a></center>";
	}
	echo "</td></tr>";
	echo "<input name='pere' type='hidden' value='".$fiche['pere']."'>";
	echo "<input name='mere' type='hidden' value='".$fiche['mere']."'>";
	echo "<input name='portrait' type='hidden' value='".$fiche['portrait']."'>";
	echo "<input name='auteur' type='hidden' value='".$fiche['auteur']."'>";
	echo "<input name='edit' type='hidden' value='modif'>";
	echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
	}

	echo "<tr><td colspan='3'><hr /><input name='submit' type='submit' value='"._T('genespip:valider')."' class='fondo'></td></tr>";
	echo "</form>";

	echo "</table>";
	echo "</fieldset>";

	echo fin_cadre_relief(true);
	
	//echo "action=".$_POST['action'];
	if ($_POST['edit']=='up_evt'){
		genespip_up_evt($id_individu,$_POST['id_type_evenement']);
	}
	if ($_POST['edit']=='del_evt'){
		genespip_del_evt($_POST['id_evenement']);
	}
	if ($_POST['edit']=='add_evt'){
		genespip_add_evt($id_individu);
	}
	echo debut_cadre_relief(  "", false, "", $titre = _T('genespip:evenements'));
	$resultevt = sql_select('*', 'spip_genespip_type_evenements');
	while ($evt = spip_fetch_array($resultevt)) {
		genespip_evt($evt['id_type_evenement'],$id_individu);
	}
	if ($_POST['edit']!='choix_evt'){
		echo "<br /><fieldset><legend>$id"._T('genespip:ajout_evenement')."</legend>";
		echo "<table width='100%'>";
		echo "<form action='".$url_action_fiche."#bottom' method='post'>";
		echo "<tr><td>"._T('genespip:evenement');
		echo "&nbsp;&nbsp;<select name='id_type_evenement' size='1'>";
		$resultevt = sql_select('*', 'spip_genespip_type_evenements');
		while ($evt = spip_fetch_array($resultevt)) {
			echo "<option value='".$evt['id_type_evenement']."'>".$evt['clair_evenement']."</option>";
		}
		echo "</select></td>";
		echo "<td><input name='submit' type='submit' value='"._T('genespip:choisir')."' class='fondo'>";
		echo "<input name='edit' type='hidden' value='choix_evt'>";
		echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
		echo "</td></tr></form>";
		echo "</table>";
		echo "</fieldset>";
	}else{
		genespip_new_evt($id_individu,$_POST['id_type_evenement']);
	}
	echo "<a name='bottom'></a>";
	
	echo fin_cadre_relief(true);
	echo fin_page(true);
}
?>
