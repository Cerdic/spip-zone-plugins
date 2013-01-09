<?php
/**
* Plugin Amap
*
* @author: Stephane Moulinet
* @author: E-cosystems
* @author: Pierre KUHN 
*
* Copyright (c) 2010-2013
* Logiciel distribue sous licence GPL.
*
**/

// Supprimer systematiquement les numeros des titres
$GLOBALS['table_des_traitements']['TITRE'][]='typo(supprimer_numero(%s),"TYPO",$connect)';
?>
