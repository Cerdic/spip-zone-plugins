<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-coloration_code?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'coloration_code_description' => 'Het volstaat om de code tussen bakens te plaatsen:
_ {{&lt;code class="langage"&gt;...&lt;/code&gt;}}
_ of met een kader:
_ {{&lt;cadre class="langage"&gt;...&lt;/cadre&gt;}}.

Herkende talen zijn die welke ondersteund worden door [->http://sourceforge.net/projects/geshi/] met een extra class: "spip".


Wanneer de code uit meer dan één regel bestaat, wordt ze in tekst in de cache gezet en kan ze worden gedownload. Deze functionaliteit wordt globaal mogelijk gemaakt met de constante PLUGIN_COLORATION_CODE_TELECHARGE. Ze kan individueel worden in- of uitgeschakeld met class "sans_telechargement" of "chargement", zoals in dit voorbeeld:
_ {{&lt;code class="php sans_telechargement"&gt;}}

Je kunt ook in een skelet gebruik maken van het filter {coloration_code_color}:
_ <code>#TEXTE**|coloration_code_color{spip,code}</code>: kleurt #TEXTE met de taal spip in code-formaat (zonder kader), zie voorbeeld lecode.html. De URL vanuit een artikel is <code>#URL_SITE_SPIP/spip.php?page=lecode&id_article=#ENV{id_article}</code>',
	'coloration_code_nom' => 'Gekleurde Code',
	'coloration_code_slogan' => 'Pas een op syntax gebaseerde kleurcode toe op broncode in artikelen'
);

?>
