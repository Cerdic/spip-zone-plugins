<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Définition du critère {archive x} (pour compatibilité avec les anciennes versions)
 * 
 * Ce critère est obsolète, il devrait être remplacé par {statut = archive} ou {statut IN publie,archive} par exemple
 */
function critere_archive_dist($idb, &$boucles, $crit){
	$boucle = &$boucles[$idb];
	$boucle -> modificateur['criteres']['archive'] = true;

	//reduit le critére à la boucle articles uniquement
	if ($boucle->type_requete == 'articles') {
		$id_table = $boucle->id_table;
		$marchive = $id_table . '.statut';

		//recherche la valeur de x dans {critere x}
		//si x vaut "seulement" alors on indique uniquement les articles archivés
		if ($crit->param[0][0]->texte == "seulement") {
			$statut = kwote("archive");
			$boucle->where[] = array("'='", "'$marchive'", $statut);
		}
		// Si pas x alors on ressort les articles publiés et en archive
		else{
			$statut = "sql_in('$marchive',array('publie','archive'), '')";
			$boucle->where[] = $statut;
		}
	}
}
?>
