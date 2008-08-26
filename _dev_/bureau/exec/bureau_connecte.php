<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_bureau_connecte_dist() {

	exec_bureau_connecte_args(_request('script'));
}

function exec_bureau_connecte_args($script=null) {
	if ($script==null) return;

	bureau_connecte($script);
}

function bureau_connecte($script) {

	if ($script == 'cron') {
		$result = sql_allfetsel("*", "spip_auteurs",  "id_auteur!=" .intval($GLOBALS['visiteur_session']['id_auteur']) .  " AND en_ligne>DATE_SUB(NOW(),INTERVAL 2 MINUTE) AND " . sql_in('statut', array('1comite', '0minirezo')));

		if ($result) {
			$users = '';
			foreach ($result as $row) {
					$users .= typo($row['nom']);
			}

			$contenu = "le/les utilisateur(s) ".$users." s'est/viennent de se connecter à l'interface privée";
			$fenetre = bureau_fenetre('Avertissement', $contenu);
			ajax_retour($fenetre);
		}
	}
	else {
		ajax_retour($fenetre);
	}

}
?>
