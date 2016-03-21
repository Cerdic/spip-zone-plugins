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
	$res = array();
	// Le champ $s contient la liste des informations
	if (isset($unimarc->s) && is_array($unimarc->s)) {
		$groupe = $unimarc->s;
		
		// si une fonction specifique existe, on la retourne
		if (function_exists($parse = 'pmb_parse_unimarc_data_' . $zone)) {
			return $parse($groupe, $id);
		}

		// sinon, on utilise le parsage prevu...
		foreach($groupe as $element) {
			$sous_zone = $element->c;
			$valeur = $element->value;

			// correction systematique de la valeur
			// car certains caracteres sont faux...
			$valeur = pmb_nettoyer_caracteres($valeur);
			$new = pmb_parse_unimarc_defaut($valeur, $zone, $sous_zone, $id, $element, $groupe);
			if ($new) {
				$res = array_merge($res, $new);
			} else {
				// impossible de parser ce code.
				# echo "<br />** ELEMENT NON TRAITE : $zone / $sous_zone";
				# echo "\n<pre>"; print_r($element); echo "</pre>";
			}
		}
	}
	return $res;
}


/**
 * Cherche dans le tableau de descripton des unimarc
 * la zone et la sous zone demandees
 * et retourne la ou les couples cle/valeurs
 * correspondantes.
 *
 * [100]
 * 
 * 		Tableau des cles autorisees
 * 		'a' => 'isbn', // sous zone => nom de la correspondance humaine. Peut etre un tableau :
 * 		'b' => array('titre', 'pmb_nettoyer_caracteres') // filtre a appliquer sur la valeur
 * 		'c' => array('texte', 'couper', 300) // filtre a appliquer sur la valeur : couper($valeur, 300)
 * 		'9' => 'id/nom' // recherche la cle 9 et une valeur extraite d'une valeur 'id:xxx' 
 * 
 * 		Ou encore, possibilite pour un attribut de donner plusieurs champs
 * 		en indiquant directement les valeurs a retourner A EVALUER (eval)...
 * 		'a' => array(
 * 				'isbn' => '$valeur',
 * 				'id' => '$id',
 * 		),
 *
 * @return array
 * 		Tableau de tableau ('cle' => cle, 'valeur' => valeur)
**/
function pmb_parse_unimarc_defaut($valeur, $zone, $sous_zone, $id, $element, $groupe) {
	static $tab = false;
	if (!$tab) {
		$tab = pmb_parse_unimarc_data();
	}

	if (!isset($tab[$zone]) or !is_array($tab[$zone])) {
		return false;
	}

	// si l'element est dedans, c'est bon signe !
	if (isset($tab[$zone][$sous_zone]) and $t = $tab[$zone][$sous_zone]) {

		// si tableau, c'est qu'il y a 
		// une association cle / valeur directement

		if (is_array($t)) {
			
			// tableau de cle/valeurs
			if (is_string(current(array_keys($t)))) {

				$res = array();
				foreach ($t as $c => $v) {
					$res[] = array(
						'cle' => $c,
						'valeur' => eval('return ' . $v . ';'),
					);
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
			// soit 'nom' soit 'id:nom'
			list($t, $sous) = array_pad(explode(':', $t), 2, null);
			if (!$sous) {
				// simple 'nom'
				$cle = $t;
			} else {
				// 'id:nom'
				list($valeur_cle, $valeur) = explode(':', $valeur, 2);
				if ($valeur AND $valeur_cle==$t) {
					$cle = $sous;
					$valeur = $valeur;
				} else {
					$cle = $valeur = '';
				}
			}
		}
		
		if ($cle and strlen($valeur)) {
			return array(
				array(
					'cle' => $cle,
					'valeur' => $valeur,
				)
			);
		}
	}

	return false;
}


/**
 * Passe les \n en <br /> 
**/
function pmb_nettoyer_caracteres_texte($valeur) {
	$valeur = str_replace(
		array( "\n"),
		array("<br />"), $valeur);
	return $valeur;
}



/**
 *  Liste des infos connues d'unimarc
 * et indique comment les traiter.
 * Cf. pmb_parse_unimarc_defaut()
 * 
**/
function pmb_parse_unimarc_data() {
	$t = array(


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
			'a' => array('titre', 'pmb_nettoyer_caracteres_texte'),
			'c' => array('titre_auteur_different', 'pmb_nettoyer_caracteres_texte'),
			'd' => array('titre_parallele', 'pmb_nettoyer_caracteres_texte'),
			'e' => array('soustitre', 'pmb_nettoyer_caracteres_texte'),
			'f' => 'auteur', // premiere mention de responsabilite...
		),


		// Mention d’édition
		'205' => array(
			'a' => 'edition' // Mention d’édition
		),
		
		// Publication, production, diffusion, etc.
		'210' => array(
			'a' => 'lieu_publication',
			'c' => array(
				'editeur'=>'$valeur',
				'id_editeur'=>'$id',
			),
			'd' => 'annee_publication',
			'e' => 'lieu_fabrication',
			'h' => 'date_fabrication',
		),


		// Description matérielle
		'215' => array(
			'a' => 'importance', // Indication du type de document et importance matérielle
			'c' => array('presentation', 'pmb_nettoyer_caracteres_texte'), // Autres caracteristiques materielles
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
			'a' => array('note_contenu', 'pmb_nettoyer_caracteres_texte'),
		),


		// Résumé ou extrait
		'330' => array(
			'a' => array('resume', 'pmb_nettoyer_caracteres_texte'),
		),


		// Parties, Ensembles
		'461' => array(
			't' => array('titre_partie', 'pmb_nettoyer_caracteres_texte'), // Partie de x
			'v' => 'numero_partie',
		),

		// Unité matérielle
		// a quelle unite apartient cette notice,
		// c'est a dire un chapitre (notice) pour un livre (unite);
		// il y a normalement un id a recuperer...
		'463' => array(
			// y a aussi plusieurs 9 decrivant un lien... ?
			't' => 'unite_materielle', // Titre
			'9' => 'id:id_unite_materielle', // il y a differents 9... avec des valeurs 'id:3219'
		),

		// Indexation en vocabulaire libre
		// (mots cles)
		'610' => array(
			'a' => 'mots_cles', // Descripteur
		),
		


		// Classification décimale Dewey
		'676' => array(
			'a' => 'indice_dewey', 
			'v' => 'edition_dewey',
		),


		// Traites a part dans des fonctions specialisees.
		// 700 - Nom de personne - Responsabilité principale
		// 701 - Nom de personne - Autre responsabilité principale
		// 702 - Nom de personne - Responsabilité secondaire
		// 710 - Nom de collectivite - Responsabilité principale
		// 711 - Nom de collectivite - Autre responsabilité principale
		// 712 - Nom de collectivite - Responsabilité secondaire

		// Adresse électronique et mode d'accès
		'856' => array(
			'u' => 'source', // Identificateur électronique normalisé (URI ex URL)
		),
		
		// Image associee
		'896' => array(
			'a' => 'source_logo',
		),

		// les 900 sont des donnees locales...
		// traitees a part dans une fonction specialisee

	);


	// on propose une surcharge de notre tableau dans une fonction
	// pmb_parse_unimarc_data_locales() qui permet egalement
	// de modifier le tableau au besoin.
	if (function_exists('pmb_parse_unimarc_data_locales')) {
		$t = pmb_parse_unimarc_data_locales($t);
	}
	
	return $t;
}

/*
// exemple de fonction locale, a placer dans un fichier de fonctions SPIP.
function pmb_parse_unimarc_data_locales($tab) {
	$tab['900'] = array(
		'a' => 'toto'
	);
	return $tab;
}
*/




/**
 * les 70x sont des liens vers des auteurs
 * Et il peut y avoir plusieurs zones identiques.
 * Il faut donc concatener avec les auteurs deja trouves
 */

// Nom de personne - Responsabilité principale
function pmb_parse_unimarc_data_700($groupe, $id) {
	return pmb_parse_unimarc_data_7xx($groupe, $id, 'personne');
}

// Nom de personne - Autre responsabilité principale
function pmb_parse_unimarc_data_701($groupe, $id) {
	return pmb_parse_unimarc_data_7xx($groupe, $id, 'personne', 2);
}

// Nom de personne - Responsabilité secondaire
function pmb_parse_unimarc_data_702($groupe, $id) {
	return pmb_parse_unimarc_data_7xx($groupe, $id, 'personne', 3);
}


// Nom de collectif - Responsabilité principale
function pmb_parse_unimarc_data_710($groupe, $id) {
	return pmb_parse_unimarc_data_7xx($groupe, $id, 'collectivite');
}

// Nom de collectif - Autre responsabilité principale
function pmb_parse_unimarc_data_711($groupe, $id) {
	return pmb_parse_unimarc_data_7xx($groupe, $id, 'collectivite', 2);
}

// Nom de collectif - Responsabilité secondaire
function pmb_parse_unimarc_data_712($groupe, $id) {
	return pmb_parse_unimarc_data_7xx($groupe, $id, 'collectivite', 3);
}

// type : 'personne', 'collectivite' ...
function pmb_parse_unimarc_data_7xx($groupe, $id, $type, $indice='') {
	$nom = $prenom = '';
	$type = '_' . $type;
	foreach ($groupe as $element) {
		switch($element->c) {
			case 'a':
				$nom = pmb_nettoyer_caracteres($element->value);
				break;
			case 'b':
				$prenom = pmb_nettoyer_caracteres($element->value);
				break;
		}
	}
	if ($nom) {
		if ($prenom) {
			$nom = $prenom . ' ' . $nom;
		}
		return array(
			array(
				'cle' => "id_auteur$type$indice",
				'valeur' => $id,
			),
			array(
				'cle' => "liensauteurs$type$indice",
				'valeur' => "<a href=\"" . generer_url_public('pmb_auteur', "id_auteur=$id") . "\">" . $nom . "</a>",
				'@post_traitements' => array('inter' => ', '),
			),
			array(
				'cle' => "lesauteurs$type$indice",
				'valeur' => $nom,
				'@post_traitements' => array('inter' => ', '),
			),

		);
	}
}


/**
 * les 900 sont des donnees locales...
 * et traitees separements...
 * il ne semble pas possible de savoir toujours ce que c'est...
 * cependant, PMB pour ses champs extras exportables envoie :
 * [900]
 * 		[a] Valeur du champ
 * 		[l] Label du champ
 * 		[n] Colonne SQL du champ.
 *
 * Il envoie plusieurs 900 aussi.
 * On en fait autant de champ #N avec #LABEL_N
 * fonction pour traiter les champs extras
 * declares en 900
 * 		n = champ sql
 * 		a = valeur
 * 		l = label humain
 */
function pmb_parse_unimarc_data_900($groupe, $id) {
	$cle = $label = $valeur = '';
	foreach ($groupe as $element) {
		switch($element->c) {
			case 'n':
				$cle = strtolower($element->value);
				break;
			case 'l':
				$label = pmb_nettoyer_caracteres($element->value);
				break;
			case 'a':
				$valeur = pmb_nettoyer_caracteres($element->value);
				break;
		}
	}
	if ($cle and $valeur and $label) {
		return array(
			array(
				'cle'    => $cle,
				'valeur' => pmb_nettoyer_caracteres($valeur),
			),
			array(
				'cle'    => 'label_' . $cle,
				'valeur' => pmb_nettoyer_caracteres($label),
			),
		);
	}
}
