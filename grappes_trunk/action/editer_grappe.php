<?php

/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

// Modifier le reglage des forums publics de l'article x
// http://doc.spip.org/@action_editer_groupe_mot_dist
function action_editer_grappe_dist()
{

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_grappe = intval($securiser_action());

	if (!$id_grappe) {
		$id_grappe = sql_insertq("spip_grappes");
	}

	// modifier le contenu via l'API
	include_spip('inc/modifier');

	$c = $opt = array();
	foreach (array(
		'titre', 'descriptif', 'liaisons','type'
	) as $champ)
		$c[$champ] = _request($champ);

	foreach (array(
		'acces'
	) as $champ)
		$opt[$champ] = _request($champ);

	$c['options'] = serialize($opt);
	$c['id_admin'] = $GLOBALS['visiteur_session']['id_auteur'];

	if (is_array($c['liaisons']))
		$c['liaisons'] = implode(',',$c['liaisons']);

	revision_grappe($id_grappe, $c);
	if ($redirect = _request('redirect')) {
		include_spip('inc/headers');
		redirige_par_entete(parametre_url(urldecode($redirect),
			'id_grappe', $id_grappe, '&'));
	} else
		return array($id_grappe,'');
}

?>
