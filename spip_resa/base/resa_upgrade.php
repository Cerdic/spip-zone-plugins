<?php

function resa_install($action)
{
	switch ($action)
	{
		case 'test' :
			include_spip('base/abstract_sql') ;
			$desc_cal = sql_showtable('spip_resa_calendrier') ;
			$desc_resa = sql_showtable('spip_resa_reservation') ;
			return isset($desc_cal['field']['id_calendrier']) && isset($desc_resa['field']['id_reservation']) ;
		break ;
		case 'install' :
			resa_upgrade('resa_base_version', 1) ;
		break ;
		case 'uninstall' :
			resa_vider_tables('resa_base_version') ;
		break ;
	}
}

function resa_upgrade($nom_meta_base_version, $version_cible)
{
	$current_version = 0.0 ;

	if ( 
		( isset($GLOBALS['meta']['resa_base_version']) )
		 && ( ($current_version = $GLOBALS['meta'][$nom_meta_base_version]) == $version_cible )
	)
		return;

	include_spip('base/create') ;
	include_spip('base/abstract_sql') ;
	
	creer_base() ;
	ecrire_meta($nom_meta_base_version, $current_version = $version_cible) ;
}

function resa_vider_tables($nom_meta_base_version)
{
	$reqArticles = sql_select(array('id_calendrier', 'id_article'), 'spip_resa_calendrier') ;
	while( $article = sql_fetch($reqArticles) )
	{
		$texte = sql_getfetsel('texte', 'spip_articles', 'id_article=' . sql_quote((int) $article['id_article'])) ;
		sql_updateq(
			'spip_articles',
			array('texte' => str_replace('<calendrier1|id_calendrier=' . $article['id_calendrier'] . '>', '', $texte)),
			'id_article=' . sql_quote((int) $article['id_article'])
		) ;
	}

	sql_drop_table('spip_resa_reservation') ;
	sql_drop_table('spip_resa_calendrier') ;

	effacer_meta($nom_meta_base_version) ;
}
