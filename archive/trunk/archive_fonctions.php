<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION"))
	return;

/**
 * Définition du critère {archive x} (pour compatibilité avec les anciennes versions)
 * 
 * Ce critère est obsolète, il devrait être remplacé par {statut = archive}
 */
function critere_archive_dist($idb, &$boucles, $crit){
	spip_log('Utilisation du critère {archive...} devenu obsolète','spip.'._LOG_ERREUR);

	$boucle = &$boucles[$idb];
	$boucle -> modificateur['criteres']['archive'] = true;

	//reduit le critére à la boucle articles uniquement
	if ($boucle -> type_requete == 'articles') {
		//recherche la valeur de x dans {critere x}
		//si x vaut "seulement" alors on indique uniquement les articles archivés
		if ($crit->param[0][0] -> texte == "seulement") {
			$id_table = $boucle->id_table;
			$marchive = $id_table . '.statut';
			$boucle->where[] = array("'='", "'$marchive'", "archive");
			//sinon tous les articles sont retournés archivé ou non
		}
	}
}
?>
