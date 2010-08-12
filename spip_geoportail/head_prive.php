<?php

function geoportail_header_prive($flux)
{
	$flux .= 
'
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/geoportail.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/jquery.dialog.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/jquery.cookie.js"></script>
<link rel="stylesheet" href="'._DIR_PLUGIN_GEOPORTAIL.'css/jqdialog.css" type="text/css" />
<link rel="stylesheet" href="'._DIR_PLUGIN_GEOPORTAIL.'css/geoportail_prive.css" type="text/css" />
';
	return $flux;
}

?>