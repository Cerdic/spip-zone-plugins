<?php

/**
 * Ajouter les chats sur les vues de rubriques
 *
 * @param 
 * @return 
**/
function chats_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec'])
	  AND $e['type'] == 'rubrique'
	  AND $e['edition'] == false) {
		  
		$id_rubrique = $flux['args']['id_rubrique'];
  	
		$bouton = '';
		if (autoriser('creerchatdans','rubrique', $id_rubrique)) {
			$bouton .= icone_verticale(_T('chat:icone_creer_chat'), generer_url_ecrire('chat_edit', "id_rubrique=$id_rubrique"), "chat-24.png", "new", 'right')
					. "<br class='nettoyeur' />";
		}
		
		$lister_objets = charger_fonction('lister_objets','inc');	
		$flux['data'] .= $lister_objets('chats',array('titre'=>_T('chat:titre_chats_rubrique') , 'id_rubrique'=>$id_rubrique, 'par'=>'nom'));
		$flux['data'] .= $bouton;
		
	}
	return $flux;
}


?>
