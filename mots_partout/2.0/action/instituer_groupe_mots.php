<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

// http://doc.spip.org/@action_instituer_groupe_mots_dist
function action_instituer_groupe_mots_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (preg_match(",^([a-zA-Z_]\w+)$,", $arg, $r)) 
	  action_instituer_groupe_mots_get($arg);
	elseif (!preg_match(",^(-?\d+)$,", $arg, $r)) {
		 spip_log("action_instituer_groupe_mots_dist $arg pas compris");
	} else action_instituer_groupe_mots_post($r[1]);
}


// http://doc.spip.org/@action_instituer_groupe_mots_post
function action_instituer_groupe_mots_post($id_groupe)
{
	$acces_comite = _request('acces_comite');
	$acces_forum = _request('acces_forum');
	$acces_minirezo = _request('acces_minirezo');
	$change_type = _request('change_type');
	$descriptif = _request('descriptif');
	$obligatoire = ((_request('obligatoire')) ? 'oui' : 'non');
	$texte = _request('texte');
	$unseul = ((_request('unseul')) ? 'oui' : 'non');
	$id_parent= _request('id_parent'); //YOANN

	if ($id_groupe < 0){
		sql_delete("spip_groupes_mots", "id_groupe=" . (0- $id_groupe));
	} else {
		$change_type = (corriger_caracteres($change_type));
		$texte = (corriger_caracteres($texte));
		$descriptif = (corriger_caracteres($descriptif));

		$valeurs = array(
			"titre" =>$change_type,
			"texte"=>$texte,
			"descriptif"=>$descriptif,
			"unseul"=>$unseul,
			"obligatoire"=>$obligatoire,
			"minirezo"=>$acces_minirezo,
			"comite"=>$acces_comite,
			"forum"=>$acces_forum,
			"id_parent"=>$id_parent);

		$tables_installees = unserialize(lire_meta('MotsPartout:tables_installees'));	
		foreach($tables_installees as $chose => $m) {
	  		$valeurs[$chose] = ((_request($chose)) ? 'oui' : 'non');
	 	}

		if ($id_groupe) {	// modif groupe
			sql_updateq("spip_mots", array("type" => $change_type), "id_groupe=$id_groupe");

			sql_updateq("spip_groupes_mots", $valeurs, "id_groupe=$id_groupe");

		} else {	//spip_log("creation groupe");
		  sql_insertq('spip_groupes_mots', $valeurs);
		}
	}
}


// http://doc.spip.org/@action_instituer_groupe_mots_get
function action_instituer_groupe_mots_get($table)
{
	$titre = _T('info_mot_sans_groupe');

	$valeurs = array(
		'titre' => $titre,
		'unseul' => 'non',
		'obligatoire' => 'non',
		'minirezo' =>  'oui',
		'comite' =>  'non',
		'forum' => 'non');

	$tables_installees = unserialize(lire_meta('MotsPartout:tables_installees'));
	foreach($tables_installees as $chose => $m) {
		$valeurs[$chose] = (($table==$chose) ? 'oui' : 'non');
	}
	$id_groupe = sql_insertq("spip_groupes_mots", $valeurs) ;		
	//YOANN : pas de gestion de l'arborescence a ce niveau 

        redirige_par_entete(parametre_url(urldecode(_request('redirect')),
					  'id_groupe', $id_groupe, '&'));
}

?>
