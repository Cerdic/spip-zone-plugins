<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function seminaire_declarer_champs_extras($champs = array()){
	$champs[] = new ChampExtra(array(
		'table' => 'evenements', // sur quelle table ?
		'champ' => 'name', // nom sql
		'label' => 'seminaire:name', // chaine de langue 'prefix:cle'
		'precisions' => 'seminaire:precisions_name', //precisions sur le champ name
		'type' => 'ligne', // type de saisie
		'sql' => "varchar(50) NOT NULL DEFAULT ''", // declaration sql
	));
	$champs[] = new ChampExtra(array(
		'table' => 'evenements', // sur quelle table ?
		'champ' => 'origin', // nom sql
		'label' => 'seminaire:origin', // chaine de langue 'prefix:cle'
		'precisions' => 'seminaire:precisions_origin', //precisions sur le champ origin
		'type' => 'ligne', // type de saisie
		'sql' => "varchar(50) NOT NULL DEFAULT ''", // declaration sql
	));
	$champs[] = new ChampExtra(array(
		'table' => 'evenements', // sur quelle table ?
		'champ' => 'abstract', // nom sql
		'label' => 'seminaire:abstract', // chaine de langue 'prefix:cle'
		'precisions' => 'seminaire:precisions_abstract', //precisions sur le champ abstract		
		'type' => 'bloc', // type de saisie
		'sql' => "text NOT NULL DEFAULT ''", // declaration sql
		'options' => array(
			'class' => 'inserer_barre_edition'
		),
	));	
	$champs[] = new ChampExtra(array(
		'table' => 'evenements', // sur quelle table ?
		'champ' => 'notes', // nom sql
		'label' => 'seminaire:notes', // chaine de langue 'prefix:cle'
		'precisions' => 'seminaire:precisions_notes', //precisions sur le champ namenotes
		'type' => 'bloc', // type de saisie
		'sql' => "text NOT NULL DEFAULT ''", // declaration sql
		'options' => array(
			'class' => 'inserer_barre_edition'
		),
	));
	return $champs;
}

//pipeline d'ajout des documents en utilisant l'api mediatheque (merci Guy Cesaro)

$GLOBALS['gestdoc_exec_colonne_document'][] = 'evenements_edit';
$GLOBALS['gestdoc_liste_champs'][] = 'descriptif';
function seminaire_pre_edition($flux){}
function seminaire_affiche_gauche($flux){
    if (($flux['args']['exec'] == 'evenements_edit')
		AND $table = preg_replace(",_edit$,","",$flux['args']['exec'])
		AND $type = objet_type($table)
		AND $id_table_objet = id_table_objet($type)
		AND ($id = intval($flux['args'][$id_table_objet]) OR $id = 0-$GLOBALS['visiteur_session']['id_auteur'])){
		if ($id_evenement = $flux['args']['id_evenement']) {
		$GLOBALS['logo_libelles']['id_evenement'] = _T('seminaire:logo_evenement');
		$iconifier = charger_fonction('iconifier', 'inc');
		$flag_editable = autoriser('modifier', 'evenement', $id_evenement, null, array('id_article' => $id_article));
		$out .= $iconifier('id_evenement', $id_evenement, 'evenements_edit', $flag_editable);
		$flux['data'] .= $out;

        }
	}
    return $flux;
}
?>