<?php

/*
function Annotations_insert_js($flux) {
	if($flux["type"]=="fichier") {
		$flux["data"]["Annotations"] = array("jquery.ifixpng","jqModal","jquery.tooltip","jquery.dimensions","jquery.annotations"); 
	}
	
	return $flux;
}
*/

function Annotations_insert_head($flux) {
	$flux .= '<link rel="stylesheet" href="'.url_absolue(find_in_path('css/jqModal.css')).'" type="text/css" />
<link rel="stylesheet" href="'.url_absolue(find_in_path('css/jquery.tooltip.css')).'" type="text/css" />';

	if(_request("carto_debug")) 
		$flux .= '<script type="text/javascript" src="'.url_absolue(find_in_path('javascript/jquery.ifixpng.js')).'"></script>
		<script type="text/javascript" src="'.url_absolue(find_in_path('javascript/jquery.ifixpng.js')).'"></script>
		<script type="text/javascript" src="'.url_absolue(find_in_path('javascript/jqModal.js')).'"></script>
		<script type="text/javascript" src="'.url_absolue(find_in_path('javascript/jquery.tooltip.js')).'"></script>
		<script type="text/javascript" src="'.url_absolue(find_in_path('javascript/jquery.dimensions.js')).'"></script>
		<script type="text/javascript" src="'.url_absolue(find_in_path('javascript/jquery.annotations.js')).'"></script>
		<script type="text/javascript" src="'.generer_url_public("cfg_annotations.js").'"></script>
		<script type="text/javascript" src="'.url_absolue(find_in_path('javascript/jquery.annotations-window.js')).'"></script>';
	else
		$flux .= '
<script type="text/javascript">
	jQuery(function(){
		//check for annotated images
		if(jQuery("img[@id^=annotated_map]").size()) {
			//load all scripts in sync mode
			var options = {
			 	dataType: "script",
			 	cache: true
			};
			var scripts = [
			 "'.url_absolue(find_in_path('javascript/jquery.ifixpng.js')).'",
			 "'.url_absolue(find_in_path('javascript/jqModal.js')).'",
			 "'.url_absolue(find_in_path('javascript/jquery.tooltip.js')).'",
			 "'.url_absolue(find_in_path('javascript/jquery.dimensions.js')).'",
			 "'.url_absolue(find_in_path('javascript/jquery.annotations.js')).'",
			 "'.generer_url_public("cfg_annotations.js").'"
			];
			
			var load_scripts = function() {
				if(!scripts.length) {
					//check for the annotation window
					if(jQuery("#annotate_window").size()) {
						options.success = null;
						options.url = "'.url_absolue(find_in_path('javascript/jquery.annotations-window.js')).'";
						jQuery.ajax(options);			
					}
					return;
				} else {
					options.url = scripts.shift();
					jQuery.ajax(options);
				}			
			}
			options.success = load_scripts;
			load_scripts();

		}
 });
</script>
';

	return $flux;
}

?>
