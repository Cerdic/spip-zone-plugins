<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/acces');
include_spip('inc/feedbacks');

function stats_total($serveur=''){
	$row = sql_fetsel("SUM(titre) AS total_absolu", "spip_feedbacks",'','','','','',$serveur);
	return $row ? $row['total_absolu'] : 0;
}