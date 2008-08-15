<?php
// Parametrage du plugin rainette: 
//	- repertoire des icones personnalises, 
//	- récurrence de mise a jour des donnees meteo, (en secondes)
//	- nombre de jour de previsions (maximum 14)
//	- systeme de mesure metrique (m) ou standard (s)
define ('_RAINETTE_ICONES_PATH','rainette/');
define ('_RAINETTE_RELOAD_TIME_PREVISIONS',2*3600); // pas la peine de recharger un flux de moins de 2h
define ('_RAINETTE_RELOAD_TIME_CONDITIONS',1800); // pas la peine de recharger un flux de moins de 30mn
define ('_RAINETTE_JOURS_PREVISION',7);
define ('_RAINETTE_SYSTEME_MESURE','m');
?>
