<?php
	function lister_extensions_affiche_gauche($flux){
		if($flux['args']['exec'] == 'admin_plugin'){
			$lister_extensions_liste = charger_fonction('lister_extensions','inc');
			$flux['data'] .= $lister_extensions_liste();	
		}
		return $flux;
	}
?>