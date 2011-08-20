<?php
// Supprimer systematiquement les numeros des titres
$GLOBALS['table_des_traitements']['TITRE'][]='typo(supprimer_numero(%s),"TYPO",$connect)';
?>
