<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2012-04-20 18:19:48
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
    'nom' => 'Séminaire LATP',
    'slogan' => 'G&#233;rer les &#233;v&#233;nements d\\\'un laboratoire de recherche',
    'description' => 'Chaque &#233;v&#233;nement est associ&#233; à des mots-cl&#233;s qui permettent, d’une part, de fixer son type, s&#233;minaire, colloque, groupe de travail... et d’autre part, de d&#233;finir le laboratoire ou l’organisme responsable. La gestion des &#233;v&#233;nements est assur&#233;e par le plugin Agenda.',
    'prefixe' => 'seminaire',
    'version' => '2.0.0',
    'auteur' => 'Amaury Adon',
    'auteur_lien' => 'http://www.spip-contrib.net/Amaury-Adon',
    'licence' => 'GNU/GPL',
    'categorie' => 'date',
    'etat' => 'dev',
    'compatibilite' => '[3.0.0-rc;3.0.*]',
    'documentation' => 'http://www.spip-contrib.net/Seminaire-LATP',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'seminaire',
    'inserer' => 
    array (
      'paquet' => '	<necessite nom="agenda" compatibilite="[3.5.1;]" />
	<necessite nom="cextras" compatibilite="[3.0.5;[" />
	<utilise nom="kitcnrs" compatibilite="[4.0.10;]" />
	<utilise nom="Z" compatibilite="[1.7.17;]" />
	<pipeline nom="declarer_champs_extras" inclure="base/seminaire.php" />',
      'administrations' => 
      array (
        'maj' => 'cextras_api_upgrade(seminaire_declarer_champs_extras(), $maj[\'create\']);
',
        'desinstallation' => '',
        'fin' => '',
      ),
      'base' => 
      array (
        'tables' => 
        array (
          'fin' => 'if (!defined("_ECRIRE_INC_VERSION")) return;

function seminaire_declarer_champs_extras($champs = array()){
	$champs[\'spip_evenements\'][\'name\'] = array(
		\'saisie\' => \'input\',// type de saisie
		\'options\' => array(
			\'nom\' => \'name\',
			\'label\' => _T(\'seminaire:name\'), 
			\'sql\' => "varchar(256) NOT NULL DEFAULT \'\'", // declaration sql
			\'rechercher\'=>true,
			\'defaut\' => \'\',	
	));
	$champs[\'spip_evenements\'][\'origin\'] = array(
		\'saisie\' => \'input\',
		\'options\' => array(
			\'nom\' => \'origin\', // nom sql
			\'label\' => _T(\'seminaire:origin\'), 
			\'sql\' => "varchar(256) NOT NULL DEFAULT \'\'", // declaration sql
			\'rechercher\'=>true,
			\'defaut\' => \'\',	
	));
	$champs[\'spip_evenements\'][\'abstract\'] = array(
		\'saisie\' => \'textarea\',
		\'options\' => array(
			\'nom\' => \'abstract\', // nom sql
			\'label\' => _T(\'seminaire:abstract\'), 
			\'sql\' => "text NOT NULL DEFAULT \'\'", // declaration sql
			\'rechercher\'=>true,
			\'defaut\' => \'\',	
			\'rows\' => 4,
			\'traitements\' => \'_TRAITEMENT_RACCOURCIS\',
			\'class\'	=>\'\',
	));	
	$champs[\'spip_evenements\'][\'notes\'] = array(
		\'saisie\' => \'textarea\',
		\'options\' => array(
			\'nom\' => \'notes\', // nom sql
			\'label\' => _T(\'seminaire:notes\'), 
			\'sql\' => "text NOT NULL DEFAULT \'\'", // declaration sql
			\'rechercher\'=>true,
			\'defaut\' => \'\',	
			\'rows\' => 4,
			\'traitements\' => \'_TRAITEMENT_RACCOURCIS\',
	));
	
	return $champs;',
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
    ),
  ),
);

?>