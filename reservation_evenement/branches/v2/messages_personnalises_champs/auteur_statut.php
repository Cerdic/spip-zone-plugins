<?php

function messages_personnalises_champs_auteur_statut_dist($valeur) {
	$desc = lister_tables_objets_sql('spip_auteurs');
	return isset($desc['statut_titres'][$valeur]) ? _T($desc['statut_titres'][$valeur]) : $valeur;
}
