<?php
/**
* saveauto : plugin de sauvegarde automatique de la base de donn�es de SPIP
*  
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
*  
**/
	$GLOBALS['i18n_saveauto_fr'] = array(
			    // saveauto_pipeline.php
						 'saveauto' => 'Sauvegarde automatique',
						 'sauvegarde_ok' => 'Sauvegarde de la base effectuee avec succes !',
                     'sauvegarde_ok_mail' => 'Sauvegarde de la base et envoi par mail effectues avec succes !',
						 'maintenance' => 'Maintenance',
						 'probleme_sauve_base' => 'Probleme de sauvegarde de la base ',
					
					// saveauto_fonctions.php
						 'message_MIME' => 'Ceci est un message au format MIME.',
						 'repertoire' => 'le r&eacute;pertoire ',
						 'corriger_config' => ' n\'existe pas, corrigez la configuration !',
						 'impossible_liste_tables' => 'impossible de lister les tables de la base ',
						 'impossible_creer' => 'impossible de cr&eacute;er ',
						 'verifier_ecriture' => ', v�rifiez les droits d\'&eacute;criture du r&eacute;pertoire ',
						 'fichier_genere' => 'Ce fichier est genere par le plugin saveauto',
						 'base' => 'Base : ',
						 'serveur' => 'Serveur : ',
						 'date' => 'Date : ',
						 'os' => 'OS Serveur : ',
						 'phpversion' => 'Version PHP : ',
						 'mysqlversion' => 'Version mySQL : ',
						 'ipclient' => 'IP Client : ',
						 'compatible_phpmyadmin' => 'Fichier SQL 100% compatible PHPMyadmin',
						 'debut_fichier' => 'debut du fichier',
						 'structure_table' => 'Structure de la table ',
						 'donnees_table' => 'Donnees de ',
						 'probleme_donnees' => 'probleme avec les donnees de ',
						 'corruption' => ', corruption possible !',
						 'fin_fichier' => 'fin du fichier',
						 'probleme_envoi' => 'probleme d\'envoi de ',
						 'adresse' => ' a l\'adresse : ',
						 'config_inadaptee' => 'Plugin saveauto : configuration non-adaptee,',
						 'mail_absent' => ' votre serveur n\'assure pas les fonctions d\'envoi de mail !',
						 
					// cfg_saveauto.php
						 'config_saveauto' => 'Sauvegarde automatis&eacute;e de la base de donn&eacute;es',
						 'plugin_saveauto' => 'Plugin saveauto : configuration',
						 'erreur_ecrire_conf' => 'Erreur lors de la cr&eacute;ation du fichier de configuration plugins/saveauto/inc/saveauto_conf.php : v&eacute;rifiez que le fichier est accessible en &eacute;criture pour le serveur apache',
						 'help_titre' => 'Cette page vous permet de configurer les options de sauvegarde automatique de la base.',
						 'version' => 'Version : ',
						 'options_config' => 'Options configur&eacute;es : ',
						 'compression_gz' => 'Zipper le fichier de sauvegarde : ',
						 'oui' => 'oui',
						 'non' => 'non',
						 'attention' => 'Attention :',
						 'compression_impossible' => ' la sauvegarde ne pourra pas &ecirc;tre compress&eacute;e car cette fonctionnalit&eacute; n\'est pas disponible sur votre serveur (support de Zlib pas activ&eacute; dans php.ini).',
						 'tables_acceptes' => 'Tables accept&eacute;es : ',
						 'pas_envoi' => ' Les sauvegardes ne seront pas envoy&eacute;es !',
						 'toutes' => ' toutes',
						 'prefixe_nom' => ' celles qui ont comme pr&eacute;fixe de nom ',
						 'donnees_ignorees' => 'Donn&eacute;es ignor&eacute;es : ',
						 'aucune' => ' aucune',
						 'repertoire_stockage' => 'R&eacute;pertoire de stockage : ',
						 'frequence' => 'Fr&eacute;quence de la sauvegarde : tous les ',
						 'jours' => ' jours',
						 'message_succes' => 'Affiche un message de succ&egrave;s si sauvegarde OK : ',
						 'a_eviter' => ' pour les tables ayant dans leur nom : ',
						 'nom_base' => 'Nom de la base SPIP : ',
						 'obsolete_jours' => 'Sauvegardes consid&eacute;r&eacute;es obsol&egrave;tes apr&egrave;s : ',
						 'envoi_mail' => 'Sauvegardes envoy&eacute;es ',
						 'structure_donnees' => 'El&eacute;ments &agrave; sauvegarder : ',
						 'structure' => ' structure des tables : ',
						 'donnees' => ' donn&eacute;es des tables : ',
						 'prefixe_sauvegardes' => 'Pr&eacute;fixe pour les sauvegardes : ',
						 'acces_redac' => 'Sauvegarde d&eacute;clench&eacute;e lors de la connexion d\'un auteur : ',
						 
						 
						 'valider' => 'Valider',
						 'sauvegardes_faites' => 'Sauvegardes existantes : ',
						 'repertoire_absent' => ' n\'existe pas, modifiez la configuration !',
						 'restauration' => 'Restauration d\'une sauvegarde :',
						 'help_restauration' => '<strong>Attention !!!</strong> les sauvegardes r&eacute;alis&eacute;es ne sont <strong>pas au format de celles de SPIP</strong> :
				  	 										 		Inutile d\'essayer de les utiliser avec l\'outil d\'administration de Spip.<br /><br />
																		Pour toute restauration il faut utiliser l\'interface <strong>phpmyadmin</strong> de votre 
																		serveur de base de donn&eacute;es : dans l\'onglet <strong>"SQL"</strong> utiliser le bouton 
																		<strong>"Emplacement du fichier texte"</strong> pour s&eacute;lectionner le fichier de sauvegarde 
																		(cocher l\'option "gzipp&eacute;" si n&eacute;cessaire) puis valider.<br /><br />
																		Les sauvegardes <strong>xxxx.gz</strong> ou <strong>xxx.sql</strong> contiennent un fichier au format SQL avec les commandes 
																		permettant d\'<strong>effacer</strong> les tables existantes du SPIP et de les <strong>remplacer</strong> par les 
																		donn&eacute;es archiv&eacute;es. Les donn&eacute;es <strong>plus r&eacute;centes</strong> que celles de la sauvegarde seront donc <strong>PERDUES</strong>!',
						 'help_envoi' => 'optionnel : envoi de la sauvegarde par mail si vous mettez une adresse de destinataire',
						 'help_obsolete' => 'd&eacute;termine &agrave; partir de combien de jours une archive est consid&eacute;r&eacute;e comme obsol&egrave;te et automatiquement supprim&eacute;e du serveur. 
						 								 		Mettez -1 pour d&eacute;sactiver cette fonctionnalit&eacute;',
						 'help_redac' => 'oui pour lancer le script lors de la connexion d\'un r&eacute;dacteur (si non : uniquement les admins)',
						 'help_msg' => 'affiche un message de succ&egrave;s dans l\'interface',
						 'help_gz' => 'si non les sauvegardes sont au format .sql',
						 'help_structure' => 'sauvegarde la structure des tables',
						 'help_donnees' => 'sauvegarde les donn&eacute;es des tables',
						 'help_insert' => 'clause INSERT avec nom des champs',
						 'help_accepter' => 'optionnel : ne sauver que les tables ayant la cha&icirc;ne sp&eacute;cifi&eacute;e dans leur nom, ex : annuaire_, important, machin. 
						 								 		Ne mettez rien pour accepter toutes les tables. S&eacute;parez les diff&eacute;rents noms par le symbole point-virgule (;)',
						 'help_eviter' => 'optionnel : si la table contient dans son nom la cha&icirc;ne sp&eacute;cifi&eacute;e : les donn&eacute;es sont ignor&eacute;es (pas la structure). S&eacute;parez les diff&eacute;rents noms par le symbole point-virgule (;)',
						 'help_rep' => 'r&eacute;pertoire o&ugrave; stocker les fichiers (chemin &agrave; partir de la <strong>racine</strong> du SPIP, tmp/data/ par ex). <strong>DOIT</strong> se terminer par un /.',
						 'help_prefixe' => 'optionnel : mettre un pr&eacute;fixe au nom du fichier de sauvegarde',
						 'help_acces_redac' => 'si non, les sauvegardes ne sont d&eacute;clench&eacute;es que lors de la connexion des administrateurs'
						 

	);

?>