<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

debut_raccourcis();

echo icone_horizontale(_T('clevermail:liste_lettres'), generer_url_ecrire("clevermail_index",""), '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/lettre-24.png', '', '');
echo icone_horizontale(_T('clevermail:creer_lettre'), generer_url_ecrire("clevermail_lists_edit","id=-1"), '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/lettre-24.png', 'creer.gif', '');
echo icone_horizontale(_T('clevermail:liste_abonnes'), generer_url_ecrire("clevermail_subscribers",""), '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/abonnes.png', '', '');
echo icone_horizontale(_T('clevermail:ajouter_abonne'), generer_url_ecrire("clevermail_subscribers_new",""), '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/abonne.png', 'creer.gif', '');
echo icone_horizontale(_T('clevermail:parametres'), generer_url_ecrire("clevermail_settings",""), '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/configuration.png', '', '');

fin_raccourcis();
?>