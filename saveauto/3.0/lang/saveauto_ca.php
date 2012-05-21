<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/saveauto?lang_cible=ca
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'attention' => 'Advertència:',

	// C
	'colonne_date' => 'Data',
	'colonne_nom' => 'Cognom',
	'colonne_taille_octets' => 'Mida',

	// E
	'envoi_mail' => 'Còpies de seguretat enviades',
	'erreur_config_inadaptee_mail' => 'Configuració no adaptada, el vostre servidor no garanteix les funcions d\'enviament de correus electrònics!',
	'erreur_impossible_creer_verifier' => 'Impossible crear el fitxer @fichier@, verifiqueu els drets d\'escriptura del directori @rep_bases@.',
	'erreur_impossible_liste_tables' => 'Impossible llistar les taules de la base de dades.',
	'erreur_mail_fichier_lourd' => 'L\'arxiu de la còpia de seguretat és massa pesat per ser enviat per correu electrònic. Podeu recuperar-lo des de la interfície d\'administració o per FTP seguint la ruta: @fichier@',
	'erreur_mail_sujet' => 'Error de còpia de seguretat SQL',
	'erreur_probleme_donnees_corruption' => 'Problema amb les dades de @table@, és possible que estiguin corruptes!',
	'erreur_repertoire_inaccessible' => 'El directori @rep@ és inaccessible a l\'escriptura.',
	'erreur_repertoire_inexistant' => 'El directori @rep@ no existeix. Verifiqueu la vostra configuració.',
	'erreur_sauvegarde_intro' => 'El missatge d\'error és el següent:',
	'erreurs_config' => 'Error(s) a la configuració',

	// H
	'help_accepter' => 'Opcional: només guardeu les taules que tinguin la cadena especificada en el seu nom. Exemple: anuari_, important, enginy.
             No hi poseu res per acceptar totes les taules. Separeu els diferents noms amb el símbol punt i coma (;)',
	'help_envoi' => 'Opcional: enviament de la còpia de seguretat per correu electrònic si poseu una adreça de destinatari',
	'help_eviter' => 'Opcional: si la taula conté en el seu nom la cadena especificada: les dades s\'ignoren (no l\'estructura). Separeu els diferents noms amb el símbol punt i coma (;)',
	'help_gz' => 'En cas contrari les còpies es faran en format .sql',
	'help_mail_max_size' => 'Algunes bases de dades poden sobrepassar la mida màxima permesa en els fitxers adjunts per un correu electrònic. Consulteu al vostre proveïdor de correu electrònic per tal de saber la mida màxima autoritzada. El límit predeterminat és de 2MB.',
	'help_msg' => 'Mostra un missatge d\'èxit a la interfície',
	'help_obsolete' => 'Determina a partir de quants dies un arxiu es considera obsolet i automàticament l\'elimina del servidor.
             Poseu-hi -1 per desactivar aquesta funció',
	'help_prefixe' => 'Opcional: posar un prefix al nom del fitxer de la còpia de seguretat',
	'help_rep' => 'Directori on emmagatzemar els fitxers (camí des de l\'<strong>arrel</strong> d\'SPIP, tmp/data/ per exemple). <strong>HA</strong> d\'acabar amb un /.',
	'help_restauration' => '<strong>Advertència!!!</strong> Les còpies de seguretat realitzades no estan <strong>en el format de les d\'SPIP</strong>:
                Inútil provar utilitzar-les amb l\'eina d\'administració d\'Spip.<br /><br />
             Per qualsevol restauració cal utilitzar la interfície <strong>phpmyadmin</strong> del vostre
             servidor de base de dades: a la pestanya <strong>"SQL"</strong> utilitzeu el botó
             <strong>"Ubicació del fitxer de text"</strong> per seleccionar el fitxer de la còpia de seguretat
             (marcar la opció "gzip" si és necessari) i valideu tot seguit.<br /><br />
             Les còpies de seguretat <strong>xxxx.gz</strong> o <strong>xxx.sql</strong> contenen un fitxer en format SQL amb les comandes
             que permeten <strong>esborrar</strong> les taules existents d\'SPIP i <strong>substituir-les</strong> per les
             dades arxivades. Per tant, les dades <strong>més recents</strong> que les de la còpia de seguretat es <strong>PERDRAN</strong>!',
	'help_titre' => 'Aquesta pàgina us permet configurar les opcions de còpia de seguretat automàtica de la base de dades.',

	// I
	'info_mail_message_mime' => 'Aquest és un missatge en format MIME.',
	'info_sauvegardes_obsolete' => 'Es conserva una còpia de seguretat de la base @nb@ dies a partir del dia que s\'ha realitzat.',
	'info_sql_base' => 'Base de dades: ',
	'info_sql_compatible_phpmyadmin' => 'Fitxer SQL 100% compatible PHPMyadmin',
	'info_sql_date' => 'Data: ',
	'info_sql_debut_fichier' => 'Inici de l\'arxiu',
	'info_sql_donnees_table' => 'Dades de @table@',
	'info_sql_fichier_genere' => 'Aquest fitxer està generat pel connector "saveauto"',
	'info_sql_fin_fichier' => 'Final de l\'arxiu',
	'info_sql_ipclient' => 'IP Client: ',
	'info_sql_mysqlversion' => 'Versió mySQL : ',
	'info_sql_os' => 'Sistema Operatiu del Servidor: ',
	'info_sql_phpversion' => 'Versió PHP: ',
	'info_sql_plugins_utilises' => '@nb@ connectors utilitzats:',
	'info_sql_serveur' => 'Servidor: ',
	'info_sql_spip_version' => 'Versió d\'SPIP: ',
	'info_sql_structure_table' => 'Estructura de la taula @table@',
	'info_telecharger_sauvegardes' => 'La taula de sota llista el conjunt de còpies de seguretat realitzades pel vostre i que us podeu baixar.',

	// L
	'label_adresse' => 'A l\'adreça: ',
	'label_compression_gz' => 'Comprimir l\'arxiu de la còpia de seguretat: ',
	'label_donnees' => 'Dades de les taules: ',
	'label_donnees_ignorees' => 'Dades ignorades: ',
	'label_frequence' => 'Freqüència de la còpia de seguretat: tots els  ',
	'label_mail_max_size' => 'La mida màxima dels fitxers adjunts als correus electrònics (en MB):',
	'label_message_succes' => 'Mostra un missatge d\'èxit si la còpia de seguretat és OK: ',
	'label_nom_base' => 'Nom de la base de dades SPIP: ',
	'label_obsolete_jours' => 'Còpies de seguretat considerades obsoletes després: ',
	'label_prefixe_sauvegardes' => 'Prefix per les còpies de seguretat: ',
	'label_repertoire_stockage' => 'Directori d\'emmagatzematge: ',
	'label_restauration' => 'Restauració d\'una còpia de seguretat:',
	'label_structure' => 'Estructura de les taules: ',
	'label_tables_acceptes' => 'Taules acceptades: ',
	'legend_structure_donnees' => 'Elements a salvaguardar: ',

	// M
	'message_aucune_sauvegarde' => 'No hi ha cap còpia de seguretat.',
	'message_pas_envoi' => 'No s\'enviaran les còpies de seguretat!',

	// S
	'sauvegarde_erreur_mail' => 'El connector "saveauto" ha trobat un error durant la còpia de seguretat de la base de dades',
	'sauvegarde_ok_mail' => 'Còpia de seguretat de la base de dades i enviament per correu electrònic realitzats amb èxit!',
	'saveauto_titre' => 'Còpia de seguretat SQL',

	// T
	'titre_boite_historique' => 'Històric de les còpies de seguretat',
	'titre_boite_sauver' => 'Connector Saveauto: còpia de seguretat SQL de la base de dades',
	'titre_page_saveauto' => 'Còpia de seguretat de la base de dades',
	'titre_saveauto' => 'Còpia de seguretat automàtica',

	// V
	'valeur_jours' => ' dies'
);

?>
