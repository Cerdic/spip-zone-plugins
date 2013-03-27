<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;
 
$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'emailtospip_titre' => 'Publication par email',

	// C
  'cfg_import_statut' => 'Statut des articles importés',
  'cfg_email' => 'Email',   
  'cfg_email_pwd' => 'Mot de passe',
  'cfg_hote_imap' => 'Adresse du serveur IMAP',
  'cfg_hote_imap_explication' => 'ex. imap.gmail.com',   
  'cfg_hote_port' => 'Port',
  'cfg_hote_port_explication' => '143, 993 (SSL) ou 993/imap/ssl (gmail), .... <a href="http://php.net/manual/fr/function.imap-open.php">infos</a>',
  'cfg_inbox' => 'Dossier distant',
  'cfg_pwd' => 'Préfixe',
  'cfg_pwd_explication' => '<i>(Facultatif)</i> Phrase secrète à ajouter dans le sujet de l\'email pour qu\'il soit traité par le plugin.<br />Si ce champs est vide, tous les emails seront importés',
  'cfg_id_rubrique' => 'Id de la rubrique où importer les articles',

	// T
  'test_connection' => 'Test de connection au serveur IMAP',
  'test_connection_ok' => 'Authentification réussie !',
  'test_connection_notok' => 'Erreur: Impossible de se connecter à<br /><i>@connection@</i>',
	'titre_page_configurer_emailtospip' => 'Publication par email',
);

?>