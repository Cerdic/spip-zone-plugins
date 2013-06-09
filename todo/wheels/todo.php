<?php
# not usefull as this file is include by the engine itself
# require_once 'engine/textwheel.php';

// Un callback pour analyser la liste puis appeler un squelette avec les paramètres
function tw_todo($t) {
	// Liste des statuts supportés
	global $todo_statuts;
	// En attendant de la config
	static $todo_statuts_finaux = array('termine', 'abandonne');
	static $todo_statuts_rappel = array('arrete');
	static $todo_statuts_alerte = array('alerte', 'inconnu');

	// Extraction de lignes du texte
	$liste = explode("\n", trim($t[0]));
	array_shift($liste);
	array_pop($liste);
	
	$todo = array();
	$complements = array();
	$utilise_priorite = false;
	$index = 0;
	foreach ($liste as $_tache){
		$priorite = '';
		$tags = array();
		$texte = trim($_tache);
		if ($texte) {
			if (strpos($texte, '&nbsp;') === 0) {
				$texte = substr($texte, 6, strlen($texte)-6);
			}
			$premier = substr($texte, 0, 1);

			if (array_key_exists($premier, $todo_statuts)) {
				// C'est une tâche
				$texte = trim(substr($texte, 1, strlen($texte)-1));

				// -- le statut
				$statut = $todo_statuts[$premier];

				// -- le titre, que l'on sépare du reste des informations complémentaires éventuelles
				// #(?:[a-z0-9]+:|@)(?:[a-z0-9-]+)(?:\s|$)#Uims
				if (preg_match('#^(.+)(?:\s|$)(?:(?:[a-z0-9_]+:|@)[-a-z0-9]+(?:\s|$))#Uims', $texte, $infos)) {
					$titre = trim($infos[1]);
					$suite = trim(str_replace($titre, '', $texte));

					if ($suite) {
						// -- la priorité
						if (preg_match('#(?:\s|^)(@([0-9]))(?:\s|$)#Uims', $suite, $infos)) {
							$priorite = $infos[2];
							$suite = trim(str_replace($infos[1], '', $suite));
							$utilise_priorite = true;
						}

						// -- les étiquettes
						if (preg_match_all('#(?:\s|^)(@([-a-z0-9]+))(?:\s|$)#Uims', $suite, $infos)) {
							$tags = $infos[2];
							$suite = trim(str_replace($infos[1], '', $suite));
						}

						// -- les informations typées
						if (preg_match_all('#(?:\s|^)(?:([a-z0-9_]+):([\.-a-z0-9]+))(?:\s|$)#Uims', $suite, $infos)) {
							$types = $infos[1];
							$valeurs = $infos[2];
							$complements[] = array_unique(array_merge($complements, $types));
						}
					}
				}
				else
					$titre = $texte;

				// Ajout de la tache dans la liste fournie au modèle
				$todo[$index] = array(
					'statut' => $statut,
					'titre' => $titre,
					'priorite' => $priorite,
					'tags' => $tags,
					'statut_final' => (in_array($statut, $todo_statuts_finaux) ? true : false),
					'alerte' => (in_array($statut, $todo_statuts_rappel) ? 'avertissement' : (in_array($statut, $todo_statuts_alerte) ? 'probleme' : ''))
				);
				$index += 1;
			}
			elseif ($premier == ':') {
				// Projet
			}
			else {
				// Descriptif libre de la tache précedente
				$todo[$index-1]['titre'] .= '<br />' . $texte;
			}
		}
	}

	if ($todo) {
		return recuperer_fond(
			'inclure/todo',
			array(
				'liste' => $todo,
				'utilise_priorite' => $utilise_priorite,
				'complements' => $complements
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
