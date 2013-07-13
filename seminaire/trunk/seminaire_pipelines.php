<?php
function seminaire_insert_head_css($flux) {
    $css = find_in_path('styles/calendrier-seminaire.css');
    $flux .= "<link rel='stylesheet' type='text/css' media='all' href='$css' />\n";
    return $flux;
}

function seminaire_post_insertion($flux) {
	if ($flux['args']['table'] == 'spip_evenements') {
		sql_insertq("spip_mots_liens", array(
			'id_mot' => _request('id_mot'),
			'id_article' =>$flux['args']['id_objet'],
			'objet' =>'evenement'));
	}
}

?>