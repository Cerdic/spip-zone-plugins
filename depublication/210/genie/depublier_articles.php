<?php

define('DEPUBLIER_DEFAUT_STATUT', 'prop');

function genie_depublier_articles_dist($t){
	

	$work = sql_allfetsel(
		'id_article, date_depublication, statut_depublication',
		'spip_articles',
		array(
			'date_depublication < NOW()',
			'date_depublication > 0',
			'statut_depublication != '. sql_quote(''),
			'statut = '. sql_quote('publie'),
		)
	);

	
	if ($work) {
		
		include_spip('action/editer_article');
		include_spip('inc/modifier');
		foreach ($work as $w) {
			instituer_article($w['id_article'], array(
				'statut' => $w['statut_depublication'] ? $w['statut_depublication'] : DEPUBLIER_DEFAUT_STATUT,
			));
			// RAZ de la depublication...
			modifier_contenu('article', $w['id_article'], array('invalideur' => "id='$objet/$id_objet'"), array(
				'date_depublication' => '0000-00-00 00:00:00', 
				'statut_depublication' => 'done'
			));

			spip_log('Depublication de ' . $w['id_article']. 'par le CRON à la date prévue de .'.$w['date_depublication']);
		}
	}
	
	return 1;
}

?>
