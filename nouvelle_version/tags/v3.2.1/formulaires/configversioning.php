<?php
 
function formulaires_configversioning_charger_dist(){
	$workflow_init = lire_config('versioning/workflow');
	$valeurs = array('workflow'=>$workflow_init);
 
	return $valeurs;
}
 
function formulaires_configversioning_verifier_dist(){
return;
}

function formulaires_configversioning_traiter_dist(){
$workflow_new = _request('workflow');
ecrire_config("versioning/workflow", $workflow_new);
return array('message_ok'=>'Votre modification a bien été prise en compte: workflow '.$workflow_new);
}
?>

