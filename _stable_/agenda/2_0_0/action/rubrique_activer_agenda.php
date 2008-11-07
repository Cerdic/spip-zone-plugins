<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function action_rubrique_activer_agenda_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	if (intval($arg)!=0) {
		if (intval($arg)>0)
			sql_updateq('spip_rubriques',array('agenda'=>1),'id_rubrique='.intval($arg));
		else
			sql_updateq('spip_rubriques',array('agenda'=>0),'id_rubrique='.(-intval($arg)));
	}
}

?>