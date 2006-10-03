<?php

	function Player_insert_head($flux){
	$flux .= 	'<script type="text/javascript" src="'.generer_url_public('player_js').'"></script>';

	return $flux;

	}

?>