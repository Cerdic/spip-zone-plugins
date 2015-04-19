<?php

function logo_auto_declarer_tables_interfaces($interfaces) {
	$interfaces["table_des_traitements"]['LOGO_ARTICLE'][]= 'traiter_logo_article(%s, $Pile[$SP][\'id_article\'])';
	$interfaces["table_des_traitements"]['LOGO_ARTICLE_RUBRIQUE'][]= 'traiter_logo_article(%s, $Pile[$SP][\'id_article\'])';
	$interfaces["table_des_traitements"]['LOGO_RUBRIQUE'][]= 'traiter_logo_rubrique(%s, $Pile[$SP][\'id_rubrique\'])';
	return $interfaces;
}

function traiter_logo_article($logo_article, $id_article) {
	return trim(recuperer_fond('logo/article', array('logo_article' => $logo_article, 'id_article' => $id_article)));
}

function traiter_logo_rubrique($logo_rubrique, $id_rubrique) {
	return trim(recuperer_fond('logo/rubrique', array('logo_rubrique' => $logo_rubrique, 'id_rubrique' => $id_rubrique)));
}