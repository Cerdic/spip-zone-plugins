<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Gestion des puces d'action rapide
 *
 * @package SPIP\Dictionnaire\Puce_statut
**/

include_spip('inc/presentation');

/**
 * Gestion de l'affichage ajax des puces d'action rapide des définitions
 *
 * Récupère l'identifiant id et le type d'objet dans les données postées
 * et appelle la fonction de traitement de cet exec.
 * 
 * @see exec_puce_statut_formulaires_args()
 * @return string Code HTML
**/
function exec_puce_statut_definitions_dist()
{
	exec_puce_statut_definitions_args(_request('id'),  _request('type'));
}

/**
 * Traitement de l'affichage ajax des puces d'action rapide des définitions
 *
 * Appelle la fonction de traitement des puces statuts
 * après avoir retrouvé le statut en cours de l'objet
 * et son parent
 * 
 * @param int $id
 *     Identifiant de l'objet
 * @param string $type
 *     Type d'objet
 * @return string Code HTML
**/
function exec_puce_statut_definitions_args($id, $type)
{
	if ($table_objet_sql = table_objet_sql($type)
		AND $d = lister_tables_objets_sql($table_objet_sql)
		AND isset($d['statut_textes_instituer'])
		AND $d['statut_textes_instituer'])
	{
		$prim = id_table_objet($type);
		$id = intval($id);
		$r = sql_fetsel("id_dictionnaire,statut", $table_objet_sql, "$prim=$id");
		$statut = $r['statut'];
		$id_parent = $r['id_dictionnaire'];
	}
	else {
		$id_parent = intval($id);
		$statut = 'prop'; // arbitraire
	}
	$puce_statut = charger_fonction('puce_statut', 'inc');
	ajax_retour($puce_statut($id,$statut,$id_parent,$type, true));
}

?>
