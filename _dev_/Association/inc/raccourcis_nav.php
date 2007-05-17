<?php

debut_raccourcis();
//icone_horizontale(_T('livre:cr&eacute;er_les_tables'), generer_url_ecrire("table"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/sql.png', 'creer.gif');
	icone_horizontale(_T('asso:Cat&eacute;gories de cotisations'), generer_url_ecrire("categories"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/calculatrice.gif', '');
	icone_horizontale(_T('asso:menu2_titre_relances_cotisations'),generer_url_ecrire('edit_relances'),  '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ico_panier.png','rien.gif' ); 
	icone_horizontale(_T('asso:menu2_titre_ventes_asso'), generer_url_ecrire('ventes') , '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/journaux.png','rien.gif' ); 
	echo "<hr />";
	icone_horizontale(_T('asso:Gestion de l\'association'), generer_url_ecrire("association"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/annonce.gif', '');
	
	fin_raccourcis();

?>