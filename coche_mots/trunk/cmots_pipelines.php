<?php

// Securite
if (!defined("_ECRIRE_INC_VERSION")) return;

function cmots_recuperer_fond($flux){
	if ($flux['args']['fond'] == 'prive/objets/liste/mots_associer-recherche' OR $flux['args']['fond'] == 'prive/objets/liste/mots_associer-select') {
		$id_objet = $flux['args']['contexte']['id_objet'];
		$objet = $flux['args']['contexte']['objet'];
		$id_groupe = $flux['args']['contexte']['id_groupe'];
		if (autoriser('modifier',$objet,$id_objet)) {
			$flux['data']['texte'] = recuperer_fond(
				'prive/inclure/coche_mots',
				array(
					'id_groupe' => $id_groupe,
					'id_objet' => $id_objet,
					'objet' => $objet,
					'retour' => self() 
				)
			).$flux['data']['texte'];
		}
	}
	return $flux;
}

?>
