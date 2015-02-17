<?php
/*
 * Plugin oEmebed The Web
 * (c) 2011 Cedric Morin
 * Distribue sous licence GPL
 *
 * http://oembed.com/
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_demo_oembed_charger_dist(){
	return array(
		'url'=>_request('url'),
	);
}

function formulaires_demo_oembed_traiter_dist(){


	return array('editable' => true);

}