<?php
/***************************************************************************\
 *  ComptaSPIP, extension comptable
 *
 * @read (licence, copyrigth, authors, credits)
 *  ../plugin.xml
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

$pc_norme = array(
	'[1-9]', //0: classes
	'[0-9]', //1: sections (en fait pas de : 09, 17, 27, 30, 31, 34, 35, 36, 38, 40, 41, 43 a 49, 50, 51, 53 a 59, 70 a 78, 81 a 84, 86, 88, 93 a 98)
	'[0-9]', //2: groupes
	'A' => array(3,4,5,6), // classes administratives (comptes de fonctionnement --charges, revenus--, comptes des investissements --depenses, recettes--)
	'B' => array(1,2), // classes de bilan (actif, passif)
	'C' => '3|(7[45][0])|79|(8[057]0)', // comptes au credit (produits) //! État de Genève : plus classes 4 (Revenus) et 6 (Recettes) ; moins 3 (Charges de fonctionnement) !\\
	'D' => '[456]|(7[45][12])|(8[057][19])', // comptes au debit (charges) //! État de Genève : sauf classes 4 (Revenus) et 6 (Recettes) ; plus 3 (Charges de fonctionnement) !\\
);

// http://www.lpg-fiduciaire-de-suisse.ch/plan-comptable-pour-les-entreprises-suisses.html
// http://campus.hesge.ch/desjacqc/doc/ID/2011/plan_comptable.pdf

?>
