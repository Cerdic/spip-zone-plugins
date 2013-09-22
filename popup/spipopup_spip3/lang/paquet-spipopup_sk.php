<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-spipopup?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// S
	'spipopup_description' => 'Unikátne ovládanie vyskakovacieho okna ({externého okna}) vo forme šablóny SPIPu a s rozmermi, ktoré sa dajú prispôsobiť podľa použitia.

{{Použitie tagu #POPUP }}
<code>
#POPUP{objekt SPIPu,šablóna,šírka,výška,nadpis,vlastnosti}
</code>
- {{objekt SPIPu:}} ’article1’ alebo ’id_article=1’ (predvolená premenná pre každý redakčný objekt SPIPu).
- {{šablóna:}} šablóna, ktorá sa použije na zobrazenie okna  ({nepovinné – predvolené: ’{{popup_defaut.html}}’}).
- {{šírka:}} šírka okna v pixeloch ({nepovinné – predvolené}{{620 px}}).
- {{výška:}} výška okna v pixeloch ({nepovinné – predvolené} {{640 px}}).
- {{nadpis:}} nadpis, ktorý bude pripojený k odkazu.
- {{vlastnosti:}} tabuľka s vlastnosťami JavaScriptu pre nové okno ({poloha, status, a i.}).

{{Použitie šablóny v článkoch}}
<pre>
<popup
|texte=text odkazu (povinné)
|lien=objekt SPIPu pre odkaz(povinné)
|skel=šablóna (nepovinné)
|width=XX (nepovinné)
|height=XX (nepovinné)
|titre=môj nadpis (nepovinné)
>
</pre>
Rovnaké vlastnosti ako tag, text odkazu a iné.

{{Výpis z tagu #POPUP }}

Tag vypíše tag odkazu (<code>a</code>) s týmito parametrami:
- href = " url "
- onclick = " _popup_set(’url’, šírka, výška, vlastnosti); return false; " 
- title = " nadpis – nové okno "',
	'spipopup_slogan' => 'Ovládanie jedinečnej šablóny SPIPu pre vyskakovacie okno'
);

?>
