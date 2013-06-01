<?php
# not usefull as this file is include by the engine itself
# require_once 'engine/textwheel.php';

// Un callback pour analyser la liste puis appeler un squelette avec les paramètres
function tw_todo($t){
	static $statuts_typo = array('+', 'o', '-', 'x', '=', '!', '?');
	static $statuts_id = array('afaire', 'encours', 'termine', 'abandonne', 'arrete', 'alerte', 'inconnu');

	$liste = explode("\n", trim($t[0]));
	array_shift($liste);
	array_pop($liste);
	
	$todo = array();
	foreach ($liste as $_tache){
		if (trim($_tache)) {
			$_tache = str_replace('&nbsp;', '', $_tache);
			$statut = substr($_tache, 0, 1);
			$texte = trim(substr($_tache, 1, strlen($_tache)-1));
			if (in_array($statut, $statuts_typo)) {
				$todo[] = array(
					'statut' => str_replace($statuts_typo, $statuts_id, $statut),
					'titre' => $texte,
					'del' => ($statut=='-' OR $statut=='x' ? true : false)
				);
			}
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
