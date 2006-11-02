<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006
	 *
	 **/

debut_raccourcis();

icone_horizontale(_T('cm:liste_lettres'), generer_url_ecrire("cm_index",""), '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/lettre-24.png', '');
icone_horizontale(_T('cm:creer_lettre'), generer_url_ecrire("cm_lists_edit","id=-1"), '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/lettre-24.png', 'creer.gif');
icone_horizontale(_T('cm:liste_abonnes'), generer_url_ecrire("cm_subscribers",""), '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/abonnes.png', '');
icone_horizontale(_T('cm:ajouter_abonne'), generer_url_ecrire("cm_subscribers_new",""), '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/abonne.png', 'creer.gif');
icone_horizontale(_T('cm:parametres'), generer_url_ecrire("cm_settings",""), '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/configuration.png', '');

fin_raccourcis();
?>