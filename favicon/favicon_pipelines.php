<?php

function favicon_insert_head($flux) {

	if (find_in_path("inclure/favicon.html")) {
		$html = recuperer_fond("inclure/favicon");
		$flux .= $html;
	}
	return $flux;
	
}