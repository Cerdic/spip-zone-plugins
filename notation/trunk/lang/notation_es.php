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
	'explication_accepter_note' => 'Si "cerrada", la notación será activada en cada objeto que contenga esta funcionalidad.',

	// I
	'info_acces' => 'Abrir el voto:',
	'info_etoiles' => 'Esta configuración le permite modificar el valor máximo de la nota (el número de estrellas, entre 1 y 10, y 5 por defecto).<br />
                    <strong style="color:red">/!\\ Atención</strong>: no ha de tocar esta configuración una vez la notación quede comprometida las notas no podrán volver a calcularse y podrían darse incoherencias en la notación...<br />
Esta configuración debe ser fijada una vez para toda la creación de las notas.',
	'info_fonctionnement_note' => 'Funcionamiento de la anotación',
	'info_ip' => 'Para un uso más fácil, la nota se adjunta a la dirección de IP del votante, lo que evita dos votos sucesivos en la base de datos, con algunos inconvenientes... en particlar si administra votos de autores.<br />
En este caso, se fija la nota en el identificador del usuario (cuando se registra, por supuesto).<br />
Si desea garantizar la unicidad de la nota, limite el voto a las <b>seules</b> personas registradas (arriba).',
	'info_modifications' => 'Modificaciones de las notas',
	'info_ponderation' => 'El factor de ponderación permite acordar más valor a los artículos que hayan recibido suficientes votos. <br /> Escriba a continuación el número de votos más allá de aquél que crea que será confiable. ',
	'ip' => 'IP',
	'item_adm' => 'a los administradores',
	'item_all' => 'a todos',
	'item_aut' => 'a los autores',
	'item_id' => 'un voto por usuario',
	'item_ide' => 'a las personas registradas',
	'item_ip' => 'un voto por IP',

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
	'nbvotes' => 'Número de votos',
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
