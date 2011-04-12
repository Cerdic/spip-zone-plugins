<?php
function urledit_boite_infos($flux){
	$type_objet=$flux['args']['type'];
	$id_objet=$flux['args']['id'];
	$statut=$flux['args']['row']['statut'];
	if (in_array($type_objet,array('article','rubrique','breve'))) {
		include_spip('inc/urledit');
		include_spip('urls/propres');
		include_spip('inc/autoriser');
		if (autoriser($type_objet,'urledit',$id_objet)){
			$redirect = self();
			$args=$type_objet."-".$id_objet;
			$urlpropre = pipeline('creer_chaine_url',
				array(
					'data' => $url_propre,  // le vieux url_propre
					'objet' => array('type' => $type, 'id_objet' => $id_objet, 'titre'=>$flux['args']['row']['titre'])
				)
			);
			$contexte = array('urlpropre'=>$urlpropre,'args'=>$args,'redirect'=>$redirect,'id_objet' => $id_objet,'type_objet' => $type_objet,'statut' => $statut, 'erreur_urledit' => _request('erreur_urledit'));
			$fond = recuperer_fond("prive/formulaires/urledit", $contexte);
			
			$flux['data'].=$fond;
		}
	}
	return $flux;
}

?>