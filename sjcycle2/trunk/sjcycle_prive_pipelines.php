<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function sjcycle_affiche_gauche(&$flux){
	include_spip('inc/documents');
	if ($flux['args']['exec'] == 'articles_edit') {
		$conf_jcycle = lire_config('sjcycle');
		if($conf_jcycle["afficher_aide"]) {
			$document='';
			$document = sql_countsel('spip_documents as docs JOIN spip_documents_liens AS lien ON docs.id_document=lien.id_document', '(lien.id_objet='.$flux["args"]["id_article"].') AND (lien.objet="article") AND (docs.extension REGEXP "jpg|png|gif")');
			if ($document >= 2){
				$flux['data'] .= debut_cadre_enfonce(url_absolue(find_in_path('images/sjcycle-24.png')), true, '', "Diaporama ".inc_aider_dist("sjcycle"), "", "");
				$flux['data'] .= '<div style="padding:2px;margin:10px 0px;" class="arial1 spip_xx-small">'
						. affiche_raccourci_doc('sjcycle', $flux["args"]["id_article"], 'left')
						. affiche_raccourci_doc('sjcycle', $flux["args"]["id_article"], 'center')
						. affiche_raccourci_doc('sjcycle', $flux["args"]["id_article"], 'right')
						. "</div>\n";
				$flux['data'] .= debut_boite_info(true)._T('sjcycle:boite_info',array('art' => $flux["args"]["id_article"]));
				$flux['data'] .= fin_boite_info(true);
				$flux['data'] .= fin_cadre_enfonce(true);
			}
		}
	 }
    return $flux;
}
?>