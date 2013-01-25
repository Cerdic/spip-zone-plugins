<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/autorite?lang_cible=it
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_mots_cles' => 'Activer la gestion par mots clef', # NEW
	'admin_complets' => 'Les administrateurs complets', # NEW
	'admin_restreints' => 'Administrateurs restreints ?', # NEW
	'admin_tous' => 'Tous les administrateurs (y compris restreints)', # NEW
	'administrateur' => 'administrateur', # NEW
	'admins' => 'Les administrateurs', # NEW
	'admins_redacs' => 'Administrateurs et Rédacteurs', # MODIF
	'admins_rubriques' => 'les administrateurs associés à des rubriques ont :', # MODIF
	'attention_crayons' => '<small><strong>Attention.</strong> Les réglages ci-dessous ne peuvent fonctionner que si vous utilisez un plugin proposant une interface d\'édition (comme par exemple <a href="http://www.spip-contrib.net/Les-Crayons">les Crayons</a>).</small>', # MODIF
	'attention_version' => 'Attention les choix suivants peuvent ne pas fonctionner	avec votre version de SPIP :', # NEW
	'auteur_message_advitam' => 'L\'auteur du message, ad vitam', # MODIF
	'auteur_message_heure' => 'L\'auteur du message, pendant une heure', # MODIF
	'auteur_modifie_article' => '<strong>Auteur modifie article</strong> : chaque rédacteur peut modifier les articles publiés dont il est l\'auteur (et, par conséquent, modérer le forum et la pétition associés).
	<br />
	<i>N.B. : cette option s\'applique aussi aux visiteurs enregistrés, s\'ils sont auteurs et si une interface spécifique est prévue.</i>', # MODIF
	'auteur_modifie_email' => '<strong>Rédacteur modifie email</strong> : chaque rédacteur peut modifier son email sur sa fiche d\'informations personnelles.', # MODIF
	'auteur_modifie_forum' => '<strong>Auteur modère forum</strong> : chaque rédacteur peut modérer le forum des articles dont il est l\'auteur.', # MODIF
	'auteur_modifie_petition' => '<strong>Auteur modère pétition</strong> : chaque rédacteur peut modérer la pétition des articles dont il est l\'auteur.', # MODIF

	// C
	'config_auteurs' => 'Configuration des auteurs', # NEW
	'config_auteurs_rubriques' => 'Quels types d\'auteurs peut-on <b>associer à des rubriques</b> ?', # MODIF
	'config_auteurs_statut' => 'A la création d\'un auteur, quel est le <b>statut par défaut</b> ?', # MODIF
	'config_plugin_qui' => 'Qui peut <strong>modifier la configuration</strong> des plugins (activation...) ?', # NEW
	'config_site' => 'Configuration du site', # NEW
	'config_site_qui' => 'Qui peut <strong>modifier la configuration</strong> du site ?', # NEW
	'crayons' => 'Crayons', # NEW

	// D
	'deja_defini' => 'Les autorisations suivantes sont déjà définies par ailleurs :', # MODIF
	'deja_defini_suite' => 'Le plugin « Autorité » ne peut pas les modifier certains des réglages ci-dessous risquent par conséquent de ne pas fonctionner.
	<br />Pour régler ce problème, vous devrez vérifier si votre fichier <tt>mes_options.php</tt> (ou un autre plugin actif) a défini ces fonctions.', # MODIF
	'descriptif_1' => 'Cette page de configuration est réservée aux webmestres du site :', # MODIF
	'descriptif_2' => '
<p>Si vous souhaitez modifier cette liste, veuillez éditer le fichier <tt>config/mes_options.php</tt> (le créer le cas échéant) et y indiquer la liste des identifiants des auteurs webmestres, sous la forme suivante :</p>
<pre>&lt;?php
  define (
  \'_ID_WEBMESTRES\',
  \'1:5:8\');
?&gt;</pre>
<p>A partir de SPIP 2.1, il est aussi possible de donner les droits de webmestre à un administrateur via la page d\'édition de l\'auteur.</p>
<p>A noter : les webmestres définis de cette manière n\'ont plus besoin de procéder à l\'authentification par FTP pour les opérations délicates (mise à niveau de la base de données, par exemple).</p>

<a href=\'http://www.spip-contrib.net/-Autorite-\' class=\'spip_out\'>Cf. documentation</a>
', # MODIF
	'details_option_auteur' => '<small><br />Pour le moment, l\'option « auteur » ne fonctionne que pour les auteurs enregistrés (forums sur abonnement, par exemple). Et, si elle est activée, les administrateurs du site ont aussi la capacité d\'éditer les forums.
	</small>', # MODIF
	'droits_des_auteurs' => 'Droits des auteurs', # NEW
	'droits_des_redacteurs' => 'Droits des rédacteurs', # MODIF
	'droits_idem_admins' => 'les mêmes droits que tous les administrateurs', # MODIF
	'droits_limites' => 'des droits limités à ces rubriques', # MODIF

	// E
	'effacer_base_option' => '<small><br />L\'option recommandée est « personne », l\'option standard de SPIP est « les administrateurs » (mais toujours avec une vérification par FTP).</small>', # MODIF
	'effacer_base_qui' => 'Qui peut <strong>effacer</strong> la base de données du site ?', # MODIF
	'espace_publieur' => 'Espace de publication ouverte', # NEW
	'espace_publieur_detail' => 'Choisissez ci-dessous un secteur à traiter comme un espace de publication ouverte pour les rédacteurs et / ou visiteurs enregistrés (à condition d\'avoir une interface, par exemple les crayons et un formulaire pour soumettre l\'article) :', # MODIF
	'espace_publieur_qui' => 'Voulez-vous ouvrir la publication — au-delà des administrateurs :', # MODIF
	'espace_wiki' => 'Espace wiki', # NEW
	'espace_wiki_detail' => 'Choisissez ci-dessous un secteur à traiter comme un wiki, c\'est-à-dire éditable par tous depuis l\'espace public (à condition d\'avoir une interface, par exemple les crayons) :', # MODIF
	'espace_wiki_mots_cles' => 'Espace wiki par mots clef', # NEW
	'espace_wiki_mots_cles_detail' => 'Choisissez ci-dessous les mots clef qui activeront le mode wiki, c\'est-à-dire éditable par tous depuis l\'espace public (à condition d\'avoir une interface, par exemple les crayons)', # MODIF
	'espace_wiki_mots_cles_qui' => 'Voulez-vous ouvrir ce wiki au-delà des administrateurs :', # MODIF
	'espace_wiki_qui' => 'Voulez-vous ouvrir ce wiki — au-delà des administrateurs :', # MODIF

	// F
	'forums_qui' => '<strong>Forums :</strong> qui peut modifier le contenu des forums :', # NEW

	// I
	'icone_menu_config' => 'Autorità',
	'infos_selection' => '(vous pouvez sélectionner plusieurs secteurs avec la touche shift)', # MODIF
	'interdire_admin' => 'Cochez les cases ci-dessous pour interdire aux administrateurs de créer', # MODIF

	// M
	'mots_cles_qui' => '<strong>Mots-clés :</strong> qui peut créer et éditer les mots-clés :', # MODIF

	// N
	'non_webmestres' => 'Ce réglage ne s\'applique pas aux webmestres.', # MODIF
	'note_rubriques' => '(Notez que seuls les administrateurs peuvent créer des rubriques, et, pour les administrateurs restreints, cela ne peut se faire que dans leurs rubriques.)', # MODIF
	'nouvelles_rubriques' => 'de nouvelles rubriques à la racine du site', # MODIF
	'nouvelles_sous_rubriques' => 'de nouvelles sous-rubriques dans l\'arborescence.', # MODIF

	// O
	'ouvrir_redacs' => 'Ouvrir aux rédacteurs du site  :', # MODIF
	'ouvrir_visiteurs_enregistres' => 'Ouvrir aux visiteurs enregistrés :', # MODIF
	'ouvrir_visiteurs_tous' => 'Ouvrir à tous les visiteurs du site :', # MODIF

	// P
	'pas_acces_espace_prive' => '<strong>Pas d\'accès à l\'espace privé :</strong> les rédacteurs n\'ont pas accès à l\'espace privé.', # MODIF
	'personne' => 'Personne', # NEW
	'petitions_qui' => '<strong>Signatures :</strong> qui peut modifier les signatures des pétitions :', # MODIF
	'publication' => 'Publication', # NEW
	'publication_qui' => 'Qui peut publier sur le site :', # NEW

	// R
	'redac_tous' => 'Tous les rédacteurs', # MODIF
	'redacs' => 'aux rédacteurs du site', # MODIF
	'redacteur' => 'rédacteur', # MODIF
	'redacteur_lire_stats' => '<strong>Rédacteur voit stats</strong> : les rédacteurs peuvent visualiser les statistiques.', # MODIF
	'redacteur_modifie_article' => '<strong>Rédacteur modifie proposés</strong> : chaque rédacteur peut modifier un article proposé à la publication, même s\'il n\'en est pas auteur.', # MODIF
	'refus_1' => '<p>Seuls les webmestres du site', # NEW
	'refus_2' => 'sont autorisés à modifier ces paramètres.</p>
<p>Pour en savoir plus, voir <a href="http://www.spip-contrib.net/-Autorite-">la documentation</a>.</p>', # MODIF
	'reglage_autorisations' => 'Réglage des autorisations', # MODIF

	// S
	'sauvegarde_qui' => 'Qui peut effectuer des <strong>sauvegardes</strong> ?', # NEW

	// T
	'tous' => 'Tous', # NEW
	'tout_deselectionner' => ' tout déselectionner', # MODIF

	// V
	'valeur_defaut' => '(valeur par défaut)', # MODIF
	'visiteur' => 'visiteur', # NEW
	'visiteurs_anonymes' => 'les visiteurs anonymes peuvent créer de nouvelles pages.', # MODIF
	'visiteurs_enregistres' => 'aux visiteurs enregistrés', # MODIF
	'visiteurs_tous' => 'à tous les visiteurs du site.', # MODIF

	// W
	'webmestre' => 'El webmaster',
	'webmestres' => 'Los webmaster'
);

?>
