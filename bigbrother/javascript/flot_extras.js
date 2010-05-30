/**
 * Une fonction de tooltip utilisée par flot
 *
 * @param x la position horizontale de l'objet sur lequel mettre le tooltip
 * @param y la position verticale de l'objet sur lequel mettre le tooltip
 * @param contents Le contenu du tooltip
 * @param id l'ID à donner au tooltip
 * @return
 */
function plot_showtooltip(x, y, contents,id) {
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
function plot_trim(string){
	return string.replace(/^\s+/g,'').replace(/\s+$/g,'')
}

/**
 * Une fonction traduisant un nombre de milisecondes dans une durée lisible
 *
 * @param ms
 * @return
 */
function plot_ms_to_timereadable(ms){
	var seconds = ms/1000;
	var uTime = Math.round(seconds*Math.pow(10,0))/Math.pow(10,0);
	var days = Math.floor(uTime/(3600*24));
	days=(days >0) ? days+'d ' : '';
	var hours = (Math.floor(uTime/3600)%24);
	hours=(hours >0) ? (hours<10?'0'+hours:hours)+':' : '';
	var minutes=(Math.floor(uTime/60)%60);
	minutes= minutes<10?'0'+minutes:minutes;
	var seconds=(uTime%60);
	seconds=seconds<10?'0'+seconds:seconds;
	var time = days+hours+minutes+':'+seconds;
	return time;
}