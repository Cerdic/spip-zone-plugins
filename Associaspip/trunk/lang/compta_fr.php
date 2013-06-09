<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin & Marcel Bolla & gilcot
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// E
	'erreur_classe_longueur' => 'Une classe comptable est un seul caractère !',
	'erreur_classe_plage' => 'Les classes comptables doivent être comprise dans la plage <em>@intervalle@</em> !', //:asso:erreur_plan_classe
	'erreur_code_classe' => 'Une référence comptable de cette classe doit commencer par <em>@nombre@</em> !',
	'erreur_code_type' => 'La référence comptable <em>@code@</em> ne peut etre de type <em>@interdit@</em> !',
	'erreur_destination_aucune' => 'Pas de destination comptable définie !', //:asso:erreur_pas_de_destination
	'erreur_plan_code_doublon' => 'Référence comptable définie plusieurs fois : <em>@code@</em> !', //:asso:erreur_plan_code_duplique
	'erreur_plan_code_format' => 'Une référence comptable doit comporter au moins <em>@nombre@ caractères</em> non accentuées dont le premier est la classe du compte et les suivants conformes au plan comptable choisi !', //:asso:erreur_plan_code
	'erreur_plan_comptes_actifs' => 'Pas de référence active définie dans le plan comptable !', //:asso:erreur_pas_de_compte
	'erreur_plan_nombre_classes' => 'Pour etre valide, le plan comptable doit comporter <em>@nombre@ classes</em> !',

// I
	'item_no_classe' => '-- Classe comptable indéterminée', //:asso:choisir_classe_compte
	'item_no_code' => '-- Choisir une référence comptable', //:asso:choisir_ref_compte
	'item_no_destination' => '-- Choisir une destination par défaut', //:asso:choisir_dest_compte
	'item_direction_credit' => 'Crédit',
	'item_direction_debit' => 'Débit',
	'item_direction_multi' => 'Multi',

// L
	'label_classe' => 'Classe', //:asso:classe
	'label_code' => 'Code', //:asso:code
	'label_compte_active' => 'Compte activé', //:asso:compte_active
	'label_date_report' => 'Date de report (AAAA-MM-JJ)', //:asso:date_report_aaaa_mm_jj
	'label_destination' => 'Dest. comptable', //:asso:config_libelle_num_dc
	'label_direction_plan' => 'Type d\'opérations', //:asso:direction_plan
	'label_intitule' => 'Intitulé', //:asso:intitule
	'label_reference' => 'Réf. comptable', //:asso:config_libelle_num_pc
	'label_solde_reporte' => 'Solde reporté (en euros)', //:asso:solde_reporte_en_euros

);

?>