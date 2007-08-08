<?php

	function Agendax_insert_head($flux) {
		$flux .= '<link rel="stylesheet" href="'.find_in_path("agendax.css").'" type="text/css" />
		<script src="?page=agendax.js" type="text/javascript"></script>';
		return $flux;
	}

?>
