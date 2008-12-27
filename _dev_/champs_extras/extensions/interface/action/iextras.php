<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_iextras_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	// droits
	include_spip('inc/autoriser');
	if (!autoriser('configurer', 'iextra')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
	
	@list($arg, $id_extra_ou_table, $champ) = explode ('/', $arg);
	
	// actions possibles
	if (!in_array($arg, array(
		'supprimer_extra',
		'desassocier_extra',
		'associer_champ',
		'supprimer_champ'))){
			include_spip('inc/minipres');
			echo minipres(_T('iextras:erreur_action',array("action"=>$arg)));
			exit;		
	}
	
	// cas de suppression
	if (($arg == 'supprimer_extra') and $id_extra = $id_extra_ou_table){
		include_spip('inc/iextras');
		$extras = iextras_get_extras();
		foreach($extras as $i=>$extra) {
			if ($extra->get_id() == $id_extra) {
				extras_log("Suppression d'un champ par auteur ".$GLOBALS['auteur_session']['id_auteur'],true);
				extras_log($extra, true);
				
				$table = table_objet_sql($extra->table);
				sql_alter("TABLE $table DROP ".$extra->champ);
				
				unset($extras[$i]);
				iextras_set_extras($extras);
				break;
			}
		}
	}

	// cas de desassociation
	if (($arg == 'desassocier_extra') and $id_extra = $id_extra_ou_table){
		include_spip('inc/iextras');
		$extras = iextras_get_extras();
		foreach($extras as $i=>$extra) {
			if ($extra->get_id() == $id_extra) {
				extras_log("Desassociation du champ $extra->table/$extra->champ par auteur ".$GLOBALS['auteur_session']['id_auteur'],true);
				
				unset($extras[$i]);
				iextras_set_extras($extras);
				break;
			}
		}
	}
	
		
	// cas de l'association d'un champ existant
	if (($arg == 'associer_champ') and $table = $id_extra_ou_table){
		// recuperer la description du champ
		include_spip('inc/cextras_gerer');
		$champs = extras_champs_anormaux();
		if (isset($champs[$table][$champ])) {
			$sql = $champs[$table][$champ];
			// creer un champ extra avec ce champ
			$extra = new ChampExtra(array(
				'table' => objet_type($table),
				'champ' => $champ,
				'label' => 'label_'.$champ,
				'type' => 'input',
				'sql' => $sql,
			));
			// penser a creer une fonction pour ajouter et supprimer un champ...
			// ajout du champ
			extras_log("Ajout d'un champ deja existant par auteur ".$GLOBALS['auteur_session']['id_auteur'],true);
			extras_log($extra, true);
			
			$extras = iextras_get_extras();
			$extras[] = $extra;
			iextras_set_extras($extras);
			
			// redirection vers le formulaire d'edition du champ
			$redirect = generer_url_ecrire('iextras_edit');
			$redirect = parametre_url($redirect,'id_extra', $extra->get_id(), '&');
			include_spip('inc/header');
			redirige_par_entete($redirect);
		}
	}

	// cas de la suppression d'un champ existant
	if (($arg == 'supprimer_champ') and $table = $id_extra_ou_table){
		// recuperer les descriptions
		// pour verifier que le champ n'est pas declare par quelqu'un
		include_spip('inc/cextras_gerer');
		$champs = extras_champs_anormaux();
		if (isset($champs[$table][$champ])) {
			// suppression
			extras_log("Suppression du champ $table/$champ par auteur ".$GLOBALS['auteur_session']['id_auteur'],true);
			
			$table = table_objet_sql($table);
			sql_alter("TABLE $table DROP ".$champ);			
		}
	}
	
}
?>
