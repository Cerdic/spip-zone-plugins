<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// faire #UNIMARC{310,a} dans boucle NOTICES

/**
 * Analyse une entree de tableau de descrition
 *  
 *
 * @param array $unimarc Code unimarc a analyser
 * 		[c:numero_zone]						010, 300, 310 ...
 * 		[id:identifiant sur pmb parfois]
 * 		[s:contenu]
 * 			[{i}] 							indice dans la liste (remplissage automatique par php depuis 0)
 * 				[c:sous_zone] 				1 caractere entre 0 et 9 ou a et z, parfois 2 caracteres.
 * 				[value:valeur] 
 * 
 * @return array
 *   - nom : le nom de la categorie
 *   - valeur : sa valeur
 * 
**/
function pmb_parse_unimarc($unimarc) {
	$zone = $unimarc->c;
	$id = $unimarc->id; // souvent vide

	// Le champ $s contient la liste des informations
	if (isset($unimarc->s) && is_array($unimarc->s)) {
		foreach($unimarc->s as $element) {
			$sous_zone = $element->c;
			$valeur = $element->value;

			return pmb_parse_unimarc_defaut($valeur, $zone, $sous_zone, $id, $element);
		}
	}
}


/**
 * Cherche dans le tableau de descripton des unimarc
 * la zone et la sous zone demandees
 * et retourne la ou les couples cle/valeurs
 * correspondantes.
 * 
 * 		Tableau des cles autorisees
 * 		'a' => 'isbn', // sous zone => nom de la correspondance humaine. Peut etre un tableau :
 * 		'b' => array('titre', 'pmb_nettoyer_caracteres') // filtre a appliquer sur la valeur
 * 		'c' => array('texte', 'couper', 300) // filtre a appliquer sur la valeur : couper($valeur, 300)
 * 
 * 		Ou encore, possibilite pour un attribut de donner plusieurs champs
 * 		en indiquant directement les valeurs a retourner A EVALUER (eval)...
 * 		'a' => array(
 * 				'isbn' => '$valeur',
 * 				'id' => '$id',
 * 		,
 * 		'b' => array(
 * 				'titre => array(
 * 					// si tableau, les parametres seront aussi retournes.
 * 					// (cle, valeur, params)
 * 					'titre', array(
 * 						'@post_traitements' => array( 
 * 							'inter' => ' '
 * 						)
 * 				)
 * 			)
 * 		),
 * 
 * @return array
 * 		Tableau de tableau ('cle' => cle, 'valeur' => valeur)
**/
function pmb_parse_unimarc_defaut($valeur, $zone, $sous_zone, $id, $element) {
	static $tab = false;
	if (!$tab) {
		$tab = pmb_parse_unimarc_data();
	}

	if (!isset($tab[$zone]) or !is_array($tab[$zone])) {
		return false;
	}


	// si l'element est dedans, c'est bon signe !
	if (isset($tab[$zone][$sous_zone]) and $t = $tab[$zone][$sous_zone]) {
		
		// si tableau, c'est qu'il y a une fonction de traitement
		// array( 'cle', 'func', array('un', 'deux'))
		// $valeur = func($valeur, 'un', 'deux');
		// ou une des associations cle / valeur directement

		if (is_array($t)) {
			
			// tableau de cle/valeurs
			if (is_string(array_shift(array_keys($t)))) {
				$res = array();
				foreach ($t as $c => $v) {
					if (is_array($v)) {
						list($v, $params) = $v;
						$res[] = array(
							'cle' => $c,
							'valeur' => eval('return ' . $v . ';'),
							'params' => $params,
						);
					} else {
						$res[] = array(
							'cle' => $c,
							'valeur' => eval('return ' . $v . ';'),
						);
					}
				}
				
				return $res;
			
			// filtre sur la donnee
			} else {
				
				$cle = $t[0];
				$filtre = $t[1];
				$args = isset($t[2]) ? $t[2] : array();
				array_unshift($args, $valeur);
				$valeur = call_user_func_array($filtre, $args);
			}
		// juste la valeur
		} else {
			$cle = $t;
		}

		return array(
			array(
				'cle' => $cle,
				'valeur' => $valeur,
			)
		);
	}

	return false;
}


function pmb_nettoyer_caracteres_titre($valeur) {
	$valeur = stripslashes($valeur);
	$valeur = str_replace(
		array("", "", "", "", "\n", ""),
		array("'", "&oelig;", "\"", "\"", "<br />", "&euro;"), $valeur);
	return $valeur;
}



/**
 *  Liste des infos connues d'unimarc
 * et indique comment les traiter.
 * Cf. pmb_parse_unimarc_defaut()
 * 
**/
function pmb_parse_unimarc_data() {
	return array(


		// ISBN (International Standard Book Number)
		'010' => array(
			'a' => 'isbn',
			'b' => 'reliure',
			'd' => 'prix' // disponibilite ou prix
		),
		

		// Langue de la ressource
		'101' => array(
			'a' => 'langues',
		),


		// Pays de publication ou de production
		'102' => array(
			'a' => 'pays',
		),


		// Titre et mention de responsabilité
		'200' => array(
			'a' => array('titre', 'pmb_nettoyer_caracteres_titre'),
			'c' => array('titre_auteur_different', 'pmb_nettoyer_caracteres_titre'),
			'd' => array('titre_parallele', 'pmb_nettoyer_caracteres_titre'),
			'e' => array('soustitre', 'pmb_nettoyer_caracteres_titre'),
			'f' => 'auteur', // premiere mention de responsabilite...
		),


		// Publication, production, diffusion, etc.
		'210' => array(
			'a' => 'lieu_publication',
			'c' => 'editeur',
			'd' => 'annee_publication',
			'e' => 'lieu_fabrication',
			'h' => 'date_fabrication',
		),


		// Description matérielle
		'215' => array(
			'a' => 'importance', // Indication du type de document et importance matérielle
			'c' => array('presentation', 'pmb_nettoyer_caracteres_titre'), // Autres caracteristiques materielles
			'd' => 'format',
			'e' => 'materiel_accompagnement', // Matériel d’accompagnement
		),


		// Collection
		'225' => array(
			'a' => array(
				'collection' => '$valeur',
				'id_collection' => '$id',
			),
			'i' => 'sous_collection', 
			'v' => 'numero_volume', // Numero dans la collection, numero du volume
		),


		// Note générale
		'300' => array(
			'a' => 'note_generale',
		),


		// Note de contenu
		'327' => array(
			'a' => array('note_contenu', 'pmb_nettoyer_caracteres_titre'),
		),


		// Résumé ou extrait
		'330' => array(
			'a' => array('resume', 'pmb_nettoyer_caracteres_titre'),
		),


		// Parties, Ensembles
		'461' => array(
			't' => array('titre_partie', 'pmb_nettoyer_caracteres_titre'), // Partie de x
			'v' => 'numero_partie',
		),


		// Indexation en vocabulaire libre
		// (mots cles)
		'461' => array(
			'a' => 'mots_cles', // Descripteur
		),


		// Classification décimale Dewey
		'676' => array(
			'a' => 'indice_dewey', 
			'v' => 'edition_dewey',
		),


		// Nom de personne - Responsabilité principale
		'700' => pmb_parse_unimarc_data_70x(),

		// Nom de personne - Autre responsabilité principale
		'701' => pmb_parse_unimarc_data_70x(2),

		// Nom de personne - Responsabilité secondaire
		'701' => pmb_parse_unimarc_data_70x(3),
		
		// Image associee
		'896' => array(
			'a' => 'logo_src',
		),

	);
}


function pmb_parse_unimarc_data_70x($indice='') {
	return array(
		'a' => array(
			"id_auteur$indice"  => '$id',
			"liensauteurs$indice" => array(
				'"<a href=\"?page=author_see&amp;id=" . $id . "\">" . $valeur . "</a>"', array(
					'@post_traitements' => array('inter' => ', '),
				)
			),
			"lesauteurs$indice" => array(
				'$valeur', array(
					'@post_traitements' => array('inter' => ', '),
				)
			),

		),
		'b' => array(
			"lesauteurs$indice"   => array(
				'$valeur', array(
					'@post_traitements' => array('inter' => ' ', 'placer_avant' => true),
				)
			),
			/*
			"liensauteurs$indice" => array(
				'$valeur', array(
					'@post_traitements' => array('inter' => ' '),
				)
			)*/
		),
	);
}

?>
