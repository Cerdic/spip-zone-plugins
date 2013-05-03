<?php
######################################################################
# RECHERCHE 				                             #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################

function recherche_sti_upgrade($nom_meta_base_version, $version_cible)
{
        $maj = array();
        $maj['create'] = array(
            array('maj_tables', array('spip_sti_groupes_mots_cles')),
		);
        include_spip('base/upgrade');
        maj_plugin($nom_meta_base_version, $version_cible, $maj);
	
}

function recherche_sti_vider_tables($nom_meta_base_version)
{
        sql_drop_table("spip_sti_groupes_mots_cles");//on efface la table
	effacer_meta($nom_meta_base_version);//on efface la version du plugin
}

?>
