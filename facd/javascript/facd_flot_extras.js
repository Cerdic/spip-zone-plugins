/**
 * Une fonction de tooltip utilisée par flot
 *
 * @param x la position horizontale de l'objet sur lequel mettre le tooltip
 * @param y la position verticale de l'objet sur lequel mettre le tooltip
 * @param contents Le contenu du tooltip
 * @param id l'ID à donner au tooltip
 * @return
 */
function facd_plot_showtooltip(x, y, contents,id) {
	id = id ? id : 'tooltip';
    $('<div id="'+id+'">' + contents + '</div>').css( {
        top: y + 5,
        left: x + 5
    }).appendTo("body").fadeIn(200);
}

/**
 * Une fonction de trim js
 * @param string La chaine à trimmer
 * @return La chaine sans espaces autour
 */
function facd_plot_trim(string){
	return string.replace(/^\s+/g,'').replace(/\s+$/g,'')
}