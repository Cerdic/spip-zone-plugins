<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function sjcycle_affiche_gauche($flux){
	include_spip('inc/documents');
	include_spip('inc/meta');

	if ($flux['args']['exec'] == 'article_edit') {
		$conf_sjcycle = lire_config('sjcycle');
		if($conf_sjcycle['afficher_aide']) {
			$document='';
			$document = sql_countsel('spip_documents as docs JOIN spip_documents_liens AS lien ON docs.id_document=lien.id_document', '(lien.id_objet='.$flux["args"]["id_article"].') AND (lien.objet="article") AND (docs.extension REGEXP "jpg|png|gif")');
			if ($document >= 2){
				$flux['data'] .= recuperer_fond('prive/navigation/bloc_aide', array('id_article' => $flux["args"]["id_article"]));
			}
		}
	 }
    return $flux;
}