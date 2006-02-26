<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_once('inc_acces_restreint.php');
include_ecrire("exec_auteurs_edit"); // la version native de spip
include_ecrire("inc_logos");
include_ecrire("inc_auteur_voir");

// surcharge de la fonction d'edition
function auteurs_edit()
{
	global $connect_id_auteur, $id_auteur;
	$id_auteur = intval($id_auteur);
	$result = spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=" .
			     $id_auteur);

	if (!$auteur = spip_fetch_array($result)) die('erreur');

	modifier_statut_auteur($auteur, $_POST['statut'], $_POST['id_parent'], $_GET['supp_rub']);

	debut_page($auteur['nom'],  "auteurs",
		   (($connect_id_auteur == $id_auteur) ? "perso" : "redacteurs"));

	echo "<br><br><br>";
	
	debut_gauche();

	cadre_auteur_infos($id_auteur, $auteur);

	if (statut_modifiable_auteur($id_auteur, $auteur)) {
		afficher_boite_logo('aut', 'id_auteur', $id_auteur,
				    _T('logo_auteur').aide ("logoart"), _T('logo_survol'), 'auteurs_edit');
	}

	table_auteurs_edit($auteur);

	$nouv_zone = $_POST['nouv_zone'];
	$supp_zone = $_GET['supp_zone'];
	// le formulaire qu'on ajoute
	global $connect_statut;
	AccesRestreint_formulaire_zones('auteurs', $id_auteur, $nouv_zone, $supp_zone, $connect_statut == '0minirezo', generer_url_ecrire('auteurs_edit',"id_auteur=$id_auteur"));

	fin_page();
}

?>
