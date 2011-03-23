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
	include_spip('compositions_fonctions');
	if (compositions_styliser_auto()){
		if (!defined('_DIR_PLUGIN_Z')){
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
		else {
			$contexte = isset($flux['args']['contexte'])?$flux['args']['contexte']:$GLOBALS['contexte'];
			if (preg_match(',(^|/)contenu/([^/]*)$,i',$flux['args']['fond'],$regs)
			  AND $type = $regs[1]
			  AND in_array($type,compositions_types())){
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
	$exec = $flux['args']['exec'];
	if (isset($GLOBALS['compositions_exec'][$exec])){
		$type = $GLOBALS['compositions_exec'][$exec];
		$_id = id_table_objet($type);
		if ($id = $flux['args'][$_id]) {
			$config = unserialize($GLOBALS['meta']['compositions']);
			$aut = autoriser('styliser',$type,$id);
			if ($config['masquer_formulaire'] != 'oui' OR $aut) {
				$deplie = $aut ? false : -1;
				$ids = 'formulaire_editer_composition_objet-' . "$type-$id";
				$bouton = bouton_block_depliable(strtoupper(_T('compositions:composition')), $deplie, $ids);
				$flux['data'] .= debut_cadre('e', chemin('compositions-24.png','images/'),'',$bouton, '', '', true);
				$flux['data'] .= recuperer_fond('prive/editer/compositions', array_merge($_GET, array('type'=>$type,'id'=>$id)));
				$flux['data'] .= fin_cadre();
			}
		}
	}

	return $flux;
}

function compositions_compositions_lister_disponibles($flux){return $flux;}

?>