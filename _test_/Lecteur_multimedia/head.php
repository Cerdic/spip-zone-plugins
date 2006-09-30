<?php

	function Player_insert_head($flux){
	$flux .= 	'<script type="text/javascript" src="'.find_in_path('player.js').'"></script>';

	return $flux;

	}

?>