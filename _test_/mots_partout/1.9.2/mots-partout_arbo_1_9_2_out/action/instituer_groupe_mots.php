<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');
include_spip('base/abstract_sql');

// http://doc.spip.org/@action_instituer_groupe_mots_dist
function action_instituer_groupe_mots()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (preg_match(",^([a-zA-Z_]\w+)$,", $arg, $r)) 
	  action_instituer_groupe_mots_get($arg);
	elseif (!preg_match(",^(-?\d+)$,", $arg, $r)) {
		 spip_log("action_instituer_groupe_mots_dist $arg pas compris");
	} else action_instituer_groupe_mots_post($r);
}


// http://doc.spip.org/@action_instituer_groupe_mots_post
function action_instituer_groupe_mots_post($r)
{
//	global $messages, $acces_comite, $acces_forum, $acces_minirezo, $new, $articles, $breves, $change_type, $descriptif, $id_groupe, $obligatoire, $rubriques, $syndic, $texte, $unseul;
	$id_groupe = intval($r[1]);

	if ($id_groupe < 0){
		spip_query("DELETE FROM spip_groupes_mots WHERE id_groupe=" . (0- $id_groupe));
	} else {
		$change_type = (corriger_caracteres($GLOBALS['change_type']));
		$texte = (corriger_caracteres($GLOBALS['texte']));
		$descriptif = (corriger_caracteres($GLOBALS['descriptif']));
		$obligatoire=$GLOBALS['obligatoire'] ? 'oui' : 'non';
		$unseul=$GLOBALS['unseul'] ? 'oui' : 'non';
		$id_parent=$GLOBALS['id_parent']; //YOANN
		$acces_comite=$GLOBALS['acces_comite'] ? 'oui' : 'non';
		$acces_forum=$GLOBALS['acces_forum'] ? 'oui' : 'non';
		$acces_minirezo=$GLOBALS['acces_minirezo'] ? 'oui' : 'non';

		$tables_installees = unserialize(lire_meta('MotsPartout:tables_installees'));	
		foreach($tables_installees as $chose => $m) { 
			$q.=", ".$chose."="._q($GLOBALS[$chose] ? 'oui' : 'non');
			$q1.=", ".$chose; 
			$q2.=", "._q($GLOBALS[$chose] ? 'oui' : 'non'); 
		}
		if ($id_groupe) {	// modif groupe
			spip_query("UPDATE spip_mots SET type=" . _q($change_type) . " WHERE id_groupe=$id_groupe");
			spip_query("UPDATE spip_groupes_mots SET titre=" . _q($change_type) . ",id_parent="._q($id_parent).", texte=" . _q($texte) . ", descriptif=" . _q($descriptif) . ", unseul=" . _q($unseul) . ", obligatoire=" . _q($obligatoire)
			.$q.", minirezo="._q($acces_minirezo).", comite="._q($acces_comite).", forum="._q($acces_forum)
			." WHERE id_groupe=".$id_groupe);

		} else {	// creation groupe
		  spip_abstract_insert('spip_groupes_mots', "(titre,id_parent, texte, descriptif, unseul,  obligatoire".$q1.", minirezo, comite, forum)", "(" . _q($change_type) . ", "._q($id_parent).", " . _q($texte) . ", " . _q($descriptif) . ", " . _q($unseul) . ", " . _q($obligatoire) . $q2. ", " . _q($acces_minirezo) . ",  " . _q($acces_comite) . ", " . _q($acces_forum) . " )");
		}
	}
}


// http://doc.spip.org/@action_instituer_groupe_mots_get
function action_instituer_groupe_mots_get($table)
{
	$titre = _T('info_mot_sans_groupe');

	$tables_installees = unserialize(lire_meta('MotsPartout:tables_installees'));	
	foreach($tables_installees as $chose => $m) { 
		$q1.=", ".$chose; 
		$q2.=", '"._q(($table==$chose) ? 'oui' : 'non'); 
	}
	$id_groupe = spip_abstract_insert("spip_groupes_mots", "(titre, unseul, obligatoire".$q1.", minirezo, comite, forum)", "(" . _q($titre) . ", 'non',  'non'".$q2.", 'oui', 'non', 'non'" . ")");
//YOANN : pas de gestion de l'arborescence a ce niveau 

        redirige_par_entete(parametre_url(urldecode(_request('redirect')),
					  'id_groupe', $id_groupe, '&'));
}

?>
