<?php
/*
 * Plugin Compositions
 * (c) 2007-2009 Cedric Morin
 * Distribue sous licence GPL
 *
 */

// lister les exec ou apparait l'interface de composition et le type correspondant
// peut etre etendu par des plugins
$GLOBALS['compositions_exec']['naviguer'] = 'rubrique';
$GLOBALS['compositions_exec']['articles'] = 'article';
$GLOBALS['compositions_exec']['mots_edit'] = 'mot';
$GLOBALS['compositions_exec']['sites'] = 'site';
$GLOBALS['compositions_exec']['breves_voir'] = 'breve';
$GLOBALS['compositions_exec']['auteur_infos'] = 'auteur';

/**
 * Fonction vide pour le pipeline homonyme
 */
function compositions_autoriser(){}

/**
 * Autorisation de modifier la composition
 *
 * @param <type> $faire
 * @param <type> $type
 * @param <type> $id
 * @param <type> $qui
 * @param <type> $opt
 * @return <type>
 */
function autoriser_styliser_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	if (!autoriser('modifier',$type,$id,$qui,$opt))
		return false;
	if (!isset($opt['row'])){
		$table_objet_sql = table_objet_sql($type);
		$id_table_objet = id_table_objet($type);
		$opt['row'] = sql_fetsel('composition,composition_lock',$table_objet_sql,"$id_table_objet=".intval($id));
	}
/*	if ($opt['row']['composition_lock']
		AND !autoriser('webmestre')
	)
		return false;*/
	return true;
}


/**
 * Pipeline styliser pour definir le fond d'un objet en fonction de sa composition
 *
 * @param array $flux
 * @return array
 */
function compositions_styliser($flux){
	include_spip('compositions_fonctions');
	if (compositions_styliser_auto()){
		$type = $flux['args']['fond']; // on fait l'approximation fond=type
		// si le type n'est pas l'objet d'une composition, ne rien faire
		if (in_array($type,compositions_types())){
			$contexte = isset($flux['args']['contexte'])?$flux['args']['contexte']:$GLOBALS['contexte'];
			$serveur = $flux['args']['connect'];

			$ext = $flux['args']['ext'];
			$table = table_objet($type);
			$table_sql = table_objet_sql($type);
			$_id_table = id_table_objet($type);

			$trouver_table = charger_fonction('trouver_table', 'base');
			$desc = $trouver_table($table,$serveur);

			if (
				isset($desc['field']['composition'])
				AND isset($contexte[$_id_table])
				AND $id = $contexte[$_id_table]
				AND $composition = sql_getfetsel('composition',$table_sql,"$_id_table=".intval($id))
				AND $fond = compositions_selectionner($composition, $type, '', $ext, true, "")){
				$flux['data'] = substr($fond, 0, - strlen(".$ext"));
			}
		}
	}
	return $flux;
}


/**
 * Affichage du formulaire de selection de la composition
 *
 * @param array $flux
 * @return array
 */
function compositions_affiche_droite($flux){
	$exec = $flux['args']['exec'];
	if (isset($GLOBALS['compositions_exec'][$exec])){
		$type = $GLOBALS['compositions_exec'][$exec];
		$_id = id_table_objet($type);
		if ($id = $flux['args'][$_id])
			$flux['data'] .= recuperer_fond('prive/editer/compositions',array_merge($_GET,array('type'=>$type,'id'=>$id)));
	}

	return $flux;
}


?>