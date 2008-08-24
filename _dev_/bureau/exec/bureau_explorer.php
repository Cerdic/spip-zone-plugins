<?php

function exec_bureau_explorer_dist() {

	exec_bureau_explorer_args(intval(_request('id_parent')));

}

function exec_bureau_explorer_args($id_parent=0) {

	if ($id_parent==0) {
		$titre = "racine";
	}
	else {
		$row = sql_fetsel('titre','spip_rubriques',"id_rubrique=$id_parent");
		$titre = $row['titre'];
	}
	bureau_explorer($id_parent,$titre);
}


function bureau_explorer($id_parent=0,$titre) {

	include_spip('inc/bureau_presentation');
	include_spip('inc/bureau_explorer');

	$rubriques = charger_fonction('bureau_explorer', 'inc');
	$contenu = $rubriques($id_parent);

	ajax_retour(bureau_fenetre('Explorateur ['.$titre.']',$contenu,'<div>Créer un nouvel article</div>','max-width:500px;'));
}

?>
