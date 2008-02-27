<?php

include_spip('base/abstract_sql');

/* API plugin open-publishing
*/
	function op_supprimer_tables() {
		if ($GLOBALS['spip_version_code'] < '1.93') {
			spip_query('DROP TABLE spip_op_config');
			spip_query('DROP TABLE spip_op_rubriques');
		}
		else {
			sql_drop_table('spip_op_config');
			sql_drop_table('spip_op_rubriques');
		}
	}
	
	function op_maj_auteurs() {
		// lire toute la table spip_op_auteurs
		if ($GLOBALS['spip_version_code'] < '1.93') {

			$res = spip_query("SELECT * FROM spip_op_auteurs");
			while ($row = spip_fetch_array($res)) {
				$extra=array(
					"OP_pseudo"=>$row['nom'],
					"OP_mail"=>$row['email']
				);
				$extra=serialize($extra);
			
				spip_query('UPDATE spip_articles SET extra = ' . spip_abstract_quote($extra) .
					' WHERE id_article = ' . spip_abstract_quote($row['id_article']) );
			}
		}
		else {
			$reponse = sql_fetsel( array('id_article','nom','email'), array('spip_op_auteurs'));
		}
		
	}

	function op_sup_auteurs() {
		if ($GLOBALS['spip_version_code'] < '1.93') {
			spip_query('DROP TABLE spip_op_auteurs');
		}
		else {
			sql_drop_table('spip_op_auteurs');
		}
	}

?>