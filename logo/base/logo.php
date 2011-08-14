<?php

if (!function_exists('lister_tables_objets_sql')) {

    /**
     * Lister les infos de toutes les tables sql declarees
     * si un argument est fourni, on ne renvoie que les infos de cette table
     * elle est auto-declaree si inconnue jusqu'alors.
     *
     * @param string $table_sql
     *   table_sql demandee explicitement
     * @param array $desc
     *   description connue de la table sql demandee
     * @return array|bool
     */
    function lister_tables_objets_sql($table_sql=null, $desc=array()){
	    static $deja_la = false;
	    static $infos_tables = null;
	    // prealablement recuperer les tables_principales
	    if (is_null($infos_tables)){
		    // pas de reentrance (cas base/serial)
		    if ($deja_la) return array();
		    $deja_la = true;
		    # recuperer les tables_principales si besoin
		    include_spip('base/serial');
		    # recuperer les tables_auxiliaires si besoin
		    include_spip('base/auxiliaires');
		    // recuperer les declarations explicites ancienne mode
		    // qui servent a completer declarer_tables_objets_sql
		    base_serial($GLOBALS['tables_principales']);
		    base_auxiliaires($GLOBALS['tables_auxiliaires']);
		    $infos_tables = pipeline('declarer_tables_objets_sql',array(
			    'spip_articles'=> array(
				    'page'=>'article',
				    'texte_retour' => 'icone_retour_article',
				    'texte_modifier' => 'icone_modifier_article',
				    'texte_creer' => 'icone_ecrire_article',
				    'texte_objets' => 'public:articles',
				    'texte_objet' => 'public:article',
				    'texte_signale_edition' => 'texte_travail_article',
				    'info_aucun_objet'=> 'info_aucun_article',
				    'info_1_objet' => 'info_1_article',
				    'info_nb_objets' => 'info_nb_articles',
				    'texte_logo_objet' => 'logo_article',
				    'titre' => 'titre, lang',
				    'date' => 'date',
				    'principale' => 'oui',
				    'champs_editables' => array('surtitre', 'titre', 'soustitre', 'descriptif','nom_site', 'url_site', 'chapo', 'texte', 'ps','virtuel'),
				    'champs_versionnes' => array('id_rubrique', 'surtitre', 'titre', 'soustitre', 'jointure_auteurs', 'descriptif', 'nom_site', 'url_site', 'chapo', 'texte', 'ps'),
				    'field' => array(
					    "id_article"	=> "bigint(21) NOT NULL",
					    "surtitre"	=> "text DEFAULT '' NOT NULL",
					    "titre"	=> "text DEFAULT '' NOT NULL",
					    "soustitre"	=> "text DEFAULT '' NOT NULL",
					    "id_rubrique"	=> "bigint(21) DEFAULT '0' NOT NULL",
					    "descriptif"	=> "text DEFAULT '' NOT NULL",
					    "chapo"	=> "mediumtext DEFAULT '' NOT NULL",
					    "texte"	=> "longtext DEFAULT '' NOT NULL",
					    "ps"	=> "mediumtext DEFAULT '' NOT NULL",
					    "date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
					    "statut"	=> "varchar(10) DEFAULT '0' NOT NULL",
					    "id_secteur"	=> "bigint(21) DEFAULT '0' NOT NULL",
					    "maj"	=> "TIMESTAMP",
					    "export"	=> "VARCHAR(10) DEFAULT 'oui'",
					    "date_redac"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
					    "visites"	=> "integer DEFAULT '0' NOT NULL",
					    "referers"	=> "integer DEFAULT '0' NOT NULL",
					    "popularite"	=> "DOUBLE DEFAULT '0' NOT NULL",
					    "accepter_forum"	=> "CHAR(3) DEFAULT '' NOT NULL",
					    "date_modif"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
					    "lang"		=> "VARCHAR(10) DEFAULT '' NOT NULL",
					    "langue_choisie"	=> "VARCHAR(3) DEFAULT 'non'",
					    "id_trad"	=> "bigint(21) DEFAULT '0' NOT NULL",
					    "nom_site"	=> "tinytext DEFAULT '' NOT NULL",
					    "url_site"	=> "VARCHAR(255) DEFAULT '' NOT NULL",
					    "virtuel"	=> "VARCHAR(255) DEFAULT '' NOT NULL",
				    ),
				    'key' => array(
					    "PRIMARY KEY"		=> "id_article",
					    "KEY id_rubrique"	=> "id_rubrique",
					    "KEY id_secteur"	=> "id_secteur",
					    "KEY id_trad"		=> "id_trad",
					    "KEY lang"		=> "lang",
					    "KEY statut"		=> "statut, date",
				    ),
				    'join' => array(
					    "id_article"=>"id_article",
					    "id_rubrique"=>"id_rubrique"
				    ),
				    'rechercher_champs' => array(
					    'surtitre' => 5, 'titre' => 8, 'soustitre' => 5, 'chapo' => 3,
					    'texte' => 1, 'ps' => 1, 'nom_site' => 1, 'url_site' => 1,
					    'descriptif' => 4
				    ),
				    'rechercher_jointures' => array(
					    'auteur' => array('nom' => 10),
				    ),
				    'statut'=> array(
					    array(
						    'champ' => 'statut',
						    'publie' => 'publie',
						    'previsu' => 'publie,prop,prepa',
						    'post_date' => 'date',
						    'exception' => 'statut'
					    )
				    ),
				    'statut_titres' => array(
					    'prepa'=>'info_article_redaction',
					    'prop'=>'info_article_propose',
					    'publie'=>'info_article_publie',
					    'refuse'=>'info_article_refuse',
					    'poubelle'=>'info_article_supprime'
				    ),
				    'statut_textes_instituer' => 	array(
					    'prepa' => 'texte_statut_en_cours_redaction',
					    'prop' => 'texte_statut_propose_evaluation',
					    'publie' => 'texte_statut_publie',
					    'refuse' => 'texte_statut_refuse',
					    'poubelle' => 'texte_statut_poubelle',
				    ),
				    'tables_jointures' => array(
					    #'id_auteur' => 'auteurs_liens' // declaration generique plus bas
				    ),
			    ),
			    'spip_auteurs' => array(
				    'page'=>'auteur',
				    'texte_retour' => 'icone_retour',
				    'texte_modifier' => 'admin_modifier_auteur',
				    'texte_objets' => 'icone_auteurs',
				    'texte_objet' => 'public:auteur',
				    'info_aucun_objet'=> 'info_aucun_auteur',
				    'info_1_objet' => 'info_1_auteur',
				    'info_nb_objets' => 'info_nb_auteurs',
				    'texte_logo_objet' => 'logo_auteur',
				    'texte_creer_associer' => 'creer_et_associer_un_auteur',
				    'titre' => "nom AS titre, '' AS lang",
				    'date' => 'date',
				    'principale' => 'oui',
				    'champs_editables' => array('nom','email','bio','nom_site','url_site','imessage','pgp'),
				    'champs_versionnes' => array('nom', 'bio', 'email', 'nom_site', 'url_site', 'login'),
				    'field' => array(
					    "id_auteur"	=> "bigint(21) NOT NULL",
					    "nom"	=> "text DEFAULT '' NOT NULL",
					    "bio"	=> "text DEFAULT '' NOT NULL",
					    "email"	=> "tinytext DEFAULT '' NOT NULL",
					    "nom_site"	=> "tinytext DEFAULT '' NOT NULL",
					    "url_site"	=> "text DEFAULT '' NOT NULL",
					    "login"	=> "VARCHAR(255) BINARY",
					    "pass"	=> "tinytext DEFAULT '' NOT NULL",
					    "low_sec"	=> "tinytext DEFAULT '' NOT NULL",
					    "statut"	=> "varchar(255)  DEFAULT '0' NOT NULL",
					    "webmestre"	=> "varchar(3)  DEFAULT 'non' NOT NULL",
					    "maj"	=> "TIMESTAMP",
					    "pgp"	=> "TEXT DEFAULT '' NOT NULL",
					    "htpass"	=> "tinytext DEFAULT '' NOT NULL",
					    "en_ligne"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
					    "alea_actuel"	=> "tinytext",
					    "alea_futur"	=> "tinytext",
					    "prefs"	=> "tinytext",
					    "cookie_oubli"	=> "tinytext",
					    "source"	=> "VARCHAR(10) DEFAULT 'spip' NOT NULL",
					    "lang"	=> "VARCHAR(10) DEFAULT '' NOT NULL"
				    ),
				    'key' => array(
					    "PRIMARY KEY"	=> "id_auteur",
					    "KEY login"	=> "login",
					    "KEY statut"	=> "statut",
					    "KEY en_ligne"	=> "en_ligne",
				    ),
				    'join' => array(
					    "id_auteur"=>"id_auteur",
					    "login"=>"login"
				    ),
				    'rechercher_champs' => array(
					    'nom' => 5, 'bio' => 1, 'email' => 1, 'nom_site' => 1, 'url_site' => 1, 'login' => 1
				    ),
				    // 2 conditions pour les auteurs : statut!=poubelle,
				    // et avoir des articles publies
				    'statut'=> array(
					    array(
						    'champ' => 'statut',
						    'publie' => '!5poubelle',
						    'previsu' => '!5poubelle',
						    'exception' => 'statut'
					    ),
					    array(
						    'champ' => array(
							    array('spip_auteurs_liens', 'id_auteur'),
							    array(
								    'spip_articles',
								    array('id_objet','id_article','objet','article')
							    ),
							    'statut'
						    ),
						    'publie' => 'publie',
						    'previsu' => '!',
						    'post_date' => 'date',
						    'exception' => array('statut','lien','tout')
					    ),
				    ),
				    'statut_images' => array(
					    'auteur-6forum-16.png',
					    '0minirezo'=>'auteur-0minirezo-16.png',
					    '1comite'=>'auteur-1comite-16.png',
					    '6forum'=>'auteur-6forum-16.png',
					    '5poubelle'=>'auteur-5poubelle-16.png',
					    'nouveau'=>''
				    ),
				    'statut_titres' => array(
					    'titre_image_visiteur',
					    '0minirezo'=>'titre_image_administrateur',
					    '1comite'=>'titre_image_redacteur_02',
					    '6forum'=>'titre_image_visiteur',
					    '5poubelle'=>'titre_image_auteur_supprime',
				    ),
				    'tables_jointures' => array(
					    #'auteurs_liens' // declaration generique plus bas
				    ),
			    ),
			    'spip_rubriques' => array(
				    'page'=>'rubrique',
				    'url_voir' => 'rubrique',
				    'url_edit' => 'rubrique_edit',
				    'texte_retour' => 'icone_retour',
				    'texte_objets' => 'public:rubriques',
				    'texte_objet' => 'public:rubrique',
				    'texte_modifier' => 'icone_modifier_rubrique',
				    'texte_creer' => 'icone_creer_rubrique',
				    'info_aucun_objet'=> 'info_aucun_rubrique',
				    'info_1_objet' => 'info_1_rubrique',
				    'info_nb_objets' => 'info_nb_rubriques',
				    'texte_logo_objet' => 'logo_rubrique',
				    'titre'=>'titre, lang',
				    'date' => 'date',
				    'principale' => 'oui',
				    'champs_editables' => array('titre', 'texte', 'descriptif', 'extra'),
				    'champs_versionnes' => array('titre', 'descriptif', 'texte'),
				    'field' => array(
					    "id_rubrique"	=> "bigint(21) NOT NULL",
					    "id_parent"	=> "bigint(21) DEFAULT '0' NOT NULL",
					    "titre"	=> "text DEFAULT '' NOT NULL",
					    "descriptif"	=> "text DEFAULT '' NOT NULL",
					    "texte"	=> "longtext DEFAULT '' NOT NULL",
					    "id_secteur"	=> "bigint(21) DEFAULT '0' NOT NULL",
					    "maj"	=> "TIMESTAMP",
					    "statut"	=> "varchar(10) DEFAULT '0' NOT NULL",
					    "date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
					    "lang"	=> "VARCHAR(10) DEFAULT '' NOT NULL",
					    "langue_choisie"	=> "VARCHAR(3) DEFAULT 'non'",
					    "statut_tmp"	=> "varchar(10) DEFAULT '0' NOT NULL",
						    "date_tmp"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL"
				    ),
				    'key' => array(
					    "PRIMARY KEY"	=> "id_rubrique",
					    "KEY lang"	=> "lang",
					    "KEY id_parent"	=> "id_parent",
				    ),
				    'rechercher_champs' => array(
					    'titre' => 8, 'descriptif' => 5, 'texte' => 1
				    ),
				    'statut' => array(
					    array(
						    'champ' => 'statut',
						    'publie' => 'publie',
						    'previsu' => '!',
						    'exception' => array('statut','tout')
					    ),
				    ),
				    'tables_jointures' => array(
					    #'id_auteur' => 'auteurs_liens' // declaration generique plus bas
				    ),
			    ),
			    // toutes les tables ont le droit a une jointure sur les auteurs
			    array('tables_jointures'=>array('id_auteur'=>'auteurs_liens'))
		    ));
		    // completer les informations manquantes ou implicites
		    $all = array();
		    foreach(array_keys($infos_tables) as $t) {
			    // les cles numeriques servent a declarer
			    // les proprietes applicables a tous les objets
			    // on les mets de cote
			    if (is_numeric($t)) {
				    $all = array_merge_recursive($all,$infos_tables[$t]);
				    unset($infos_tables[$t]);
			    }
			    else
				    $infos_tables[$t] = renseigner_table_objet_sql($t,$infos_tables[$t]);
		    }
		    // repercuter les proprietes generales communes a tous les objets
		    foreach(array_keys($infos_tables) as $t) {
			    $infos_tables[$t] = array_merge_recursive($infos_tables[$t],$all);
		    }

		    // completer les tables principales et auxiliaires
		    // avec celles declarees uniquement dans declarer_table_objets_sql
		    // pour assurer la compat en transition
		    foreach($infos_tables as $table=>$infos) {
			    $principale_ou_auxiliaire = ($infos['principale']?'tables_principales':'tables_auxiliaires');
			    // memoriser des champs eventuels declares par des plugins dans le pipeline tables_xxx
			    // qui a ete appelle avant
			    $mem = (isset($GLOBALS[$principale_ou_auxiliaire][$table])?$GLOBALS[$principale_ou_auxiliaire][$table]:array());
			    // l'ajouter au tableau
			    $GLOBALS[$principale_ou_auxiliaire][$table] = array();
			    if (isset($infos['field']) AND isset($infos['key'])){
				    foreach(array('field','key','join') as $k)
					    if (isset($infos_tables[$table][$k]))
						    $GLOBALS[$principale_ou_auxiliaire][$table][$k] = &$infos_tables[$table][$k];
			    }
			    else {
				    // ici on ne renvoie que les declarations, donc RIEN
				    // pour avoir la vrai description en base, il faut passer par trouver_table
				    $GLOBALS[$principale_ou_auxiliaire][$table] = array();
			    }
			    if (count($mem)){
				    foreach(array_keys($mem) as $k)
					    if (isset($GLOBALS[$principale_ou_auxiliaire][$table][$k]))
						    $GLOBALS[$principale_ou_auxiliaire][$table][$k] = array_merge($GLOBALS[$principale_ou_auxiliaire][$table][$k],$mem[$k]);
					    else
						    $GLOBALS[$principale_ou_auxiliaire][$table][$k] = $mem[$k];
			    }
		    }

		    // recuperer les interfaces (table_titre, table_date)
		    // on ne le fait que dans un second temps pour que table_objet soit fonctionnel
		    // dans le pipeline de declarer_tables_interfaces
		    include_spip('public/interfaces');
		    foreach(array_keys($infos_tables) as $t) {
			    $infos_tables[$t] = renseigner_table_objet_interfaces($t,$infos_tables[$t]);
		    }

		    $deja_la = false;
		    // lever la constante qui dit qu'on a tout init et qu'on peut cacher
		    define('_init_tables_objets_sql',true);
	    }
	    if ($table_sql AND !isset($infos_tables[$table_sql])){
		    #$desc = renseigner_table_objet_sql($table_sql,$desc);
		    $desc = renseigner_table_objet_interfaces($table_sql,$desc);
		    return $desc;
	    }
	    if ($table_sql)
		    return isset($infos_tables[$table_sql])?$infos_tables[$table_sql]:array();

	    return $infos_tables;
    }

}

if (!function_exists('renseigner_table_objet_sql')) {
    /**
     * Auto remplissage des informations non explicites
     * sur un objet d'une table sql
     *
     * table_objet
     * table_objet_surnoms
     * type
     * type_surnoms
     * url_voir
     * url_edit
     * icone_objet
     *
     * texte_retour
     * texte_modifier
     * texte_creer
     * texte_objets
     * texte_objet
     *
     * info_aucun_objet
     * info_1_objet
     * info_nb_objets
     *
     * texte_logo_objet
     *
     * principale
     * champs_contenu : utlise pour generer l'affichage par defaut du contenu
     * editable
     * champs_editables : utilise pour prendre en compte le post lors de l'edition
     * 
     * champs_versionnes
     *
     * statut
     * statut_images
     * statut_titres
     * statut_textes_instituer
     *
     * modeles : permet de declarer les modeles associes a cet objet
     * 
     * les infos non renseignees sont auto deduites par conventions
     * ou laissees vides
     *
     * @param string $table_sql
     * @param array $infos
     * @return array
     */
    function renseigner_table_objet_sql($table_sql,&$infos){
	    if (!isset($infos['type'])){
		    // si on arrive de base/trouver_table, on a la cle primaire :
		    // s'en servir pour extrapoler le type
		    if (isset($desc['key']["PRIMARY KEY"])){
			    $primary = $desc['key']["PRIMARY KEY"];
			    $primary = explode(',',$primary);
			    $primary = reset($primary);
			    $infos['type'] = preg_replace(',^spip_|^id_|s$,', '', $primary);
		    }
		    else
			    $infos['type'] = preg_replace(',^spip_|s$,', '', $table_sql);
	    }
	    if (!isset($infos['type_surnoms']))
		    $infos['type_surnoms'] = array();

	    if (!isset($infos['table_objet']))
		    $infos['table_objet'] = preg_replace(',^spip_,', '', $table_sql);
	    if (!isset($infos['table_objet_surnoms']))
		    $infos['table_objet_surnoms'] = array();

	    if (!isset($infos['principale']))
		    $infos['principale'] = (isset($GLOBALS['tables_principales'][$table_sql])?'oui':false);

	    // normaliser pour pouvoir tester en php $infos['principale']?
	    // et dans une boucle {principale=oui}
	    $infos['principale'] = (($infos['principale'] AND $infos['principale']!='non')?'oui':false);

	    // declarer et normaliser pour pouvoir tester en php $infos['editable']?
	    // et dans une boucle {editable=oui}
	    if (!isset($infos['editable'])) $infos['editable'] = 'oui';
	    $infos['editable'] = (($infos['editable'] AND $infos['editable']!='non')?'oui':false);

	    // les urls publiques sont par defaut page=type pour les tables principales, et rien pour les autres
	    // seules les exceptions sont donc a declarer
	    if (!isset($infos['page']))
		    $infos['page'] = ($infos['principale']?$infos['type']:'');

	    if (!isset($infos['url_voir']))
		    $infos['url_voir'] = $infos['type'];
	    if (!isset($infos['url_edit']))
		    $infos['url_edit'] = $infos['url_voir'].($infos['editable']?"_edit":'');
	    if (!isset($infos['icone_objet']))
		    $infos['icone_objet'] = $infos['type'];

	    // chaines de langue
	    // par defaut : objet:icone_xxx_objet
	    if (!isset($infos['texte_retour']))
		    $infos['texte_retour'] = 'icone_retour';
	    if (!isset($infos['texte_modifier']))
		    $infos['texte_modifier'] = $infos['type'].':'.'icone_modifier_'.$infos['type'];
	    if (!isset($infos['texte_creer']))
		    $infos['texte_creer'] = $infos['type'].':'.'icone_creer_'.$infos['type'];
	    if (!isset($infos['texte_objets']))
		    $infos['texte_objets'] = $infos['type'].':'.'titre_'.$infos['table_objet'];
	    if (!isset($infos['texte_objet']))
		    $infos['texte_objet'] = $infos['type'].':'.'titre_'.$infos['type'];
	    if (!isset($infos['texte_logo_objet']))  // objet:titre_logo_objet "Logo de ce X"
		    $infos['texte_logo_objet'] = $infos['type'].':'.'titre_logo_'.$infos['type'];
		
	    // objet:info_aucun_objet
	    if (!isset($infos['info_aucun_objet']))
		    $infos['info_aucun_objet'] = $infos['type'].':'.'info_aucun_'.$infos['type'];
	    // objet:info_1_objet
	    if (!isset($infos['info_1_objet']))
		    $infos['info_1_objet'] = $infos['type'].':'.'info_1_'.$infos['type'];
	    // objet:info_nb_objets
	    if (!isset($infos['info_nb_objets']))
		    $infos['info_nb_objets'] = $infos['type'].':'.'info_nb_'.$infos['table_objet'];


	    if (!isset($infos['champs_editables']))
		    $infos['champs_editables'] = array();
	    if (!isset($infos['champs_versionnes']))
		    $infos['champs_versionnes'] = array();
	    if (!isset($infos['rechercher_champs']))
		    $infos['rechercher_champs'] = array();
	    if (!isset($infos['rechercher_jointures']))
		    $infos['rechercher_jointures'] = array();

	    if (!isset($infos['modeles']))
		    $infos['modeles'] = array($infos['type']);

	    return $infos;
    }
}

if (!function_exists('renseigner_table_objet_interfaces')) {

    /**
     * Renseigner les infos d'interface compilateur pour les tables objets
     * complete la declaration precedente
     * 
     * titre
     * date
     * statut
     * tables_jointures
     *
     * @param $table_sql
     * @param $infos
     * @return array
     */
    function renseigner_table_objet_interfaces($table_sql,&$infos){
	    if (!isset($infos['titre']))
		    $infos['titre'] = isset($GLOBALS['table_titre'][$infos['table_objet']]) ? $GLOBALS['table_titre'][$infos['table_objet']] : '';
	    if (!isset($infos['date']))
		    $infos['date'] = isset($GLOBALS['table_date'][$infos['table_objet']]) ? $GLOBALS['table_date'][$infos['table_objet']] : '';
	    if (!isset($infos['statut']))
		    $infos['statut'] = isset($GLOBALS['table_statut'][$table_sql]) ? $GLOBALS['table_statut'][$table_sql] : '';
	    if (!isset($infos['tables_jointures']))
		    $infos['tables_jointures'] = array();
	    if (isset($GLOBALS['tables_jointures'][$table_sql]))
		    $infos['tables_jointures'] = array_merge($infos['tables_jointures'],$GLOBALS['tables_jointures'][$table_sql]);
	    return $infos;
    }
}

?>
