<?php
$GLOBALS['gestdoc_exec_colonne_document'][] = 'evenements_edit';
$GLOBALS['gestdoc_liste_champs'][] = 'descriptif';
function docs_logos_agenda2_post_edition($flux){}
function docs_logos_agenda2_affiche_gauche($flux){
    if (($flux['args']['exec'] == 'evenements_edit')
		AND $table = preg_replace(",_edit$,","",$flux['args']['exec'])
		AND $type = objet_type($table)
		AND $id_table_objet = id_table_objet($type)
		AND ($id = intval($flux['args'][$id_table_objet]) OR $id = 0-$GLOBALS['visiteur_session']['id_auteur'])){
		if ($id_evenement = $flux['args']['id_evenement']) {
		$GLOBALS['logo_libelles']['id_evenement'] = _T('docs_logos_agenda2:logo_evenement');
		$iconifier = charger_fonction('iconifier', 'inc');
		$flag_editable = autoriser('modifier', 'evenement', $id_evenement, null, array('id_article' => $id_article));
		$out .= $iconifier('id_evenement', $id_evenement, 'evenements_edit', $flag_editable);
		$flux['data'] .= $out;

        }
	}
    return $flux;
}

function docs_logos_agenda2_formulaire_admin($flux) {
	if (
	 isset($flux['args']['contexte']['objet'])
	 AND $objet = $flux['args']['contexte']['objet']
	 AND $objet = 'evenement'
	 AND isset($flux['args']['contexte']['id_objet'])
	 AND $id_objet = $flux['args']['contexte']['id_objet']
	 ) {
		
			$btn = recuperer_fond('prive/bouton/evenements', array(
			'objet' => $objet,
			'id_objet' => $id_objet,
			'voir_objet' => generer_url_ecrire_evenement($id_objet),
			'nom_objet_lang' => 'docs_logos_agenda2:evenement'
			));
			$flux['data'] = preg_replace('%(</div>)%is', $btn.'$1', $flux['data'].'');			
		}
	return $flux;
}

?>