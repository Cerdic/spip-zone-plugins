<?php 

// exec/fmp3_pipeline_insert_head.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

function fmp3_insert_head ($flux) {

$flux .= "

<!-- "._FMP3_PREFIX." -->
<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/fmp3_public.css'))."' />
<!--[if IE]>
<link rel='stylesheet' type='text/css' href='".url_absolue(find_in_path('css/fmp3_public_ie.css'))."' />
<![endif]-->
<script type='text/javascript' src='".url_absolue(find_in_path('javascript/jquery.fmp3.js'))."'></script>
<!-- / "._FMP3_PREFIX." -->

";

	return ($flux);
} // end 

?>