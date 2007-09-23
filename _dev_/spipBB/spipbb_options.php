<?php
//modifiez ici les dossiers squelettes dans l'ordre souhaite
//$dossier_squelettes.=":squelettesforum"; a priori inutile avec le plugin
//permet de se passer du filtre supprimer_numero ecriture des titres 22. untitre
$table_des_traitements['TITRE'][]= 'supprimer_numero(typo(%s))';
?>
