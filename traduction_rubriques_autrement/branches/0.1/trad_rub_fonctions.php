<?php
function destination_traduction($lang,$id_trad){
	$id_trad_parent=sql_fetsel('id_trad,id_parent','spip_rubriques','id_rubrique='.sql_quote($id_trad));
	if($id_trad_parent['id_trad']){
		$trads = sql_getfetsel('id_parent','spip_rubriques','id_trad='.sql_quote($id_trad_parent['id_trad']).' AND lang='.sql_quote($lang));	
		if($id_trad=sql_getfetsel('id_trad','spip_rubriques','id_trad='.sql_quote($id_trad_parent['id_parent']).' AND lang='.sql_quote($lang)))
			$trads = sql_getfetsel('id_rubrique','spip_rubriques','id_trad='.sql_quote($id_trad).' AND lang='.sql_quote($lang));	
		}
	elseif($id_trad_parent['id_parent']){
		$id_trad_parent2=sql_fetsel('id_trad,id_parent','spip_rubriques','id_rubrique='.sql_quote($id_trad_parent['id_parent']));
		if($id_trad_parent2['id_trad']){
				$trads = sql_getfetsel('id_rubrique','spip_rubriques','id_trad='.sql_quote($id_trad_parent2['id_trad']).' AND lang='.sql_quote($lang));
			}
		}
 	if(!$trads){
 	 	$trads = sql_getfetsel('id_rubrique','spip_rubriques','id_parent=0 AND lang='.sql_quote($lang));		
 		}
return $trads;
}	 
?>