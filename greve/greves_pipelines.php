<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function greves_styliser($flux){
	
	if($id_greve=greve_active()){
		if(substr_count($flux['args']['fond'],'/')==0 and $flux['args']['fond']!='inc-head' and $flux['args']['fond']!='inc-entete'){

			$flux['data'] 				= 	str_replace('.html','',find_in_path('greve.html'));
			$flux['args']['fond']		= 	'greve';
		}
	}
	return $flux;	
}

?>
