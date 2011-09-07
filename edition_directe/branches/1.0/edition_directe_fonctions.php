<?php
// teste si l'objet est en mode edition directe ou non
function objet_edition_directe($objet){
	if(!$config=lire_config('edition_directe'))
	$config=array('article'=>'on');
	
	$pipeline= pipeline('edition_directe_controle',array(
		    'args'=>array(
			'objet'=>$objet
		    ), 
		    'data'=>$config
		));
	return ($pipeline ? $pipeline[$objet]:$config[$objet]);
}
?>