// -------------------------------------
// - transformer les intertitres en tabs
// -------------------------------------
//
// TODO : gerer le fragment #inter-0-1 dans l'URL => ouvre la bonne page
//
$(document).ready(
function() {

// configuration :
var BLOC = 'texte';
var HEADING = 'h3';

if ($('.'+BLOC+' '+HEADING).size() > 1) {
$('.'+BLOC).each(
	function (j) {
		var table='';
		var block = -1;
		var children = this.childNodes;

		for (k=0; k<children.length; k++) {
			if (block==-1 || children[k].tagName == HEADING.toUpperCase()) {
				block ++;
				classe = 'inter-'+j;
				nom = classe+'-'+block;

				children[k].onclick='$(\'.'+classe+'\').slideUp();'
				+ '$(\'#'+nom+'\').filter(\':hidden\').slideDown();';

				table = table
					+ '<li><a onclick="$(\'.'+classe+'\').slideUp();'
					+ '$(\'#'+nom+'\').filter(\':hidden\').slideDown();">'
					+ children[k].innerHTML
					+ '</a></li>';

				$(this).before('<div class="' + BLOC + '">'
					+'<div id="'+nom+'" class="'+classe+'">'
					+'</div>'
					+'</div>');
				$("#"+nom).hide().before(children[k]);

			} else {
				$("#"+nom).append(children[k]);
			}
		}
		$(this).empty();
		$('#inter-'+j+'-0').parent().prepend(
			'<div class="tdm" style="float: right; width: 120px;">'
			+ '<ul>'+table+'</ul>'
			+ '</div>'
			);
		}
);
}
}
);
