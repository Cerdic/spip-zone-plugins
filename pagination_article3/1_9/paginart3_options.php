<?php
global $table_des_traitements;
include_spip('inc/texte');
$table_des_traitements['TEXTE'][]= 'paginart3_paginer(propre(%s))';
tester_variable('BarreTypoEnrichie', 
					array(
						'intertitre_p_debut'=>array(
							"chercher"=>"/(^|[^{])\{p\{/S",
							"remplacer"=>"\$1\n\n<h3 class=\"paginart3\">"),
						'intertitre_p_fin'=>array(
							"chercher"=>"/\}p\}($|[^}])/S",
							"remplacer"=>"</h3>\n\n\$1")
						)
					);

?>