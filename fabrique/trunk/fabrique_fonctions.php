<?php

/**
 * Fonctions utiles pour les squelettes de la fabrique
 *
 * @package SPIP\Fabrique\Fonctions
**/

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Déterminer le répertoire de travail
 * de la Fabrique.
 *
 * Dans :
 * - plugins/fabrique_auto si possible, sinon dans
 * - tmp/cache/fabrique
 *
 * @return string
 *     Le chemin de destination depuis la racine de SPIP.
**/
function fabrique_destination() {
	static $destination = null;
	if (is_null($destination)) {
		if (is_writable(_DIR_PLUGINS . rtrim(FABRIQUE_DESTINATION_PLUGINS, '/'))) {
			$destination = _DIR_PLUGINS . FABRIQUE_DESTINATION_PLUGINS;
		} else {
			sous_repertoire(_DIR_CACHE, rtrim(FABRIQUE_DESTINATION_CACHE, '/'));
			$destination = _DIR_CACHE . FABRIQUE_DESTINATION_CACHE;
		}
	}
	return $destination;
}


/**
 * Crée l'arborescence manquante
 *
 * Crée tous les répertoires manquants dans une arborescence donnée.
 * Les répertoires sont séparés par des '/'
 * 
 * @example 
 *     sous_repertoire_complet('a/b/c/d');
 *     appelle sous_repertoire() autant de fois que nécéssaire.
 * 
 * @param string $arbo
 *     Arborescence, tel que 'prive/squelettes/contenu'
 * @return void
**/
function sous_repertoire_complet($arbo) {
	$a = explode('/', $arbo);
	if ($a[0] == '.' OR $a[0] == '..') {
		$base = $a[0] . '/' . $a[1];
		array_shift($a);
		array_shift($a);
	} else {
		$base = $a[0];
		array_shift($a);
	}

	foreach ($a as $dir) {
		$base .= '/' . $dir;
		sous_repertoire($base);
	}
}


/**
 * Concatène en utilisant implode, un tableau, de maniere récursive 
 *
 * @param array $tableau
 *     Tableau à transformer
 * @param string $glue
 *     Chaine insérée entre les valeurs
 * @return string|bool
 *     - False si pas un tableau
 *     - Chaine concaténée sinon
**/
function fabrique_implode_recursif($tableau, $glue='') {
	if (!is_array($tableau)) {
		return false;
	}

	foreach ($tableau as $c =>$valeur) {
		if (is_array($valeur)) {
			$tableau[$c] = fabrique_implode_recursif($valeur, $glue);
		}
	}

	return implode($glue, $tableau);
}


/**
 * Écrit une ouverture de code PHP
 * 
 * Fait écrire '<?php' sans que ce php soit executé par SPIP !
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée du code à produire
**/
function balise_PHP_dist($p) {
	$p->code = "'<?php echo \'<?php\n\'; ?>'";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Convertie une chaîne pour en faire une chaîne de langue
 *
 * Permet d'insérer un texte comme valeur d'une clé de langue, lorsqu'on
 * crée un fichier de langue avec la fabrique.
 * 
 * Transforme les caractères &# et échappe les apostrophes :
 * - &#xxx => le bon caractère
 * - ' => \' 
 *
 * @example
 *     '#ENV{prefixe}_description' => '[(#ENV{paquet/description}|chaine_de_langue)]',
 * 
 * @link http://www.php.net/manual/fr/function.html-entity-decode.php#104617
 * @param string $texte
 *     Le texte à écrire dans la chaîne de langue
 * @return string
 *     Le texte transformé
**/
function chaine_de_langue($texte) {
	$texte = html_entity_decode($texte, ENT_QUOTES, 'UTF-8');
	# egalement 
	# http://www.php.net/manual/fr/function.html-entity-decode.php#104617

	return addslashes($texte);
}

/**
 * Modifie le nom de la clé de langue pour
 * utiliser le vrai nom de l'objet
 *
 * Remplace 'objets' par le nom de l'objet, et 'objet' par le type d'objet,
 * mais ne touche pas à '\objets' ou '\objet'.
 *
 * @note
 *     Cette fonction ne sert pas uniquement à définir des clés pour
 *     les fichiers de chaînes de langue, et pourrait être renommée
 * 
 * @example
 *     cle_de_langue('titre_objets') => titre_chats
 *     cle_de_langue('icone_creer_objet') => icone_creer_chat
 *     cle_de_langue('prive/\objets/infos/objet.html') => prive/objets/infos/chat.html
 * @param string $cle
 *     La clé à transformer
 * @param array $desc_objet
 *     Couples d'information sur l'objet en cours, avec les index
 *     'objet' et 'type' définis
 * @retrun string
 *     La clé transformée
**/
function cle_de_langue($cle, $desc_objet) {
	// on permet d'echapper \objets pour trouver 'objets' au bout du compte
	// sans qu'il soit transforme dans le nom reel de l'objet
	// cas : 'prive/\objets/infos/objet.html' => 'prive/objets/infos/nom.html'
	$cle = str_replace("\o", "\1o\\", $cle);
	$cle =  str_replace(
		array('objets', 'objet'),
		array($desc_objet['objet'], $desc_objet['type']), $cle);
	return str_replace("\1o\\", "o", $cle);
}

/**
 * Identique à |cle_de_langue sur toutes les valeurs d'un tableau 
 *
 * @see cle_de_langue()
 * @param array $tableau
 *     Tableau dont on veut transformer les valeurs
 * @param array $desc_objet
 *     Couples d'information sur l'objet en cours, avec les index
 *     'objet' et 'type' définis
 * @return array
 *     Tableau avec les valeurs transformées
**/
function tab_cle_de_langue($tableau, $desc_objet) {
	foreach($tableau as $c => $v) {
		$tableau[$c] = cle_de_langue($v, $desc_objet);
	}
	return $tableau;
}

/**
 * Cherche s'il existe une chaîne de langue pour les clés de tableaux
 * et ajoute alors la traduction dans la valeur de ce tableau
 *
 * @param array $tableau
 *     Couples (clé => texte)
 * @param string $prefixe_cle
 *     Préfixe ajouté aux clés pour chercher les trads
 * @param string $sep
 *     Séparateur entre l'ancienne valeur et la concaténation de traduction
 * @return array
 *     Couples (clé => texte complété de la traduction si elle existe)
**/
function tab_cle_traduite_ajoute_dans_valeur($tableau, $prefixe_cle="", $sep = "&nbsp;: ") {
	foreach($tableau as $c => $v) {
		$trad = _T("fabrique:". $prefixe_cle . $c, array(), array('force' => false));
		if ($trad) {
			$tableau[$c] = $v . $sep . $trad;
		} else {
			$tableau[$c] = $v;
		}
	}
	return $tableau;
}

/**
 * Équivalent de wrap() sur les valeurs du tableau
 * 
 * @param array $tableau
 *     Tableau cle => texte
 * @param string $balise
 *     Balise qui encapsule
 * @return array $tableau
 *     Tableau clé => <balise>texte</balise>
**/
function tab_wrap($tableau, $balise) {
	foreach ($tableau as $c => $v) {
		$tableau[$c] = wrap($v, $balise);
	}
	return $tableau;
}


/**
 * Fabrique un tableau de chaînes de langues
 * avec des clés d'origines passées dans la fonctions
 * cle_de_langue, et trie.
 *
 * @param array $objet
 *     Description de l'objet dans la fabrique
 * @return array
 *     Couples (clé de langue => Texte)
**/
function fabriquer_tableau_chaines($objet) {
	$chaines = array();
	if (!is_array($objet)) { return $chaines; }
	if (!$table = $objet['table'] OR !isset($objet['chaines']) OR !is_array($objet['chaines'])) {
		return $chaines;
	}
	// les chaines definies pour l'objet
	foreach ($objet['chaines'] as $cle => $chaine) {
		$chaines[ cle_de_langue($cle, $objet) ] = $chaine;
	}
	// les chaines definies pour chaque champ de l'objet
	if (is_array($objet['champs'])) {
		foreach ($objet['champs'] as $info) {
			$chaines[ cle_de_langue('label_' . $info['champ'], $objet) ] = $info['nom'];
			if ($info['explication']) {
				$chaines[ cle_de_langue('explication_' . $info['champ'], $objet) ] = $info['explication'];
			}
		}
	}
	// les rôles définis
	if ($roles = fabrique_description_roles($objet)) {
		foreach ($roles['roles_trads'] as $cle => $texte) {
			$chaines[ $cle ] = $texte;
		}
	}

	ksort($chaines);
	return $chaines;
}

/**
 * Retourne la description des rôles pour un objet
 *
 * @param array $objet
 *     Descrption de l'objet
 * @return array
 *     Description des rôles. 4 index :
 *     - roles_colonne : la colonne utilisée, toujours 'role'
 *     - roles_titre   : couples clé du role, clé de langue du role
 *     - roles_objets  : tableau objet => liste des clés de roles
 *     - roles_trads   : couples clé de langue => Texte
 *     - roles_defaut  : la clé du role par défaut
 */
function fabrique_description_roles($objet) {
	$desc = array();
	// les rôles définis
	if (!options_presentes($objet, array('table_liens', 'roles'))
	OR !($roles = trim($objet['roles']))) {
		return $desc;
	}
	$desc['roles_colonne'] = 'role';
	$desc['roles_titres'] = array(); # cle => cle_langue
	$desc['roles_objets'] = array();
	$desc['roles_trads']  = array(); # cle_langue => Texte
	$desc['roles_defaut']  = '';

	$roles = explode("\n", $roles);
	foreach ($roles as $i=>$r) {
		list($cle, $texte) = explode(',', $r, 2);
		$cle = trim($cle);
		if (!$i) $desc['roles_defaut'] = $cle; # la valeur par défaut est la première indiquée
		$cle_langue = 'role_' . $cle;
		$desc['roles_titres'][$cle] = $objet['type'] . ':' . $cle_langue;
		$desc['roles_trads'][$cle_langue] = trim($texte);
	}
	$liens = array_map('table_objet', $objet['vue_liens']);
	foreach ($liens as $l) {
		$desc['roles_objets'][$l] = array_keys($desc['roles_titres']);
	}
	return $desc;
}


/**
 * Indique si le champ est présent dans l'objet
 *
 * Champ, au sens de colonne SQL
 *
 * @param array $objet
 *     Descrption de l'objet
 * @param string $champ
 *     Nom du champ SQL à tester
 * @return string
 *     Même retour que le filtre |oui :
 *     - Un espace si le champ SQL exsitera dans l'objet
 *     - Chaîne vide sinon
**/
function champ_present($objet, $champ) {
	if (is_array($objet['champs'])) {
		foreach ($objet['champs'] as $info) {
			if ($info['champ'] == $champ) {
				return " "; // true
			}
		}
	}
	// id_rubrique, id_secteur
	if (isset($objet['rubriques']) AND is_array($objet['rubriques'])) {
		if (in_array($champ, $objet['rubriques'])) {
			return " "; // true
		}
	}
	// lang, langue_choisie, id_trad
	if (isset($objet['langues']) AND is_array($objet['langues'])) {
		if (in_array($champ, $objet['langues'])) {
			return " "; // true
		}
		if ($objet['langues']['lang'] and ($champ == 'langue_choisie')) {
			return " "; // true
		}
	}
	// date
	if ($objet['champ_date']) {
		if ($champ == $objet['champ_date']) {
			return " "; // true
		}
	}
	// statut
	if ($objet['statut']) {
		if ($champ == 'statut') {
			return " "; // true
		}
	}

	return ""; // false
}


/**
 * Indique si toutes les options sont présentes dans l'objet
 * 
 * Option au sens de clé de configuration, pas un nom de champ SQL
 *
 * @param array $objet
 *     Descrption de l'objet
 * @param array $champs
 *     Liste de nom d'options à tester
 * @return string
 *     Même retour que le filtre |oui :
 *     - Un espace si toutes les options sont présentes dans l'objet
 *     - Chaîne vide sinon
**/
function options_presentes($objet, $champs) {
	if (!$champs) return false;
	if (!is_array($champs)) $champs = array($champs);
	foreach ($champs as $champ) {
		if (!option_presente($objet, $champ)) {
			return ""; // false
		}
	}
	return " "; // true

}

/**
 * Indique si une option est présente dans l'objet
 * 
 * Option au sens de clé de configuration, pas un nom de champ SQL
 *
 * @param array $objet
 *     Descrption de l'objet
 * @param array $champ
 *     Nom de l'option à tester
 * @return string
 *     Même retour que le filtre |oui :
 *     - Un espace si l'option est présente dans l'objet
 *     - Chaîne vide sinon
**/
function option_presente($objet, $champ) {
	// a la racine
	if (isset($objet[$champ]) and $objet[$champ]) {
		return " "; // true
	}

	// id_rubrique, vue_rubrique
	if (isset($objet['rubriques']) AND is_array($objet['rubriques'])) {
		if (in_array($champ, $objet['rubriques'])) {
			return " "; // true
		}
	}

	// lang, id_trad
	if (isset($objet['langues']) AND is_array($objet['langues'])) {
		if (in_array($champ, $objet['langues'])) {
			return " "; // true
		}
	}
	
	// menu_edition, outils_rapides
	if (isset($objet['boutons']) AND is_array($objet['boutons'])) {
		if (in_array($champ, $objet['boutons'])) {
			return " "; // true
		}
	}

	return ""; // false
}


/**
 * Indique si une option donnée est presente dans la définition d'un champ
 * de la fabrique
 *
 * @param array $champ
 *     Description d'un champ SQL d'un objet créé avec la fabrique
 * @param string $option
 *     Option testée
 * @return string
 *     Même retour que le filtre |oui :
 *     - Un espace si l'option est présente dans le champ de l'objet
 *     - Chaîne vide sinon
 */
function champ_option_presente($champ, $option) {
	if (isset($champ[$option]) and $champ[$option]) {
		return " "; // true
	}

	// editable, versionne, obligatoire
	if (isset($champ['caracteristiques']) and is_array($champ['caracteristiques'])) {
		if (in_array($option, $champ['caracteristiques'])) {
			return " "; // true
		}
	}

	return false;
}

/**
 * Retourne les objets possédant un certain champ SQL
 * 
 * Cela simplifie des boucles DATA
 *
 * @example
 *     #OBJETS|objets_champ_present{id_rubrique}
 * 
 *     On peut ne retourner qu'une liste de type de valeur (objet, type, id_objet)
 *     #OBJETS|objets_champ_present{id_rubrique, objet} // chats,souris
 *
 * @param array $objets
 *     Liste des descriptions d'objets créés avec la fabrique
 * @param string $champ
 *     Type de champ sélectionné
 * @param string $type
 *     Information de retour désiré :
 *     - vide pour toute la description de l'objet
 *     - clé dans la description de l'objet pour obtenir uniquement ces descriptions
 * @return array
 *     - tableau de description des objets sélectionnés (si type non défini)
 *     - tableau les valeurs du type demandé dans les objets sélectionnés (si type défini)
**/
function objets_champ_present($objets, $champ, $type='') {
	return _tableau_option_presente('champ_present', $objets, $champ, $type);
}


/**
 * Retourne les objets possédant une certaine option
 *
 * Option au sens des clés du formulaire de configuration de l'objet
 *
 * @example 
 *     #OBJETS|objets_option_presente{vue_rubrique}
 *     #OBJETS|objets_option_presente{auteurs_liens}
 *
 *     On peut ne retourner qu'une liste de type de valeur (objet, type, id_objet)
 *     #OBJETS|objets_option_presente{auteurs_liens, objet} // chats,souris
 *
 * @param array $objets
 *     Liste des descriptions d'objets créés avec la fabrique
 * @param string $option
 *     Type d'option sélectionnée
 * @param string $type
 *     Information de retour désiré :
 *     - vide pour toute la description de l'objet
 *     - clé dans la description de l'objet pour obtenir uniquement ces descriptions
 * @return array
 *     - tableau de description des objets sélectionnés (si type non défini)
 *     - tableau les valeurs du type demandé dans les objets sélectionnés (si type défini)
**/
function objets_option_presente($objets, $option, $type='') {
	return _tableau_option_presente('option_presente', $objets, $option, $type);
}


/**
 * Retourne les objets possédant plusieurs options
 * 
 * Option au sens des clés du formulaire de configuration de l'objet
 *
 * @example
 *     #OBJETS|objets_options_presentes{#LISTE{table_liens,vue_liens}}
 * 
 *     On peut ne retourner qu'une liste de type de valeur (objet, type, id_objet)
 *     #OBJETS|objets_options_presentes{#LISTE{table_liens,vue_liens}, objet} // chats,souris
 * 
 * @param array $objets
 *     Liste des descriptions d'objets créés avec la fabrique
 * @param array $options
 *     Liste de type d'option à sélectionner
 * @param string $type
 *     Information de retour désiré :
 *     - vide pour toute la description de l'objet
 *     - clé dans la description de l'objet pour obtenir uniquement ces descriptions
 * @return array
 *     - tableau de description des objets sélectionnés (si type non défini)
 *     - tableau les valeurs du type demandé dans les objets sélectionnés (si type défini)
**/
function objets_options_presentes($objets, $options, $type='') {
	return _tableau_options_presentes('option_presente', $objets, $options, $type);
}

/**
 * Retourne des champs en fonction d'une option trouvée
 *
 * @example
 *     #CHAMPS|champs_option_presente{editable}
 *     #CHAMPS|champs_option_presente{versionne}
 *
 * @param array $champs
 *     Liste des descriptions de champs d'un objet créé avec la fabrique
 * @param string $option
 *     Type d'option sélectionnée
 * @param string $type
 *     Information de retour désiré :
 *     - vide pour toute la description du champ
 *     - clé dans la description du champ pour obtenir uniquement ces descriptions
 * @return array
 *     - tableau de description des champs sélectionnés (si type non défini)
 *     - tableau les valeurs du type demandé dans les champs sélectionnés (si type défini)
**/
function champs_option_presente($champs, $option, $type='') {
	return _tableau_option_presente('champ_option_presente', $champs, $option, $type);
}

/**
 * Retourne des champs en fonction des options trouvées
 *
 * @example
 *     #CHAMPS|champs_options_presentes{#LISTE{obligatoire,saisie}}
 *
 * @param array $champs
 *     Liste des descriptions de champs d'un objet créé avec la fabrique
 * @param array $options
 *     Liste de type d'options à sélectionner
 * @param string $type
 *     Information de retour désiré :
 *     - vide pour toute la description du champ
 *     - clé dans la description du champ pour obtenir uniquement ces descriptions
 * @return array
 *     - tableau de description des champs sélectionnés (si type non défini)
 *     - tableau les valeurs du type demandé dans les champs sélectionnés (si type défini)
**/
function champs_options_presentes($champs, $options, $type='') {
	return _tableau_options_presentes('champ_option_presente', $champs, $options, $type);
}


/**
 * Fonction générique pour retourner une liste de choses dans un tableau
 *
 * @param string $func
 *     Nom de la fonction à appeler, tel que
 *     - champ_option_presente
 *     - option_presente
 *     - ...
 * @param array $tableau
 *     Tableau de descriptions (descriptions d'objet ou descriptions de champ d'objet)
 * @param string $option
 *     Nom de l'option dont on teste la présence
 * @param string $type
 *     Information de retour désiré :
 *     - vide pour toute la description
 *     - clé dans la description pour obtenir uniquement ces descriptions
 * @return array
 *     Liste des descriptions correspondant à l'option demandée
 */
function _tableau_option_presente($func, $tableau, $option, $type='') {
	$o = array();

	if (!is_array($tableau) OR !$func) {
		return $o;
	}
	// tableau est un tableau complexe de donnee
	foreach ($tableau as $objet) {
		// on cherche la donnee 'option' dans le tableau
		// en utilisant une fonction specifique de recherche (option_presente, champ_present, ...)
		if ($func($objet, $option)) {
			// si on a trouve notre option :
			// type permet de recuperer une cle specifique dans la liste des cles parcourues.
			// sinon, retourne tout le sous tableau.
			if ($type and isset($objet[$type])) {
				$o[] = $objet[$type];
			} elseif (!$type) {
				$o[] = $objet;
			}
		}
	}
	return $o;
}

/**
 * Fonction générique pour retourner une liste de choses multiples dans un tableau
 *
 * @param string $func
 *     Nom de la fonction à appeler, tel que
 *     - champ_option_presente
 *     - option_presente
 *     - ...
 * @param array $tableau
 *     Tableau de descriptions (descriptions d'objet ou descriptions de champ d'objet)
 * @param array $options
 *     Nom des l'options dont on teste leur présence
 * @param string $type
 *     Information de retour désiré :
 *     - vide pour toute la description
 *     - clé dans la description pour obtenir uniquement ces descriptions
 * @return array
 *     Liste des descriptions correspondant aux options demandées
 */
function _tableau_options_presentes($func, $tableau, $options, $type='') {
	if (!$options) return array();

	if (!is_array($options)) {
		$options = array($options);
	}

	$first = false;
	foreach ($options as $option) {
		$r = _tableau_option_presente($func, $tableau, $option, $type);
		if (!$first) {
			$res = $r;
			$first = true;
		} else {
			#$res = array_intersect($res, $r);
			// array_intersect() ne prend pas en compte les sous tableaux
			foreach ($res as $i => $v) {
				if (false === array_search($v, $r)) {
					unset($res[$i]);
				}
			}
			$res = array_values($res);
		}
	}

	return $res;
}


/**
 * Retourne une ecriture de criteres
 * {id_parent?}{id_documentation?}
 * avec tous les champs id_x declarés dans l'interface
 * dans la liste des champs.
 * 
 * Cela ne concerne pas les champs speciaux (id_rubrique, id_secteur, id_trad)
 * qui ne seront pas inclus. 
 *
 * @param array $objet
 *     Description de l'objet dans la fabrique
 * @return string
 *     L'écriture des critères de boucle
**/
function criteres_champs_id($objet) {
	$ids = array();
	if (is_array($objet['champs'])) {
		foreach ($objet['champs'] as $info) {
			if (substr($info['champ'], 0, 3) == 'id_') {
				$ids[] = $info['champ'];
			}
		}
	}
	if (!$ids) {
		return "";
	}
	return "{" . implode("?}{", $ids) . "?}";
}

/**
 * Retourne un tableau de toutes les tables SQL
 * pour tous les objets.
 *
 * @param array $objets
 *     Liste des descriptions d'objets créés avec la fabrique
 * @param string $quoi
 *     Choix du retour désiré :
 *     - 'tout'   => toutes les tables (par défaut)
 *     - 'objets' => les tables d'objet (spip_xx, spip_yy)
 *     - 'liens'  => les tables de liens (spip_xx_liens, spip_yy_liens)
 * @return array
 *     Liste des tables
**/
function fabrique_lister_tables($objets, $quoi='tout') {
	static $tables = array();

	if (!$objets) return array();

	$hash = md5(serialize($objets));
	
	if (!isset($tables[$hash])) {
		$tables[$hash] = array(
			'tout' => array(),
			'objets' => array(),
			'liens' => array(),
		);
		foreach ($objets as $o) {
			// tables principales
			if (isset($o['table']) and $o['table']) {
				$tables[$hash]['objets'][] = $o['table'];
				$tables[$hash]['tout'][] = $o['table'];
				// tables de liens
				if ($o['table_liens']) {
					$tables[$hash]['liens'][] = $o['nom_table_liens'];
					$tables[$hash]['tout'][]  = $o['nom_table_liens'];
				}
			}
		}
	}
	
	return $tables[$hash][$quoi];
}


/**
 * Indique si un des objets a besoin du pipeline demandé
 *
 * @param array $objets
 *     Liste des descriptions d'objets créés avec la fabrique
 * @param string $pipeline
 *     Nom du pipeline
 * @return array
 *     Liste des objets (descriptions) utilisant le pipeline
 */
function fabrique_necessite_pipeline($objets, $pipeline) {

	if (!$objets) return false;

	switch ($pipeline) {
		case "autoriser":
		case "declarer_tables_objets_sql":
		case "declarer_tables_interfaces":
			return (bool)fabrique_lister_tables($objets, 'objets');
			break;

		case "declarer_tables_auxiliaires":
			return (bool)fabrique_lister_tables($objets, 'liens');
			break;

		case "affiche_enfants":
			if (objets_option_presente($objets, 'vue_rubrique')) {
				return true;
			}
			break;

		case "affiche_milieu":
			if (objets_option_presente($objets, 'auteurs_liens')
			OR  objets_options_presentes($objets, array('table_liens', 'vue_liens'))) {
				return true;
			}
			break;

		case "affiche_auteurs_interventions":
			if (objets_option_presente($objets, 'vue_auteurs_liens')) {
				return true;
			}
			break;

		case "afficher_contenu_objet":
			return false;
			break;

		case "optimiser_base_disparus":
			# nettoie depuis spip_{objet}_liens
			# mais aussi les liaisions vers spip_{objet} (uniquement si une table de liens existe)
			return (bool)fabrique_lister_tables($objets, 'liens');
			#return (bool)fabrique_lister_tables($objets, 'objets');
			break;
	}
	return false;
}


/**
 * Crée le code PHP de création d'un tableau
 * 
 * Fonction un peu équivalente à var_export()
 * 
 * @param array $tableau
 *     Le tableau dont on veut obtenir le code de création array( ... )
 * @param bool $quote
 *     Appliquer sql_quote() sur chaque valeur (dans le code retourne)
 * @param string $defaut
 *     Si $tableau est vide ou n'est pas une chaîne, la fonction retourne cette valeur
 * @return string
 *     - Le code de création du tableau, avec éventuellement le code pour appliquer sql_quote.
 *     - $defaut si le tableau est vide
**/
function ecrire_tableau($tableau, $quote = false, $defaut = "array()") {
	// pas de tableau ?
	if (!is_array($tableau) OR !count($tableau)) {
		return $defaut;
	}

	$res = "array('" . implode("', '", array_map('addslashes', $tableau)) . "')";

	if ($quote) {
		$res = "array_map('sql_quote', $res)";
	}
	return $res;
}

/**
 * Crée le code PHP de création d'un tableau sauf s'il est vide
 * 
 * Identique à ecrire_tableau() mais ne retourne rien si le tableau est vide
 * @see ecrire_tableau()
 * 
 * @param array $tableau
 *     Le tableau dont on veut obtenir le code de création array( ... )
 * @param bool $quote
 *     Appliquer sql_quote() sur chaque valeur (dans le code retourne)
 * @return string
 *     - Le code de création du tableau, avec éventuellement le code pour appliquer sql_quote.
 *     - Chaîne vide si le tableau est vide
**/
function ecrire_tableau_sinon_rien($tableau, $quote = false) {
	return ecrire_tableau($tableau, $quote, "");
}

/**
 * Ajoute autant des espaces à la fin d'une chaîne jusqu'à la taille indiquée
 * 
 * Fonction un peu equivalente à str_pad() mais avec une valeur par défaut
 * définie par la constante _FABRIQUE_ESPACER
 *
 * @param string $texte
 *     Texte à compléter
 * @param int $taille
 *     Taille spécifique, utilisée à la place de la constante si renseignée
 * @return
 *     Texte complété des espaces de fin
 */
function espacer($texte, $taille = 0) {
	if (!$taille) $taille = _FABRIQUE_ESPACER;
	return str_pad($texte, $taille);
}


/**
 * Tabule à gauche chaque ligne du nombre de tabulations indiquées
 * + on enleve les espaces sur les lignes vides
 *
 * @param string $texte
 *     Un texte, qui peut avoir plusieurs lignes
 * @param int $nb_tabulations
 *     Nombre de tabulations à appliquer à gauche de chaque ligne
 * @return string
 *     Texte indenté du nombre de tabulations indiqué
 */
function fabrique_tabulations($texte, $nb_tabulations) {
	$tab = "";
	if ($nb_tabulations) {
		$tab = str_pad("\t", $nb_tabulations);
	}
	$texte = explode("\n", $texte);
	foreach ($texte as $c => $ligne) {
		$l = ltrim(ltrim($ligne), "\t");
		if (!$l) {
			$texte[$c] = "";
		} else {
			$texte[$c] = $tab . $ligne;
		}
	}
	return implode("\n", $texte);
}




/**
 * Passer en majuscule en utilisant mb de préférence
 * s'il est disponible. 
 *
 * @param string $str
 *     La chaine à passer en majuscule
 * @return string
 *     La chaine en majuscule
**/
function fabrique_mb_strtoupper($str) {
	if (function_exists('mb_strtoupper')) {
		return mb_strtoupper($str);
	} else {
		return strtoupper($str);
	}
}

/**
 * Passer en minuscule en utilisant mb de préférence
 * s'il est disponible. 
 *
 * @param string $str
 * 		La chaine à passer en minuscule
 * @return string
 * 		La chaine en minuscule
**/
function fabrique_mb_strtolower($str) {
	if (function_exists('mb_strtolower')) {
		return mb_strtolower($str);
	} else {
		return strtolower($str);
	}
}


/**
 * Crée une balise HTML <img> à partir d'un fichier,
 * réactualisée à chaque calcul, selon une réduction donnée.
 * 
 * Cela évite un |array_shift qui ne passe pas en PHP 5.4
 * 
 * Attention à bien rafraîchir l'image réduite lorsqu'on change de logo.
 *
 * @example
 *     #URL_IMAGE|fabrique_miniature_image{128}
 *
 *     Applique l'équivalent de :
 *     #URL_IMAGE|image_reduire{128}|extraire_attribut{src}
 *         |explode{?}|array_shift|timestamp|balise_img
 *
 * @param string $fichier
 *     Chemin du fichier
 * @param int $taille
 *     Taille de réduction de l'image
 * @return string
 *     Balise HTML IMG de l'image réduite et à jour
 */
function filtre_fabrique_miniature_image($fichier, $taille=256) {
	$im = filtrer('image_reduire', $fichier, $taille);
	$im = extraire_attribut($im, 'src');
	$im = explode('?', $im);
	$im = array_shift($im);
	$im = timestamp($im);
	$im = filtrer('balise_img', $im);
	return $im;
}



/**
 * Retourne un tableau table_sql=>Nom des objets de SPIP
 * complété des objets declares dans la fabrique ainsi
 * que de tables indiquees même si elles ne font pas parties
 * de declarations connues.
 *
 * @param array $objets_fabrique
 * 		Déclaration d'objets de la fabrique
 * @param array $inclus
 * 		Liste de tables SQL que l'on veut forcement presentes
 * 		meme si l'objet n'est pas declare
 * @param array $exclus
 * 		Liste de tables SQL que l'on veut forcement exclues
 * 		meme si l'objet n'est pas declare
 * @return array
 * 		Tableau table_sql => Nom
**/
function filtre_fabrique_lister_objets_editoriaux($objets_fabrique, $inclus=array(), $exclus=array()) {

	// les objets existants
	$objets = lister_tables_objets_sql();
	foreach ($objets as $sql => $o) {
		if ($o['editable']) {
			$liste[$sql] = _T($o['texte_objets']);
		}
	}
	unset($objets);

	// les objets de la fabrique
	foreach ($objets_fabrique as $o) {
		if (isset($o['table']) and !isset($liste[$o['table']])) {
			$liste[ $o['table'] ] = $o['nom'];
		}
	}

	// des objets qui n'existent pas mais qui sont actuellement coches dans la saisie
	foreach ($inclus as $sql) {
		if (!isset($liste[$sql])) {
			$liste[$sql] = $sql; // on ne connait pas le nom
		}
	}

	// tables forcement exclues
	foreach ($exclus as $sql) {
		unset($liste[$sql]);
	}
	// enlever un eventuel element vide
	unset($liste['']);

	asort($liste);

	return $liste;
}


/**
 * Retourne le code pour tester un type d'autorisation
 *
 * @param string $type
 * 		Quelle type d'autorisation est voulue
 * @return string
 * 		Code de test de l'autorisation
**/
function fabrique_code_autorisation($type) {
	switch($type) {

		case "jamais":
			return "false";
			break;

		case "toujours":
			return "true";
			break;

		case "redacteur":
			return "in_array(\$qui['statut'], array('0minirezo', '1comite'))";
			break;

		case "administrateur_restreint":
			return "\$qui['statut'] == '0minirezo'";
			break;

		case "administrateur":
			return "\$qui['statut'] == '0minirezo' AND !\$qui['restreint']";
			break;

		case "webmestre":
			return "autoriser('webmestre', '', '', \$qui)";
			break;

	}

	return "";
}

/**
 * Retourne la valeur de type d'autorisation
 * qui s'applique par defaut pour une autorisation donnee 
 *
 * @param string $autorisation
 * 		Nom de l'autorisation (objet et objets remplacent le veritable type et nom d'objet)
 * @return string
 * 		Type d'autorisation par defaut (jamais, toujours, redacteur, ...)
**/
function fabrique_autorisation_defaut($autorisation) {
	switch($autorisation) {
		case 'objet_voir':
			return "toujours";
			break;

		case 'objet_creer':
		case 'objet_modifier':
			return "redacteur";
			break;

		case 'objet_supprimer':
		case 'associerobjet':
			return "administrateur";
			break;
	}
}

/**
 * Retourne le code d'autorisation indique
 * sinon celui par defaut pour une fonction d'autorisation
 *
 * @param array $autorisations
 * 		Les autorisations renseignees par l'interface pour un objet
 * @param string $autorisation
 * 		Le nom de l'autorisation souhaitee
 * @return string
 * 		Code de l'autorisation
**/
function fabrique_code_autorisation_defaut($autorisations, $autorisation) {
	if (!$autorisation) return "";

	// trouver le type d'autorisation souhaitee, soit indiquee, soit par defaut
	if (!isset($autorisations[$autorisation]) OR !$type = $autorisations[$autorisation]) {
		$type = fabrique_autorisation_defaut($autorisation);
	}

	// retourner le code PHP correspondant
	return fabrique_code_autorisation($type);
}

/**
 * Retourne le type pour le nom d'une fonction d'autorisation 
 * 'article' => 'article'
 * 'truc_muche' => 'trucmuche'
 * 
 * @param string $type
 * 		Type ou objet
 * @return string
 * 		Type pour le nom d'autorisation
**/
function fabrique_type_autorisation($type) {
	return str_replace('_', '', $type);
}

?>
