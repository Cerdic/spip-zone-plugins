<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/autorite/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_mots_cles' => 'Activer la gestion par mots clef',
	'admin_complets' => 'Les administrateurs complets',
	'admin_restreints' => 'Administrateurs restreints ?',
	'admin_tous' => 'Tous les administrateurs (y compris restreints)',
	'administrateur' => 'administrateur',
	'admins' => 'Les administrateurs',
	'admins_redacs' => 'Administrateurs et Rédacteurs',
	'admins_rubriques' => 'les administrateurs associés à des rubriques ont :',
	'attention_crayons' => '<small><strong>Attention.</strong> Les réglages ci-dessous ne peuvent fonctionner que si vous utilisez un plugin proposant une interface d’édition (comme par exemple <a href="http://contrib.spip.net/Les-Crayons">les Crayons</a>).</small>',
	'attention_version' => 'Attention les choix suivants peuvent ne pas fonctionner	avec votre version de SPIP :',
	'auteur_message_advitam' => 'L’auteur du message, ad vitam',
	'auteur_message_heure' => 'L’auteur du message, pendant une heure',
	'auteur_modifie_article' => '<strong>Auteur modifie article</strong> : chaque rédacteur peut modifier les articles publiés dont il est l’auteur.
	<br />
	<i>N.B. : cette option s’applique aussi aux visiteurs enregistrés, s’ils sont auteurs et si une interface spécifique est prévue.</i>',
	'auteur_modifie_email' => '<strong>Rédacteur modifie email</strong> : chaque rédacteur peut modifier son email sur sa fiche d’informations personnelles.',
	'auteur_modifie_forum' => '<strong>Auteur modère forum</strong> : chaque rédacteur peut modérer le forum des articles dont il est l’auteur.',
	'auteur_modifie_petition' => '<strong>Auteur modère pétition</strong> : chaque rédacteur peut modérer la pétition des articles dont il est l’auteur.',

	// C
	'config_auteurs' => 'Configuration des auteurs',
	'config_auteurs_rubriques' => 'Quels types d’auteurs peut-on <b>associer à des rubriques</b> ?',
	'config_auteurs_statut' => 'A la création d’un auteur, quel est le <b>statut par défaut</b> ?',
	'config_plugin_qui' => 'Qui peut <strong>modifier la configuration</strong> des plugins (activation...) ?',
	'config_site' => 'Configuration du site',
	'config_site_qui' => 'Qui peut <strong>modifier la configuration</strong> du site ?',
	'crayons' => 'Crayons',

	// D
	'deja_defini' => 'Les autorisations suivantes sont déjà définies par ailleurs :',
	'deja_defini_suite' => 'Le plugin « Autorité » ne peut pas les modifier certains des réglages ci-dessous risquent par conséquent de ne pas fonctionner.
	<br />Pour régler ce problème, vous devrez vérifier si votre fichier <tt>mes_options.php</tt> (ou un autre plugin actif) a défini ces fonctions.',
	'descriptif_1' => 'Cette page de configuration est réservée aux webmestres du site :',
	'descriptif_2' => '<p>Si vous souhaitez modifier cette liste, veuillez éditer le fichier <tt>config/mes_options.php</tt> (le créer le cas échéant) et y indiquer la liste des identifiants des auteurs webmestres, sous la forme suivante :</p>
<pre>&lt;?php
  define(
    \'_ID_WEBMESTRES\',
    \'1:5:8\');
?&gt;</pre>
<p>A partir de SPIP 2.1, il est aussi possible de donner les droits de webmestre à un administrateur via la page d’édition de l’auteur.</p>
<p>A noter : les webmestres définis de cette manière n’ont plus besoin de procéder à l’authentification par FTP pour les opérations délicates (mise à niveau de la base de données, par exemple).</p>

<a href=\'http://contrib.spip.net/Autorite\' class=\'spip_out\'>Cf. documentation</a>
',
	'details_option_auteur' => '<small><br />Pour le moment, l’option « auteur » ne fonctionne que pour les auteurs enregistrés (forums sur abonnement, par exemple). Et, si elle est activée, les administrateurs du site ont aussi la capacité d’éditer les forums.
	</small>',
	'droits_des_auteurs' => 'Droits des auteurs',
	'droits_des_redacteurs' => 'Droits des rédacteurs',
	'droits_idem_admins' => 'les mêmes droits que tous les administrateurs',
	'droits_limites' => 'des droits limités à ces rubriques',

	// E
	'effacer_base_option' => '<small><br />L’option recommandée est « personne », l’option standard de SPIP est « les administrateurs » (mais toujours avec une vérification par FTP).</small>',
	'effacer_base_qui' => 'Qui peut <strong>effacer</strong> la base de données du site ?',
	'espace_publieur' => 'Espace de publication ouverte',
	'espace_publieur_detail' => 'Choisissez ci-dessous un secteur à traiter comme un espace de publication ouverte pour les rédacteurs et / ou visiteurs enregistrés (à condition d’avoir une interface, par exemple les crayons et un formulaire pour soumettre l’article) :',
	'espace_publieur_qui' => 'Voulez-vous ouvrir la publication — au-delà des administrateurs :',
	'espace_wiki' => 'Espace wiki',
	'espace_wiki_detail' => 'Choisissez ci-dessous un secteur à traiter comme un wiki, c’est-à-dire éditable par tous depuis l’espace public (à condition d’avoir une interface, par exemple les crayons) :',
	'espace_wiki_mots_cles' => 'Espace wiki par mots clef',
	'espace_wiki_mots_cles_detail' => 'Choisissez ci-dessous les mots clef qui activeront le mode wiki, c’est-à-dire éditable par tous depuis l’espace public (à condition d’avoir une interface, par exemple les crayons)',
	'espace_wiki_mots_cles_qui' => 'Voulez-vous ouvrir ce wiki au-delà des administrateurs :',
	'espace_wiki_qui' => 'Voulez-vous ouvrir ce wiki — au-delà des administrateurs :',

	// F
	'forums_qui' => '<strong>Forums :</strong> qui peut modifier le contenu des forums :',

	// I
	'icone_menu_config' => 'Autorité',
	'infos_selection' => '(vous pouvez sélectionner plusieurs secteurs avec la touche shift)',
	'interdire_admin' => 'Cochez les cases ci-dessous pour interdire aux administrateurs de créer',

	// M
	'mots_cles_qui' => '<strong>Mots-clés :</strong> qui peut créer et éditer les mots-clés :',

	// N
	'non_webmestres' => 'Ce réglage ne s’applique pas aux webmestres.',
	'note_rubriques' => '(Notez que seuls les administrateurs peuvent créer des rubriques, et, pour les administrateurs restreints, cela ne peut se faire que dans leurs rubriques.)',
	'nouvelles_rubriques' => 'de nouvelles rubriques à la racine du site',
	'nouvelles_sous_rubriques' => 'de nouvelles sous-rubriques dans l’arborescence.',

	// O
	'ouvrir_redacs' => 'Ouvrir aux rédacteurs du site :',
	'ouvrir_visiteurs_enregistres' => 'Ouvrir aux visiteurs enregistrés :',
	'ouvrir_visiteurs_tous' => 'Ouvrir à tous les visiteurs du site :',

	// P
	'pas_acces_espace_prive' => '<strong>Pas d’accès à l’espace privé :</strong> les rédacteurs n’ont pas accès à l’espace privé.',
	'personne' => 'Personne',
	'petitions_qui' => '<strong>Signatures :</strong> qui peut modifier les signatures des pétitions :',
	'publication' => 'Publication',
	'publication_qui' => 'Qui peut publier sur le site :',

	// R
	'redac_tous' => 'Tous les rédacteurs',
	'redacs' => 'aux rédacteurs du site',
	'redacteur' => 'rédacteur',
	'redacteur_lire_stats' => '<strong>Rédacteur voit stats</strong> : les rédacteurs peuvent visualiser les statistiques.',
	'redacteur_modifie_article' => '<strong>Rédacteur modifie proposés</strong> : chaque rédacteur peut modifier un article proposé à la publication, même s’il n’en est pas auteur.',
	'refus_1' => '<p>Seuls les webmestres du site',
	'refus_2' => 'sont autorisés à modifier ces paramètres.</p>
<p>Pour en savoir plus, voir <a href="http://contrib.spip.net/Autorite">la documentation</a>.</p>',
	'reglage_autorisations' => 'Réglage des autorisations',

	// S
	'sauvegarde_qui' => 'Qui peut effectuer des <strong>sauvegardes</strong> ?',

	// T
	'tous' => 'Tous',
	'tout_deselectionner' => ' tout déselectionner',

	// V
	'valeur_defaut' => '(valeur par défaut)',
	'visiteur' => 'visiteur',
	'visiteurs_anonymes' => 'les visiteurs anonymes peuvent créer de nouvelles pages.',
	'visiteurs_enregistres' => 'aux visiteurs enregistrés',
	'visiteurs_tous' => 'à tous les visiteurs du site.',

	// W
	'webmestre' => 'Le webmestre',
	'webmestres' => 'Les webmestres'
);

?>
