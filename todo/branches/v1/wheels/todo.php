<?php
# not usefull as this file is include by the engine itself
# require_once 'engine/textwheel.php';

// Un callback pour analyser la liste puis appeler un squelette avec les paramètres
function tw_todo($t){
	$liste = explode("\n", trim($t[0]));
	array_shift($liste);
	array_pop($liste);
	
	$todo = array();
	foreach ($liste as $ligne){
		if(preg_match('/([+-o])(.*)/', $ligne, $chose)){
			$todo[] = array(
				'statut' => str_replace(array('+', 'o', '-'), array('afaire', 'encours', 'termine'), $chose[1]),
				'titre' => trim($chose[2])
			);
		}
	}
	
	if ($todo){
		return recuperer_fond(
			'inclure/todo',
			array(
				'liste' => $todo
			),
			array(
				'ajax' => true
			)
		);
	}
	else{
		return $t;
	}
}

?>
