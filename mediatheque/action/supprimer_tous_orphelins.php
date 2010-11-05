<?php



if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_tous_orphelins() {

	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();
	$ids = explode('-',$arg);
	if(count($ids))
	{
	include_spip('action/supprimer_document');
		foreach ($ids as $id_document) {
		action_supprimer_document_dist($id_document);
		}
	}
} 


?>
