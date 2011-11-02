<?php

/* * *************************************************************************
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 08/2011                                      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */

echo "<script type=\"text/javascript\" >\n";
echo "<!-- // --><![CDATA[ // ><!--\n";

foreach ($GLOBALS['association_metas'] as $key => $val) {
	if (substr($key, 0, 6) === "classe") { // ne prendre dans les metas que les classes !!!
		echo "classe" . $val . " = new Array();\n";
		$tableau = association_liste_plan_comptable($val);
		foreach ($tableau as $k => $v) {
			echo "classe" . $val . "[" . $k . "]='" . $v . "';\n";
		}
	}
}

echo "// --><!]]>\n";
echo "</script>\n";
?>
