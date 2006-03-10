<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/acces_restreint_base');
include_spip('inc/acces_restreint');
include_spip('inc/acces_restreint_gestion');

function exec_stats_pub(){

	global $connect_statut;
	global $connect_toutes_rubriques;
	global $couleur_claire;
	global $spip_lang_right;
  	include_spip('inc/presentation');
	include_spip('base/create');
	creer_base(); // au cas ou
	  
	debut_page(_T('statspub:titre_page'));
	
	echo "<br /><br /><br />";
	gros_titre(_T('statspub:titre_page'));
	debut_gauche();
	
	debut_boite_info();
	echo propre(_T('statspub:info_page'));	
	fin_boite_info();
	
	debut_droite();
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	$stats_pub_nb_jours = 30;
	$stats_pub_compteur = 0;

	debut_cadre_relief();
	echo'<table>';
	echo'<tr><td width="20%"><strong>';
	echo propre(_T('statspub:date'));
	echo '</strong></td><td width="20%"><strong>';
	echo propre(_T('statspub:articles_publies'));
	echo '</strong></td><td width="20%"><strong>';
	echo propre(_T('statspub:articles_proposes'));
	echo '</strong></td><td width= 20%;><strong>';
	echo propre(_T('statspub:articles_refuses'));
	echo '</strong></td></tr>';
	while($stats_pub_compteur != $stats_pub_nb_jours)
	{
		$date = date("Y-m-d",time()-($stats_pub_compteur*24*3600));
		$requete = "SELECT
			COUNT(*) AS nb
			FROM spip_articles
			WHERE DATE_FORMAT(date,'%Y-%m-%d') = '$date'";
		$r_publies = spip_fetch_array(spip_query("$requete AND statut='publie'"));
		$r_proposes = spip_fetch_array(spip_query("$requete AND statut='prop'"));
		$r_refuses = spip_fetch_array(spip_query("$requete AND statut='refuse'"));
		echo '<tr>
			<td>'.affdate_court($date).'</td>
			<td>'.$r_publies['nb'].'</td>
			<td>'.$r_proposes['nb'].'</td>
			<td>'.$r_refuses['nb'].'</td>
			</tr>';
		$stats_pub_compteur++;
	}
	echo '</table>';
	fin_cadre_relief();

	fin_page();

}

?>
