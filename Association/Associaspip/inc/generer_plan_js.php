<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

echo "<script type=\"text/javascript\" >\n";
echo "<!-- // --><![CDATA[ // ><!--\n";
foreach ($GLOBALS['association_metas'] as $key => $val) {
	if (substr($key, 0, 6)==="classe") { // ne prendre dans les metas que les classes !!!
		echo "var classe$val = new Array();\n";
		$tableau = association_liste_plan_comptable($val,1);
		foreach ($tableau as $k => $v) {
			if($k!=$GLOBALS['association_metas']['pc_intravirements']) { // code virement interne !!!!
				echo "classe$val" . "['$k'] = '". addslashes($v) ."';\n";
			}
		}
	}
}
echo "// --><!]]>\n";
echo "</script>\n";

?>