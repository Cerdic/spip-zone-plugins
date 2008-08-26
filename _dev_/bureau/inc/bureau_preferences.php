<?php

function inc_bureau_preferences_dist($row,$script=null) {

	// l'id doit être numérique, mais aussi unique !
	// pe y ajouter la date aaaammjjhhss$id_auteur
	$id = 1;
	$retour = "bureau_preferences";

	if ($script==null) $script = "bureau_preferences";

	$extra = unserialize($row['extra']);

	$check_transparence=''; $check_demons='';
	if ($extra['BUREAU_transparence'] =='oui') $check_transparence ='checked';
	if ($extra['BUREAU_demons'] == 'oui') $check_demons="checked";

	$corps = '<fieldset>'
		.'<input type="checkbox" '.$check_transparence.' name="transparence" value="oui">Transparence des fenêtres pasives</input><br />'
		.'<input type="checkbox" '.$check_demons.' name="demons" value="oui">Activer les démons (tâches de fond)</input><br />'
		.'<input type="submit" value="Enregistrer" />'
		.'</fieldset>'
		.'<div id="'.$retour.'-'.$id.'"></div>';


	return ajax_action_auteur ($retour, $id, $script,'id_auteur='.$row['id_auteur'], $corps, '', '');
}


?>
