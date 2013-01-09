<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/notation?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'acces' => 'Accesibilidad',
	'afficher_tables' => 'Mostrar notas',
	'aide' => 'Ayuda',
	'articles' => 'Artículos',
	'auteur' => 'Autor',

	// B
	'bouton_radio_fermee' => 'Cerrada',
	'bouton_radio_ouvert' => 'Abierta',
	'bouton_voter' => 'Votar',

	// C
	'change_note_label' => 'Autorizar a los votantes cambiar sus notas',
	'configuration_notation' => 'Configurar las anotaciones',
	'creation' => 'Creación de tablas',
	'creation_des_tables_mysql' => 'Creación de tablas',
	'cree' => 'Tablas creadas',
	'creer_tables' => 'Crear las tablas',

	// D
	'date' => 'fecha',
	'derniers_votes' => 'Últimos votos',
	'destruction' => 'Destrucción de tablas',
	'detruire' => '<strong style="color:red">¡Atención, este comando destruirá las tablas del plugin!</strong><br />No debe utilizarlo si lo que desea es desactivar el plugin...',
	'detruit' => 'Tablas destruidas...',

	// E
	'effacer_tables' => 'Borrar las tablas',
	'err_balise' => '[NOTATION_ERR: etiqueta fuera de artículo]',
	'err_db_notation' => '[NOTATION ERREUR: una sola anotación por artículo]',
	'exemple' => 'Distribución de notas (nota= 5, factor de ponderación= @ponderation@): ',
	'explication_accepter_note' => 'Si "fermée", la notation sera activable au cas par cas sur les objets ayant cette fonctionnalité.', # NEW

	// I
	'info_acces' => 'Abrir el voto:',
	'info_etoiles' => 'Ce paramètre vous permet de modifier la valeure maximale de la note (le nombre d\'étoiles, entre 1 et 10, et 5 par défaut).<br />
                    <strong style="color:red">/!\\ Attention</strong> : vous ne devez pas toucher à ce paramètre une fois la notation engagée car les notes ne seront pas recalculées et cela peut provoquer des incohérences dans la notation...<br />
                    Ce paramètres doit être fixé une fois pour toute à la création des notes.', # NEW
	'info_fonctionnement_note' => 'Funcionamiento de la anotación',
	'info_ip' => 'Pour être le plus facile possible d\'utilisation, la note est fixée sur l\'adresse IP du votant, ce qui évite deux votes successifs dans la base, avec quelques inconvénients... en particulier si vous gérez des votes d\'auteurs.<br />
                Dans ce cas, on fixe la note sur l\'identifiant de l\'utilisateur (quand celui-ci est enregistré, bien sûr).<br />
                Si vous voulez garantir l\'unicité de la note, limitez le vote aux <b>seules</b> personnes enregistrées (ci-dessus).', # NEW
	'info_modifications' => 'Modificaciones de las notas',
	'info_ponderation' => 'Le facteur de pondération permet d\'accorder plus de valeur aux articles ayant reçu suffisament de votes. <br /> Entrez ci-dessous la nombre de votes au delà duquel vous pensez que la note est fiable.', # NEW
	'ip' => 'IP',
	'item_adm' => 'a los administradores',
	'item_all' => 'a todos',
	'item_aut' => 'a los autores',
	'item_id' => 'un voto por usuario',
	'item_ide' => 'a las personas registradas',
	'item_ip' => 'un voto por IP', # MODIF

	// J
	'jaidonnemonavis' => '¡He dado mi opinión!',
	'jaime' => 'Me gusta',
	'jaimepas' => 'No me gusta',
	'jaimeplus' => 'Ya no me gusta',
	'jechangedavis' => 'Retiro mi opinión',

	// L
	'label_accepter_note' => 'Estado de la calificación en todos los objetos',

	// M
	'moyenne' => 'Media',
	'moyennep' => 'Media ponderada',

	// N
	'nb_etoiles' => 'Valor de las notas',
	'nbobjets_note' => 'Número de objetos con nota: ',
	'nbvotes' => 'Número de votos', # MODIF
	'nbvotes_moyen' => 'Número de votos de media por objeto:',
	'nbvotes_total' => 'Número total de votos en el sitio: ',
	'notation' => 'Anotaciones',
	'note' => 'Nota: ',
	'note_1' => 'Nota: 1',
	'note_10' => 'Nota: 10',
	'note_2' => 'Nota: 2',
	'note_3' => 'Nota: 3',
	'note_4' => 'Nota: 4',
	'note_5' => 'Nota: 5',
	'note_6' => 'Nota: 6',
	'note_7' => 'Nota: 7',
	'note_8' => 'Nota: 8',
	'note_9' => 'Nota: 9',
	'note_pond' => 'Notas ponderadas',
	'notes' => 'Notas',

	// O
	'objets' => 'Objetos',

	// P
	'param' => 'Ajuste',
	'ponderation' => 'Ponderación de la nota',

	// T
	'titre_ip' => 'Modo de funcionamiento:',
	'topnb' => 'Los 10 objetos más anotados',
	'topten' => 'Las 10 mejores notas',
	'toptenp' => 'Las 10 mejores notas (ponderadas)',
	'totaux' => 'Totales',

	// V
	'valeur_nb_etoiles' => 'Calificación de 1 a ',
	'valeur_ponderation' => 'Factor de ponderación',
	'vos_notes' => 'Sus 5 mejores notas',
	'vote' => 'voto',
	'voter' => 'Votar: ',
	'votes' => 'votos',
	'votre_note' => 'Su nota'
);

?>
