<?php

debut_raccourcis();
//icone_horizontale(_T('livre:cr&eacute;er_les_tables'), generer_url_ecrire("table"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/sql.png', 'creer.gif');
	icone_horizontale(_T('asso:effacer_les_tables'), generer_url_ecrire("efface"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/sql.png', 'supprimer.gif');
	icone_horizontale(_T('asso:profil_de_lassociation'), generer_url_ecrire("profil"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ecole.gif', 'rien.gif');
	icone_horizontale(_T('asso:categories_de_cotisations'), generer_url_ecrire("categories"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/calculatrice.gif', '');
	icone_horizontale(_T('asso:gestion_des_banques'), generer_url_ecrire("banques"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/EuroOff.gif', '');	
	icone_horizontale(_T('asso:gestion_de_lassoc'), generer_url_ecrire("adherents"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/annonce.gif', '');
fin_raccourcis();

?>