<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/popup/spipopup_spip3/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// S
	'spipopup_description' => 'Gestion d’une fenêtre popup ({fenêtre externe}) unique sous forme de squelette SPIP et aux dimensions réglables pour différents usages.

{{Utilisation de la balise #POPUP }}
<code>
#POPUP{objet SPIP,squelette,width,height,titre,options}
</code>
- {{objet SPIP}} : ’article1’ ou ’id_article=1’ (valable par défaut pour tout objet éditorial de SPIP).
- {{squelette}} : squelette utilisé pour afficher la fenêtre ({optionnel - par défaut : ’{{popup_defaut.html}}’}).
- {{width}} : la largeur de la fenêtre en pixels ({optionnel - {{620px}} par défaut}).
- {{height}} : la hauteur de la fenêtre en pixels ({optionnel - {{640px}} par défaut}).
- {{titre}} : le titre ajouté au lien.
- {{options}} : un tableau d’options JavaScript pour la nouvelle fenêtre ({location, status ...}).

{{Utilisation du modèle dans les articles}}
<code>
<popup
|texte=le texte du lien (necessaire)
|lien=objet SPIP pour le lien (necessaire)
|skel=squelette (option)
|width=XX (option)
|height=XX (option)
|titre=mon titre (option)
>
</code>
Mêmes options que la balise, le texte du lien en plus.

{{Retour de la balise #POPUP }}

La balise retourne un tag de lien (<code>a</code>) avec les attributs suivants :
- href = " url "
- onclick = " _popup_set(’url’, width, height, options) ; return false ; " 
- title = " titre - nouvelle fenêtre "
',
	'spipopup_slogan' => 'Gestion d’une fenêtre popup unique en squelette SPIP'
);

?>
