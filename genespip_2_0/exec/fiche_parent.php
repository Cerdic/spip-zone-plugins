<?php
/*
--------G.E.N.E.S.P.I.P-------
---SITE genealogique & SPIP---
------Christophe RENOU--------
*/

include_spip('inc/presentation');

function exec_fiche_parent(){
	global $connect_statut, $connect_toutes_rubriques;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('genespip:fiche_parents'), "", "");
	$url_action_fiche=generer_url_ecrire('fiche_parent');
	$url_action_detail=generer_url_ecrire('fiche_detail');
	$url_retour = $_SERVER['HTTP_REFERER'];
	$id_individu = $_GET['id_individu'].$_POST['id_individu'];

	if ($_POST['edit']=='modif'){
		genespip_modif_parent($id_individu);
	}
	echo debut_gauche('',true);
	
	include_spip('inc/boite_info');

	include_spip('inc/raccourcis_fiche');
	echo debut_droite('',true);

	echo debut_cadre_relief(  "", false, "", $titre = _T('genespip:fiche_parents'));

	//Requêtes parents
	$result = sql_select('*', 'spip_genespip_individu', 'id_individu='.sql_quote(_request('id_individu')).' and poubelle <> 1');
	while ($fiche = spip_fetch_array($result)) {
		$numi_pere=$fiche['pere'];
		$numi_mere=$fiche['mere'];
		$nom=$fiche['nom'];
		$prenom=$fiche['prenom'];
		echo gros_titre(_T($fiche['nom']." ".$fiche['prenom']), '', false);
		echo "<br /><fieldset><legend>"._T('genespip:derniere_modification')." &ndash;&rsaquo;<i><b>".$fiche['date_update']."</b></i></legend>";

		echo "<table style='border:1px;border-color:black'>";
		echo "<form action='".$url_action_fiche."' method='post'>";
		echo "<tr><td><b>"._T('genespip:pere')." : </b></td><td>";
		echo "<select size='1' name='pere'>";
		$result_pere = sql_select('*', 'spip_genespip_individu', 'id_individu="'.$numi_pere.'" and poubelle <> 1');
		while ($fiche_pere = spip_fetch_array($result_pere)) {
			$resultN = sql_select('date_evenement', 'spip_genespip_evenements', 'id_type_evenement=1 and id_individu' => sql_quote($fiche_pere['id_individu']).);
			$naissance=NULL;
			while (list ($date_evenement) = mysql_fetch_array($resultN)) {
				$naissance=genespip_datefr($date_evenement);
			}
			$info_pere=$fiche_pere['nom']."&nbsp;".$fiche_pere['prenom']." (&ordm;".$naissance.")"." [".$fiche_pere['id_individu']."]";
			echo "<option style='font-weight:600' value='".$fiche_pere['id_individu']."'>".$info_pere."</option>";
		}
		$result_pere = sql_select('*', 'spip_genespip_individu', 'sexe = 0 and poubelle <> 1', 'nom');
		echo "<option value=''>--"._T('genespip:pere_inconnu')."--</option>";
		while ($fiche_pere = spip_fetch_array($result_pere)) {
				$resultN = sql_select('date_evenement', 'spip_genespip_evenements', 'id_type_evenement=1 and id_individu = "'.$fiche_pere['id_individu']);
				$naissance=NULL;
				while (list ($date_evenement) = mysql_fetch_array($resultN)) {
				$naissance=genespip_datefr($date_evenement);
				}

		$info_pere=$fiche_pere['nom']."&nbsp;".$fiche_pere['prenom']." (&ordm;".$naissance.")"." [".$fiche_pere['id_individu']."]";
		 echo "<option value='".$fiche_pere['id_individu']."'>".$info_pere."</option>";
		}
		echo "</select>";
		echo "</td></tr>";
		echo "<tr><td colspan='2'>".genespip_nom_prenom($numi_pere,1)."</td></tr>";
		echo "<tr><td><b>"._T('genespip:mere')." : </b></td><td>";
		echo "<select size='1' name='mere'>";
		$result_mere = sql_select('*', 'spip_genespip_individu', 'id_individu=".$numi_mere." and poubelle <> 1');
		while ($fiche_mere = spip_fetch_array($result_mere)) {
			$resultN = sql_select('date_evenement', 'spip_genespip_evenements', 'id_type_evenement=1 and id_individu = "'.$fiche_mere['id_individu']);
			$naissance=NULL;
			while (list ($date_evenement) = mysql_fetch_array($resultN)) {
				$naissance=genespip_datefr($date_evenement);
			}
			$info_mere=$fiche_mere['nom']."&nbsp;".$fiche_mere['prenom']." (&ordm;".$naissance.")"." [".$fiche_mere['id_individu']."]";
			echo "<option style='font-weight:600' value='".$fiche_mere['id_individu']."'>".$info_mere."</option>";
		}
		$result_mere = sql_select('*', 'spip_genespip_individu', 'sexe = 1 and poubelle <> 1', 'nom');
		echo "<option value=''>--"._T('genespip:mere_inconnu')."--</option>";
		while ($fiche_mere = spip_fetch_array($result_mere)) {
			$resultN = sql_select('date_evenement', 'spip_genespip_evenements', 'id_type_evenement=1 and id_individu = "'.$fiche_mere['id_individu']);
			$naissance=NULL;
			while (list ($date_evenement) = mysql_fetch_array($resultN)) {
				$naissance=genespip_datefr($date_evenement);
			}
			$info_mere=$fiche_mere['nom']."&nbsp;".$fiche_mere['prenom']." (&ordm;".$naissance.")"." [".$fiche_mere['id_individu']."]";
			echo "<option value='".$fiche_mere['id_individu']."'>".$info_mere."</option>";
		}
		echo "</select>";
		echo "</td></tr>";
		echo "<tr><td colspan='2'>".genespip_nom_prenom($numi_mere,1)."</td></tr>";
		echo "<input name='edit' type='hidden' value='modif'>";
		echo "<input name='id_individu' type='hidden' value='".$id_individu."'>";
		echo "<td colspan='2'><input name='submit' type='submit' value='"._T('genespip:valider')."' class='fondo'></td></tr>";
		echo "</form>";
		echo "</table>";
		echo "</fieldset>";
	}

	echo fin_cadre_relief();  

	echo fin_page();
}
?>
