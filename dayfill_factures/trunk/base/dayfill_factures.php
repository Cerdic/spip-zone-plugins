<?php
/***
 * Ajoute un champ "id_facture" à la table spip_projets_activites
 */

function dayfill_factures_declarer_champs_extras($champs = array()){
	$champs['spip_projets_activites']['id_facture'] = array(
		'saisie' => 'factures',
		'options' => array(
			'nom' => 'id_facture',
			'label' => _T('dayfill_factures:label_id_facture'),
			'sql' => "int(11) NOT NULL DEFAULT 0", // un jour, si champs extra permet de gerer les afters...  AFTER `id_projet`
			'defaut' => '',// Valeur par dÃ©faut
			'restrictions'=>array(	'voir' 		=> array('auteur'=>''),//Tout le monde peut voir
									'modifier'	=> array('auteur'=>'webmestre'))),//Seuls les webmestre peuvent modifier
        'verifier' => array());

	return $champs;
}


?>
