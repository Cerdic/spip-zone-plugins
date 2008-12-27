<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_iextras_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	// droits
	include_spip('inc/autoriser');
	if (!autoriser('configurer', 'iextra')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	list($arg, $id_extra) = explode ('/', $arg);
	
	// actions possibles
	if (!in_array($arg, array(
		'supprimer_extra'))){
			include_spip('inc/minipres');
			echo minipres(_T('iextras:erreur_action',array("action"=>$arg)));
			exit;		
	}
	
	// cas de suppression
	if ($id_extra and ($arg == 'supprimer_extra')){
		include_spip('inc/iextras');
		$extras = iextras_get_extras();
		foreach($extras as $i=>$extra) {
			if ($extra->get_id() == $id_extra) {
				extras_log("Suppression d'un champ par auteur ".$GLOBALS['auteur_session']['id_auteur'],true);
				extras_log($extra, true);
				
				$table = table_objet_sql($extra->table);
				sql_alter("TABLE $table DROP ".$extra->champ);
				
				unset($extras[$i]);
				iextras_set_extras($extras);
				break;
			}
		}
	}
	
}
?>
