<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// I
	'icone_plan' => 'Coup de balai',
	'icone_balayer' => 'Balayer !',

	// T
	'texte_items_proteges' => 'Ces items sont explicitement protégés du coup de balai',
	'texte_items_proteges_par_heritage' => 'Ces items sont protégés par héritage (ils appartiennent à une rubrique protégée)',
	'texte_items_non_proteges' => 'Ces items seront supprimés lors du coup de balai',
	'texte_rubrique_heritier_protege' => 'Cette rubrique contient au moins un élément protégé (elle ne sera pas donc pas supprimée mais elle ne protège pas son contenu)',
	'texte_pointeur' => 'pointe l\'article ou la rubrique dont on vient (s\'il y en a un(e))',
	'texte_protege' => 'Protégé',
	'texte_non_protege' => 'Non protégé',
	'texte_detail' => 'Détail',
	'texte_article_protege_par' => 'Cet article a été protégé par ',
	'texte_article_hierarchie_protegee' =>'Cet article est protégé par hierarchie',
	'texte_article_non_protege' => 'Cet article n\'est pas protégé',
	'texte_rubrique_protegee_par' => 'Cette rubrique et son contenu ont été protégés par ',
	'texte_rubrique_protegee' =>'Cette rubrique est protégée par héritage',
	'texte_rubrique_non_protegee' => 'Cette rubrique n\'est pas protégée',
	'texte_sous_rub_protegee' => 'Elle comporte au moins une sous-rubrique directement protégée',
	'texte_comporte_art_protege' => 'Elle comporte au moins un article protégé',
	'texte_confirmation' => 'Êtes-vous certain de vouloir appliquer le coup de balai ?',
	'texte_info_coup_de_balai' => 'Cliquer sur ce bouton pour appliquer le coup de balai. Une sauvegarde préalable de la base de donnée est vivement conseillée.
    Les articles non protégés seront mis à la poubelle. Les rubriques non protégées et qui ne contiennent aucun item protégé seront supprimées.'
);
