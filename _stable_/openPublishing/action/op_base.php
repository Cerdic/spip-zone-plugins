<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Ce fichier disparaitra dans les prochaine version
 * il fournie les fonctions de transitions entre openPublishing et Publication ouverte
 */

#
# action generique
#
function action_op_base() {

	global $action, $arg, $hash, $id_auteur;
	include_spip('inc/securiser_action');
	if (!verifier_action_auteur("$action-$arg", $hash, $id_auteur)) {
		include_spip('inc/minipres');
		minipres(_T('info_acces_interdit'));
	}

	preg_match('/^(\w+)\W(.*)$/', $arg, $r);
	$var_nom = 'action_op_base_' . $r[1];
	if (function_exists($var_nom)) {
		spip_log("$var_nom $r[2]");
		$var_nom($r[2]);
	}
	else {
		spip_log("action $action: $arg incompris");
	}
}

function action_op_base_SupTables($arg) {
	global $redirect;
	
	sql_drop_table('spip_op_config');
	sql_drop_table('spip_op_rubriques');

	redirige_par_entete(rawurldecode($redirect));
}

function action_op_base_Maj($arg) {
	global $redirect;

 	$select = sql_select(
		array('id_article','nom','email'),
		array('spip_op_auteurs')
	);

	while ($rep = sql_fetch($select)) {
		$c++; // compteur
		
 		$extra=array(
 			"OP_pseudo"=>$rep['nom'],
 			"OP_mail"=>$rep['email']
 		);

		$id = $rep['id_article'];

		// on recupere les extras de l'article associe
		$article = sql_fetsel(array('extra'), array('spip_articles'),array('id_article = '.$id));

		
		if (isset($article['extra']) // on merge les extra si besoin
			AND is_array($article = @unserialize($article['extra']))) {
				$extra = array_merge($article, $extra);
		}

		$extra = serialize($extra);

		// et on update l'article
		
		sql_update(
			array('spip_articles'),
			array('extra' => sql_quote($extra)),
			array('id_article = '.$id)
		);
	}
	
	redirige_par_entete(rawurldecode($redirect));
}

function action_op_base_SupAuteur($arg) {
	global $redirect;

	sql_drop_table('spip_op_auteurs');
	redirige_par_entete(rawurldecode($redirect));
}
?>