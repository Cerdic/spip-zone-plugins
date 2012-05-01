<?php


/* pour que le pipeline ne rale pas ! */
function spipservice_autoriser(){}

/*
 autoriser('creer', 'spipservice', null);
 => e la recherche des fonctions suivantes
 1 - autoriser_$type_$faire
 2 - autoriser_$type
 3 - autoriser_$faire

 $type  = 'spipservice' (forcement)
 $faire = 'spipservice_truc' (au lieu de 'truc' tout court (pb compatibilite avec acces restreint...)

 */

/* Documenter (joindre doc) */
/*
function autoriser_spipservice_documenter($faire, $type, $id, $qui, $opt) {
	$type = $opt['type'];
	$id = $opt['id'];
	return autoriser('joindredocument', $type, $id);
}
*/
/* Instituer */

function autoriser_spipservice_instituer($faire, $type, $id, $qui, $opt) {
	
	$type = $opt['type'];
	$id = $opt['id'];
	$statut = $opt['statut'];

	if (in_array($qui['statut'], array('0minirezo')) || $qui['webmestre']=='oui'){
		return true;
	}else{
		// test pour un statut particulier
		if ($statut){
			if (autoriser('modifier', $type, $id) && $statut!= 'publie' && $statut!= 'refuse' && $statut!= 'poubelle'){
				return true;
			}
		}
		// test general
		else{
			return autoriser('modifier', $type, $id);
		}
	}
	return false;
}

function autoriser_spipservice_purger($faire, $type, $id, $qui, $opt) {

	if (in_array($qui['statut'], array('0minirezo')) || $qui['webmestre']=='oui'){
		return true;
	}
	return false;
}

/* Voir */
/*
function autoriser_spipservice_voir($faire, $type, $id, $qui, $opt) {
	$type = $opt['type'];
	$id = $opt['id'];
	return autoriser('voir', $type, $id);
}
*/
// Creer
/*
function autoriser_spipservice_creer($faire, $type, $id, $qui, $opt) {
	$type = $opt['type'];
	$id = $opt['id'];
	if (!$id) $id = 0;
	return autoriser('creer'.$type.'dans','rubrique',$id);
}
*/
// Supprimer
function autoriser_spipservice_supprimer($faire, $type, $id, $qui, $opt) {
	$type = $opt['type'];
	$id = $opt['id'];
	if (in_array($qui['statut'], array('0minirezo')) || $qui['webmestre']=='oui'){
		if ($type == 'article'){
			return true;
		}else if ($type == 'breve'){
			return true;
		}else if ($type == 'rubrique'){
			// check si la rubrique a encore des articles fils dont le statut n'est pas 'poubelle'
			$res1 = sql_countsel("spip_articles", "id_rubrique=".$opt['id']." and statut not like 'poubelle'");
			// check si la rubrique a encore des rubriques filles
			$res2 = sql_countsel("spip_rubriques", "id_parent=".$opt['id']);
			if ($res1==0 && $res2==0){
				return true;
			}
		}
	}else if($type == 'article'){
		return autoriser('instituer','article',$id,'',array('statut'=>'poubelle'));
	}
	return false;
}

// Modifier
/*
function autoriser_spipservice_modifier($faire, $type, $id, $qui, $opt) {
	$type = $opt['type'];
	$id = $opt['id'];
	return autoriser('modifier', $type, $id);
}
*/
// Iconifier
/*
function autoriser_spipservice_iconifier($faire, $type, $id, $qui, $opt) {
	$type = $opt['type'];
	$id = $opt['id'];
	return autoriser('iconifier', $type, $id);
}
*/
// Configurer (interface ecrire/)
/*
function autoriser_spipservice_conf($faire, $type, $id, $qui, $opt) {
	return (in_array($qui['statut'], array('0minirezo')) || $qui['webmestre']=='oui');
}
*/
?>
