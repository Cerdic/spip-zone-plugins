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
	'erreur_classe_plage' => 'Les classes comptables doivent être comprise dans la plage @intervalle@ !',
	'erreur_code_classe' => 'Une référence comptable de cette classe doit commencer par @nombre@ !',
	'erreur_code_longueur' => 'Une référence comptable doit avoir au moins @nombre@ caractères !',
	'erreur_plan_code_doublon' => 'Référence comptable définie plusieurs fois : <em>@code@</em> !',
	'erreur_plan_code_format' => 'Une référence comptable est composée uniquement de caractères non accentuées et doit comporter @nombre@ caractères dont le premier est la classe du compte !',
	'erreur_plan_nombre_classes' => 'Pour etre valide, le plan comptable doit comporter @nombre@ classes !',

);

?>