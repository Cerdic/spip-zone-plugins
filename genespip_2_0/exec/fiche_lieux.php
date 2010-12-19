<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

include_spip('inc/presentation');

function exec_fiche_lieux(){
	global $connect_statut, $connect_toutes_rubriques;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('genespip:fiche_lieux'), "", "");

	$url_action_fiche=generer_url_ecrire('fiche_lieux');
	$url_action_evt=generer_url_ecrire('fiche_evt_par_lieu');
	$url_retour = $_SERVER['HTTP_REFERER'];
	if ($_GET['id_individu']!=NULL){$id_individu = $_GET['id_individu'];}else{$id_individu=$_POST['id_individu'];}
	
	echo gros_titre(_T('genespip:liste_des_lieux'), '', false);
	
	echo debut_gauche('',true);
	include_spip('inc/boite_info');
	include_spip('inc/raccourcis_intro');
	
	echo debut_droite('',true);

	echo debut_cadre_relief(  "", false, "", $titre = _T('genespip:fiche_lieux'));
	
	//Actions
	if ($_POST['action']=='up_lieu'){genespip_up_lieu($_POST['id_lieu']);}
	if ($_POST['action']=='add_lieu'){genespip_add_lieu();}
	if ($_GET['action']=='del_lieu'){genespip_del_lieu($_GET['id_lieu']);}

	
	echo "<br /><fieldset><legend>"._T('genespip:ajout_lieu')."</b></i></legend>";
	echo "<table style='border:1px;border-color:black'>";
	echo "<tr>",
		  "<td>"._T('genespip:ville')."</td>",
		  "<td>"._T('genespip:departement')."</td>",
		  "<td>"._T('genespip:num_departement')."</td>",
		  "<td>"._T('genespip:region')."</td>",
		 "</tr>";
	echo "<form action='".$url_action_fiche."' method='post'>";
	echo "<tr>",
		  "<td><input size='16' type='text' name='ville' value='' /></td>",
		  "<td><input size='8' type='text' name='departement' value='' /></td>",
		  "<td><input size='2' type='text' name='code_departement' value='' /></td>",
		  "<td><input size='8' type='text' name='region' value='' /></td></tr>",
		  "<tr><td colspan='3'>"._T('genespip:pays').":";
	include('pays_fr.php');
	  echo "<select tabindex='5' size='1' name='pays' onchange='update_flag1(this)'>";

	foreach ($FLAGS_LANG as $flag => $clair_pays) {
	   echo "<option value='".$flag."'>".$clair_pays."</option>\n";
	}
	echo "</select>";

	echo "</td><td><input type='submit' value='"._T('genespip:creer')."' class='fondo' /></td></tr>";
	echo "<input name='action' type='hidden' value='add_lieu'>";
	echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
	echo "</form>";
	echo "</table><br />";
	echo "</fieldset>";
	echo "<br /><fieldset><legend>"._T('genespip:liste_des_lieux')."</b></i></legend>";
	echo "<table cellpadding='3' cellspacing='0'>";
	echo "<tr>",
		  "<td><b>"._T('genespip:ville')."</b></td>",
		  "<td><b>"._T('genespip:departement')."</b></td>",
		  "<td><b>"._T('genespip:num_departement')."</b></td>",
		  "<td><b>"._T('genespip:region')."</b></td>",
		  "<td><b>"._T('genespip:pays')."</b></td>",
		  "<td colspan='2'></td>",
		 "</tr>";
	$result = sql_select('*', 'spip_genespip_lieux', 'ville');
	$n=1;
	while ($lieux = spip_fetch_array($result)) {
	$n=$n+1;
	   if($n%2){$color="white";}else{$color="#E2FF94";}
	$resultnb = sql_select('*', 'spip_genespip_evenements', 'id_lieu='.sql_quote(_request('id_lieu')));
	$compte = mysql_num_rows($resultnb);
	if ($lieux['id_lieu']!=1){
	echo '<form action="'.$url_action_fiche.'" method="post">';
	$ville=stripslashes($lieux["ville"]);
	$departement=stripslashes($lieux["departement"]);
	$region=stripslashes($lieux["region"]);
	echo "<tr style='background-color:$color'>",
		  "<td><input size='16' type='text' name='ville' value='".$ville."' /></td>",
		  "<td><input size='8' type='text' name='departement' value='".$departement."' /></td>",
		  "<td><input size='2' type='text' name='code_departement' value='".$lieux['code_departement']."' /></td>",
		  "<td><input size='8' type='text' name='region' value='".$region."' /></td>",
		  "<td><img src='"._DIR_PLUGIN_GENESPIP."img_pack/pays/".$lieux['pays'].".png' /></td>",
		  "<td><input type='image' src='"._DIR_PLUGIN_GENESPIP."img_pack/update.gif' name='update' /></td>",
		  "<td><a href='".$url_action_fiche."&action=del_lieu&id_individu=".$id_individu."&id_lieu=".$lieux['id_lieu']."'><img border='0' noborder src='"._DIR_PLUGIN_GENESPIP."img_pack/del.gif' alt='"._T('genespip:supprimer')."' /></a></td>",
		  "</tr>",
		  "<tr style='background-color:$color'><td colspan='3' style='border-bottom:1px solid #808080'>"._T('genespip:pays').": ";
	include('pays_fr.php');
	  echo "<select tabindex='5' size='1' name='pays' onchange='update_flag1(this)'>";
	   while (list($key,$val) = each($FLAGS_LANG)){
	   if ($key==$lieux['pays'] or $val==$lieux['pays']){
	if ($val==$lieux['pays']){
	$action_sql = sql_update('spip_genespip_lieux', array('pays' => sql_quote($key), 'id_lieu' => sql_quote($id_lieu)));
	}
	   echo "<option value='$key'>$val</option>\n";
	   }}
	   foreach ($FLAGS_LANG as $flag => $clair_pays) {
	if ($clair_pays==$lieux['pays'] or $flag==$lieux['pays']){
	$drapeaun=$flag;
	}
	   echo "<option value='".$flag."'>".$clair_pays."</option>\n";
	   }
	  echo "</select>";
	echo "<td colspan='4' style='border-bottom:1px solid #808080;text-align:center'><a href='".$url_action_evt."&id_lieu=".$lieux['id_lieu']."'>"._T('genespip:evenements_lies')." &raquo; $compte</a></td></tr>";
	echo "<input name='action' type='hidden' value='up_lieu'>";
	echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
	echo "<input name='id_lieu' type='hidden' value='".$lieux['id_lieu']."'>";
	echo "</form>";
	}}
	echo "</table><br /><br />";
	   echo "</fieldset>";

	echo fin_cadre_relief(true);  

	echo fin_page(true);
}
?>
