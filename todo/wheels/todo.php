<?php

// Regexp permettant de récupérer chacune des informations additionnelles qui peuvent compléter le tire de la tâche :
// - @p ou p=1..9 et désigne la priorité. Exemple : @1
// - @tag ou tag est un mot. Exemple : @courses
// - type:valeur ou type et valeur sont des mots. Exemple : fin:2013-06-02 ou commit:z72324
if (!defined('_TODO_REGEXP_INFOS_COMPLEMENTAIRES'))
	define('_TODO_REGEXP_INFOS_COMPLEMENTAIRES', '#([\w-]+:|@)([\w.-]+)(?:\s|$)#Uims');


/**
 * Analyse le contenu du bloc inclus entre les marqueurs de début et de fin de la todolist
 * puis appelle un squelette avec les paramètres calculés
 *
 * @param array	$t	l'index 0 représente le contenu du bloc
 * @return string	le html généré à partir d'un squelette
 */
function tw_todo($t) {
	// Liste des statuts supportés
	global $todo_statuts;

	// Initialisation du html calculé
	$html = $t;

	// En attendant de la config
	static $todo_statuts_finaux = array('termine', 'abandonne');
	static $todo_statuts_rappel = array('arrete');
	static $todo_statuts_alerte = array('alerte', 'inconnu');

	// Extraction de lignes du texte
	$lignes = explode("\n", trim($t[0]));
	array_shift($lignes);
	array_pop($lignes);

	// Initialisation des variables propres à l'ensemble des todos du bloc
	$todos = array();
	$index_todo = 0;
	$index_tache = 0;

	// Analyse de chaque ligne du bloc
	foreach ($lignes as $_ligne){
		// Initialisation des variables de la todolist en cours
		if ($index_tache == 0) {
			$types_info[$index_todo] = array();
			$priorite_utilisee[$index_todo] = false;
		}

		// Initialisation des variables de la tache en cours
		$priorite = '';
		$tags = $infos = array();
		$texte = trim($_ligne);

		if ($texte) {
			// Extraction du premier caractère de la ligne qui détermine soit :
			// - le statut d'une tâche,
			// - l'indicateur d'un projet,
			// - et sinon le descriptif libre de la tâche précédente.
			// Les caractères de statut ! et ? sont traités par SPIP et précédés d'un &nbsp; qu'il faut
			// au préalable supprimer.
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
				if (preg_match_all(_TODO_REGEXP_INFOS_COMPLEMENTAIRES, $texte, $infos_complementaires)) {
					// Extraction du titre
					$titre = trim(str_replace($infos_complementaires[0], '', $texte));

					// Extraction des informations complémentaires
					foreach($infos_complementaires[1] as $_cle => $_prefixe) {
						$type = rtrim($_prefixe, ':');
						$valeur = $infos_complementaires[2][$_cle];
						if ($type == '@') {
							if (preg_match('#^[1-9]$#', $valeur, $m)) {
								// -- la priorité
								$priorite = $valeur;
								$priorite_utilisee[$index_todo] = true;
							}
							else {
								// -- les étiquettes
								$tags[] = $valeur;
							}
						}
						else {
							// -- les informations typées
							if ($formater = charger_fonction("formater_${type}", 'inc', true))
								$infos[$type] = $formater($valeur);
							else
								$infos[$type] = $valeur;
							if (!in_array($type, $types_info[$index_todo]))
								$types_info[$index_todo][] = $type;
						}
					}
				}
				else
					$titre = $texte;

				// Ajout de la tache dans la liste fournie au modèle
				$todos[$index_todo][$index_tache] = array(
					'statut' => array(
									'id' =>$statut,
									'final' => (in_array($statut, $todo_statuts_finaux) ? true : false),
									'alerte' => (in_array($statut, $todo_statuts_rappel) ? 'mineure' : (in_array($statut, $todo_statuts_alerte) ? 'majeure' : ''))),
					'titre' => $titre,
					'tags' => $tags,
					'infos' => ($priorite_utilisee[$index_todo] ? array_merge($infos, array('priorite' => $priorite)) : $infos),
				);
				$index_tache += 1;
			}
			elseif ($premier == ':') {
				// Projet
				$index_todo += 1;
				$projets[$index_todo] = trim(substr($texte, 1, strlen($texte)-1));
				$index_tache = 0;
			}
			else {
				// Descriptif libre de la tache précedente
				$todos[$index_todo][$index_tache-1]['titre'] .= '<br />' . $texte;
			}
		}
	}

	// Appel pour chaque todolist du modèle par défaut
	if ($todos) {
		$html = '';
		foreach($todos as $_cle => $_taches) {
			if ($_taches) {
				$html .= recuperer_fond(
					'inclure/todo',
					array(
						'projet' => (isset($projets[$_cle]) ? $projets[$_cle] : ''),
						'taches' => $_taches,
						'types_info' => ($priorite_utilisee[$_cle] ? array_merge($types_info[$_cle], array('priorite')) : $types_info[$_cle])
					),
					array(
						'ajax' => true
					)
				);
			}
		}
	}

	return $html;
}

?>
