<?php

//include_spip('inc/agenda_filtres'); // declaration directe dans le xml pour eviter un find_in_path
include_spip('public/criteres_agenda');
include_spip('inc/agenda_filtres');

function Agenda_heure_selector($date,$suffixe){
	$d = strtotime($date);
	$heure = date('H',$d);
	$minute = date('i',$d);
	return
		afficher_heure($heure, "name='heure_evenement$suffixe' size='1' class='fondl'") .
  	afficher_minute($minute, "name='minute_evenement$suffixe' size='1' class='fondl'");
}

//
// <BOUCLE(EVENEMENTS)>
//
function boucle_EVENEMENTS_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_evenements";

	// Restreindre aux elements publies
/* Modif Patrice : Commit SPIP 8062
$boucle->statut devient deprecie et est remplac� par
$boucle->modificateur['criteres']['statut']
Il vous faut donc mettre a jour le code des boucles qui utilisent ce critere
Pour avoir une compatibilit� trans version il faut ecrire le test sous la forme :
if ($boucle->statut OR $boucle->modificateur['criteres']['statut'])
*/	
$temp_statut = $boucle->statut OR $boucle->modificateur['criteres']['statut'];
	if (!$temp_statut) {
		// Si pas de lien avec un article, selectionner
		// uniquement les auteurs d'un article publie
		if (!$GLOBALS['var_preview'])
			if (!isset($boucle->modificateur['lien']) AND !isset($boucle->modificateur['tout'])
			AND (!isset($boucle->lien) OR !$boucle->lien) AND (!isset($boucle->tout) OR !$boucle->tout)) {
				$boucle->from["articles"] =  "spip_articles";
				$boucle->where[]= array("'='", "'articles.id_article'", "'$id_table.id_article'");
				$boucle->where[]= array("'='", "'articles.statut'", "'\"publie\"'");
				$boucle->group[] = $boucle->id_table . '.' . $boucle->primary;  
			}
	}

	return calculer_boucle($id_boucle, $boucles); 
}

?>