<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_iextra_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	// droits
	include_spip('inc/autoriser');
	if (!autoriser('configurer', 'iextra')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	list($arg, $id) = explode ('/', $arg);
	
	// actions possibles
	if (!in_array($arg, array(
		'supprimer_extra'))){
			include_spip('inc/minipres');
			echo minipres(_T('iextra:erreur_action',array("action"=>$arg)));
			exit;		
	}
	
	// cas de suppression
	if ($arg == 'supprimer_extra'){
		include_spip('inc/iextra');
		$extras = iextra_get_extras();
		if ($id = intval($id)) {
			// $id a 1 de plus
			$extra = $extras[--$id];
			unset($extras[$id]);
			iextra_set_extras($extras);
			
			$table = table_objet_sql($extra['table']);
			sql_alter("TABLE $table DROP $extra[champ]");
		}
	}
	
}
?>
