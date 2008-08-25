<?php

function inc_bureau_preferences_dist($row,$script=null) {

	// l'id doit être numérique, mais aussi unique !
	$id = 1;
	$retour = "bureau_preferences";

	if ($script==null) $script = "bureau_preferences";

	$extra = unserialize($row['extra']);

	$check=''; 
	if ($extra['BUREAU_transparence'] =='oui') $check ='checked';

	$corps = '<fieldset>'
		.'<input type="checkbox" '.$check.' name="transparence" value="oui">Transparence des fenêtres pasives</input><br />'
		.'<input type="submit" value="Enregistrer" />'
		.'</fieldset>'
		.'<div id="'.$retour.'-'.$id.'"></div>';


	return ajax_action_auteur ($retour, $id, $script,'id_auteur='.$row['id_auteur'], $corps, '', '');
}


?>
