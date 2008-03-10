<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');

// Compatibilite 1.9.2
if (version_compare($GLOBALS['spip_version_code'],'1.9300','<'))
	include_spip('inc/compat_op');

function op_supprimer_tables() {
	sql_drop_table('spip_op_config');
	sql_drop_table('spip_op_rubriques');
	return 'Les tables "spip_op_config" et "spip_op_rubriques" ont bien &eacute;t&eacute; supprim&eacute;es';
}
	
function op_maj_auteurs() {
	
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
	
	return $c . ' articles mis &agrave; jours';
}

function op_sup_auteurs() {
	sql_drop_table('spip_op_auteurs');
	return 'La table "spip_op_auteurs" a &eacute;t&eacute; supprim&eacute;e';
}

$action = $_GET['action'];

switch ($action) {
	case 'SupTables' :
		ecrire_config('op/retourFonction', op_supprimer_tables());
		break;
	case 'Maj' :
		ecrire_config('op/retourFonction', op_maj_auteurs());
		break;
	case 'SupAuteur' :
		ecrire_config('op/retourFonction', op_sup_auteurs());
		break;
	default :
		ecrire_config('op/retourFonction', '');
}

?>