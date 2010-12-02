<?php
/******************************************************************************************
 * Dépublication permet de dépublier un article ou un auteur  à une date donnée.          *
 * Copyright (C) 2005-2010 Nouveaux Territoires support<at>nouveauxterritoires.fr		  *
 * http://www.nouveauxterritoires.fr							    					  *
 *                                                                                        *
 * Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes *
 * de la Licence Publique Générale GNU publiée par la Free Software Foundation            *
 * (version 3).                                                                           *
 *                                                                                        *
 * Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       *
 * ni explicite ni implicite, y compris les garanties de commercialisation ou             *
 * d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  *
 * pour plus de détails.                                                                  *
 *                                                                                        *
 * Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    *
 * en même temps que ce programme ; si ce n'est pas le cas,								  * 
 * regardez http://www.gnu.org/licenses/ 												  *
 * ou écrivez à la	 																	  *
 * Free Software Foundation,                                                              *
 * Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   *
 ******************************************************************************************/

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'articles_dep' => 'Articles dépubliés',
	'att_nouvel_etat' => 'Attention: Si vous spécifiez ici une valeur, celle-ci sera prioritaire et utilisée par rapport aux valeurs de la liste ci-dessus.',
	'active_depub_auteur' => 'Active la gestion de la date d\'expiration sur les auteurs',
	
	// C
	'configuration' => 'Etat après dépublication d\'un article',

	// D
	'description' => 'Lors de la dépublication d\'un article à la date fixée, le système change l\'etat de l\'article. Veuillez donc sélectionner l\'etat dans lequel doit être l\'article après la dépublication. Par défaut, l\'article est mis dans l\'état \'à la poubelle\'',
	'date_depub_article' => "Veuillez sélectionner la date de dépublication de votre article",
	'date_depub_auteur' => 'Veuillez sélectionner la date d\'expiration de l\'auteur',
	'depublication_article' => 'DATE DE DEPUBLICATION',
	'depublication_auteur' => 'DATE D\'EXPIRATION',
	'date_depub' => 'Date de dépublication',
	'depublication_articles' => 'Dépublication',
	'date_modif'	=> 'Date de mise à jour',
	'delai_unite_secondes' => 'seconde(s)',
	'delai_unite_minutes' => 'minute(s)',
	'delai_unite_heures' => 'heure(s)',
	'delai_unite_jours' => 'jour(s)',
	'delai_unite_semaines' => 'semaine(s)',
	'delai_unite_mois' => 'mois',
	'delai_unite_annees' => 'année(s)',
	'delai' => 'Spécifiez ici le délai par défaut de la dépublication d\'un article ou l\'expiration d\'un auteur. Cette valeur est utilisé lorsque vous validez la dépublication d\'un article ou l\'expiration d\'un auteur sans modifier la date.',
	'depublication' => 'Dépublication',	
	'depublication_articles' => 'Dépublication des articles prévus',
	'default_delai' => 'Par défaut, le délai est de 1 mois',

	// E
	'expiration_auteurs' => 'Expiration des auteurs prévus',
	
	// I
	'info_page' => 'Affichage de tous les articles qui ont été dépubliés par le plugin.<img align="right" src="'._DIR_PLUGIN_DEPUBLICATION.'/images/depublication.png" alt="depublication" title="depublication"/><br/><br/> Vous pouvez donc leur changer l\'état afin qu\'il réapparaisse dans la partie administration.<br/><br/>',

	// N
	'nouvel_etat' => 'Vous pouvez ici ajouter un nouvel état dans lequel passera votre article lors de la dépublication.',
	'nodate' => 'Pas de date fixée pour l\'instant',
	'numero' => 'Id',
	'nom_auteur' => 'Nom de l\'auteur',

	// O
	'options' => 'Options',
	
	// P
	'prepa'=> 'en cours de rédaction',
	'prop' => 'proposé à l\'évaluation',
	'poubelle' => 'à la poubelle',
	'publie' => 'publié',

	// R
	'refuse' => 'refusé',
	
	// S
	'statut_courant' => 'Statut courant',
	'apres_depub' => 'Statut après dépublication',
	'supp_date_article' => 'Supprimer la date de dépublication',
	'supp_date_auteur' => 'Supprimer la date d\'expiration',
	'statut_apres_depublication' => 'Statut après dépublication',
	'statut_apres_expiration' => 'Statut après expiration',
	// T
	'title_infos' => 'Liste des articles dépubliés',
	'titre' => 'Titre de l\'article',
	'titre_articles' => 'Titre de l\'article'



);

?>