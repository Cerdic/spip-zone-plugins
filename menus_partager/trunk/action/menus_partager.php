<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function action_menus_partager_dist($identifiant=null) {
	if (is_null($identifiant)){
		$identifiant = _request('identifiant');
	}
	
	if (
		$identifiant
		and is_string($identifiant)
		// Si on trouve bien le menu demandé
		and $id_menu = intval(sql_getfetsel('id_menu', 'spip_menus', 'identifiant = '.sql_quote($identifiant)))
	) {
		$html = trim(recuperer_fond(
			'inclure/menu_partager',
			array(
				'id_menu' => $id_menu,
			)
		));
		
		// On enlève le <ul> d'entourage pour pouvoir inclure le menu dans une autre liste déjà là
		$html = preg_replace('|^<ul[^>]*>|is', '', $html);
		$html = preg_replace('|</ul>$|is', '', $html);
		
		header('Status: 200 OK');
		header("Content-type: text/html; charset=utf-8");
		echo $html;
		exit;
	}
	
	header('Status: 404 Not Found');
	exit;
}
