<?php
/**
 * Fonctions utiles au plugin Mots arborescents
 *
 * @plugin     Mots arborescents
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Motsar\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



/**
 * Ajoute un espace (ou de quoi faire un espace en css) en fonction d'une profondeur donnée
 *
 * @param int $profondeur
 * @return string Code HTML
**/
function mostar_tabulation($profondeur) {
	if ($profondeur) {
		return "<span class='profondeur_mot'>" . str_repeat(' ◆ &nbsp; ', $profondeur) . "</span>";
	}
	return '';
}


/**
 * Gère des héritages sur les mots
 * 
 * Sur les mots
 * - définir les mots racines
 * - définir les mots hérités (de la racine)
 *
 * Sur les groupes dont la configuration (champ mots_arborescents) indique :
 * - oui : calcule les héritages des mots
 * - '' ou autre : enlève les héritages de mots (remet tous les mots à plat !)
 *
 * @example
 *     motsar_definir_heritages();  // recalcule tout
 *     motsar_definir_heritages(3); // recalcule les mots (et ses enfants) du groupe 3 
 * 
 * @param int $id_groupe
 *     Identifiant du groupe à modifier
 *     C'est le seul paramètre éventuellement a passer
 * @return void
**/
function motsar_definir_heritages($id_groupe = null) {

	// plugin GMA actif ? (tenter au minimum un semblant de compatibilité !)
	include_spip('inc/filtres');
	$info_plugin = chercher_filtre('info_plugin');
	$gma = $info_plugin('gma', 'est_actif');

	// pas de groupe spécifique ?
	if (is_null($id_groupe)) {
		// on applique notre fonction à tous les groupes
		$where = array();
		if ($gma) {
			$where[] = 'id_parent=' . sql_quote(0);
		}
		$groupes = sql_allfetsel('id_groupe', 'spip_groupes_mots', $where);
		$groupes = array_map('array_shift', $groupes);
		foreach ($groupes as $id_groupe) {
			motsar_definir_heritages($id_groupe);
		}
		return true;
	}

	// pas de vide par erreur
	if (!$id_groupe = intval($id_groupe)) {
		return false;
	}

	$groupe = sql_fetsel(array('id_groupe', 'titre', 'mots_arborescents'), 'spip_groupes_mots', 'id_groupe=' . $id_groupe);
	if (!$groupe) {
		return false;
	}


	// le groupe n'est pas arborescent ?
	// mettre à plat tous les mots du groupe
	if ($groupe['mots_arborescents'] !== 'oui') {
		sql_update('spip_mots', array(
			'type' => sql_quote($groupe['titre']),
			'id_parent' => 0,
			'id_mot_racine' => 'id_mot',
			'profondeur' => 0,
		), 'id_groupe=' . $id_groupe);
		return true;
	}

	// sinon, pour chaque mot racine, définir ses héritages
	$mots_racines = sql_allfetsel('id_mot', 'spip_mots', array(
		'id_parent=' . 0,
		'id_groupe=' . $id_groupe
	));
	$mots_racines = array_map('array_shift', $mots_racines);

	// un groupe, mais aucun mot
	if (!$mots_racines) {
		// on suppose que c'est bien rangé, et qu'il n'y a pas de mot
		// dans le groupe. Donc, aucun mot présent avec id_parent<>0 (incohérence)
		return true;
	}

	// les mots racines héritent au moins du titre du groupe
	sql_update('spip_mots', array(
		'type' => sql_quote($groupe['titre']),
		'profondeur' => 0,
		'id_mot_racine' => 'id_mot',
	), sql_in('id_mot', $mots_racines));

	foreach ($mots_racines as $id_mot) {
		motsar_definir_heritages_mot($id_mot);
	}
	return true;
}


/**
 * Affecte les champs hérités automatiquement aux mots enfants d'un mot donné.
 *
 * Certains champs sont automatiquement définis comme étant identiques
 * pour toute une branche de mot. C'est le cas de id_groupe, type, id_mot_racine,
 * et éventuellement de certains champs ajoutés par le pipeline mots_arborescents_heritages.
 *
 * @pipeline_appel mots_arborescents_heritages
 *
 * @param int $id_mot
 * @param null|array $heritages
 *     Champs à hériter aux enfants
 * @return bool false si mot invalide, true sinon.
**/
function motsar_definir_heritages_mot($id_mot, $heritages = null) {
	// liste des champs herites
	static $champs_herites = null;
	static $champs = array('id_parent');

	if (!$id_mot = intval($id_mot)) {
		return false;
	}

	if (is_null($champs_herites)) {
		// liste des champs qui doivent hériter du mot racine automatiquement
		$champs_herites = pipeline('mots_arborescents_heritages', array('id_groupe', 'type', 'id_mot_racine'));
		// ajout des héritages à la liste des champs
		$champs = array_merge($champs, $champs_herites);
	}

	// pour le mot en cours, on retrouve les valeurs des champs à hériter
	if (is_null($heritages)) {
		$heritages = sql_fetsel($champs, 'spip_mots', 'id_mot=' . $id_mot);
		if (!$heritages) {
			return false;
		}
		if ($heritages['id_parent']) {
			$heritages = sql_fetsel($champs, 'spip_mots', 'id_mot=' . sql_quote($heritages['id_parent']));
		}
		// le reste est la config a faire hériter
		unset($heritages['id_parent']);
	}

	// a ce stade, on a les infos des champs à hériter
	// pour chaque groupe enfant, on les transmet
	$mots = calcul_branche_mot_in($id_mot);
	sql_updateq('spip_mots', $heritages, sql_in('id_mot', $mots));
	return true;
}





/**
 * Recalcule les id_mot_racine et les profondeurs des mots
 *
 * Cherche les mots ayant des id_mot_racine ou profondeurs ne correspondant pas
 * avec leur parent, et les met à jour.
 * On procede en iterant la profondeur de 1 en 1 pour ne pas risquer une boucle infinie sur reference circulaire
 * 
 * @return void
**/
function propager_les_mots_arborescents()
{
	// Profondeur 0
	// Tous les mots racines sont de profondeur 0
	// et fixer les id_mot_racine des mots racines
	sql_update('spip_mots', array('id_mot_racine'=>'id_mot','profondeur'=>0), "id_parent=0");
	// Tout mot non racine est de profondeur >0
	sql_updateq('spip_mots', array('profondeur'=>1), "id_parent<>0 AND profondeur=0");

	// securite : pas plus d'iteration que de mots dans la base
	$maxiter = sql_countsel("spip_mots");

	// reparer les mots qui n'ont pas l'id_mot_racine de leur parent
	// on fait profondeur par profondeur

	$prof = 0;
	do {
		$continuer = false;

		// Par recursivite : si tous les mots de profondeur $prof sont bons
		// on fixe le profondeur $prof+1

		// Tous les mots dont le parent est de profondeur $prof ont une profondeur $prof+1
		// on teste A.profondeur > $prof+1 car :
		// - tous les mots de profondeur 0 à $prof sont bons
		// - si A.profondeur = $prof+1 c'est bon
		// - cela nous protege de la boucle infinie en cas de reference circulaire dans les mots
		$maxiter2 = $maxiter;
		while ($maxiter2--
			AND $rows = sql_allfetsel(
			"A.id_mot AS id, R.id_mot_racine AS id_mot_racine, R.profondeur+1 as profondeur",
			"spip_mots AS A JOIN spip_mots AS R ON A.id_parent = R.id_mot",
			"R.profondeur=".intval($prof)." AND (A.id_mot_racine <> R.id_mot_racine OR A.profondeur > R.profondeur+1)",
		  "","R.id_mot_racine","0,100")){

			$id_mot_racine = null;
			$ids = array();
			while ($row = array_shift($rows)) {
				if ($row['id_mot_racine']!==$id_mot_racine){
					if (count($ids))
						sql_updateq("spip_mots", array("id_mot_racine" => $id_mot_racine, 'profondeur' => $prof+1), sql_in('id_mot',$ids));
					$id_mot_racine = $row['id_mot_racine'];
					$ids = array();
				}
				$ids[] = $row['id'];
			}
			if (count($ids))
				sql_updateq("spip_mots", array("id_mot_racine" => $id_mot_racine, 'profondeur' => $prof+1), sql_in('id_mot',$ids));
		}


		// Tous les mots de profondeur $prof+1 qui n'ont pas un parent de profondeur $prof sont decalees
		$maxiter2 = $maxiter;
		while ($maxiter2--
			AND $rows = sql_allfetsel(
			"id_mot as id",
			"spip_mots",
			"profondeur=".intval($prof+1)." AND id_parent NOT IN (".sql_get_select("zzz.id_mot","spip_mots AS zzz","zzz.profondeur=".intval($prof)).")",'','','0,100')){
			$rows = array_map('reset',$rows);
			sql_updateq("spip_mots", array('profondeur' => $prof+2), sql_in("id_mot",$rows));
		}

		// ici on a fini de valider $prof+1, tous les mots de prondeur 0 a $prof+1 sont OK
		// si pas de mot a profondeur $prof+1 pas la peine de continuer
		// si il reste des mots non vus, c'est une branche morte ou reference circulaire (base foireuse)
		// on arrete les frais
		if (sql_countsel("spip_mots", "profondeur=" . intval($prof+1))){
			$prof++;
			$continuer = true;
		}
	}
	while ($continuer AND $maxiter--);

	// loger si la table des mots semble foireuse
	// et mettre un id_mot_racine=0 sur ces mots pour eviter toute selection par les boucles
	if (sql_countsel("spip_mots","profondeur>".intval($prof+1))){
		spip_log("Les mots de profondeur>".($prof+1)." semblent suspects (branches morte ou reference circulaire dans les parents)", _LOG_CRITIQUE);
		sql_update("spip_mots", array('id_mot_racine'=>0), "profondeur>".intval($prof+1));
	}
}




/**
 * Calcul d'une branche de mots
 * 
 * Liste des id_mot contenus dans un mot donné
 * pour le critere {branche_mot}
 *
 * @internal
 *     Fonction quasiment identique a inc_calcul_branche_in_dist() du core
 * 
 * @param string|int|array $id
 *     Identifiant du mot dont on veut récuperer toute la branche
 * @return string
 *     Liste des ids, séparés par des virgules
 */
function calcul_branche_mot_in($id) {
	static $b = array();

	// normaliser $id qui a pu arriver comme un array, comme un entier, ou comme une chaine NN,NN,NN
	if (!is_array($id)) $id = explode(',',$id);
	$id = join(',', array_map('intval', $id));
	if (isset($b[$id]))
		return $b[$id];

	// Notre branche commence par le mot de depart
	$branche = $r = $id;

	// On ajoute une generation (les filles de la generation precedente)
	// jusqu'a epuisement
	while ($filles = sql_allfetsel(
					'id_mot',
					'spip_mots',
					sql_in('id_parent', $r)." AND ". sql_in('id_mot', $r, 'NOT')
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
 * Sélectionne dans une boucle les éléments appartenant à une branche d'un mot
 * 
 * Calcule une branche d'un mot et conditionne la boucle avec.
 * Cherche l'identifiant du mot en premier paramètre du critère {branche_mot XX}
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
 * 		restreignant celle ci aux mots de la branche
**/
function critere_branche_mot_dist($idb, &$boucles, $crit){

	$not = $crit->not;
	$boucle = &$boucles[$idb];
	// prendre en priorite un identifiant en parametre {branche_mot XX}
	if (isset($crit->param[0])) {
		$arg = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	}
	// sinon on le prend chez une boucle parente
	else {
		$arg = kwote(calculer_argument_precedent($idb, 'id_mot', $boucles));
	}

	// Trouver une jointure
	$champ = "id_mot";
	$desc = $boucle->show;
	// Seulement si necessaire
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

	$c = "sql_in('$cle".".$champ', calcul_branche_mot_in($arg)"
	     .($not ? ", 'NOT'" : '').")";
	$boucle->where[] = !$crit->cond ? $c :
		("($arg ? $c : ".($not ? "'0=1'" : "'1=1'").')');
}



/**
 * Boucle HIERARCHIE_MOTS
**/
function boucle_HIERARCHIE_MOTS_dist($id_boucle, &$boucles) {
	return boucle_HIERARCHIE_PARENT_dist($id_boucle, $boucles, 'id_mot', 'spip_mots');
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
