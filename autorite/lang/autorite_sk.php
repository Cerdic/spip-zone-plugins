<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_mots_cles' => 'Aktivovať riadenie podľa kľúčových slov',
	'admin_complets' => 'Plnoprávni administrátori',
	'admin_restreints' => 'Administrátori s obmedzeniami?',
	'admin_tous' => 'Všetci administrátori (vrátane tých s obmedzeniami)',
	'administrateur' => 'administrátor',
	'admins' => 'Administrátori',
	'admins_redacs' => 'Administrátori a redaktori',
	'admins_rubriques' => 'administrátori prepojení s rubrikami majú:',
	'attention_crayons' => '<small><strong>Attention.</strong> Les réglages ci-dessous ne peuvent fonctionner que si vous utilisez un plugin proposant une interface d\'édition (comme par exemple <a href="http://www.spip-contrib.net/Les-Crayons">les Crayons</a>).</small>', # NEW
	'attention_version' => 'Majte na pamäti, že tieto nastavenia nemusia vo vašej verzii SPIPu fungovať:',
	'auteur_message_advitam' => 'Autor správy natrvalo',
	'auteur_message_heure' => 'Autor správy na hodinu',
	'auteur_modifie_article' => '<strong>Auteur modifie article</strong> : chaque rédacteur peut modifier les articles publiés dont il est l\'auteur (et, par conséquent, modérer le forum et la pétition associés).
	<br />
	<i>N.B. : cette option s\'applique aussi aux visiteurs enregistrés, s\'ils sont auteurs et si une interface spécifique est prévue.</i>', # NEW
	'auteur_modifie_email' => '<strong>Rédacteur modifie email</strong> : chaque rédacteur peut modifier son email sur sa fiche d\'informations personnelles.', # NEW
	'auteur_modifie_forum' => '<strong>Auteur modère forum</strong> : chaque rédacteur peut modérer le forum des articles dont il est l\'auteur.', # NEW
	'auteur_modifie_petition' => '<strong>Auteur modère pétition</strong> : chaque rédacteur peut modérer la pétition des articles dont il est l\'auteur.', # NEW

	// C
	'config_auteurs' => 'Nastavenia autorov',
	'config_auteurs_rubriques' => 'Aký typ autorov môže byť <b>prepojený s rubrikami?</b>',
	'config_auteurs_statut' => 'Aká je <b>predvolená funkcia</b> pri zápise autora?',
	'config_plugin_qui' => 'Kto môže <strong>meniť nastavenia</strong>  zásuvných modulov (aktivácia, atď.)?',
	'config_site' => 'Nastavenia stránky',
	'config_site_qui' => 'Kto môže<strong>meniť nastavenia</strong> stránky?',
	'crayons' => 'Crayons',

	// D
	'deja_defini' => 'Inde už boli definované tieto povolenia:',
	'deja_defini_suite' => 'Le plugin « Autorité » ne peut pas les modifier certains des réglages ci-dessous risquent par conséquent de ne pas fonctionner.
	<br />Pour régler ce problème, vous devrez vérifier si votre fichier <tt>mes_options.php</tt> (ou un autre plugin actif) a défini ces fonctions.', # NEW
	'descriptif_1' => 'Táto stránka s nastaveniami je vyhradená pre webmasterov stránky:',
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
', # NEW
	'details_option_auteur' => '<small><br />Pour le moment, l\'option « auteur » ne fonctionne que pour les auteurs enregistrés (forums sur abonnement, par exemple). Et, si elle est activée, les administrateurs du site ont aussi la capacité d\'éditer les forums.
	</small>', # NEW
	'droits_des_auteurs' => 'Práva autorov',
	'droits_des_redacteurs' => 'Práva redaktorov',
	'droits_idem_admins' => 'rovnaké práva pre všetkých administrátorov',
	'droits_limites' => 'obmedzené práva na tieto rubriky',

	// E
	'effacer_base_option' => '<small><br />L\'option recommandée est « personne », l\'option standard de SPIP est « les administrateurs » (mais toujours avec une vérification par FTP).</small>', # NEW
	'effacer_base_qui' => 'Kto môže <strong>mazať</strong> databázu stránky?',
	'espace_publieur' => 'Otvoriť zónu publikovania',
	'espace_publieur_detail' => 'Choisissez ci-dessous un secteur à traiter comme un espace de publication ouverte pour les rédacteurs et / ou visiteurs enregistrés (à condition d\'avoir une interface, par exemple les crayons et un formulaire pour soumettre l\'article) :', # NEW
	'espace_publieur_qui' => 'Chcete otvoriť publikovanie — za administrátormi:',
	'espace_wiki' => 'Zóna wiki',
	'espace_wiki_detail' => 'Choisissez ci-dessous un secteur à traiter comme un wiki, c\'est-à-dire éditable par tous depuis l\'espace public (à condition d\'avoir une interface, par exemple les crayons) :', # NEW
	'espace_wiki_mots_cles' => 'Zóna wiki podľa kľúčových slov',
	'espace_wiki_mots_cles_detail' => 'Choisissez ci-dessous les mots clef qui activeront le mode wiki, c\'est-à-dire éditable par tous depuis l\'espace public (à condition d\'avoir une interface, par exemple les crayons)', # NEW
	'espace_wiki_mots_cles_qui' => 'Chcete otvoriť túto stránku wiki za administrátormi:',
	'espace_wiki_qui' => 'Chcete otvoriť túto stránku wiki — za administrátormi:',

	// F
	'forums_qui' => '<strong>Diskusné fóra:</strong> kto môže meniť obsah diskusných fór:',

	// I
	'icone_menu_config' => 'Autorita',
	'infos_selection' => '(viac možností môžete vybrať klávesom Shift)',
	'interdire_admin' => 'Zaškrtnite polia, ak chcete zakázať administrátorom vytváranie',

	// M
	'mots_cles_qui' => '<strong>Kľúčové slová:</strong> kto môže vytvárať a upravovať kľúčové slová:',

	// N
	'non_webmestres' => 'Toto nastavenie neplatí pre webmasterov.',
	'note_rubriques' => '(Notez que seuls les administrateurs peuvent créer des rubriques, et, pour les administrateurs restreints, cela ne peut se faire que dans leurs rubriques.)', # NEW
	'nouvelles_rubriques' => 'de nouvelles rubriques à la racine du site', # NEW
	'nouvelles_sous_rubriques' => 'de nouvelles sous-rubriques dans l\'arborescence.', # NEW

	// O
	'ouvrir_redacs' => 'Otvoriť redaktorom stránky:',
	'ouvrir_visiteurs_enregistres' => 'Otvoriť zaregistrovaným návštevníkom:',
	'ouvrir_visiteurs_tous' => 'Otvoriť všetkým návštevníkom stránky:',

	// P
	'pas_acces_espace_prive' => '<strong>Pas d\'accès à l\'espace privé :</strong> les rédacteurs n\'ont pas accès à l\'espace privé.', # NEW
	'personne' => 'Hocikto',
	'petitions_qui' => '<strong>Podpisy:</strong> kto môže meniť podpisy pod petíciami:',
	'publication' => 'Publikovanie',
	'publication_qui' => 'Kto môže publikovať na stránke:',

	// R
	'redac_tous' => 'Všetci redaktori',
	'redacs' => 'redaktorom stránky',
	'redacteur' => 'redaktor',
	'redacteur_lire_stats' => '<strong>Rédacteur voit stats</strong> : les rédacteurs peuvent visualiser les statistiques.', # NEW
	'redacteur_modifie_article' => '<strong>Rédacteur modifie proposés</strong> : chaque rédacteur peut modifier un article proposé à la publication, même s\'il n\'en est pas auteur.', # NEW
	'refus_1' => '<p>Iba webmasteri stránky',
	'refus_2' => 'sont autorisés à modifier ces paramètres.</p>
<p>Pour en savoir plus, voir <a href="http://www.spip-contrib.net/-Autorite-">la documentation</a>.</p>', # NEW
	'reglage_autorisations' => 'Nastavenie povolení',

	// S
	'sauvegarde_qui' => 'Kto môže vytvárať <strong>zálohy?</strong>',

	// T
	'tous' => 'Všetko',
	'tout_deselectionner' => ' odznačiť všetko',

	// V
	'valeur_defaut' => '(predvolená hodnota)',
	'visiteur' => 'návštevník',
	'visiteurs_anonymes' => 'anonymní návštevníci môžu vytvárať nové stránky.',
	'visiteurs_enregistres' => 'prihláseným návštevníkom',
	'visiteurs_tous' => 'všetkým návštevníkom stránky.',

	// W
	'webmestre' => 'Webmaster',
	'webmestres' => 'Webmasteri'
);

?>
