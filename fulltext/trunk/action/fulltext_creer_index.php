<?php
/**
 * Plugin Fulltext
 */
if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/fulltext');
function action_fulltext_creer_index_dist($arg=null){

	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	list($table,$nom) = explode("/",$arg);

	$ok = $erreur = "";
	if (autoriser('webmestre')){

		$tables = fulltext_liste_des_tables();
		if ($table AND isset($tables[$table]) AND isset($tables[$table]['index_prop'][$nom])){
			list($ok,$erreur) = fulltext_creer_index($table,$nom,$tables[$table]['index_prop'][$nom]);
		}
		elseif($table=="all"){
			foreach($tables as $table=>$desc){
				foreach($desc['index_prop'] as $nom=>$champs){
					fulltext_creer_index($table,$nom,$champs);
				}
			}
		}
	}

	$GLOBALS['redirect'] = _request('redirect');
	if ($ok) $GLOBALS['redirect'] = parametre_url($GLOBALS['redirect'],"ok",$ok);
	if ($erreur) $GLOBALS['redirect'] = parametre_url($GLOBALS['redirect'],"erreur",$erreur);

}



function fulltext_creer_index($table, $nom, $vals) {
	$index = fulltext_index($table, $vals, $nom);

	if ($table == 'document' && $nom == 'tout') {
		// On initialise l'indexation du contenu des documents
		sql_updateq("spip_documents", array('contenu' => ''), "extrait='non'");
	}
	if (!$s = sql_alter($query="TABLE " . table_objet_sql($table) . " ADD FULLTEXT " . $index)){
		spip_log($query,"fulltext"._LOG_ERREUR);
		return array('',"$table : " . _T('spip:erreur') . " " . mysql_errno() . " " . mysql_error());
	}
	sql_optimize(table_objet_sql($table));

	$keys = fulltext_keys($table);
	if (isset($keys[$nom]))
		return array("$table : " . _T('fulltext:fulltext_cree') . " : $keys[$nom]","");
	else
		return array("","$table : "._T('spip:erreur'));

}