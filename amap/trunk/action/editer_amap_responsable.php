<?php
/**
* @plugin	Amap
* @author	Stephane Moulinet
* @author	E-cosystems
* @author	Pierre KUHN 
* @copyright 2010-2013
* @licence	GNU/GPL
*
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_amap_responsable_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas de amap_responsable ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_amap_responsable = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_amap_responsable = insert_amap_responsable();
	}

	if ($id_amap_responsable) $err = revision_amap_responsable($id_amap_responsable);
	return array($id_amap_responsable,$err);
}


function insert_amap_responsable() {
	$champs = array();

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_amap_responsables',
		),
		'data' => $champs
	));

	$id_amap_responsable = sql_insertq("spip_amap_responsables", $champs);
	return $id_amap_responsable;
}


// Enregistrer certaines modifications d'un amap_responsable
function revision_amap_responsable($id_amap_responsable, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array('date_distribution', 'id_auteur') as $champ) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}

	include_spip('inc/modifier');
	modifier_contenu('amap_responsable', $id_amap_responsable, array(
			'invalideur' => "id='id_amap_responsable/$id_amap_responsable'"
		),
		$c);
}
?>
