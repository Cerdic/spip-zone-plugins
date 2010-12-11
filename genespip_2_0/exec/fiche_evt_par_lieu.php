<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

include_spip('inc/presentation');
include_spip('exec/genespip_evt');
function exec_fiche_evt_par_lieu(){
	global $connect_statut, $connect_toutes_rubriques;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('genespip:fiche_evt_par_lieu'), "", "");

	$url_action_fiche=generer_url_ecrire('fiche_detail');
	$url_action_accueil=generer_url_ecrire('genespip');
	$url_retour = $_SERVER['HTTP_REFERER'];
	if ($_GET['id_individu']!=NULL){$id_individu = $_GET['id_individu'];}else{$id_individu=$_POST['id_individu'];}
	if ($_GET['id_lieu']	!=NULL){$id_lieu = $_GET['id_lieu'];}else{$id_lieu=$_POST['id_lieu'];}


	echo debut_gauche('',true);
	include_spip('inc/boite_info');
	include_spip('inc/raccourcis_intro');
	echo debut_droite('',true);	

	if ($_POST['action']=='up_evt'){
	genespip_up_evt($id_individu,$_POST['id_type_evenement']);
	}
	if ($_POST['action']=='del_evt'){
	genespip_del_evt($_POST['id_evenement']);
	}

	echo debut_cadre_relief(true);
		echo gros_titre(_T('genespip:evenements'), '', false);
		echo debut_boite_info(true);
			//$resultevt = sql_select('*', 'GENESPIP_EVENEMENTS', 'id_lieu=".$id_lieu);
			//while ($evt = spip_fetch_array($resultevt)) {

			$resultevt = sql_select('*', 'spip_genespip_individu,spip_genespip_evenements,spip_genespip_lieux,spip_genespip_type_evenements', 'spip_genespip_evenements.id_lieu=".$id_lieu." and spip_genespip_lieux.id_lieu=spip_genespip_evenements.id_lieu and spip_genespip_evenements.id_type_evenement=spip_genespip_type_evenements.id_type_evenement and spip_genespip_evenements.id_individu = spip_genespip_individu.id_individu');
			while ($evt = spip_fetch_array($resultevt)) {
			$date_evt = genespip_datefr($evt['date_evenement']);
			if ($evt['id_epoux']!=0){$union="<b>"._T('genespip:avec')." ".genespip_nom_prenom($evt['id_epoux'],1)."</b>";}else{$union=NULL;}
			$precision_date=$evt['precision_date'];
			$lieu = $evt['ville'].", ".$evt['departement']."(".$evt['code_departement']."), ".$evt['region'].", ".$evt['pays'];
			$lieu_court = $evt['ville'].", ".$evt['departement']."(".$evt['code_departement'].")";
			$id_lieu = $evt['id_lieu'];
			$clair_evenement= $evt['clair_evenement'];
			echo "<br /><fieldset><legend>"._T('genespip:'.$clair_evenement)." ".$evt['nom']." ".$evt['prenom']."</legend>";
			echo "<table width='100%'>";
			echo "<form action='".$url_action_evt."' method='post'>";
			echo "<tr><td colspan='3'>".$precision_date." ".$date_evt.", ".$lieu." ".$union."</td></tr>";
			echo "<tr><td>"._T('genespip:date')."&deg;</td>";
			echo "<td>".$precision_date."</td>";
			echo "<td><i>jj/mm/aaaa</i> <input name='date_evenement' value='".$date_evt."' size='15' /></td></tr>";
			echo "<tr><td>"._T('genespip:lieu')."</td>";
			echo "<td colspan='2'><select name='id_lieu' size='1'>";
			echo "<option value='".$id_lieu."'>".$lieu_court."</option>";
			$resultlieu = sql_select('*', 'spip_genspip_lieux', 'ville');
			while ($lieu = spip_fetch_array($resultlieu)) {
			echo "<option value='".$lieu['id_lieu']."'>".$lieu['ville'].", ".$lieu['departement']."(".$lieu['code_departement'].")</option>";
			}
			echo "</select></td></tr>";
			echo "<input name='id_type_evenement' type='hidden' value='".$evt['id_type_evenement']."'>";
			echo "<input name='id_epoux' type='hidden' value='".$evt['id_epoux']."'>";
			echo "<input name='id_individu' type='hidden' value='".$evt['id_individu']."'>";
			echo "<input name='action' type='hidden' value='up_evt'>";
			echo "<tr><td colspan='3'><hr /><table><tr><td>";
			echo "<input name='submit' type='submit' value='"._T('genespip:modifier')."' class='fondo'></form>";
			echo "</td><form action='".$url_action_fiche."' method='post'><td>";
			echo "<input name='submit' type='submit' value='"._T('genespip:supprimer')."' class='fondo'>";
			echo "<input name='id_evenement' type='hidden' value='".$evt['id_evenement']."'>";
			echo "<input name='id_individu' type='hidden' value='".$evt['id_individu']."'>";
			echo "<input name='action' type='hidden' value='del_evt'>";
			echo "</form></td></tr></table></td></tr>";
			echo "</table>";
			echo "</fieldset>";
			}
			echo "<a name='bottom'>Pas d'evenement donc pas de lieu.</a>";
		 echo fin_boite_info(true);
	echo fin_cadre_relief(true);	
	echo fin_page();
}
?>
