<?php
// <script src="'._DIR_PLUGIN_GMAPS_V3.'theme/js/gmaps_v3.js.html'.'" type="text/javascript"></script>

function gmaps_v3_insert_head($flux){
	$flux .='
<!-- Google Maps API v3 -->
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<!-- // Google Maps API v3 -->
	'."\n";

	return $flux;
}
