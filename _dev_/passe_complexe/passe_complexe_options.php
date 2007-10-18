<?php
function passe_complexe_insert_head($flux){
	if (($r=_request('exec'))=='auteur_infos'){

		$flux .= '<script type="text/javascript" src="'.generer_url_public('jquery.pstrength.1.2.js').'"></script>';
		$flux .= '<script type="text/javascript"><!--
		$(document).ready(function() {
            $("input.formo").filter(function(el) {return $(this).attr("name") == "new_pass";}).pstrength({minchar: 6});
		});
		--></script>';
	}
	return $flux;
}

?>