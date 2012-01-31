<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'alt_logo_conf' => 'Logo zásuvného modulu Oznamy',
	'article_prive' => 'Publikovanie článkov',
	'article_prive_admins_restreints' => '<strong>Administrátori:</strong> administrátori s obmedzeniami dostávajú oznamy pri odoslaní článkov do ich rubriky',
	'article_prive_auteurs' => '<strong>Autori:</strong> autori dostávajú oznamy pri publikovaní svojich článkov',
	'article_propose_detail' => 'Článok "@titre@" bol odoslaný na publikovanie
	z(o)',
	'article_propose_sujet' => '[@nom_site_spip@] Odoslaný: @titre@',
	'article_propose_titre' => 'Článok bol odoslaný
	---------------',
	'article_propose_url' => 'Vous êtes invité à venir le consulter et à donner votre opinion
	dans le forum qui lui est attaché. Il est disponible à l\'adresse :', # NEW
	'article_publie_detail' => 'Článok "@titre@" práve publikoval(a) @connect_nom@.',
	'article_publie_sujet' => '[@nom_site_spip@] PUBLIKOVANÝ: @titre@',
	'article_publie_titre' => 'Publikovaný článok
	--------------',
	'article_valide_date' => 'Môže sa zmeniť, tento článok bude publikovaný',
	'article_valide_detail' => 'Článok "@titre@" schválil(a) @connect_nom@.',
	'article_valide_sujet' => '[@nom_site_spip@] POTVRDENÝ: @titre@',
	'article_valide_titre' => 'Článok bol schválený
	--------------',
	'article_valide_url' => 'Zatiaľ je zobrazený na tejto dočasnej adrese:',

	// B
	'breve_propose_detail' => 'Novinka "@titre@" sa posiela na publikovanie
	z(o)',
	'breve_propose_sujet' => '[@nom_site_spip@] Odoslaný: @titre@',
	'breve_propose_titre' => 'Novinka bola odoslaná
	---------------',
	'breve_propose_url' => 'Vous êtes invité à venir la consulter et à donner votre opinion
	dans le forum qui lui est attaché. Elle est disponible à l\'adresse :', # NEW
	'breve_publie_detail' => 'Novinku "@titre@" práve publikoval(a) @connect_nom@.',
	'breve_publie_sujet' => '[@nom_site_spip@] PUBLIKOVANÝ: @titre@',
	'breve_publie_titre' => 'Novinka bola publikovaná
	--------------',

	// E
	'evenement_notification' => 'Ďalšie udalosti môžu vyvolať odoslanie oznamu e-mailom.',

	// F
	'forum_prives_auteur' => '<strong>Autori:</strong> autori dostávajú oznamy odoslané z diskusných fór pod ich článkami alebo správami v súkromnej zóne.',
	'forum_prives_moderateur' => 'Zadajte e-mailovú adresu moderátora súkromných diskusných fór (alebo viac, oddelených čiarkami).',
	'forum_prives_thread' => '<strong>Téma:</strong> diskutujúci v rovnakom vlánke dostávajú oznami z (verejných) diskusných fór.',
	'forums_prives' => 'Súkromné diskusné fóra',
	'forums_public' => 'Verejné diskusné fóra',
	'forums_public_a_noter' => 'À noter : dans le cas des forums modérés à priori, seuls les auteurs ayant le droit de valider les forums sont notifiés lors de l\'envoi du forum ; les autres destinataires sont notifiés lors de la validation du message par le modérateur.', # NEW
	'forums_public_auteurs' => '<strong>Autori:</strong> autori dostávajú oznamy odoslané z diskusných fór pod ich článkami na verejne prístupnej stránke.',
	'forums_public_moderateur' => 'Zadajte e-mailovú adresu moderátora verejných diskusných fór (alebo viac, oddelených čiarkami).',
	'forums_public_thread' => '<strong>Téma:</strong> diskutujúci v rovnakom vlákne dostávajú oznamy z (verejných) diskusných fór.',

	// I
	'inscription' => 'Prihlásenie redaktorov',
	'inscription_admins' => 'Administrátori',
	'inscription_explication' => 'Ktorí autori dostanú oznamy pri zaregistrovaní nových redaktorov?',
	'inscription_label' => 'Stav',
	'inscription_statut_aucun' => 'Žiaden',
	'inscription_statut_webmestres' => 'Webmasteri',

	// L
	'lien_documentation' => '<a href="http://www.spip-contrib.net/Notifications" class="spip_out">Porov. s dokumentáciou</a>',

	// M
	'message_voir_configuration' => 'Zobraziť nastavenia oznamov',
	'messagerie_interne' => 'Súkromný odkazovač',
	'messagerie_interne_signaler' => '<strong>Signaler les nouveaux messages privés</strong> : activer cette option pour que le site envoie une notification lorsqu\'un rédacteur n\'a pas vu un nouveau message dans sa messagerie. Le système attend 20 minutes avant de notifier le rédacteur, de manière à ne pas spammer un rédacteur déjà en ligne dans l\'espace privé.', # NEW
	'moderateur' => '<strong>Moderátor</strong>',

	// N
	'notifications' => 'Oznamy',

	// S
	'signature_petition' => 'Podpisy pod petíciu',
	'signature_petition_moderateur' => 'Zadajte e-mailovú adresu moderátora petícií (alebo viac, oddelených čiarkami).',
	'suivis_perso' => 'Prispôsobené sledovanie',
	'suivis_perso_activer_option' => 'Si vous activez cette option, chaque visiteur qui se connecte sur cet URL de suivi sera enregistré dans la table <code>spip_auteurs</code>, avec le statut <code>6visiteur</code>. Il pourra alors voir l\'ensemble des messages qu\'il a signés sur le forum, régler ses options de notification, etc.', # NEW
	'suivis_perso_non' => 'Bez sledovania',
	'suivis_perso_oui' => 'Sledovať aktivitu',
	'suivis_perso_url_suivis' => '<strong>Ajouter une URL de suivi personnalisé</strong> dans chacun des emails de notification. À partir de cette URL, l\'utilisateur pourra configurer ses préférences individuelles de notification.', # NEW
	'suivis_public_article_thread' => 'TREBA: zaškrtávacie políčko pri každom článku/vlákne',
	'suivis_public_changer_email' => 'TREBA: zmeniť e-mail',
	'suivis_public_description' => 'Vous pourrez (quand ce sera fonctionnel...) y retrouver tous vos messages de forum, obtenir un fil RSS des réponses qui y seront apportées, choisir votre mode de notification, etc.', # NEW
	'suivis_public_notif_desactiver' => 'TREBA: zaškrtávacie políčko na zastavenie prijímania oznamov',
	'suivis_public_vos_forums' => 'Vaše diskusné fóra',
	'suivis_public_vos_forums_date' => 'Vaše diskusné fóra podľa dátumu',
	'suivis_public_votre_page' => 'Toto je vaša vlastná stránka na sledovanie webu.'
);

?>
