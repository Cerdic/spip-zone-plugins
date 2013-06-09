<?php

if (!defined('_TODO_REGEXP_INFOS_COMPLEMENTAIRES'))
	define('_TODO_REGEXP_INFOS_COMPLEMENTAIRES', '#([a-z0-9]+:|@)([a-z0-9-]+)(?:\s|$)#Uims');

// Un callback pour analyser la liste puis appeler un squelette avec les paramètres
function tw_todo($t) {
	// Liste des statuts supportés
	global $todo_statuts;
	// En attendant de la config
	static $todo_statuts_finaux = array('termine', 'abandonne');
	static $todo_statuts_rappel = array('arrete');
	static $todo_statuts_alerte = array('alerte', 'inconnu');

	// Extraction de lignes du texte
	$lignes = explode("\n", trim($t[0]));
	array_shift($lignes);
	array_pop($lignes);
	
	$taches = array();
	$types_info = array();
	$priorite_utilisee = false;
	$index_tache = 0;
	foreach ($lignes as $_ligne){
		$priorite = '';
		$tags = $types = $infos = array();
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
							if ((intval($valeur) >=1) AND (intval($valeur) <=9)) {
								// -- la priorité
								$priorite = $valeur;
								$priorite_utilisee = true;
							}
							else {
								// -- les étiquettes
								$tags[] = $valeur;
							}
						}
						else {
							// -- les informations typées
							$infos[$type] = $valeur;
							if (!in_array($type, $types_info))
								$types_info[] = $type;
						}
					}
				}
				else
					$titre = $texte;

				// Ajout de la tache dans la liste fournie au modèle
				$taches[$index_tache] = array(
					'statut' => array(
									'id' =>$statut,
									'final' => (in_array($statut, $todo_statuts_finaux) ? true : false),
									'alerte' => (in_array($statut, $todo_statuts_rappel) ? 'avertissement' : (in_array($statut, $todo_statuts_alerte) ? 'probleme' : ''))),
					'titre' => $titre,
					'tags' => $tags,
					'infos' => ($priorite_utilisee ? array_merge($infos, array('priorite' => $priorite)) : $infos),
				);
				$index_tache += 1;
			}
			elseif ($premier == ':') {
				// Projet
			}
			else {
				// Descriptif libre de la tache précedente
				$taches[$index_tache-1]['titre'] .= '<br />' . $texte;
			}
		}
	}

	if ($taches) {
		return recuperer_fond(
			'inclure/todo',
			array(
				'taches' => $taches,
				'types_info' => ($priorite_utilisee ? array_merge($types_info, array('priorite')) : $types_info)
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
