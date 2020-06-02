<?php
/**
 * Analyse des textes pour trouver et marquer comme vu les selections utilisés dedans
 *
 * @package SPIP\SelectionsEditoriales\Fonctions
 **/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// la dist ne regarde que chapo et texte, on laisse comme ca,
// mais ca permet d etendre a descriptif ou toto depuis d autres plugins
$GLOBALS['selections_liste_champs'][] = 'texte';
$GLOBALS['selections_liste_champs'][] = 'chapo';

/**
 * Trouver les selections utilisées dans le texte d'un objet et enregistrer cette liaison comme vue.
 *
 * La liste des champs susceptibles de contenir des selections indiquée
 * par la globale `selections_liste_champs` (un tableau).
 *
 * Le contenu de ces champs (du moins ceux qui existent pour l'objet demandé) est récupéré et analysé.
 * La présence d'un modèle de selection dans ces contenus, tel que selection_editoXX
 * indique que la selection est utilisée et doit être liée à l'objet, avec le champ `vu=oui`
 *
 * S'il y avait des anciens liens avec vu=oui qui n'ont plus lieu d'être, ils passent à non.
 *
 * @note
 *     La fonction pourrait avoir bien moins d'arguments : seuls $champs, $id, $type ou $objet, $desc, $serveur
 *     sont nécessaires. On calcule $desc s'il est absent, et il contient toutes les infos…
 *
 * @param array $champs
 *     Couples [champ => valeur] connus de l'objet
 * @param int $id
 *     Identifiant de l'objet
 * @param string $type
 *     Type d'objet éditorial (ex: article)
 * @param string $id_table_objet
 *     Nom de la clé primaire sur la table sql de l'objet
 * @param string $table_objet
 *     Nom de l'objet éditorial (ex: articles)
 * @param string $spip_table_objet
 *     Nom de la table sql de l'objet
 * @param array $desc
 *     Description de l'objet, si déjà calculé
 * @param string $serveur
 *     Serveur sql utilisé.
 * @return void|null
 **/
function inc_marquer_doublons_selection_dist(
	$champs,
	$id,
	$type,
	$id_table_objet,
	$table_objet,
	$spip_table_objet,
	$desc = array(),
	$serveur = ''
) {
	if (!$champs) {
		return;
	}

	// On conserve uniquement les champs qui modifient le calcul des doublons de selections
	// S'il n'y en a aucun, les doublons ne sont pas impactés, donc rien à faire d'autre..
	if (!$champs = array_intersect_key($champs, array_flip($GLOBALS['selections_liste_champs']))) {
		return;
	}

	if (!$desc) {
		$trouver_table = charger_fonction('trouver_table', 'base');
		$desc = $trouver_table($table_objet, $serveur);
	}

	// Il faut récupérer toutes les données qui impactent les liens de selections vus
	// afin de savoir lesquels sont présents dans les textes, et pouvoir actualiser avec
	// les liens actuellement enregistrés.
	$absents = array();

	// Récupérer chaque champ impactant qui existe dans la table de l'objet et qui nous manque
	foreach ($GLOBALS['selections_liste_champs'] as $champ) {
		if (isset($desc['field'][$champ]) and !isset($champs[$champ])) {
			$absents[] = $champ;
		}
	}

	// Retrouver les textes des champs manquants
	if ($absents) {
		$row = sql_fetsel($absents, $spip_table_objet, "$id_table_objet=" . sql_quote($id));
		if ($row) {
			$champs = array_merge($row, $champs);
		}
	}

	include_spip('inc/texte');
	include_spip('base/abstract_sql');
	include_spip('action/editer_liens');
	include_spip('base/objets');

	// récupérer la liste des modèles qui considèrent une selection comme vu s'ils sont utilisés dans un texte
	$modeles = lister_tables_objets_sql('spip_selections');
	$modeles = $modeles['modeles'];

	// liste d'id_selections trouvés dans les textes
	$GLOBALS['doublons_selections_inclus'] = array();

	// detecter les doublons dans ces textes
	traiter_modeles(implode(' ', $champs), array('selections' => $modeles), '', '', null, array(
		'objet' => $type,
		'id_objet' => $id,
		$id_table_objet => $id
	));

	$texte_selections_vus = $GLOBALS['doublons_selections_inclus'];

	// on ne modifie les liaisons que si c'est nécessaire
	$bdd_selections_vus = array(
		'oui' => array(),
		'non' => array()
	);

	$liaisons = objet_trouver_liens(array('selection' => '*'), array($type => $id));
	foreach ($liaisons as $l) {
		$bdd_selections_vus[$l['vu']][] = $l['id_selection'];
	}

	// il y a des nouvelles selections vus dans le texte
	$nouveaux = array_diff($texte_selections_vus, $bdd_selections_vus['oui']);
	// il y a des anciennes selections vus dans la bdd
	$anciens = array_diff($bdd_selections_vus['oui'], $texte_selections_vus);

	if ($nouveaux) {
		// on vérifie que les selections indiqués vus existent réellement tout de même (en cas d'erreur de saisie)
		$ids = sql_allfetsel('id_selection', 'spip_selections', sql_in('id_selection', $nouveaux));
		$ids = array_map('reset', $ids);
		if ($ids) {
			// Creer le lien s'il n'existe pas déjà
			objet_associer(array('selection' => $ids), array($type => $id), array('vu' => 'oui'));
			objet_qualifier_liens(array('selection' => $ids), array($type => $id), array('vu' => 'oui'));
		}
	}

	if ($anciens) {
		objet_qualifier_liens(array('selection' => $anciens), array($type => $id), array('vu' => 'non'));
	}
}
