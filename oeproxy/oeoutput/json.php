<?php
/*
 * Plugin oEmebed The Web
 * (c) 2011 Cedric Morin
 * Distribue sous licence GPL
 *
 * http://oembed.com/
 *
 */


function oeoutput_json_dist($res){


	header("Content-type: application/json;");
	echo json_encode($res);
	
}