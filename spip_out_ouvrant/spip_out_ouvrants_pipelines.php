<?php

function spip_out_ouvrants_insert_head($flux) {

	$flux .= '<script type="text/javascript" src="'.find_in_path('spip_out_ouvrants.js').'"></script>';

	return $flux;
}
