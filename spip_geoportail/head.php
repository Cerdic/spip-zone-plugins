<?php

function geoportail_insert_head($flux)
{
	$flux .= 
'
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/geoportail.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/jquery.dialog.js"></script>
<script type="text/javascript" src="'._DIR_PLUGIN_GEOPORTAIL.'js/jquery.cookie.js"></script>
<script language="javascript" type="text/javascript">
'
.($GLOBALS['meta']['geoportail_osm_tah'] ? "jQuery.geoportail.osm_tah=true;" : "jQuery.geoportail.osm_tah=false;")
.($GLOBALS['meta']['geoportail_osm_mquest'] ? "jQuery.geoportail.osm_mquest=true;" : "jQuery.geoportail.osm_mquest=false;")
.($GLOBALS['meta']['geoportail_osm_layer'] ? "jQuery.geoportail.osm_layer='".$GLOBALS['meta']['geoportail_osm_layer']."';" : "jQuery.geoportail.osm_mquest='mapnik';")
.'
</script>
<!--_GEOPORTAIL_HEADER_-->
<link rel="stylesheet" href="'._DIR_PLUGIN_GEOPORTAIL.'css/jqdialog.css" type="text/css"  />
<link rel="stylesheet" href="'._DIR_PLUGIN_GEOPORTAIL.'css/geoportail.css" type="text/css" />
<!-- IE8 compatibility mode pour la version 1.0b4 -->
<!--[if IE 8]>
<meta http-equiv="X-UA-Compatible" content="IE=7"/>
<![endif]-->

';
	return $flux;
}

?>