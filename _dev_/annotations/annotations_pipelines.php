<?php

/*Not used at the moment because of the limitations of the insert_js pipeline (no . allowed in the script names)*/ 

function Annotations_insert_js($flux) {
	if($flux["type"]=="fichier") {
		$flux["data"]["Annotations"] = array("jquery.ifixpng","jqModal","jquery.tooltip","jquery.dimensions","jquery.annotations"); 
	}
	
	return $flux;
}

?>
