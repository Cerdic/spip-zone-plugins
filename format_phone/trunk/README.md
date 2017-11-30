# Formateur de n° de téléphone #

Ce plugin intègre la librairie [phoneformat.js](https://github.com/albeebe/phoneformat.js), pour permettre de formater automatiquement les n° de téléphone.
Il suffit de donner la classe `autoformat-numero` à une balise `<input>` pour que la saisie dans le champ correspondant soit automatiquement formatée au fur et à mesure de la saisie.

Par défaut, les numéros au format local (qui ne commencent pas par un +) sont considérés comme des numéros belges, mais on peut changer ceci en définissant la constante `FORMAT_PHONE_PAYS` dans le fichier `mes_options.php`, comme p.ex :

```php
define('FORMAT_PHONE_PAYS', 'FR');
```
