<?php

function popularite_upgrade() {

	// Ajouter les champs "popularite" aux tables concernees
	sql_alter("TABLE spip_rubriques ADD popularite DOUBLE DEFAULT '0' NOT NULL");
	sql_alter("TABLE spip_mots ADD popularite DOUBLE DEFAULT '0' NOT NULL");
	sql_alter("TABLE spip_auteurs ADD popularite DOUBLE DEFAULT '0' NOT NULL");

}
