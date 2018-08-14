Le plugin translate_yandex a été créé pour tenter d'utiliser d'autres services de traductions que celui déjà trop connu.

Le plugin translate_yandex nécessite une clef de sécurité
Pour l'utiliser il vous faudra donc

- vous inscrire sur yandex avec un email valide
- puis créer une key sur
https://translate.yandex.com/developers/keys
ou
https://tech.yandex.com/keys/get/?service=trnsl
- et entrer cette clef dans la configuration SPIP du plugin

Vous pouvez également lire les termes d'utilisation de Yandex
https://translate.yandex.com/developers/offer

Le plugin necessite la librairie Yandex à installer dans lib/Yandex (ces répertoires sont à créer)
Cette librairie se trouve sur github https://github.com/yandex-php/translate-api

Pour essayer le plugin, aller sur
/?page=demo/test_translate_yandex

Pour effectuer des traductions à la volée, il vous faut inclure le fichier js/translate_yandex.js
[<script src="(#CHEMIN{js/translate_yandex.js})" type="text/javascript"></script>]
dans le <head> du html
Ajoutez class="translate_me", l'attribut dir="ltr" qui signifie left to right et "rtl" right to left `

exemples

 <div dir="ltr" lang="en" class="translate_me">Hello World</div>
 <div dir="ltr" lang="es" class="translate_me">Buenos dias el mundo</div>
 <div dir="ltr" lang="ar" class="translate_me">مرحبا بالعالم</div>


Pour aller plus loin 
l'API 1.5 du traducteur Yandex est expliqué sur 
https://tech.yandex.com/translate/doc/dg/reference/translate-docpage/#JSON

https://translate.yandex.net/api/v1.5/tr.json/translate
 ? key=<API key>
 & text=<text to translate>
 & lang=<translation direction>
 & [format=<text format>]
 & [options=<translation options>]
 & [callback=<name of the callback function>]
