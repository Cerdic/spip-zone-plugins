<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

if ($code_invitation = _request('invitation')){
	include_spip('base/abstract_sql');
	sql_updateq(
		'spip_filleuls',
		array(
			'statut' => 'visite',
		),
		array(
			'code_invitation = '.sql_quote($code_invitation),
			sql_in('statut', array('invite', 'sans_nouvelles'))
		)
	);
}

function parrainage_importer_contacts($contacts){
	$ajouter_filleul = charger_fonction('ajouter_filleul', 'action/');
	if(is_array($contacts)){
		$nb = 0;
		foreach ($contacts as $contact){
			set_request('email', $contact['email']);
			set_request('nom', $contact['nom']);
			$ajouter_filleul(0);
			$nb++;
		}
		if($nb > 0){
			include_spip('inc/invalideur');
			suivre_invalideur('1');
		}
	}
}

?>
