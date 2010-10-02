<?php

function zeroclipboard_insert_head($flux){
	$flux .= '
<script type="text/javascript" src="'.find_in_path(_DIR_LIB_ZEROCLIPBOARD.'ZeroClipboard.js').'"></script>
<script type="text/javascript" src="'.generer_url_public('zeroclipboard.js').'"></script>
';
	return $flux;
}

?>