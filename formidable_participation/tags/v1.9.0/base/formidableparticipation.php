<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formidableparticipation_declarer_tables_auxiliaires($tables) {
			$table['spip_evenements_participants']['field']['id_formulaires_reponse'] = "bigint(21)";
	return $tables;
}
