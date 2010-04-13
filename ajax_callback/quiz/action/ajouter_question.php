<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_ajouter_question_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$id_rubrique = intval($arg);

	// echo'<script type="text/javascript">alert("Ajout question rubrique " + '.$id_rubrique.')</script>';
	
	sql_insertq('spip_articles', array(
		'id_rubrique' => $id_rubrique,
		'titre' => '',
		'statut' => 'publie',
		'date' => date("Y-m-d H:i:s")
		)
	);

	// retour
	include_spip('inc/headers');
	redirige_par_entete(urldecode(_request('redirect')));

}


?>