<?php

function action_bureau_preferences_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_auteur = $arg;
	$transparence = _request('transparence');

	if ($transparence !="oui") $transparence = "non";

	// on rajoute l'option dans les extras
	$extra=array(
		"BUREAU_transparence"=>$transparence
	);
	$extra=serialize($extra);

	sql_update('spip_auteurs',array('extra'=>sql_quote($extra)),'id_auteur='.$id_auteur);

}

?>
