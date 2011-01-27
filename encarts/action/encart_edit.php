<?php
/*
 * Plugin Encarts
 * (c) 2011 Camille Lafitte, Cyril Marion
 * Avec l'aide de Matthieu Marcillaud
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_encart_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas d'id ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_encart = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_encart = insert_encart();
	}

	if ($id_encart) $err = revisions_encarts($id_encart);
	return array($id_encart,$err);
}


function insert_encart() {
	$champs = array(
		'titre' => _T('item_nouveau_titre')
	);
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_encarts',
		),
		'data' => $champs
	));
	
	$id_encart = sql_insertq("spip_encarts", $champs);
	// Attention il faut aussi ici insérer le lien avec l'article
	// sql_insertq("spip_encarts_liens", ...);
	
	return $id_encart;
}


// Enregistrer certaines modifications d'un encart
function revisions_encarts($id_encart, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array(
				'id_article',
				'titre', 'texte', 'date') as $champ
		) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}

	include_spip('inc/modifier');
	modifier_contenu('encart', $id_encart, array(
			'invalideur' => "id='id_encart/$id_encart'"
		),
		$c);


	$champs = array();
	
	if ($champs) {
		sql_updateq('spip_encarts', $champs, "id_encart=$id_encart");
		// faire un truc pour spip_enarts_liens ?
	}

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_encart/$id_encart'");
	
}
?>