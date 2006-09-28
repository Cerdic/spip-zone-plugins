<?php
global $table_des_traitements;
include_spip('inc/texte');
$table_des_traitements['TEXTE'][]= 'paginart3_paginer(propre(%s))';
tester_variable('debut_intertitre_p', "<h3 class=\"paginart3\">");
tester_variable('fin_intertitre_p', "</h3>");
?>