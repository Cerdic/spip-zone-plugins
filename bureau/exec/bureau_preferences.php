<?php

function exec_bureau_preferences_dist() {

	exec_bureau_preferences_args(intval(_request('id_auteur')),_request('script'),intval(_request('var_ajax_redir')));
}

function exec_bureau_preferences_args($id_auteur,$script=null,$var_ajax_redir=0) {

	if (!is_int($id_auteur) || $id_auteur ==0)return ajax_retour('auteur invalide : ' .$id_auteur);
	$row = sql_fetsel("*", "spip_auteurs", "id_auteur=$id_auteur");
	
	bureau_preferences($id_auteur,$row,$script,$var_ajax_redir);
}

function bureau_preferences($id_auteur,$row,$script=null,$var_ajax_redir=0){
	include_spip('inc/bureau_presentation');

	$contenu = "<b>Avertissement : fenêtre en cours de réalisation</b><br /> "
		. gros_titre($row['nom'],'',false);


	include_spip('inc/bureau_preferences');
	$preferences = charger_fonction('bureau_preferences','inc');
	$contenu .= $preferences($row,$script);

	$menu="";

	if ($var_ajax_redir)
		ajax_retour("modifications enregistrées, rechargez la page pour qu'elles soient prisent en compte.");	
	else
		ajax_retour(bureau_fenetre('Préférences ['.$row['nom'].']',$contenu,$menu,"width:500px;"));
}

?>
