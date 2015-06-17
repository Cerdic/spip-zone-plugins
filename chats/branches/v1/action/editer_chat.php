<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_editer_chat_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// pas de chat ? on en cree un nouveau, mais seulement si 'oui' en argument.
	if (!$id_chat = intval($arg)) {
		if ($arg != 'oui') {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_chat = insert_chat();
	}

	if ($id_chat) $err = revisions_chats($id_chat);
	return array($id_chat,$err);
}


function insert_chat() {
	$champs = array(
		'nom' => _T('chats:item_nouveau_chat')
	);
	
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion', array(
		'args' => array(
			'table' => 'spip_chats',
		),
		'data' => $champs
	));
	
	$id_chat = sql_insertq("spip_chats", $champs);
	return $id_chat;
}


// Enregistrer certaines modifications d'un chat
function revisions_chats($id_chat, $c=false) {

	// recuperer les champs dans POST s'ils ne sont pas transmis
	if ($c === false) {
		$c = array();
		foreach (array('nom', 'race', 'robe', 'annee_naissance', 'infos') as $champ) {
			if (($a = _request($champ)) !== null) {
				$c[$champ] = $a;
			}
		}
	}
	
	include_spip('inc/modifier');
	modifier_contenu('chat', $id_chat, array(
			'nonvide' => array('nom' => _T('info_sans_titre')),
			'invalideur' => "id='id_chat/$id_chat'"
		),
		$c);
}