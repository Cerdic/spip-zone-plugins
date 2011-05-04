<?php
// Supprimer systematiquement les numeros des titres et noms
$GLOBALS['table_des_traitements']['TITRE'][]= 'typo(supprimer_numero(%s), "TYPO", $connect)';
?>
