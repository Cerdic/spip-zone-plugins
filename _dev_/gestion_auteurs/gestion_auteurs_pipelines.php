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
			jQuery('>ul>li',el).each(function(){
				var mon_li = jQuery(this),val,cont;
				if(mon_li.find('>ul>li').size()) {
					cont = jQuery('<div>').append(mon_li.children(':not(ul)')).html();
					val = getDataAuteurs(mon_li);
				} else {
					cont = mon_li.html();
					val = mon_li.find('span').text();
				}
				data.push([cont,val]);
			})
			return data;
		}
	</script>
	";
	return $flux;
}

?>
