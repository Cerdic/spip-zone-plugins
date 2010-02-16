<?php 
	function ga_insert_head($flux) {
		include_spip("inc/presentation");
		include_spip('public/assembler');
		$flux .= recuperer_fond("public/ga_head",array());
		return $flux;
	}

?>
