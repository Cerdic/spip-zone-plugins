<?php

function lire_aussi_install($action,$prefix,$version_cible){
	return true;
}


function lire_aussi_upgrade() {

	// Ajouter le champ "id_lire" a la table "spip_articles"
	sql_alter("TABLE spip_articles ADD id_lire bigint(21) DEFAULT '0' NOT NULL");

}

?>