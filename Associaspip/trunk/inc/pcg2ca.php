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
	'[1-8S]', //0: classes
	'[\.][1-9]', //1: sections (en fait [13][\.][1-4], 2[\.][1-9], [45][\.][12], [678S][\.][1-3], 9[\.][4-5], en gros)
	'([\.][1-7])?', //2: groupes
	'A' => array(4,5,6,7,9), // classes de gestion
	'B' => array(1,2,3), // classes de bilan
	'C' => '4|(9[\.]4)', // comptes au credit
	'D' => '5|(9[\.]5)', // comptes au debit
);

// http://stds.statcan.gc.ca/coa-pc/main-principal-fra.asp
// http://www.economie.gouv.qc.ca/pageSingleCFile/bibliotheques/outils/gestion-dune-entreprise/gestion-financiere/plan-comptable-et-etats-financiers/?tx_igfileimagectypes_pi1%5Buid%5D=1124&tx_igfileimagectypes_pi1%5BdlImage%5D=1&tx_igfileimagectypes_pi1%5Bindex%5D=0

?>
