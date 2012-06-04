<?php

// Détermine l'id_parent de la nouvell rubrique traduite
function destination_traduction($lang,$id_trad,$creer_racine=''){
	$id_trad_parent='';
	if($lang AND intval($id_trad)){
		// on établit l'id_parent
		$id_trad_parent=sql_getfetsel('id_parent','spip_rubriques','id_rubrique='.$id_trad);
	
		//puis sa traduction
		if($id_trad_parent)$id_parent_trad=sql_getfetsel('id_trad','spip_rubriques','id_rubrique='.$id_trad_parent);
		
		// S'il il existe une traduction parente dans la langue demandé on retourne son id
		if($id_parent_trad) {
			$rub= sql_fetsel('id_rubrique,id_trad','spip_rubriques','id_trad='.$id_parent_trad.' AND lang='.sql_quote($lang));
			if($rub){
				$trads =array(0=>$rub['id_rubrique'],1=>$id_trad,2=>$creer_racine);
				}
			else {
				$id_trad= sql_getfetsel('id_trad','spip_rubriques','id_trad='.$id_parent_trad);
				$trads=destination_traduction($lang,$id_trad,'oui');
				}			
			}
		elseif($id_trad_parent){
			$trads=destination_traduction($lang,$id_trad_parent,'oui');
			}

		return $trads;	
		}
	}
	 
?>
