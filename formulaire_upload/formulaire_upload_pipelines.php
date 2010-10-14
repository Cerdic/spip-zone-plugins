<?php

function FormulaireUpload_insert_head($flux){
	$flux .='<script src="'._DIR_PLUGIN_FORMULAIREUPLOAD.'javascript/jquery.MultiFile.js" type="text/javascript"></script>';
	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_FORMULAIREUPLOAD.'formulaire_upload.css" type="text/css" media="all" />';

	return $flux;
}

?>
