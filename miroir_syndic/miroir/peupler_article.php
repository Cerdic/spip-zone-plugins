<?php
/*
 * Plugin miroir_syndic
 * (c) 2006-2010 Fil, Cedric
 * Distribue sous licence GPL
 *
 */

function miroir_peupler_article_dist($id_article,$row) {
	
	articles_set($id_article,array(
		'titre'=>$row['titre'],
		'date'=>$row['date'],
		'statut'=>'publie',
		_MIROIR_CHAMP_LESAUTEURS=>$row['lesauteurs'],
		_MIROIR_CHAMP_DESCRIPTIF=>$row['descriptif'],
		_MIROIR_CHAMP_TAGS=>$row['tags'],
		));

	
}
?>