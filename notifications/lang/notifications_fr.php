<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/_dev_/notifications/lang
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// Page de configuration dans CFG
'evenement_notification' => 'Les &#233;v&#233;nements suivants peuvent d&#233;clencher une notification par email.',
'article_prive' => 'Publication d\'articles',
'article_prive_auteurs' => '<strong>Auteurs</strong>&nbsp;: les auteurs re&#231;oivent les notifications lors de la publication de leur(s) article(s)',
'article_prive_admins_restreints' => '<strong>Administrateurs</strong>&nbsp;: Les administrateurs restreints re&#231;oivent les notifications lors de proposition d\'articles dans leur rubrique',
'forums_public' => 'Forums publics',
'forums_public_auteurs' => '<strong>Auteurs</strong>&nbsp;: les auteurs re&#231;oivent les notifications des forums post&#233;s sous leurs articles dans le site public.',
'forums_public_thread' => '<strong>Fil de discussion</strong>&nbsp;: les participants au m&#234;me fil de discussion re&#231;oivent les notifications des forums (publics).',
'forums_public_moderateur' => 'Indiquez ci-dessous l\'adresse email du mod&#233;rateur des forums publics (ou plusieurs, s&#233;par&#233;s par des virgules).',
'forums_public_a_noter' => '&Agrave; noter : dans le cas des forums mod&#233;r&#233;s &#224; priori, seuls les auteurs ayant le droit de valider les forums sont notifi&#233;s lors de l\'envoi du forum ; les autres destinataires sont notifi&#233;s lors de la validation du message par le mod&#233;rateur.',
'forums_prives' => 'Forums priv&#233;s',
'forum_prives_auteur' => '<strong>Auteurs</strong>&nbsp;: les auteurs re&#231;oivent les notifications des forums post&#233;s sous leurs articles ou leurs messages dans le site priv&#233;.',
'forum_prives_thread' => '<strong>Fil de discussion</strong>&nbsp;: les participants au m&#234;me fil de discussion re&#231;oivent les notifications des forums (priv&#233;s).',
'forum_prives_moderateur' => 'Indiquez ci-dessous l\'adresse email du mod&#233;rateur des forums priv&#233;s (ou plusieurs, s&#233;par&#233;s par des virgules).',
'inscription' => 'Inscription des r&eacute;dacteurs',
'inscription_explication' => 'Quels auteurs re&#231;oivent les notifications lors de l\'inscription de nouveaux rédacteurs ?',
'inscription_label' => 'Statut',
'inscription_admins' => 'Administrateurs',
'inscription_statut_aucun' => 'Aucun',
'inscription_statut_webmestres' => 'Webmestres',
'messagerie_interne' => 'Messagerie interne',
'messagerie_interne_signaler' => '<strong>Signaler les nouveaux messages priv&#233;s</strong>&nbsp;: activer cette option pour que le site envoie une notification lorsqu\'un r&#233;dacteur n\'a pas vu un nouveau message dans sa messagerie. Le syst&#232;me attend 20 minutes avant de notifier le r&#233;dacteur, de mani&#232;re &#224; ne pas spammer un r&#233;dacteur d&#233;j&#224; en ligne dans l\'espace priv&#233;.',
'moderateur' => '<strong>Mod&#233;rateur</strong>',
'notifications' => 'Notifications',
'signature_petition' => 'Signatures de p&#233;tition',
'signature_petition_moderateur' => 'Indiquez ci-dessous l\'adresse email du mod&#233;rateur des p&#233;titions (ou plusieurs, s&#233;par&#233;s par des virgules).',
'suivis_perso' => 'Suivi personnalis&#233;',
'suivis_perso_url_suivis' => '<strong>Ajouter une URL de suivi personnalis&#233;</strong> dans chacun des emails de notification. &#192; partir de cette URL, l\'utilisateur pourra configurer ses pr&#233;f&#233;rences individuelles de notification.',
'suivis_perso_activer_option' => 'Si vous activez cette option, chaque visiteur qui se connecte sur cet URL de suivi sera enregistr&#233; dans la table <code>spip_auteurs</code>, avec le statut <code>6visiteur</code>. Il pourra alors voir l\'ensemble des messages qu\'il a sign&#233;s sur le forum, r&#233;gler ses options de notification, etc.',
'suivis_perso_non' => 'Pas de suivi',
'suivis_perso_oui' => 'Suivi activ&#233;',


// Page de suivis public
'suivis_public_votre_page' => 'Ceci est votre page personnalis&#233;e de suivi du site', // #NOM_SITE_SPIP
'suivis_public_description' => 'Vous pourrez (quand ce sera fonctionnel...) y retrouver tous vos messages de forum, obtenir un fil RSS des r&#233;ponses qui y seront apport&#233;es, choisir votre mode de notification, etc.',
'suivis_public_notif_desactiver' => 'TODO: case à cocher pour ne plus recevoir de notifications',
'suivis_public_article_thread' => 'TODO: case à cocher sur chaque article/thread',
'suivis_public_changer_email' => 'TODO: changer d\'email',
'suivis_public_vos_forums' => 'Vos forums',
'suivis_public_vos_forums_date' => 'Vos forums, par date',
'suivis_public_info_email' => 'Adresse email',

// Mails de suivis


// à venir

);

?>
