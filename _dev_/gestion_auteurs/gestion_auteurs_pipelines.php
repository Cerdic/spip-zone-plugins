<?php

function gestion_auteurs_header_prive($flux) {
	if(_request('exec')=='auteurs')
	$flux .= "<script src='"._DIR_PLUGIN_GESTION_AUTEURS."treemap.js'></script>
	<script type='text/javascript'>
		jQuery(function(){
			jQuery('#auteurs_nav').treemap(500,250,{getData:getDataAuteurs});
		});
		function getDataAuteurs(el) {
			var data = [];
			jQuery('li',el).each(function(){
				var mon_li = jQuery(this);
				data.push([mon_li.html(),mon_li.find('span').text()]);
			})
			return data;
		}
	</script>
	";
	return $flux;
}

?>
