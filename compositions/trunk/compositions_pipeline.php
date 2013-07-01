<?php
/*
 * Plugin Compositions
 * (c) 2007-2009 Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Declaration des champs sur les objets
 *
 * @param array $tables
 * @return array
 */
function compositions_declarer_tables_objets_sql($tables){

	// champs composition et composition_lock sur tous les objets
	// c'est easy
	$tables[]['field']['composition'] = "varchar(255) DEFAULT '' NOT NULL";
	$tables[]['field']['composition_lock'] = "tinyint(1) DEFAULT 0 NOT NULL";
	$tables['spip_rubriques']['field']['composition_branche_lock'] = "tinyint(1) DEFAULT 0 NOT NULL";

	return $tables;
}


/**
 * Fonction vide pour le pipeline homonyme
 */
function compositions_autoriser(){}

/**
 * Autorisation de modifier la composition
 *
 * @param string $faire
 * @param string $type
 * @param int $id
 * @param array $qui
 * @param array $opt
 * @return bool
 */
function autoriser_styliser_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL){
	include_spip('compositions_fonctions');
	if (!autoriser('modifier',$type,$id,$qui,$opt))
		return false;
	if (compositions_verrouiller($type, $id) AND !autoriser('webmestre'))
		return false;
	return true;
}


/**
 * Pipeline styliser pour definir le fond d'un objet en fonction de sa composition
 *
 * @param array $flux
 * @return array
 */
function compositions_styliser($flux){
	// en contexte Z, c'est Z ou Z-core qui stylise (on ne n'en occupe donc pas ici)
	if(!defined('_DIR_PLUGIN_Z') AND !defined('_DIR_PLUGIN_ZCORE')){
		include_spip('compositions_fonctions');
		if (compositions_styliser_auto()){
			$type = $flux['args']['fond']; // on fait l'approximation fond=type
			// si le type n'est pas l'objet d'une composition, ne rien faire
			if (in_array($type,compositions_types())){
				$contexte = isset($flux['args']['contexte'])?$flux['args']['contexte']:$GLOBALS['contexte'];
				$serveur = $flux['args']['connect'];

				$ext = $flux['args']['ext'];
				$_id_table = id_table_objet($type);

				if ($id = $contexte[$_id_table] AND $composition = compositions_determiner($type,$id,$serveur)){
					if ($fond = compositions_selectionner($composition, $type, '', $ext, true, "")){
						$flux['data'] = substr($fond, 0, - strlen(".$ext"));
					}
				}
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
function compositions_affiche_milieu($flux){
	$e = trouver_objet_exec($flux['args']['exec']);
	$objets = compositions_objets_actives();
	if (in_array($e['type'],$objets)
	  AND $e['edition']===false){
		$type = $e['type'];
		if ($id = $flux['args'][$e['id_table_objet']]) {
			$config = unserialize($GLOBALS['meta']['compositions']);
			$aut = autoriser('styliser',$type,$id);
			if ((!isset($config['masquer_formulaire']) OR $config['masquer_formulaire'] != 'oui' OR $aut)
				AND (
					($c=compositions_lister_disponibles($type) AND is_array(reset($c)))
					OR ($type == 'rubrique' AND (!isset($config['tout_verrouiller']) OR $config['tout_verrouiller'] != 'oui'))
				  )
				) {
				$ids = 'formulaire_editer_composition_objet-' . "$type-$id";
				$texte = recuperer_fond(
					'prive/editer/compositions',
					array(
						'type'=>$type,
						'id'=>$id,
					)
				);

				if (($p = strpos($flux['data'],'<!--affiche_milieu-->'))!==false)
					$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
				else
					$flux['data'] .= $texte;
			}
		}
	}

	return $flux;
}

/**
 * Le pipeline compositions_declarer_heritage permet de declarer les heritages
 * sous la forme :
 *		$heritages['objet'] = 'parent';
 * ce qui permet ensuite de faire dans le fichier parent-ma_compo.xml
 * <branche type="objet" composition="une_compo" />
 * 
 * A partir de compositions 3.3.0 cette declaration est obligatoire.
 * Les objets "standards" de SPIP sont declares ici. (a deplacer dans leurs plugins respectifs ?)
 */
function compositions_compositions_declarer_heritage($heritages){
	$heritages['mot'] = 'groupe_mots';
	$heritages['rubrique'] = 'rubrique';
	$heritages['article'] = 'rubrique';
	$heritages['breve'] = 'rubrique';
	$heritages['site'] = 'rubrique';
	return $heritages;
}


?>
