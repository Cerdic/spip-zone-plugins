<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;
 
$GLOBALS[$GLOBALS['idx_lang']] = array(

	// F
	'facteurmonitoring_titre' => 'Monitoring du Facteur',

	// C
	'cfg_exemple' => 'Exemple',
	'cfg_exemple_explication' => 'Explication de cet exemple',
	'cfg_titre_parametrages' => 'Paramétrages',
  'cfg_titre_parametrages_option_page' => 'Monitoring par consultation de page',
  'cfg_parametrages_option_page_info' => 'Le principe du plugin est d\'afficher une page publique affichant l\'état de fonctionnement de
  l\'envoi d\'email par le facteur. En cas de bon fonctionnement on affiche OK sinon NOTOK.
  Cette page pourra être monitoré par des applications tierces.',
  'cfg_email_dest' => 'Email à envoyer',
  'cfg_destinataire'  => 'Email',
  'cfg_destinataire_info' => 'Boite email vérifiant l\'email de monitoring',
  'cfg_sujet' => 'Sujet', 
  'cfg_sujet_explication' => '(Facultatif) Titre personnalisé de l\'email (permet par exemple, d\'ajouter un préfixe pour filtrer l\'email avec un logiciel de messagerie)', 
  'cfg_cle' => 'Clé secrète',
  'cfg_cle_explication' => 'Indiquer une chaine de caractères secrète permettant de limiter l\'accès à la page',
  'cfg_frequence' => 'Frèquence d\'envoi',
  'cfg_frequence_explication' => 'Délai en heures entre chaque envoi d\'email.',
  'cfg_page_appel' => 'Adresse de la page à appeler par votre script de monitoring :',
  'cfg_email_pwd' => 'Mot de passe',
  'cfg_hote_imap' => 'Adresse du serveur IMAP',
  'cfg_hote_imap_explication' => 'ex. imap.gmail.com',   
  'cfg_hote_port' => 'Port',
  'cfg_hote_port_explication' => '143, 993 (SSL) ou 993/imap/ssl (gmail), .... <a href="http://php.net/manual/fr/function.imap-open.php">infos</a>',
  'cfg_inbox' => 'Dossier distant',
  'cfg_pwd' => 'Préfixe',
  
	
  // T
	'titre_page_configurer_facteurmonitoring' => 'Monitoring du Facteur',
  'test_connection' => 'Etape 2: Test de connection au serveur IMAP',
  'test_connection_ok' => 'OK. Authentification à la boite email réussie !',
  'test_connection_notok' => 'Erreur: Impossible de se connecter avec les parametres renseignés. Merci de corriger.',
	'titre_page_configurer_emailtospip' => 'Publication par email',
  'test_imap' => 'Etape 1: Vérification de la présence de IMAP dans votre configuration PHP',
  'test_imap_exist_true' => 'OK. Les fonctions IMAP sont disponibles.',
  'test_imap_exist_false' => 'Erreur : Les fonctions IMAP ne sont pas disponibles dans votre configuration PHP. Le plugin ne peut pas fonctionner.',
  
  // N
  'no-reply' => 'Ceci est un message automatique pour vérifier le bon fonctionnement des envois d\'email du site @site@ . Merci de ne pas répondre',

);

?>