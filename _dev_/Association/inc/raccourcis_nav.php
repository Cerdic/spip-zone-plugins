<?php

debut_raccourcis();
//icone_horizontale(_T('livre:cr&eacute;er_les_tables'), generer_url_ecrire("table"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/sql.png', 'creer.gif');
	icone_horizontale(_T('asso:abonnements'), generer_url_ecrire("categories"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/calculatrice.gif', '');
	icone_horizontale(_T('asso:menu2_titre_relances_abo'),generer_url_ecrire('edit_relances'),  '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ico_panier.png','rien.gif' ); 
	icone_horizontale(_T('asso:menu2_titre_ventes_abo'), generer_url_ecrire('ventes') , '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/journaux.png','rien.gif' ); 
	echo "<hr />";
	icone_horizontale(_T('asso:Profil'), generer_url_ecrire("profil"), '../'._DIR_PLUGIN_ASSOCIATION.'/img_pack/ecole.gif', 'rien.gif');
	echo "<div style='float:right;width:2em'><a href='".generer_url_ecrire("association")."'>+</a></div>" ;
	
	fin_raccourcis();

?>