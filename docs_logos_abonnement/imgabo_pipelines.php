<?php
$GLOBALS['gestdoc_exec_colonne_document'][] = 'abonnement_edit';
$GLOBALS['gestdoc_liste_champs'][] = 'descriptif';
function imgabo_post_edition($flux){}
function imgabo_affiche_gauche($flux){
    if (($flux['args']['exec'] == 'abonnement_edit')
		AND $table = preg_replace(",_edit$,","",$flux['args']['exec'])
		AND $type = objet_type($table)
		AND $id_table_objet = id_table_objet($type)
		AND $id_abonnement = intval($flux['args'][$id_table_objet])){
		$GLOBALS['logo_libelles']['id_abonnement'] = _T('imgabo:logo_abonnement');
		$iconifier = charger_fonction('iconifier', 'inc');
		$flag_editable = autoriser('modifier', 'abonnement', $id_abonnement, null, array('id_article' => $id_article));
		$out .= $iconifier('id_abonnement', $id_abonnement, 'abonnement_edit', $flag_editable);
		$flux['data'] .= $out;
	}
    return $flux;
}

?>