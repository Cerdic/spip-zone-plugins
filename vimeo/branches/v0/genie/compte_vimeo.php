<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	function genie_compte_vimeo_dist($t){
		
		include_spip('action/vimeo');
		action_video_dist();
		
		return 1;

	}

?>