<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function action_seances_activer_rubrique_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	
	if (intval($arg)!=0) {
		if (intval($arg)>0)
			sql_updateq('spip_rubriques',array('seance'=>1),'id_rubrique='.intval($arg));
		else
			// desactiver 
			$id_rubrique = (-intval($arg));
			sql_updateq('spip_rubriques',array('seance'=>0),'id_rubrique='.$id_rubrique);
			// supprimer les séances enregistrées pour les articles de la rubrique
			$result = sql_select('id_article', 'spip_articles', 'id_rubrique='.$id_rubrique);
			while ($row = sql_fetch($result)) { 
				sql_delete('spip_seances','id_article='.$row['id_article'] );
			}
	}
}
?>