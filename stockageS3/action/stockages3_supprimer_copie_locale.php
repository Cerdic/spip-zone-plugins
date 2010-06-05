<?php
/**
 * Plugin Stockage S3
 * Licence GPL (c) 2010 Natxo, Cedric
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_stockageS3_supprimer_copie_locale_dist($arg=null) {

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action','inc');
		$arg = $securiser_action();
	}

	if ($id_document = intval($arg)
		AND autoriser('stocker','document',$id_document)
		AND $t = sql_fetsel('*','spip_documents','id_document='.intval($id_document))
		AND $t['distant'] == 'oui'
		){

		include_spip('inc/distant');

		@unlink (_DIR_RACINE.fichier_copie_locale($t['fichier']));
	}
}



?>