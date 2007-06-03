<?php

debut_raccourcis();
//icone_horizontale(_T('livre:cr&eacute;er_les_tables'), generer_url_ecrire("table"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/sql.png', 'creer.gif');
	icone_horizontale(_T('asso:effacer_les_tables'), generer_url_ecrire("efface"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/sql.png', 'supprimer.gif');
	icone_horizontale(_T('asso:Profil de l\'association'), '?exec=cfg&cfg=association', '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ecole.gif', 'rien.gif');
	icone_horizontale(_T('asso:Cat&eacute;gories de cotisations'), generer_url_ecrire("categories"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/calculatrice.gif', '');
	icone_horizontale(_T('asso:Gestion des banques'), generer_url_ecrire("banques"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/EuroOff.gif', '');	
	icone_horizontale(_T('asso:Gestion de l\'association'), generer_url_ecrire("adherents"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/annonce.gif', '');
fin_raccourcis();

?>