<?php

/**
 * Plugin Groupes arborescents de mots clés
 * (c) 2012 Marcillaud Matthieu
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Gère des héritages sur les groupes et mots
 * 
 * Sur les tables groupes
 * - définir les groupes racines
 * - définir les configs héritées (de la racine)
 * Sur les mots
 * - définir les groupes racines
 *
 * @example
 * 		gma_definir_heritages();  // recalcule tout
 * 		gma_definir_heritages(3); // recalcule le groupe 3 et ses enfants
 * 
 * @param int $id_groupe
 * 		Identifiant du groupe à modifier
 * 		C'est le seul paramètre éventuellement a passer
 * @param null|array $heritage
 * 		Config a heriter aux enfants
 * @param bool $update_mots
 * 		Met a jour les mots au passage (id_groupe_racine)
 * @return void
**/
function gma_definir_heritages($id_groupe = null, $heritage=null, $update_mots=true) {
	// liste des champs de config
	static $champs_herites = null;
	static $champs = array('id_groupe', 'id_parent', 'id_groupe_racine');

	if (is_null($champs_herites)) {
		// liste des champs qui doivent hériter du groupe racine automatiquement
		$champs_herites = pipeline('groupes_mots_arborescents_heritages', array(
			'unseul', 'obligatoire', 'comite', 'forum', 'minirezo', 'tables_liees'
		));
		// ajout des héritages à la liste des champs
		$champs += $champs_herites;
	}

	// pas de groupe spécifique ?
	if (is_null($id_groupe)) {
		// on applique notre fonction à tous les groupes racines
		$groupes = sql_allfetsel($champs, 'spip_groupes_mots', 'id_parent=' . sql_quote(0));
		foreach ($groupes as $groupe) {
			$id_groupe = $groupe['id_groupe'];
			// le reste est la config a faire hériter
			unset($groupe['id_groupe'], $groupe['id_parent']);
			$groupe['id_groupe_racine'] = $id_groupe;
			gma_definir_heritages($id_groupe, $groupe);
		}
		return true;
	}

	// pas de vide par erreur
	if (!$id_groupe) {
		return false;
	}

	// pour le groupe en cours
	// on retrouve la racine et la config s'ils ne sont pas connus,
	// si possible dans le groupe parent (s'il existe)
	if (is_null($heritage)) {
		$heritage = sql_fetsel($champs, 'spip_groupes_mots', 'id_groupe=' . sql_quote($id_groupe));
		if (!$heritage) {
			return false;
		}
		if ($heritage['id_parent']) {
			$heritage = sql_fetsel($champs, 'spip_groupes_mots', 'id_groupe=' . sql_quote($heritage['id_parent']));
		}
		// le reste est la config a faire hériter
		unset($heritage['id_groupe'], $heritage['id_parent']);
	}

	// a ce stade, on a la racine et la config
	// pour chaque groupe enfant, on les transmets
	$groupes = calcul_branche_groupe_in($id_groupe);
	sql_updateq('spip_groupes_mots', $heritage, sql_in('id_groupe', $groupes));

	// pour chaque mots du groupe, on definit la racine
	if ($update_mots) {
		sql_updateq('spip_mots',
			array('id_groupe_racine' => $heritage['id_groupe_racine']),
			sql_in('id_groupe', $groupes));
	}

}




/**
 * Calcul d'une branche de groupes de mots
 * 
 * Liste des id_groupes contenus dans un groupe de mots donné
 * pour le critere {branche_groupe}
 *
 * @internal
 *     Fonction quasiment identique a inc_calcul_branche_in_dist() du core
 * 
 * @param string|int|array $id
 *     Identifiant du groupe dont on veut récuperer toute la branche
 * @return string
 *     Liste des ids, séparés par des virgules
 */
function calcul_branche_groupe_in($id) {
	static $b = array();

	// normaliser $id qui a pu arriver comme un array, comme un entier, ou comme une chaine NN,NN,NN
	if (!is_array($id)) $id = explode(',',$id);
	$id = join(',', array_map('intval', $id));
	if (isset($b[$id]))
		return $b[$id];

	// Notre branche commence par la rubrique de depart
	$branche = $r = $id;

	// On ajoute une generation (les filles de la generation precedente)
	// jusqu'a epuisement
	while ($filles = sql_allfetsel(
					'id_groupe',
					'spip_groupes_mots',
					sql_in('id_parent', $r)." AND ". sql_in('id_groupe', $r, 'NOT')
					)) {
		$r = join(',', array_map('array_shift', $filles));
		$branche .= ',' . $r;
	}

	# securite pour ne pas plomber la conso memoire sur les sites prolifiques
	if (strlen($branche)<10000)
		$b[$id] = $branche;
	return $branche;
}



/**
 * Sélectionne dans une boucle les éléments appartenant à une branche d'un groupe de mot
 * 
 * Calcule une branche d'un groupe de mots et conditionne la boucle avec.
 * Cherche l'identifiant du groupe en premier paramètre du critère {branche_groupe XX}
 * sinon dans les boucles parentes ou par jointure.
 *
 * @internal
 * 		Copie quasi identique de critere_branche_dist()
 * 
 * @param string $idb
 * 		Identifiant de la boucle
 * @param array $boucles
 * 		AST du squelette
 * @param Critere $crit
 * 		Paramètres du critère dans cette boucle
 * @return
 * 		AST complété de la condition where au niveau de la boucle,
 * 		restreignant celle ci aux groupes de la branche
**/
function critere_branche_groupe_dist($idb, &$boucles, $crit){

	$not = $crit->not;
	$boucle = &$boucles[$idb];
	// prendre en priorite un identifiant en parametre {branche_groupe XX}
	if (isset($crit->param[0])) {
		$arg = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	}
	// sinon on le prend chez une boucle parente
	else {
		$arg = kwote(calculer_argument_precedent($idb, 'id_groupe', $boucles));
	}
	
	//Trouver une jointure
	$champ = "id_groupe";
	$desc = $boucle->show;
	//Seulement si necessaire
	if (!array_key_exists($champ, $desc['field'])){
		$cle = trouver_jointure_champ($champ, $boucle);
		$trouver_table = charger_fonction("trouver_table", "base");
		$desc = $trouver_table($boucle->from[$cle]);
		if (count(trouver_champs_decomposes($champ, $desc))>1){
			$decompose = decompose_champ_id_objet($champ);
			$champ = array_shift($decompose);
			$boucle->where[] = array("'='", _q($cle.".".reset($decompose)), '"'.sql_quote(end($decompose)).'"');
		}
	}
	else $cle = $boucle->id_table;

	$c = "sql_in('$cle".".$champ', calcul_branche_groupe_in($arg)"
	     .($not ? ", 'NOT'" : '').")";
	$boucle->where[] = !$crit->cond ? $c :
		("($arg ? $c : ".($not ? "'0=1'" : "'1=1'").')');
}



/**
 * Boucle HIERARCHIE_GROUPES_MOTS
**/
function boucle_HIERARCHIE_GROUPES_MOTS_dist($id_boucle, &$boucles) {
	return boucle_HIERARCHIE_PARENT_dist($id_boucle, $boucles, 'id_groupe', 'spip_groupes_mots');
}



if (!function_exists('boucle_HIERARCHIE_PARENT_dist')) {
/**
 * Boucle HIERARCHIE mais
 * qui n'est pas dependante d'un id_rubrique
 * 
**/
function boucle_HIERARCHIE_PARENT_dist($id_boucle, &$boucles, $primary, $table) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table . "." . $primary;

	// Si la boucle mere est une boucle de $TABLE il faut ignorer la feuille
	// sauf en presence du critere {tout} (vu par phraser_html)
	// ou {id_article} qui positionne aussi le {tout}

	$boucle->hierarchie = 'if (!($id_objet = intval('
	. calculer_argument_precedent($boucle->id_boucle, $primary, $boucles)
	. ")))\n\t\treturn '';\n\t"
	. '$hierarchie = '
	. (isset($boucle->modificateur['tout']) ? '",$id_objet"' : "''")
	. ";\n\t"
	. 'while ($id_objet = sql_getfetsel("id_parent","' . $table . '","' . $primary . '=" . $id_objet,"","","", "", $connect)) { 
		$hierarchie = ",$id_objet$hierarchie";
	}
	if (!$hierarchie) return "";
	$hierarchie = substr($hierarchie,1);';

	// On enlève l'ancien critère "id_truc" du where
	// provenant de <BOUCLE_h(HIERARCHIE_TRUC){id_truc} />
	foreach ($boucle->where as $cle=>$where) {
		if (count($where) == 3
			and ($where[0] == "'='" or $where[0] == '"="')
			and ($where[1] == "'$id_table'" or $where[1] == '"'.$id_table.'"')){
				unset($boucle->where[$cle]);
				$boucle->where = array_values($boucle->where); // recalculer les cles du tableau
		}
	}

	$boucle->where[] = array("'IN'", "'$id_table'", '"($hierarchie)"');

	$order = "FIELD($id_table, \$hierarchie)";

	if (!isset($boucle->default_order[0]) OR $boucle->default_order[0] != " DESC")
		$boucle->default_order[] = "\"$order\"";
	else
		$boucle->default_order[0] = "\"$order DESC\"";
	return calculer_boucle($id_boucle, $boucles); 
}
}
