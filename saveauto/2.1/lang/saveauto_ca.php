<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'attention' => 'Advert&egrave;ncia:',

	// C
	'colonne_date' => 'Data',
	'colonne_nom' => 'Cognom',
	'colonne_taille_octets' => 'Mida',

	// E
	'envoi_mail' => 'C&ograve;pies de seguretat enviades',
	'erreur_config_inadaptee_mail' => 'Configuraci&oacute; no adaptada, el vostre servidor no garanteix les funcions d\'enviament de correus electr&ograve;nics!',
	'erreur_impossible_creer_verifier' => 'Impossible crear el fitxer @fichier@, verifiqueu els drets d\'escriptura del directori @rep_bases@.',
	'erreur_impossible_liste_tables' => 'Impossible llistar les taules de la base de dades.',
	'erreur_mail_fichier_lourd' => 'L\'arxiu de la c&ograve;pia de seguretat &eacute;s massa pesat per ser enviat per correu electr&ograve;nic. Podeu recuperar-lo des de la interf&iacute;cie d\'administraci&oacute; o per FTP seguint la ruta: @fichier@',
	'erreur_mail_sujet' => 'Error de c&ograve;pia de seguretat SQL',
	'erreur_probleme_donnees_corruption' => 'Problema amb les dades de @table@, &eacute;s possible que estiguin corruptes!',
	'erreur_repertoire_inaccessible' => 'El directori @rep@ &eacute;s inaccessible a l\'escriptura.',
	'erreur_repertoire_inexistant' => 'El directori @rep@ no existeix. Verifiqueu la vostra configuraci&oacute;.',
	'erreur_sauvegarde_intro' => 'El missatge d\'error &eacute;s el seg&uuml;ent:',
	'erreurs_config' => 'Error(s) a la configuraci&oacute;',

	// H
	'help_accepter' => 'Opcional: nom&eacute;s guardeu les taules que tinguin la cadena especificada en el seu nom. Exemple: anuari_, important, enginy.
             No hi poseu res per acceptar totes les taules. Separeu els diferents noms amb el s&iacute;mbol punt i coma (;)',
	'help_envoi' => 'Opcional: enviament de la c&ograve;pia de seguretat per correu electr&ograve;nic si poseu una adre&ccedil;a de destinatari',
	'help_eviter' => 'Opcional: si la taula cont&eacute; en el seu nom la cadena especificada: les dades s\'ignoren (no l\'estructura). Separeu els diferents noms amb el s&iacute;mbol punt i coma (;)',
	'help_gz' => 'En cas contrari les c&ograve;pies es faran en format .sql',
	'help_mail_max_size' => 'Algunes bases de dades poden sobrepassar la mida m&agrave;xima permesa en els fitxers adjunts per un correu electr&ograve;nic. Consulteu al vostre prove&iuml;dor de correu electr&ograve;nic per tal de saber la mida m&agrave;xima autoritzada. El l&iacute;mit predeterminat &eacute;s de 2MB.',
	'help_msg' => 'Mostra un missatge d\'&egrave;xit a la interf&iacute;cie',
	'help_obsolete' => 'Determina a partir de quants dies un arxiu es considera obsolet i autom&agrave;ticament l\'elimina del servidor.
             Poseu-hi -1 per desactivar aquesta funci&oacute;',
	'help_prefixe' => 'Opcional: posar un prefix al nom del fitxer de la c&ograve;pia de seguretat',
	'help_rep' => 'Directori on emmagatzemar els fitxers (cam&iacute; des de l\'<strong>arrel</strong> d\'SPIP, tmp/data/ per exemple). <strong>HA</strong> d\'acabar amb un /.',
	'help_restauration' => '<strong>Advert&egrave;ncia!!!</strong> Les c&ograve;pies de seguretat realitzades no estan <strong>en el format de les d\'SPIP</strong>:
                In&uacute;til provar utilitzar-les amb l\'eina d\'administraci&oacute; d\'Spip.<br /><br />
             Per qualsevol restauraci&oacute; cal utilitzar la interf&iacute;cie <strong>phpmyadmin</strong> del vostre
             servidor de base de dades: a la pestanya <strong>"SQL"</strong> utilitzeu el bot&oacute;
             <strong>"Ubicaci&oacute; del fitxer de text"</strong> per seleccionar el fitxer de la c&ograve;pia de seguretat
             (marcar la opci&oacute; "gzip" si &eacute;s necessari) i valideu tot seguit.<br /><br />
             Les c&ograve;pies de seguretat <strong>xxxx.gz</strong> o <strong>xxx.sql</strong> contenen un fitxer en format SQL amb les comandes
             que permeten <strong>esborrar</strong> les taules existents d\'SPIP i <strong>substituir-les</strong> per les
             dades arxivades. Per tant, les dades <strong>m&eacute;s recents</strong> que les de la c&ograve;pia de seguretat es <strong>PERDRAN</strong>!',
	'help_titre' => 'Aquesta p&agrave;gina us permet configurar les opcions de c&ograve;pia de seguretat autom&agrave;tica de la base de dades.',

	// I
	'info_mail_message_mime' => 'Aquest &eacute;s un missatge en format MIME.',
	'info_sauvegardes_obsolete' => 'Es conserva una c&ograve;pia de seguretat de la base @nb@ dies a partir del dia que s\'ha realitzat.',
	'info_sql_base' => 'Base de dades: ',
	'info_sql_compatible_phpmyadmin' => 'Fitxer SQL 100% compatible PHPMyadmin',
	'info_sql_date' => 'Data: ',
	'info_sql_debut_fichier' => 'Inici de l\'arxiu',
	'info_sql_donnees_table' => 'Dades de @table@',
	'info_sql_fichier_genere' => 'Aquest fitxer est&agrave; generat pel connector "saveauto"',
	'info_sql_fin_fichier' => 'Final de l\'arxiu',
	'info_sql_ipclient' => 'IP Client: ',
	'info_sql_mysqlversion' => 'Versi&oacute; mySQL : ',
	'info_sql_os' => 'Sistema Operatiu del Servidor: ',
	'info_sql_phpversion' => 'Versi&oacute; PHP: ',
	'info_sql_plugins_utilises' => '@nb@ connectors utilitzats:',
	'info_sql_serveur' => 'Servidor: ',
	'info_sql_spip_version' => 'Versi&oacute; d\'SPIP: ',
	'info_sql_structure_table' => 'Estructura de la taula @table@',
	'info_telecharger_sauvegardes' => 'La taula de sota llista el conjunt de c&ograve;pies de seguretat realitzades pel vostre i que us podeu baixar.',

	// L
	'label_adresse' => 'A l\'adre&ccedil;a: ',
	'label_compression_gz' => 'Comprimir l\'arxiu de la c&ograve;pia de seguretat: ',
	'label_donnees' => 'Dades de les taules: ',
	'label_donnees_ignorees' => 'Dades ignorades: ',
	'label_frequence' => 'Freq&uuml;&egrave;ncia de la c&ograve;pia de seguretat: tots els  ',
	'label_mail_max_size' => 'La mida m&agrave;xima dels fitxers adjunts als correus electr&ograve;nics (en MB):',
	'label_message_succes' => 'Mostra un missatge d\'&egrave;xit si la c&ograve;pia de seguretat &eacute;s OK: ',
	'label_nom_base' => 'Nom de la base de dades SPIP: ',
	'label_obsolete_jours' => 'C&ograve;pies de seguretat considerades obsoletes despr&eacute;s: ',
	'label_prefixe_sauvegardes' => 'Prefix per les c&ograve;pies de seguretat: ',
	'label_repertoire_stockage' => 'Directori d\'emmagatzematge: ',
	'label_restauration' => 'Restauraci&oacute; d\'una c&ograve;pia de seguretat:',
	'label_structure' => 'Estructura de les taules: ',
	'label_tables_acceptes' => 'Taules acceptades: ',
	'legend_structure_donnees' => 'Elements a salvaguardar: ',

	// M
	'message_aucune_sauvegarde' => 'No hi ha cap c&ograve;pia de seguretat.',
	'message_pas_envoi' => 'No s\'enviaran les c&ograve;pies de seguretat!',

	// S
	'sauvegarde_erreur_mail' => 'El connector "saveauto" ha trobat un error durant la c&ograve;pia de seguretat de la base de dades',
	'sauvegarde_ok_mail' => 'C&ograve;pia de seguretat de la base de dades i enviament per correu electr&ograve;nic realitzats amb &egrave;xit!',
	'saveauto_titre' => 'C&ograve;pia de seguretat SQL',

	// T
	'titre_boite_historique' => 'Hist&ograve;ric de les c&ograve;pies de seguretat',
	'titre_boite_sauver' => 'Connector Saveauto: c&ograve;pia de seguretat SQL de la base de dades',
	'titre_page_saveauto' => 'C&ograve;pia de seguretat de la base de dades',
	'titre_saveauto' => 'C&ograve;pia de seguretat autom&agrave;tica',

	// V
	'valeur_jours' => ' dies'
);

?>
