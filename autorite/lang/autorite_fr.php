<?php

// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(

//A
'activer_mots_cles' => 'Activer la gestion par mots clef',
'admin_complets' => 'Les administrateurs complets',
'admin_restreints' => 'Administrateurs restreints ?',
'admin_tous' => 'Tous les administrateurs (y compris restreints)',
'admins' => 'Les administrateurs',
'admins_redacs' => 'Administrateurs et R&#233;dacteurs',
'admins_rubriques' => 'les administrateurs associ&#233;s &#224; des rubriques ont :',
'administrateur' => 'administrateur',
'attention_crayons' => '<small><strong>Attention.</strong> Les r&#233;glages ci-dessous ne peuvent fonctionner que si vous utilisez un plugin proposant une interface d&#39;&#233;dition (comme par exemple <a href="http://www.spip-contrib.net/Les-Crayons">les Crayons</a>).</small>',
'attention_version' => 'Attention les choix suivants peuvent ne pas fonctionner	avec votre version de SPIP :',
'auteur_message_advitam' => 'L&#39;auteur du message, ad vitam',
'auteur_message_heure' => 'L&#39;auteur du message, pendant une heure',
'auteur_modifie_article' => '<strong>Auteur modifie article</strong> : chaque r&#233;dacteur peut modifier les articles publi&#233;s dont il est l&#39;auteur (et, par cons&#233;quent, mod&#233;rer le forum et la p&#233;tition associ&#233;s).
	<br />
	<i>N.B. : cette option s&#39;applique aussi aux visiteurs enregistr&#233;s, s&#39;ils sont auteurs et si une interface sp&#233;cifique est pr&#233;vue.</i>',
'auteur_modifie_email' => '<strong>R&#233;dacteur modifie email</strong> : chaque r&#233;dacteur peut modifier son email sur sa fiche d&#39;informations personnelles.',
'auteur_modifie_forum' => '<strong>Auteur mod&#232;re forum</strong> : chaque r&#233;dacteur peut mod&#233;rer le forum des articles dont il est l&#39;auteur.',
'auteur_modifie_petition' => '<strong>Auteur mod&#232;re p&#233;tition</strong> : chaque r&#233;dacteur peut mod&#233;rer la p&#233;tition des articles dont il est l&#39;auteur.',

//B

 
//C
'config_auteurs' => 'Configuration des auteurs',
'config_auteurs_rubriques' => 'Quels types d&#39;auteurs peut-on <b>associer &#224; des rubriques</b> ?',
'config_auteurs_statut' => 'A la cr&#233;ation d&#39;un auteur, quel est le <b>statut par d&#233;faut</b> ?',
'config_site' => 'Configuration du site',
'config_site_qui' => 'Qui peut <strong>modifier la configuration</strong> du site ?',
'config_plugin_qui' => 'Qui peut <strong>modifier la configuration</strong> des plugins (activation...) ?',
'crayons' => 'Crayons',
//D
'descriptif_1' => 'Cette page de configuration est r&#233;serv&#233;e aux webmestres du site :',
'descriptif_2' => "<hr />
<p><small>Si vous souhaitez modifier cette liste, veuillez &#233;diter le fichier <tt>config/mes_options.php</tt> (le cr&#233;er le cas &#233;ch&#233;ant) et y indiquer la liste des identifiants des auteurs webmestres, sous la forme suivante :</small></p>
<pre>&lt;?php
  define ('_ID_WEBMESTRES',
  '1:5:8');
?&gt;</pre>
<p><small>A partir de SPIP 2.1, il est aussi possible de donner les droits de webmestre &agrave; un administrateur via la page d'&eacute;dition de l'auteur.</small></p>
<p><small>A noter : les webmestres d&#233;finis de cette mani&#232;re n&#39;ont plus besoin de proc&#233;der &#224; l&#39;authentification par FTP pour les op&#233;rations d&#233;licates (mise &#224; niveau de la base de donn&#233;es, par exemple).</small></p>

<a href='http://www.spip-contrib.net/-Autorite-' class='spip_out'>Cf. documentation</a>
",
'deja_defini' => 'Les autorisations suivantes sont d&#233;j&#224; d&#233;finies par ailleurs :',
'deja_defini_suite' => 'Le plugin &#171;&nbsp;Autorit&#233;&nbsp;&#187; ne peut pas les modifier certains des r&#233;glages ci-dessous risquent par cons&#233;quent de ne pas fonctionner.
	<br />Pour r&#233;gler ce probl&#232;me, vous devrez v&#233;rifier si votre fichier <tt>mes_options.php</tt> (ou un autre plugin actif) a d&#233;fini ces fonctions.',
'details_option_auteur' => '<small><br />Pour le moment, l&#39;option &#171;&#160;auteur&#160;&#187; ne fonctionne que pour les auteurs enregistr&#233;s (forums sur abonnement, par exemple). Et, si elle est activ&#233;e, les administrateurs du site ont aussi la capacit&#233; d&#39;&#233;diter les forums.
	</small>',
'droits_des_auteurs' => 'Droits des auteurs',
'droits_des_redacteurs' => 'Droits des r&#233;dacteurs',
'droits_idem_admins' => 'les m&#234;mes droits que tous les administrateurs',
'droits_limites' => 'des droits limit&#233;s &#224; ces rubriques',

//E
'effacer_base_option' => '<small><br />L&#39;option recommand&#233;e est &#171;&nbsp;personne&nbsp;&#187;, l&#39;option standard de SPIP est &#171;&nbsp;les administrateurs&nbsp;&#187; (mais toujours avec une v&#233;rification par FTP).</small>',
'effacer_base_qui' => 'Qui peut <strong>effacer</strong> la base de donn&#233;es du site ?',
'espace_wiki' => 'Espace wiki',
'espace_wiki_detail' => 'Choisissez ci-dessous un secteur &#224; traiter comme un wiki, c&#39;est-&#224;-dire &#233;ditable par tous depuis l&#39;espace public (&#224; condition d&#39;avoir une interface, par exemple les crayons) :',
'espace_wiki_mots_cles' => 'Espace wiki par mots clef',
'espace_wiki_mots_cles_detail' => 'Choisissez ci-dessous les mots clef qui activeront le mode wiki, c&#39;est-&#224;-dire &#233;ditable par tous depuis l&#39;espace public (&#224; condition d&#39;avoir une interface, par exemple les crayons)',
'espace_wiki_mots_cles_qui' => 'Voulez-vous ouvrir ce wiki au-del&#224; des administrateurs :',
'espace_wiki_qui' => 'Voulez-vous ouvrir ce wiki &mdash; au-del&#224; des administrateurs :',
'espace_publieur' => 'Espace de publication ouverte',
'espace_publieur_detail' => 'Choisissez ci-dessous un secteur &#224; traiter comme un espace de publication ouverte pour les r&#233;dacteurs et / ou visiteurs enregistr&#233;s (&#224; condition d&#39;avoir une interface, par exemple les crayons et un formulaire pour soumettre l\'article) :',
'espace_publieur_qui' => 'Voulez-vous ouvrir la publication &mdash; au-del&#224; des administrateurs :',

//F
'forums_qui' => '<strong>Forums :</strong> qui peut modifier le contenu des forums :',


//G

//H


//I
'icone_menu_config' => 'Autorit&#233;',
'infos_selection' => '(vous pouvez s&#233;lectionner plusieurs secteurs avec la touche shift)',
'interdire_admin' => 'Cochez les cases ci-dessous pour interdire aux administrateurs de cr&#233;er',

//J

//K

//L


//M
'mots_cles_qui' => '<strong>Mots-cl&#233;s :</strong> qui peut cr&#233;er et &#233;diter les mots-cl&#233;s :',

//N
'non_webmestres' => 'Ce r&#233;glage ne s&#39;applique pas aux webmestres.',
'note_rubriques' => '<small><br />(Notez que seuls les administrateurs peuvent cr&eacute;er des rubriques, et, pour les administrateurs restreints, cela ne peut se faire que dans leurs rubriques.)</small>',
'nouvelles_rubriques' => 'de nouvelles rubriques &#224; la racine du site',
'nouvelles_sous_rubriques' => 'de nouvelles sous-rubriques dans l&#39;arborescence.',

//O
'ouvrir_redacs' => 'Ouvrir aux r&#233;dacteurs du site&nbsp; :',
'ouvrir_visiteurs_enregistres' => 'Ouvrir aux visiteurs enregistr&#233;s :',
'ouvrir_visiteurs_tous' => 'Ouvrir &agrave; tous les visiteurs du site :',

//Q


//P
'pas_acces_espace_prive' => '<strong>Pas d\'acc&egrave;s &agrave; l\'espace priv&eacute; :</strong> les r&#233;dacteurs n\'ont pas acc&egrave;s &agrave; l\'espace priv&eacute;.',
'personne' => 'Personne',
'petitions_qui' => '<strong>Signatures :</strong> qui peut modifier les signatures des p&#233;titions :',
'publication' => 'Publication',
'publication_qui' => 'Qui peut publier sur le site :',

//R
'redac_tous' => 'Tous les r&#233;dacteurs',
'redacs' => 'aux r&#233;dacteurs du site',
'redacteur' => 'r&#233;dacteur',
'redacteur_lire_stats' => '<strong>R&#233;dacteur voit stats</strong> : les r&#233;dacteurs peuvent visualiser les statistiques.',
'redacteur_modifie_article' => '<strong>R&#233;dacteur modifie propos&#233;s</strong> : chaque r&#233;dacteur peut modifier un article propos&eacute; &#224; la publication, m&ecirc;me s&#39;il n&#39;en est pas auteur.',
'reglage_autorisations' => 'R&#233;glage des autorisations',
'refus_1' => '<p>Seuls les webmestres du site',
'refus_2' => 'sont autoris&#233;s &#224; modifier ces param&#232;tres.</p>
<p>Pour en savoir plus, voir <a href="http://www.spip-contrib.net/-Autorite-">la documentation</a>.</p>',

//S
'sauvegarde_qui' => 'Qui peut effectuer des <strong>sauvegardes</strong> ?',

//T
'tous' => 'Tous',
'tout_deselectionner' => ' tout d&#233;selectionner',

//U


//V
'valeur_defaut' => '(valeur par d&#233;faut)',
'visiteur' => 'visiteur',
'visiteurs_anonymes' => 'les visiteurs anonymes peuvent cr&#233;er de nouvelles pages.',
'visiteurs_enregistres' => 'aux visiteurs enregistr&#233;s',
'visiteurs_tous' => '&#224; tous les visiteurs du site.',
//W
'webmestre' => 'Le webmestre',
'webmestres' => 'Les webmestres',
);

?>
