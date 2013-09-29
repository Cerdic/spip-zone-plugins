<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/saveauto?lang_cible=ca
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'colonne_nom' => 'Cognom',

	// E
	'erreur_impossible_creer_verifier' => 'Impossible crear el fitxer @fichier@, verifiqueu els drets d’escriptura del directori @rep_bases@.',
	'erreur_impossible_liste_tables' => 'Impossible llistar les taules de la base de dades.',
	'erreur_probleme_donnees_corruption' => 'Problema amb les dades de @table@, és possible que estiguin corruptes!',
	'erreur_repertoire_inaccessible' => 'El directori @rep@ és inaccessible a l’escriptura.',

	// H
	'help_mail_max_size' => 'Algunes bases de dades poden sobrepassar la mida màxima permesa en els fitxers adjunts per un correu electrònic. Consulteu al vostre proveïdor de correu electrònic per tal de saber la mida màxima autoritzada. El límit predeterminat és de 2MB.', # MODIF
	'help_obsolete' => 'Determina a partir de quants dies un arxiu es considera obsolet i automàticament l’elimina del servidor.
             Poseu-hi -1 per desactivar aquesta funció', # MODIF
	'help_prefixe' => 'Opcional: posar un prefix al nom del fitxer de la còpia de seguretat', # MODIF
	'help_restauration' => '<strong>Advertència!!!</strong> Les còpies de seguretat realitzades no estan <strong>en el format de les d’SPIP</strong>:
                Inútil provar utilitzar-les amb l’eina d’administració d’Spip.<br /><br />
             Per qualsevol restauració cal utilitzar la interfície <strong>phpmyadmin</strong> del vostre
             servidor de base de dades: a la pestanya <strong>"SQL"</strong> utilitzeu el botó
             <strong>"Ubicació del fitxer de text"</strong> per seleccionar el fitxer de la còpia de seguretat
             (marcar la opció "gzip" si és necessari) i valideu tot seguit.<br /><br />
             Les còpies de seguretat <strong>xxxx.gz</strong> o <strong>xxx.sql</strong> contenen un fitxer en format SQL amb les comandes
             que permeten <strong>esborrar</strong> les taules existents d’SPIP i <strong>substituir-les</strong> per les
             dades arxivades. Per tant, les dades <strong>més recents</strong> que les de la còpia de seguretat es <strong>PERDRAN</strong>!', # MODIF

	// I
	'info_sql_base' => 'Base de dades: ',
	'info_sql_compatible_phpmyadmin' => 'Fitxer SQL 100% compatible PHPMyadmin',
	'info_sql_date' => 'Data: ',
	'info_sql_debut_fichier' => 'Inici de l’arxiu',
	'info_sql_donnees_table' => 'Dades de @table@', # MODIF
	'info_sql_fichier_genere' => 'Aquest fitxer està generat pel connector "saveauto"', # MODIF
	'info_sql_fin_fichier' => 'Final de l’arxiu',
	'info_sql_ipclient' => 'IP Client: ',
	'info_sql_mysqlversion' => 'Versió mySQL : ', # MODIF
	'info_sql_os' => 'Sistema Operatiu del Servidor: ',
	'info_sql_phpversion' => 'Versió PHP: ',
	'info_sql_plugins_utilises' => '@nb@ connectors utilitzats:',
	'info_sql_serveur' => 'Servidor: ',
	'info_sql_spip_version' => 'Versió d’SPIP: ',
	'info_sql_structure_table' => 'Estructura de la taula @table@',

	// L
	'label_donnees' => 'Dades de les taules: ', # MODIF
	'label_frequence' => 'Freqüència de la còpia de seguretat: tots els  ', # MODIF
	'label_mail_max_size' => 'La mida màxima dels fitxers adjunts als correus electrònics (en MB):', # MODIF
	'label_obsolete_jours' => 'Còpies de seguretat considerades obsoletes després: ', # MODIF
	'label_prefixe_sauvegardes' => 'Prefix per les còpies de seguretat: ', # MODIF
	'label_structure' => 'Estructura de les taules: ', # MODIF

	// M
	'message_aucune_sauvegarde' => 'No hi ha cap còpia de seguretat.', # MODIF

	// T
	'titre_boite_historique' => 'Històric de les còpies de seguretat', # MODIF
	'titre_boite_sauver' => 'Connector Saveauto: còpia de seguretat SQL de la base de dades', # MODIF
	'titre_page_saveauto' => 'Còpia de seguretat de la base de dades', # MODIF
	'titre_saveauto' => 'Còpia de seguretat automàtica'
);

?>
