<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function contact_header_prive($flux){
	$flux .= "\n<script type=\"text/javascript\" src=\"".find_in_path('javascript/contact_sortable.js', false)."\"></script>";
	return $flux;
}


?>