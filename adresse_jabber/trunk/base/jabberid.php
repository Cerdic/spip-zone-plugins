<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function jabberid_declarer_champs_extras($champs = array()){
	$champs['spip_auteurs']['jid'] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'jid',
			'label' => _T('jabberid:adresse_jabber'),
			'precisions' => _T('jabberid:adresse_jabber_precisions'),
			'sql' => "text NOT NULL DEFAULT ''",
		),
		'verifier' => array(),
	);
	return $champs;
}
?>
