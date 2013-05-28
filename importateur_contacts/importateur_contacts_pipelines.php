<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function importateur_contacts_importateur_contacts_moteurs($moteurs){
	
	$infos = array(
		'titre' => 'importateur contacts',
		'url' => '',
		'fournisseurs' => array(
							'email_simple' => array('titre' => _T('importateurcontacts:email_simple'),'type' =>'liste','domaines'=> array(0 => array ('regex' => '/(.*)/','titre' => '*'))),
							'email_liste'=>array('titre' => _T('importateurcontacts:email_liste'),'type' =>'liste','domaines'=> array(0 => array ('regex' => '/(.*)/','titre' => '*')))
						)
	);
	
	$moteurs['importateurcontacts'] = $infos;
	
	return $moteurs;
}

?>
