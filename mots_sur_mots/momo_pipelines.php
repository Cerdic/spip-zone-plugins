<?php
/**
 * Plugin Momo pour Spip 2.0
 * Licence GPL
 *
 */

/**
 * Ajout du bloc d'attribution de mot-clé
**/
function momo_affiche_milieu($flux) {

	if ($flux["args"]["id_mot"] and $flux["args"]["exec"] =='mots_edit') {
		$contexte = array('id_mot'=>$flux["args"]["id_mot"]);
		$fond = recuperer_fond("prive/mots_parents_mot", $contexte);
		$flux["data"] .= $fond;
	}
        return $flux;
    }

// ajouter la checkbox sur les mots
function momo_editer_contenu_objet($flux){
	if ($flux['args']['type']=='groupe_mot'){
		$checked = in_array('mots',$flux['args']['contexte']['tables_liees']);
		$checked = $checked?" checked='checked'":'';
		$input = "<div class='choix'><input type='checkbox' class='checkbox' name='tables_liees&#91;&#93;' value='mots'$checked id='mots' /><label for='mots'>"._T('momo:item_mots_cles_association_mots')."</label></div>";
		$flux['data'] = str_replace('<!--choix_tables-->',"$input\n<!--choix_tables-->",$flux['data']);
	}
	return $flux;
}

function momo_libelle_association_mots($flux){
	$flux['mots'] = 'momo:objet_mots';
	return $flux;
}

?>