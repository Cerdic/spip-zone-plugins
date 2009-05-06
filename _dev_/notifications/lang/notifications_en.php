<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/_dev_/notifications/lang
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// Configuration page in CFG
'article_prive' => 'Articles in private area',
'article_prive_auteurs' => '<strong>Authors</strong>&nbsp;: Authors get notified when their article(s) is(are) published',
'forums_public' => 'Public forums',
'forums_public_auteurs' => '<strong>Authors</strong>&nbsp;: Authors get notified when new comments are posted to their article(s) on the public area.',
'forums_public_thread' => '<strong>Forum thread</strong>&nbsp;: Posters to the same thread get notified when a new comment is posted to the (public) thread.',
'forums_public_moderateur' => '<strong>Moderator</strong>&nbsp;: Please write here moderators\' email adress for public forums, coma separated.',
'forums_public_a_noter' => 'Note : if forums are awaiting validation from moderators before publication, only authors with rights to validate forums get notified when the comment is posted ; other recipients get notified only when moderators validate the comment.',
'forums_prives' => 'Forums in private area',
'forum_prives_auteur' => '<strong>Authors</strong>&nbsp;: Authors get notified when comments are posted to their article(s) or comment(s) on the private area.',
'forum_prives_thread' => '<strong>Forum thread</strong>&nbsp;: Posters to the same thread get notified when a new comment is posted to the (private) thread.',
'forum_prives_moderateur' => '<strong>Moderator</strong>&nbsp;: Please write here moderators\' email adress for private forums, coma separated.',
'messagerie_interne' => 'Private messages',
'messagerie_interne_signaler' => '<strong>Notify new private messages</strong>&nbsp;: activate this to get redactors notified when they haven\'t seen a Private Message had been sent to them. Redactors get notified 20 minutes after the Private Message is sent, in order to avoid spam, when the redactor is connected to the private area.',
'signature_petition' => 'Petition signatures',
'signature_petition_moderateur' => '<strong>Moderator</strong>&nbsp;: Please write here moderators\' email adress for petitions, coma separated.',
'suivis_perso' => 'Personnal notifications follow-up',
'suivis_perso_url_suivis' => '<strong>Add an URL for notifications follow-up</strong> in each notification email. CLicking on this URL will let the user configure his own notification preferences.',
'suivis_perso_activer_option' => 'If you activate this option, each visitor clicking this follow-up URL will be registered in the <code>spip_auteurs</code> DB table, with status <code>6visiteur</code>. He\'ll be then able to view all the messages he posted on the website, configure his own notification options, ...',
'suivis_perso_non' => 'No follow-up',
'suivis_perso_oui' => 'Follow-up activated',

// Public follow-up page
'suivis_public_votre_page' => 'This is your personnal Notifications follow-up for the website', // #NOM_SITE_SPIP
'suivis_public_description' => 'You will be able (when this will be operationnal...) find here all your comments on this website, get a RSS stream for their answers, choose your notification mode, ...',
'suivis_public_notif_desactiver' => 'TODO: Tickbox to stop notifications',
'suivis_public_article_thread' => 'TODO: Tickbox on each article/thread',
'suivis_public_changer_email' => 'TODO: Change your email',
'suivis_public_vos_forums' => 'Your forums',
'suivis_public_vos_forums_date' => 'Your forums, by date',

// Notification mails
// to come

);

?>
