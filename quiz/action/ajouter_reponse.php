<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_ajouter_reponse_dist() {
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_article = intval($arg);

	
	sql_insertq('spip_reponses', array(
		'id_article' => $id_article,
		'texte' => ''
		)
	);

	// echo'<script type="text/javascript">alert("Reponse correctement ajoutee sur article " + '.$id_article.')</script>';
	
	// Y a t'il déja un corrigé ? Si non on en ajoute un 
	if (
	sql_countsel(
	'spip_corrections',
	"id_article = '$id_article'") == 0) {
	
		// echo'<script type="text/javascript">alert("Pas de correction pour article " + '.$id_article.')</script>';
		
		sql_insertq('spip_corrections', array(
			'id_article' => $id_article
			)
		);
	} else {
		// echo'<script type="text/javascript">alert("on a une correction pour article " + '.$id_article.')</script>';
	}


	// retour
	include_spip('inc/headers');
	redirige_par_entete(urldecode(_request('redirect')));

}

?>