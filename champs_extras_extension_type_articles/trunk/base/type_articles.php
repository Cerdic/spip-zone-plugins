<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function type_articles_declarer_champs_extras($champs = array()){
	$champs['spip_articles']['type'] = array(
		'saisie' => 'selection',
		'options' => array(
			'nom' => 'type', 
			'label' => _T('type_articles:titre'), 
			'sql' => "text NOT NULL DEFAULT ''",
			'defaut' => '',
			'datas' => array(
				'' => _T('type_articles:type_defaut'),
				'1' => _T('type_articles:type_1'),
				'2' => _T('type_articles:type_2'),
			)
		));

	return $champs;	
}
