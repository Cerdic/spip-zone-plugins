<?php
$GLOBALS['medias_exec_colonne_document'][] = 'evenements_edit';
$GLOBALS['medias_liste_champs'][] = 'descriptif';
function docs_logos_agenda2_post_edition($flux){return $flux;}
function docs_logos_agenda2_affiche_gauche($flux){
    if (($flux['args']['exec'] == 'evenements_edit')
		AND $table = preg_replace(",_edit$,","",$flux['args']['exec'])
		AND $type = objet_type($table)
		AND $id_table_objet = id_table_objet($type)
		AND $id_evenement = intval($flux['args'][$id_table_objet])){
		$GLOBALS['logo_libelles']['id_evenement'] = _T('docs_logos_agenda2:logo_evenement');
		$iconifier = charger_fonction('iconifier', 'inc');
		$flag_editable = autoriser('modifier', 'evenement', $id_evenement, null, array('id_article' => $id_article));
		$out .= $iconifier('id_evenement', $id_evenement, 'evenements_edit', $flag_editable);
		$flux['data'] .= $out;
	}
    return $flux;
}

?>