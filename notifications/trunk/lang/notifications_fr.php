<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/notifications/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'alt_logo_conf' => 'Logo du plugin Notifications',

	// 0
	'0' => '--------------',

	// A
	'article_prive' => 'Publication d’articles',
	'article_prive_admins_restreints' => '<strong>Administrateurs restreints</strong> : Les administrateurs restreints reçoivent les notifications lors de proposition d’articles dans leur rubrique. Pour les administrateurs généralistes, voir <a href="?exec=configurer_interactions#suivi_edito_non">l’outil par défaut de SPIP</a>.',
	'article_prive_auteurs' => '<strong>Auteurs</strong> : les auteurs reçoivent les notifications lors de la proposition, publication ou suppression de leur(s) article(s).',
	'article_prive_auteurs_refus' => '<strong>Auteurs</strong> : les auteurs reçoivent les notifications lors du refus de publications de leur(s) article(s).',
	'article_prive_publieur' => 'Si l’un des auteurs est celui qui publie l’article, ne pas le prévenir.',
	'article_propose_detail' => 'L’article "@titre@" est proposé à la publication
	depuis',
	'article_propose_sujet' => '[@nom_site_spip@] Proposé : @titre@',
	'article_propose_titre' => 'Article proposé
	---------------',
	'article_propose_url' => 'Vous êtes invité à venir le consulter et à donner votre opinion
	dans le forum qui lui est attaché. Il est disponible à l’adresse :',
	'article_publie_detail' => 'L’article "@titre@" vient d’être publié par @connect_nom@.',
	'article_publie_sujet' => '[@nom_site_spip@] PUBLIE : @titre@',
	'article_publie_titre' => 'Article publié
	--------------',
	'article_refuse_detail' => 'L’article "@titre@" vient d’être refusé par @connect_nom@.',
	'article_refuse_sujet' => '[@nom_site_spip@] REFUSE : @titre@',
	'article_refuse_titre' => 'Article refusé',
	'article_valide_date' => 'Sous réserve de changement, cet article sera publié',
	'article_valide_detail' => 'L’article "@titre@" a été validé par @connect_nom@.',
	'article_valide_sujet' => '[@nom_site_spip@] VALIDE : @titre@',
	'article_valide_titre' => 'Article validé
	--------------',
	'article_valide_url' => 'En attendant, il est visible à cette adresse temporaire :',

	// B
	'bouton_changer_pass' => 'Changer mon mot de passe',
	'bouton_finir_inscription' => 'Finir mon inscription',
	'breve_propose_detail' => 'La brève "@titre@" est proposée à la publication
	depuis',
	'breve_propose_sujet' => '[@nom_site_spip@] Propose : @titre@',
	'breve_propose_titre' => 'Brève proposée
	---------------',
	'breve_propose_url' => 'Vous êtes invité à venir la consulter et à donner votre opinion
	dans le forum qui lui est attaché. Elle est disponible à l’adresse :',
	'breve_publie_detail' => 'La brève "@titre@" vient d’être publiée par @connect_nom@.',
	'breve_publie_sujet' => '[@nom_site_spip@] PUBLIE : @titre@',
	'breve_publie_titre' => 'Brève publiée
	--------------',

	// E
	'evenement_notification' => 'Les événements suivants peuvent déclencher une notification par email.',

	// F
	'form_forum_confirmer_email' => 'Pour confirmer votre adresse email, cliquez sur le bouton ci-dessous : ',
	'forum_prives_auteur' => '<strong>Auteurs</strong> : les auteurs reçoivent les notifications des forums postés sous leurs articles ou leurs messages dans le site privé.',
	'forum_prives_moderateur' => 'Indiquez ci-dessous l’adresse email du modérateur des forums privés (ou plusieurs, séparées par des virgules).',
	'forum_prives_thread' => '<strong>Fil de discussion</strong> : les participants au même fil de discussion reçoivent les notifications des forums (privés).',
	'forums_admins_restreints' => '<strong>Administrateurs</strong> : les administrateurs  restreints reçoivent les notifications lors de nouveaux messages publiés sur la branche.',
	'forums_limiter_rubriques_explication' => 'Renseignez ici les identifiants de chaque rubrique où vous voulez déclencher les notifications, séparé par une virgule. ex : "11,26"',
	'forums_limiter_rubriques_label' => 'Limiter à ces rubriques :',
	'forums_prives' => 'Forums privés',
	'forums_public' => 'Forums publics',
	'forums_public_a_noter' => 'À noter : dans le cas des forums modérés à priori, seuls les auteurs ayant le droit de valider les forums sont notifiés lors de l’envoi du forum ; les autres destinataires sont notifiés lors de la validation du message par le modérateur.',
	'forums_public_article' => '<strong>Réponse à l’article</strong> : les personnes ayant répondu publiquement à l’article recoivent les notifications des forums (publics) de l’article (utile pour les forums « à plat »). Sont exclus les messages supprimés ou marqués comme SPAM.',
	'forums_public_auteurs' => '<strong>Auteurs</strong> : les auteurs reçoivent les notifications des forums postés sous leurs articles dans le site public.',
	'forums_public_liste' => '<strong>Adresse supplémentaire : </strong>une adresse email qui recevra les messages publiés en public (ou plusieurs séparées par des virgules), utile par exemple pour les forums non modérés.',
	'forums_public_moderateur' => 'Indiquez ci-dessous l’adresse email du modérateur des forums publics (ou plusieurs, séparées par des virgules).',
	'forums_public_thread' => '<strong>Fil de discussion</strong> : les participants au même fil de discussion reçoivent les notifications des forums (publics). Sont exclus les messages supprimés ou marqués comme SPAM.',

	// I
	'info_diffusion_nouveaute_partielle_non' => 'Diffuser le contenu complet',
	'info_diffusion_nouveaute_partielle_oui' => 'Ne diffuser qu’un extrait',
	'info_diffusion_nouveautes' => 'Contenu des mails annonçant les nouveautés du site',
	'info_lien_publier_commentaire' => 'Publier ce commentaire',
	'info_lien_signaler_spam_commentaire' => 'Signaler comme SPAM',
	'info_lien_supprimer_commentaire' => 'Supprimer ce commentaire',
	'info_moderation_confirmee_off' => 'Le message #@id_forum@ a bien été supprimé',
	'info_moderation_confirmee_publie' => 'Le message #@id_forum@ a bien été publié',
	'info_moderation_confirmee_spam' => 'Le message #@id_forum@ a bien été signalé en SPAM',
	'info_moderation_deja_faite' => 'Le message #@id_forum@ a déjà été modéré en "@statut@".',
	'info_moderation_interdite' => 'Vous n’avez pas le droit de modérer ce message',
	'info_moderation_lien_titre' => 'Modérer ce message depuis l’espace privé',
	'info_moderation_url_perimee' => 'Ce lien de modération n’est plus valide.',
	'info_nouveau_commentaire' => 'Nouveau commentaire',
	'inscription' => 'Inscription des rédacteurs',
	'inscription_admins' => 'Administrateurs',
	'inscription_explication' => 'Quels auteurs reçoivent les notifications lors de l’inscription de nouveaux rédacteurs ?',
	'inscription_label' => 'Statut',
	'inscription_statut_aucun' => 'Aucun',
	'inscription_statut_webmestres' => 'Webmestres',

	// L
	'lien_documentation' => '<a href="https://contrib.spip.net/Notifications" class="spip_out">Cf. documentation</a>',
	'limiter_rubriques_explication' => 'Renseignez ici les identifiants de chaque rubrique où vous voulez déclencher les notifications, séparé par une virgule. ex : "11,26"',
	'limiter_rubriques_label' => 'Limiter à ces rubriques :',

	// M
	'message_a_valider' => 'Message à valider : ',
	'message_fin_explication' => 'Message qui sera affiché en fin d’email (permettant d’indiquer pourquoi les gens reçoivent ce mail, méthode de désabonnement…)',
	'message_fin_label' => 'Message en fin d’email : ',
	'message_spam_a_confirmer' => 'SPAM à confirmer : ',
	'message_voir_configuration' => 'Voir la configuration des notifications',
	'messagerie_interne' => 'Messagerie interne',
	'messagerie_interne_signaler' => '<strong>Signaler les nouveaux messages privés</strong> : activer cette option pour que le site envoie une notification lorsqu’un rédacteur n’a pas vu un nouveau message dans sa messagerie. Le système attend 20 minutes avant de notifier le rédacteur, de manière à ne pas spammer un rédacteur déjà en ligne dans l’espace privé.',
	'moderateur' => '<strong>Modérateur</strong>',
	'moderation_email_protection_antibot' => '<b>Protéger la modération par email contre les bots</b> qui cliquent sur les liens contenus dans les emails',

	// N
	'notifications' => 'Notifications',

	// P
	'pass_mail_passcookie_1' => 'Pour retrouver votre accès au site @nom_site_spip@, cliquez sur le bouton : ',
	'pass_mail_passcookie_2' => 'Vous pourrez alors entrer un nouveau mot de passe et vous reconnecter au site.',

	// S
	'signature_petition' => 'Signatures de pétition',
	'signature_petition_moderateur' => 'Indiquez ci-dessous l’adresse email du modérateur des pétitions (ou plusieurs, séparées par des virgules).',
	'suivi_texte_acces_page' => 'Modifier mes abonnements aux discussions',
	'suivis_perso' => 'Suivi personnalisé',
	'suivis_perso_activer_option' => 'Si vous activez cette option, chaque visiteur qui se connecte sur cet URL de suivi sera enregistré dans la table <code>spip_auteurs</code>, avec le statut <code>6visiteur</code>. Il pourra alors voir l’ensemble des messages qu’il a signés sur le forum, régler ses options de notification, etc.',
	'suivis_perso_non' => 'Pas de suivi',
	'suivis_perso_oui' => 'Suivi activé',
	'suivis_perso_url_suivis' => '<strong>Ajouter une URL de suivi personnalisé</strong> dans chacun des emails de notification. À partir de cette URL, l’utilisateur pourra configurer ses préférences individuelles de notification.',
	'suivis_public_article_thread' => 'TODO : case à cocher sur chaque article/thread',
	'suivis_public_changer_email' => 'TODO : changer d’email',
	'suivis_public_description' => 'Vous pourrez (quand ce sera fonctionnel...) y retrouver tous vos messages de forum, obtenir un fil RSS des réponses qui y seront apportées, choisir votre mode de notification, etc.',
	'suivis_public_notif_desactiver' => 'TODO : case à cocher pour ne plus recevoir de notifications',
	'suivis_public_vos_forums' => 'Vos forums',
	'suivis_public_vos_forums_date' => 'Vos forums, par date',
	'suivis_public_votre_page' => 'Ceci est votre page personnalisée de suivi du site',

	// T
	'titre_moderation' => 'Modération'
);
