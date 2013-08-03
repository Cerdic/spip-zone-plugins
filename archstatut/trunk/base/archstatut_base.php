<?php
/**
 * Utilisations de pipelines par ArchivageStatut
 *
 * @plugin     Archivage
 * @copyright  2013
 * @author     Luc Tech
 * @licence    GNU/GPL
 * @package    SPIP\Arch\Base\Base
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Appel au pipeline declarer_tables_objets_sql.
 *
 * Ajout du statut "archive" et infos associees pour les articles et les breves
 *
 */
function archstatut_declarer_tables_objets_sql($tables) {


  /* Ajout statut archive pour ARTICLES */
  /* tableau de reference dans ecrire/base/objets.php */
  
  /* pre-requis = declaration d'un tableau "statut_images" 
  pour que la fonction "statut_image" (ecrire/inc/puce_statut.php) 
  l'utilise au lieu du case en dur prevu pour les articles.
  (peut-etre que cela sera inclus dans les prochaines versions de SPIP ?!, 
  comme c'est deja fait pour les auteurs dans la declaration dans "lister_tables_objets_sql" 
  (ecrire/base/objets.php) */
  $tables['spip_articles']['statut_images'] 
				=  array(
					'prepa' => 'puce-preparer-8.png',
					'prop' => 'puce-proposer-8.png',
					'publie' => 'puce-publier-8.png',
					'refuse' => 'puce-refuser-8.png',
					'poubelle' => 'puce-supprimer-8.png',
					'poub' => 'puce-supprimer-8.png',
				); 
/* ajout des champs pour statut "archive" des articles */
$tables['spip_articles']['statut_images'] = 
			$tables['spip_articles']['statut_images']
			+ array('archive' => 'puce-archiver-8.png')
			;
$tables['spip_articles']['statut_textes_instituer'] = 
			$tables['spip_articles']['statut_textes_instituer']
			+ array('archive' => 'archstatut:texte_statut_archive')
			;
 $tables['spip_articles']['statut_titres'] = 
			$tables['spip_articles']['statut_titres']
			+ array('archive' => 'archstatut:info_article_archive')
			;
 
   /* Ajout statut archive pour BREVES */
   
   /* pre-requis = declaration d'un tableau "statut_images" 
  pour changer comportement de la fonction "statut_image" (ecrire/inc/puce_statut.php) */
  $tables['spip_breves']['statut_images'] 
				=  array(
					'prop' => 'puce-proposer-8.png',
					'publie' => 'puce-publier-8.png',
					'refuse' => 'puce-refuser-8.png',
				); 
   /* = completer les declarations de plugins-dist/breves/base/breves.php */
   /* statut s'appelle "archiv" (sans "e") car colonne limitee a 6 carac dans table spip_breves */
$tables['spip_breves']['statut_images'] = 
			$tables['spip_breves']['statut_images']
			+ array('archiv' => 'puce-archiver-8.png')
			;
			$tables['spip_breves']['statut_textes_instituer'] = 
			$tables['spip_breves']['statut_textes_instituer']
			+ array('archiv' => 'archstatut:texte_statut_archive_breve')
			;
 $tables['spip_breves']['statut_titres'] = 
			$tables['spip_breves']['statut_titres']
			+ array('archiv' => 'archstatut:info_breve_archive')
			;
 
	return $tables;
}


?>