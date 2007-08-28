<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
//include_spip('inc/actions');
include_spip('inc/agenda_gestion');
include_spip('inc/agenda_saisie_rapide');
//include_spip('inc/pim_agenda_gestion');

function exec_agenda_test_dist()
{
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_page_mots_tous'), "naviguer", "mots"), '<br /><br /><br />';
	gros_titre(_L('Tests pour la saisie rapide et l\'affichage des dates'));
	echo '<br />';

	$un_mot = spip_fetch_array(spip_query("SELECT titre FROM spip_mots"));
	$test0 = '04/05/2001 20 - 05/05 22 "m&ecirc;me mois" REP=1/1,2/1 MOTS='.$un_mot['titre'].',mot2
	04/05/01 20	-	05/05 :30 "m&ecirc;me mois" "toto" "titi" MOTS=mot REP=1/1
	1/7 "Vacances !" "ici" "et l&agrave;..." REP=2,3,4,5,6
	
	4/5
	4/5  "m&ecirc;me jour"
	4/5 20 "m&ecirc;me jour"
	4/5 20 - 22 "m&ecirc;me jour"
	4/5 :20 "m&ecirc;me jour"
	4/5 -:30 "m&ecirc;me jour"
	4/5 :20-:30 "m&ecirc;me jour"
	
	04/05/2000 "m&ecirc;me jour"
	04/05/2000 20 "m&ecirc;me jour"
	04/05/2000 20-22 "m&ecirc;me jour"
	04/05/2000 20:30 "m&ecirc;me jour"
	04/05/2000 20:30-22 "m&ecirc;me jour"
	04/05/2000 20:30-22:30 "m&ecirc;me jour"
	
	04/05-05/05 "m&ecirc;me mois"
	04/05 20-05/05 "m&ecirc;me mois"
	04/05-05/05 22 "m&ecirc;me mois"
	04/05 20-05/05 22 "m&ecirc;me mois"
	
	04/05/2000-05/05 "m&ecirc;me mois"
	04/05/2000 20-05/05 "m&ecirc;me mois"
	04/05/2000-05/05 22 "m&ecirc;me mois"
	04/05/2001 20-05/05 22 "m&ecirc;me mois"
	04/05/01 20-05/05 22 "m&ecirc;me mois"

	4/5-4/6 "m&ecirc;me ann&eacute;e"
	4/5 20-4/6 "m&ecirc;me ann&eacute;e"
	4/5-4/6 22 "m&ecirc;me ann&eacute;e"
	4/5 20-4/6 22 "m&ecirc;me ann&eacute;e"
	4/5/02-4/6/02 "m&ecirc;me ann&eacute;e"
	4/5/02 20-4/6/02 "m&ecirc;me ann&eacute;e"

	4/5/02-4/5/03 "tout diff&eacute;rent"
	4/5/02 20-4/5/03 "tout diff&eacute;rent"
	4/5/02-4/5/03 22 "tout diff&eacute;rent"

Exemple 1 : 20/09/2006 19:30-22:00 "Réunion de rentrée" "Les Gobelins" "Reprise de contact et mise au point des calendriers"
Exemple 2 : 17/08-23/08 "Stage d’été 2007" "Les Salines" MOTS=photos, Agenda:privé
Exemple 3 : 01/01/2007 "Bonne année à tous !" REP=01/01/2008,01/01/2009,01/01/2010
Beispiel 1: 04/05/2007 20:00-22:00 "Was bleibt vom westlichen Marxismus?" "Autonomes Zentrum KTS Freiburg" "Praxis, Subjekt und Hegemonie im 20. und 21. Jahrhundert"
Beispiel 2: 03/02-07/02 "2007 Ausstellung: Pueblo in armas" "Autonomes Zentrum KTS Freiburg" MOTS=Ausstellung, Agenda:Public
Beispiel 3: 01/01/2008 "Auf ein revolutionäres neues Jahr!" REP=01/01/2009,01/01/2010,01/01/2011
	';
	$test = preg_split(",[\n\r]+,", $test0);
	echo "<table class='arial11' style='border:1px solid; border-collapse:collapse; margin:1em;' border=0>";
	echo "<tr class='tr_liste'><th>Chaine de saisie rapide</th><th>Code interpr&eacute;t&eacute;</th><th>Agenda_afficher_date_evenement</th><th>Filtre affdate_debut_fin</th></tr>";
	foreach($test as $s) {
		$e = Agenda_compile_une_ligne($s);
		if ($e) $e="<tr class='tr_liste'><td>$s</td><td>" 
			. sprintf("%s/%s/%s %s:%s - %s/%s/%s %s:%s - %s<br/>'%s' '%s' '%s' '%s' '%s'", 
						$e[0][1], $e[0][2], $e[0][3], $e[0][4], $e[0][5], $e[0][6], $e[0][7], $e[0][8], $e[0][9], $e[0][10], $e['horaire'], 
						$e['titre'], $e['lieu'], $e['descrip'], join(', ', $e['selected_rep']), join(', ', $e['mots']) )
			. '</td><td>' . Agenda_afficher_date_evenement(
					$a1 = mktime($e[0][4], $e[0][5], 0, $e[0][2], $e[0][1], $e[0][3]),
					$a2 = mktime($e[0][9], $e[0][10], 0, $e[0][7], $e[0][6], $e[0][8]), $e['horaire'])
			. '</td><td>' . Agenda_affdate_debut_fin( 
					$a1 = format_mysql_date($e[0][3], $e[0][2], $e[0][1], $e[0][4], $e[0][5]), 
					$a2 = format_mysql_date($e[0][8], $e[0][7], $e[0][6], $e[0][9], $e[0][10]), $e['horaire'])
			. "</td></tr>";
		echo $e?$e:("<tr class='tr_liste'><td>$s</td><td colspan=3>".(trim($s)?"?":"")."</td></tr>");
	}
	echo "</table>";
	
	$e = Agenda_compile_texte_saisie_rapide($test0);
	//echo nl2br(var_export($e)), '<hr>';
	
	set_request('evenements_saisie_rapide', $test0);
	echo '<div style="width:600px; margin: 0pt auto; ">', Agenda_formulaire_saisie_rapide_previsu(), '</div>'; 
/*	// test idiot ;-)	
	include_spip('public/assembler');
	$mots=recuperer_fond('exec/liste_mots');
	$mots="\$mots=array$mots;";
	eval($mots);
*/
	
	fin_page();
}

?>
