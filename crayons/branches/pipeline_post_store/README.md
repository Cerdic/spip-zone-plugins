# plugins Crayons

## Documentation 

https://contrib.spip.net/Les-crayons

https://contrib.spip.net/Crayons-Controleurs-et-Vues

## Notes 

### Definir de maniere personalisee quel input mode (ligne ou texte) utiliser champ par champ:
Exemple de fonction personnalisée (à mettre dans le *mes_options.php* par exemple)

cf https://git.spip.net/spip-contrib-extensions/crayons/commit/a91edba87999a1c2670df9f5c107eba1f1ac1729
```
/**
 * imposer textarea sur les crayons pour des champs extra de la table spip_rubriques
 * cette fonction est appelée auto-magiquement par action/crayons_html.php
 * 
 * @param $type : l'objet SPIP (article, rubrique...)
 * @param $champ : le nom du champ
 * @param $sqltype la description SQL du champ (?)
 * @return : ligne | texte
 * 
 **/
function crayons_determine_input_mode_type_rubrique($type, $champ, $sqltype) {
	// array de tous les champs devant êtres en textarea
	$Ttextarea = array('titre_machin', 'texte_truc', 'champ_bidule');

	return in_array($champ, $Ttextarea) ? 'texte' : 'ligne';
}
```
