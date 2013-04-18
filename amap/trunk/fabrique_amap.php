<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2013-04-05 17:20:25
 *
 *  Ce fichier de sauvegarde peut servir à recréer
 *  votre plugin avec le plugin «Fabrique» qui a servi à le créer.
 *
 *  Bien évidemment, les modifications apportées ultérieurement
 *  par vos soins dans le code de ce plugin généré
 *  NE SERONT PAS connues du plugin «Fabrique» et ne pourront pas
 *  être recréées par lui !
 *
 *  La «Fabrique» ne pourra que régénerer le code de base du plugin
 *  avec les informations dont il dispose.
 *
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

$data = array (
  'fabrique' => 
  array (
    'version' => 5,
  ),
  'paquet' => 
  array (
    'nom' => 'Amap',
    'slogan' => 'Gestion des paniers et adhérents pour les AMAP',
    'description' => 'Le plugin est développé pour géré les paniers et adhérents (dit amapiens).
Les paniers sont créés et attribués à un amapien, ce dernier pourra mettre à disposition un panier si il n’est pas présent pour le récupérer le jour prévu.',
    'prefixe' => 'amap',
    'version' => '3.0.2',
    'auteur' => 'Pierre KUHN',
    'auteur_lien' => 'http://www.pierre-kuhn.org',
    'licence' => 'GNU/GPL',
    'categorie' => 'divers',
    'etat' => 'dev',
    'compatibilite' => '[3.0.7;3.0.*]',
    'documentation' => 'http://contrib.spip.net/4331',
    'administrations' => 'on',
    'schema' => '1.2.1',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Configurer AMAP',
    'fichiers' => 
    array (
      0 => 'autorisations',
      1 => 'fonctions',
      2 => 'pipelines',
    ),
    'inserer' => 
    array (
      'paquet' => '',
      'administrations' => 
      array (
        'maj' => '',
        'desinstallation' => '',
        'fin' => '',
      ),
      'base' => 
      array (
        'tables' => 
        array (
          'fin' => '//creation de champs extra
function amap_declarer_champs_extras($champs = array()){
	// type d\'adhérent
	$champs[\'spip_auteurs\'][\'type_adherent\'] = array(
		\'saisie\' => \'radio\', // Type du champ (voir plugin Saisies)
		\'options\' => array(
				\'nom\' => \'type_adherent\',
				\'label\' => _T(\'amap:type_adherent_auteur\'),
				\'sql\' => "varchar(15) DEFAULT \'\'",
				\'defaut\' => \'\', // Valeur par défaut
				\'restrictions\'=>array(
						\'voir\' => array(\'auteur\' => \'\'), // Tout le monde peut voir
						\'modifier\' => array(\'auteur\' => \'\'), // Seuls les auteurs peuvent modifier
				),
				\'datas\' => array(
						\'adherent\' => _T(\'amap:adherent\'),
						\'producteur\' => _T(\'amap:producteur\'),
				),
		),
	);

	// l\'adhésion
	$champs[\'spip_auteurs\'][\'adhesion\'] = array(
		\'saisie\' => \'input\',//Type du champ (voir plugin Saisies)
		\'options\' => array(
				\'nom\' => \'adhesion\',
				\'label\' => _T(\'amap:adhesion_auteur\'),
				\'sql\' => "bigint(21) NULL", // declaration sql
				\'defaut\' => \'\',// Valeur par défaut
				\'restrictions\'=>array(
						\'voir\' => array(\'auteur\' => \'\'), // Tout le monde peut voir
						\'modifier\' => array(\'auteur\' => \'\'), // Seuls les auteurs peuvent modifier
				),
		),
	);

	// type de panier
	$champs[\'spip_auteurs\'][\'type_panier\'] = array(
		\'saisie\' => \'radio\',//Type du champ (voir plugin Saisies)
		\'options\' => array(
				\'nom\' => \'type_panier\',
				\'label\' => _T(\'amap:type_panier_auteur\'),
				\'sql\' => "varchar(10) DEFAULT \'\'", // declaration sql
				\'defaut\' => \'\',// Valeur par défaut
				\'restrictions\'=>array(
						\'voir\' => array(\'auteur\' => \'\'), // Tout le monde peut voir
						\'modifier\' => array(\'auteur\' => \'\'), // Seuls les auteurs peuvent modifier
				),
				\'datas\' => array(
						\'petit\' => _T(\'amap:petit\'),
						\'grand\' => _T(\'amap:grand\'),
				)
		)
	);

	return $champs;
}',
        ),
      ),
    ),
    'scripts' => 
    array (
      'pre_copie' => '',
      'post_creation' => '',
    ),
    'exemples' => '',
  ),
  'objets' => 
  array (
    0 => 
    array (
      'nom' => 'Paniers',
      'nom_singulier' => 'Panier',
      'genre' => 'masculin',
      'logo_variantes' => '',
      'table' => 'spip_amap_paniers',
      'cle_primaire' => 'id_amap_panier',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'amap_panier',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Id auteur',
          'champ' => 'id_auteur',
          'sql' => 'bigint(21) NOT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Id producteur',
          'champ' => 'id_producteur',
          'sql' => 'bigint(21) NOT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Date distribution',
          'champ' => 'date_distribution',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'Dispo',
          'champ' => 'dispo',
          'sql' => 'varchar(3) DEFAULT \'non\'',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => '',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Panier',
        'titre_objet' => 'Panier',
        'info_aucun_objet' => 'Aucun panier',
        'info_1_objet' => 'Un panier',
        'info_nb_objets' => '@nb@ paniers',
        'icone_creer_objet' => 'Créer un panier',
        'icone_modifier_objet' => 'Modifier ce panier',
        'titre_logo_objet' => 'Logo de ce panier',
        'titre_langue_objet' => 'Langue de ce panier',
        'titre_objets_rubrique' => 'Paniers de la rubrique',
        'info_objets_auteur' => 'Les amap de cet auteur',
        'retirer_lien_objet' => 'Retirer ce panier',
        'retirer_tous_liens_objets' => 'Retirer tous les paniers',
        'ajouter_lien_objet' => 'Ajouter ce panier',
        'texte_ajouter_objet' => 'Ajouter un panier',
        'texte_creer_associer_objet' => 'Créer et associer un panier',
        'texte_changer_statut_objet' => 'Ce panier est :',
      ),
      'table_liens' => '',
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'echafaudages' => 
      array (
        0 => 'prive/squelettes/contenu/objets.html',
        1 => 'prive/objets/infos/objet.html',
        2 => 'prive/squelettes/contenu/objet.html',
      ),
      'autorisations' => 
      array (
        'objet_creer' => 'administrateur',
        'objet_voir' => 'administrateur',
        'objet_modifier' => 'administrateur',
        'objet_supprimer' => 'administrateur',
        'associerobjet' => 'administrateur',
      ),
    ),
    1 => 
    array (
      'nom' => 'Responsables',
      'nom_singulier' => 'Responsable',
      'genre' => 'masculin',
      'logo_variantes' => '',
      'table' => 'spip_amap_responsables',
      'cle_primaire' => 'id_amap_responsable',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'amap_responsable',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Id auteur',
          'champ' => 'id_auteur',
          'sql' => 'bigint(21) NOT NULL',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Date distribution',
          'champ' => 'date_distribution',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => '',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Responsables',
        'titre_objet' => 'Responsable',
        'info_aucun_objet' => 'Aucun responsable',
        'info_1_objet' => 'Un responsable',
        'info_nb_objets' => '@nb@ responsables',
        'icone_creer_objet' => 'Créer un responsable',
        'icone_modifier_objet' => 'Modifier ce responsable',
        'titre_logo_objet' => 'Logo de ce responsable',
        'titre_langue_objet' => 'Langue de ce responsable',
        'titre_objets_rubrique' => 'Responsables de la rubrique',
        'info_objets_auteur' => 'Les responsables de cet auteur',
        'retirer_lien_objet' => 'Retirer ce responsable',
        'retirer_tous_liens_objets' => 'Retirer tous les responsables',
        'ajouter_lien_objet' => 'Ajouter ce responsable',
        'texte_ajouter_objet' => 'Ajouter un responsable',
        'texte_creer_associer_objet' => 'Créer et associer un responsable',
        'texte_changer_statut_objet' => 'Ce responsable est :',
      ),
      'table_liens' => 'on',
      'vue_liens' => 
      array (
        0 => 'spip_amap_livraisons',
      ),
      'roles' => '',
      'auteurs_liens' => 'on',
      'vue_auteurs_liens' => '',
      'echafaudages' => 
      array (
        0 => 'prive/squelettes/contenu/objets.html',
        1 => 'prive/objets/infos/objet.html',
        2 => 'prive/squelettes/contenu/objet.html',
      ),
      'autorisations' => 
      array (
        'objet_creer' => 'administrateur',
        'objet_voir' => 'administrateur',
        'objet_modifier' => 'administrateur',
        'objet_supprimer' => 'administrateur',
        'associerobjet' => 'administrateur',
      ),
      'saisies' => 
      array (
        0 => 'objets',
      ),
    ),
    2 => 
    array (
      'nom' => 'Livraisons',
      'nom_singulier' => 'Livraison',
      'genre' => 'feminin',
      'logo_variantes' => '',
      'table' => 'spip_amap_livraisons',
      'cle_primaire' => 'id_amap_livraison',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'amap_livraison',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Date livraison',
          'champ' => 'date_livraison',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Contenu panier',
          'champ' => 'contenu_panier',
          'sql' => 'text',
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => '',
      'champ_date' => '',
      'statut' => '',
      'chaines' => 
      array (
        'titre_objets' => 'Livraisons',
        'titre_objet' => 'Livraison',
        'info_aucun_objet' => 'Aucune livraison',
        'info_1_objet' => 'Une livraison',
        'info_nb_objets' => '@nb@ livraisons',
        'icone_creer_objet' => 'Créer une livraison',
        'icone_modifier_objet' => 'Modifier cette livraison',
        'titre_logo_objet' => 'Logo de cette livraison',
        'titre_langue_objet' => 'Langue de cette livraison',
        'titre_objets_rubrique' => 'Livraisons de la rubrique',
        'info_objets_auteur' => 'Les livraisons de cet auteur',
        'retirer_lien_objet' => 'Retirer cette livraison',
        'retirer_tous_liens_objets' => 'Retirer toutes les livraisons',
        'ajouter_lien_objet' => 'Ajouter cette livraison',
        'texte_ajouter_objet' => 'Ajouter une livraison',
        'texte_creer_associer_objet' => 'Créer et associer un livraison',
        'texte_changer_statut_objet' => 'Cette livraison est :',
      ),
      'table_liens' => 'on',
      'vue_liens' => 
      array (
        0 => 'spip_amap_responsables',
      ),
      'roles' => '',
      'auteurs_liens' => '',
      'vue_auteurs_liens' => '',
      'echafaudages' => 
      array (
        0 => 'prive/squelettes/contenu/objets.html',
        1 => 'prive/objets/infos/objet.html',
        2 => 'prive/squelettes/contenu/objet.html',
      ),
      'autorisations' => 
      array (
        'objet_creer' => 'administrateur',
        'objet_voir' => 'administrateur',
        'objet_modifier' => 'administrateur',
        'objet_supprimer' => 'administrateur',
        'associerobjet' => 'administrateur',
      ),
      'saisies' => 
      array (
        0 => 'objets',
      ),
    ),
  ),
  'images' => 
  array (
    'paquet' => 
    array (
      'logo' => 
      array (
        0 => 
        array (
          'extension' => 'png',
          'contenu' => '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',
        ),
      ),
    ),
    'objets' => 
    array (
      0 => 
      array (
      ),
      1 => 
      array (
      ),
      2 => 
      array (
      ),
    ),
  ),
);

?>