<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function action_editer_lien_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	// pas de lien ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_blink = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_blink = insert_lien();
	}
	if ($id_blink) $err = revisions_liens($id_blink);
	return array($id_blink,$err);
}
function insert_lien() {
	$champs = array(
		'nom' => _T('blinks:item_nouveau_lien')
	);
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_blinks',
		),
		'data' => $champs
	));
	
	$id_blink = sql_insertq("spip_blinks", $champs);
	return $id_blink;
}
// Enregistrer certaines modifications d'un lien
function revisions_liens($id_blink, $c=false) {
	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array('identifiant_blink', 'url_blink', 'keywords_blink') as $champ) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}
	
	include_spip('inc/modifier');
	modifier_contenu('lien', $id_blink, array(
			'nonvide' => array('identifiant_blink' => _T('info_sans_titre')),
			'invalideur' => "id='id_blink/$id_blink'"
		),
		$c);
}
?>