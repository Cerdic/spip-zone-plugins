<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// chargement des valeurs par defaut des champs du formulaire
function formulaires_supprimer_visiteur_charger_dist(){
	$valeurs = array();
	
	// trouver l'email qui va avec s
	if ($p=_request('s')) {
		$p = preg_replace(',[^0-9a-f.],i','',$p);
		if ($p AND $row = sql_fetsel(array('id_auteur','nom','email'),'spip_auteurs',array('cookie_oubli='.sql_quote($p),"statut<>'5poubelle'")))
			$valeurs['_hidden'] = '<input type="hidden" name="s" value="'.$p.'" />';
	}

	if ($row['id_auteur']){
		$valeurs['id_auteur'] = $row['id_auteur']; // a toutes fins utiles pour le formulaire
		$valeurs['nom'] = $row['nom'];
		$valeurs['email'] = $row['email'];
	}
	else {
		$valeurs['_hidden'] = _T('pass_erreur_code_inconnu');
		$valeurs['editable'] =  false; // pas de saisie
	}
		
	return $valeurs;

}


function formulaires_supprimer_visiteur_verifier_dist(){
	$erreurs = array();

	if ($p=_request('s')) {
		if ($id_auteur = sql_getfetsel('id_auteur','spip_auteurs',array('cookie_oubli='.sql_quote($p),"statut='0minirezo'")))
			$erreurs['oubli'] =  _T('inscription2:effacement_auto_impossible');
		
	}else{
	$erreurs['inconnu'] = _T('inscription2:effacement_auto_impossible');
	}

	return $erreurs;
}



// la saisie a ete validee, on peut agir
function formulaires_supprimer_visiteur_traiter_dist(){
	
	if ($p=_request('s')) {
		if ($id_auteur = sql_getfetsel('id_auteur','spip_auteurs',array('cookie_oubli='.sql_quote($p),"statut<>'0minirezo'")))
			$erreurs['oubli'] = _T('inscription2:effacement_auto_impossible');
		
	}else{
	$erreurs['inconnu'] = _T('inscription2:effacement_auto_impossible');
	}
	
	//supprimer un auteur
		$row = sql_getfetsel("statut","spip_auteurs","id_auteur='$id_auteur'");
		
		if($row['statut'] !='0minirezo' and $row['statut'] !='1comite')
			sql_delete("spip_auteurs","id_auteur='$id_auteur'");

		sql_delete("spip_auteurs_elargis","id_auteur='$id_auteur'");
                
        if(defined('_DIR_PLUGIN_ACCESRESTREINT'))
            sql_delete("spip_zones_auteurs","id_auteur='$id_auteur'");

        if(defined('_DIR_PLUGIN_SPIPLISTES'))
            sql_delete("spip_auteurs_listes","id_auteur='$id_auteur'");
	
	
	$message = _T('inscription2:compte_efface');
    return array('message_ok' => $message);
}


?>
