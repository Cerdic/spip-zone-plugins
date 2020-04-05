<?php

function action_supprimer_visuel($arg=null){
	if(is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode(':',$arg);
	list($visuel, $id_objet, $objet, ) = $arg;

	sql_delete("spip_visuels", "id_visuel=$visuel");
	sql_delete("spip_visuels_liens", "id_visuel=$visuel AND id_objet=$id_objet AND objet='$objet'");
}