<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-saveauto?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// S
	'saveauto_description' => 'Umožňuje realizovať zálohovanie celej databázy MySQL, ktorú používa SPIP.
			Získaný súbor .zip (alebo .sql) bude uložený do priečinka (podľa predvolených nastavení /tmp/dump, configurable)
			a môžete ho poslať aj e-mailom.

			Zálohy,  ktoré sa považujú za zastaralé (v závislosti od príslušného nastavenia v konfigurácii)
			sa automaticky odstraňujú.
		
Rozhranie umožňuje manuálne spúšťanie zálohovania a rozhodovať o vytvorených súboroch',
	'saveauto_nom' => 'Automatické zálohovanie',
	'saveauto_slogan' => 'Automatická záloha MySQL údajov v databáze SPIPu'
);

?>
