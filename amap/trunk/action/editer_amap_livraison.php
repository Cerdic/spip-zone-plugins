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

function action_editer_amap_livraison_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas de amap_livraison ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_amap_livraison = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_amap_livraison = insert_amap_livraison();
	}

	if ($id_amap_livraison) $err = revision_amap_livraison($id_amap_livraison);
	return array($id_amap_livraison,$err);
}


function insert_amap_livraison() {
	$champs = array();

	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_amap_livraisons',
		),
		'data' => $champs
	));

	$id_amap_livraison = sql_insertq("spip_amap_livraisons", $champs);
	return $id_amap_livraison;
}


// Enregistrer certaines modifications d'un amap_livraison
function revision_amap_livraison($id_amap_livraison, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array('date_livraison', 'contenu_panier') as $champ) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}

	include_spip('inc/modifier');
	modifier_contenu('amap_livraison', $id_amap_livraison, array(
			'invalideur' => "id='id_amap_livraison/$id_amap_livraison'"
		),
		$c);
}
?>
