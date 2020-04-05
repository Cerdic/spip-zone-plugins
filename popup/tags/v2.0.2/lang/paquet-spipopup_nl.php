<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/paquet-spipopup?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// S
	'spipopup_description' => 'Beheer van een uniek popup-venster in de vorm van een SPIP skelet en met instelbare afmetingen voor verschillende toepassingen.

{{Gebruik van het baken #POPUP }}
<code>
#POPUP{SPIP object,skelet,breedte,hoogte,titel,opties}
</code>
- {{SPIP object}}: ’article1’ of ’id_article=1’ (geldig voor alle editoriale objecten van SPIP).
- {{skelet}}: voor de weergave te gebruiken skelet ({optioneel, standaardwaarde: ’{{popup_defaut.html}}’}).
- {{breedte}}: de breedte van het venster in pixels ({optioneel, standaardwaarde {{620px}} }).
- {{hoogte}}:  de hoogte van het venster in pixels ({optioneel, standaardwaarde {{640px}} }).
- {{titel}}: de aan de link te koppelen titel.
- {{opties}}: een tabel van JavaScript opties voor het nieuwe venster ({plaatsings, status ...}).

{{Gebruik van het model in artikelen}}
<code>
<popup
|texte=de tekst van de link (verplicht)
|lien=SPIP object voor de link (verplicht)
|skel=skelet (optioneel)
|width=XX (optioneel)
|height=XX (optioneel)
|titre=titel (optioneel)
>
</code>
Dezelfde opties als bij het baken, maar ook een tekst voor de link.

{{Resultaat van het baken #POPUP }}

Het baken resulteert in een tag (<code>a</code>) met de volgende attributen:
- href = " url "
- onclick = " _popup_set(’url’, width, height, options) ; return false ; " 
- title = " titel - niuewe venster "
',
	'spipopup_slogan' => 'Beheer van een uniek popup venster in SPIP skelet'
);
