<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/paquet-spipopup?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// S
	'spipopup_description' => 'Manage a single popup window ({external window}) as a SPIP skeleton with adjustable size for different uses.

{{#POPUP tag use}}
<code>
#POPUP{object SPIP,skeleton,width,height,title,options}
</code>
- {{objet SPIP}} : ’article1’ or ’id_article=1’ (Valid by default  for any editorial SPIP purpose).
- {{squelette}} : skeleton used to display the window ({optionnal - by default : ’{{popup_defaut.html}}’}).
- {{width}} : window width in pixels ({optionnel - {{620px}} by default}).
- {{height}} : window height in pixels ({optionnal - {{640px}} by default}).
- {{titre}} : the title added to the link.
- {{options}} : A JavaScript table options for the new window ({location, status ...}).

{{Model use in your articles}}
<code>
<popup
|text=text of the link (mandatory)
|link=SPIP object for the link (mandatory)
|skel=skeleton (option)
|width=XX (option)
|height=XX (option)
|title=my title (option)
>
</code>
Same options than the tag, the text of the link has been added.

{{Back to the #POPUP tag}}

The tag gives a tag link (<code>a</code>) with the following attributs :
- href = " url "
- onclick = " _popup_set(’url’, width, height, options); return false; " 
- title = " title - new window "
',
	'spipopup_slogan' => 'Manage a single popup window in SPIP skeleton'
);

?>
