<?php
######################################################################
# RECHERCHE MULTI-CRITERES                                           #
# Auteur: Dominique (Dom) Lepaisant - Octobre 2007                   #
# Adaptation de la contrib de Paul Sanchez - Netdeveloppeur          #
# http://www.netdeveloppeur.com                                      #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################
function rec_mc_install($action){
	switch ($action) {
	// La base est deja cree ?
		case 'test':
			// Verifier que le champ id_rec_mc est present...
			include_spip('base/abstract_sql');
			$desc = spip_abstract_showtable("spip_rmc_rubs_groupes", '', true);
			return (isset($desc['field']['id_groupe']));
			break;
	// Installer la base
		case 'install':
			include_spip('base/create');  // definir la fonction
			include_spip('base/rec_mc_tables'); // definir sa structure
			creer_base();
			break;
	// Supprimer la base
		case 'uninstall':
			spip_query("DROP TABLE spip_rmc_rubs_groupes_conf");
			spip_query("DROP TABLE spip_rmc_rubs_groupes");
			spip_query("DROP TABLE spip_rmc_mots_exclus");
		break;
	}
}
?>