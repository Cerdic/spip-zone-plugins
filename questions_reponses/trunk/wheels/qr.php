<?php

// Regexp permettant de récupérer chacune des informations additionnelles qui peuvent compléter le titre de la tâche :
// - #tag ou tag est un mot. Exemple : #courses ou #перевод-шаблон
// - type:valeur ou type et valeur sont des mots. Pas utilisé pour l'instant
if (!defined('_QR_REGEXP_INFOS_COMPLEMENTAIRES'))
	define('_QR_REGEXP_INFOS_COMPLEMENTAIRES', '%([\w-]+:|#)([\w.-]+)(?:\s|$)%Uu');


/**
 * Analyse le contenu du bloc inclu entre les marqueurs de début et de fin de la FAQ
 * puis appelle un squelette avec les paramètres calculés
 *
 * @param array	$t	l'index 4 représente le contenu du bloc, l'index 3 la valeur du format si il existe.
 * @return string	le html généré à partir d'un squelette
 */
function tw_qr($t) {
	// Initialisation du html calculé
	$html = $t;

	// Extraction de lignes du texte
	// La wheel renvoie un tableau à cette callback qui est le résultat d'un preg_match_all.
	// Le contenu du tableau est le suivant :
	// - index 0 : la capture du pattern complet
	// - index 1 : la capture de l'attribut format si il existe
	// - index 2 : la capture des quotes entourant la valeur de l'attribut format
	// - index 3 : la capture de la valeur de l'attribution format
	// - index 4 : la capture du texte compris entre les balises <faq> et </faq>
	// --> Seuls les index 3 et 4 sont utilisés.
	$lignes = explode("\n", trim($t[4]));

	// Initialisation des variables propres à l'ensemble des faqs du bloc
	$faqs = array();
	$index_faq = 0;
	$index_qr = 0;
	$index_ligne = 0;
	$question_en_cours = false;

	// Analyse de chaque ligne du bloc
	while ($index_ligne <= count($lignes)){
		// Initialisation des variables de la faq en cours
		// (pour un bloc faq contenant plusieurs faq séparées par des titres)
		if (($index_qr == 0) AND !$question_en_cours) {
			$types_info[$index_faq] = array();
		}

		// On vérifie qu'on a atteint la fin du bloc de texte compris entre <faq> et </faq>.
		// -- si c'est le cas, on ajoute la question-reponse en cours si elle existe
		// -- sinon, on traite la nouvelle ligne
		if ($index_ligne == count($lignes)) {
			if ($question_en_cours) {
				$faqs[$index_faq][$index_qr] = array(
					'question' => $question,
					'reponse' => trim($reponse),
					'tags' => $tags,
					'infos' => $infos,
				);
				$question_en_cours = false;
			}
		}
		else {
			// Extraction de la nouvelle ligne à traiter
			$texte = trim($lignes[$index_ligne]);

			if ($texte) {
				// Extraction du premier caractère de la ligne qui détermine soit :
				// - l'indicateur du titre de la question,
				// - l'indicateur d'un titre pour la faq,
				// - et sinon la réponse comme un descriptif libre de la question précédente.
				// Le caractère de question '?' est traité par SPIP et précédés
				// d'un '&nbsp;' parfois à cause de la typographie et il faut donc au préalable le supprimer.
				if (strpos($texte, '&nbsp;') === 0) {
					$texte = substr($texte, 6, strlen($texte)-6);
				}
				$premier = substr($texte, 0, 1);

				if (($premier != '?')
				AND ($premier != ':')) {
					// La ligne correspond à un texte de réponse non vide si une question est en cours
					if ($question_en_cours)
						$reponse .= $reponse ? "\n" . $texte : $texte;
				}
				else {
					// Il faut tester si une question est en cours. Si c'est le cas il faut clore la question en cours
					// avant de commencer la nouvelle question ('?') ou la nouvelle faq par son titre (':').
					if ($question_en_cours) {
						$faqs[$index_faq][$index_qr] = array(
							'question' => $question,
							'reponse' => trim($reponse),
							'tags' => $tags,
							'infos' => $infos,
						);
						$question_en_cours = false;
						$index_qr += 1;
					}

					if ($premier == '?') {
						// On démarre une nouvelle question
						// -- initialisation des variables de la question en cours
						$tags = $infos = array();
						$question_en_cours = true;
						$reponse = '';
						$texte = trim(substr($texte, 1, strlen($texte)-1));

						// -- le texte de la question, que l'on sépare du reste des informations complémentaires éventuelles
						if (preg_match_all(_QR_REGEXP_INFOS_COMPLEMENTAIRES, $texte, $infos_complementaires)) {
							// Extraction du titre
							$question = trim(str_replace($infos_complementaires[0], '', $texte));

							// Extraction des informations complémentaires
							foreach($infos_complementaires[1] as $_cle => $_prefixe) {
								$type = rtrim($_prefixe, ':');
								$valeur = $infos_complementaires[2][$_cle];
								if ($type == '#') {
									// -- les étiquettes
									$tags[] = $valeur;
								}
								else {
									// -- les informations typées
									if ($formater = charger_fonction("qr_formater_${type}", 'inc', true)) {
										$infos[$type] = $formater($valeur);
									}
									else
										$infos[$type] = $valeur;
									if (!in_array($type, $types_info[$index_faq]))
										$types_info[$index_faq][] = $type;
								}
							}
						}
						else
							$question = $texte;
					}
					elseif ($premier == ':') {
						// Titre d'une nouvelle faq incluse dans le bloc faq en cours de traitement
						$index_faq += 1;
						$titres[$index_faq] = trim(substr($texte, 1, strlen($texte)-1));
						$index_qr = 0;
					}
				}
			}
			elseif ($question_en_cours) {
				// Ligne vide. Comme elle est incluse dans le texte de la réponse on la conserve
				$reponse .= $reponse ? "\n" . $texte : $texte;
			}
		}

		$index_ligne++;
	}

	// Appel pour chaque todolist du modèle par défaut
	if ($faqs) {
		$html = '';
		$format = $t[3] ? $t[3] : 'dl';
		foreach($faqs as $_cle => $_faq) {
			if ($_faq) {
				$html .= recuperer_fond(
					"inclure/qr_${format}",
					array(
						'titre' => (isset($titres[$_cle]) ? $titres[$_cle] : ''),
						'faq' => $_faq,
						'types_info' => $types_info[$_cle]
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
