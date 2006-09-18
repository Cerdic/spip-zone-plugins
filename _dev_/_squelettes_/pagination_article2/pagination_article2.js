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
var HEADING = pagination_article2_HEADING || 'h3.spip';

if ($('.'+BLOC+'>'+HEADING).size() > 1) {
	$('.'+BLOC).each(
		function (j) {
			//prepend table to the texte block
			$(this).prepend(
				'<div class="tdm breves" style="float: right; width: 120px;">'
				+ '<ul></ul>'
				+ '</div>'
				);
			//get the <ul> for later use
			var table=$('div.tdm>ul',this);

			var h = [];
			//search for indexes of headers
			children = $('>',this).each(
				function(k) {
					if($(this).is(HEADING)) h[h.length] = k;
				}
			)
			h[h.length] = children.size();
			classe = 'inter-'+j;
			//reverse iteration not to change live collection indexes of the elements before the current
			for (var k=h.length-1; k>0; k--) {
				nom = classe+'-'+k;
				//create onclick function. On slideDown complete scroll to the right heading
				eval("var f = function() {"+
					"$('."+classe+"+div:visible').slideUp();$('#"+nom+"+div:hidden').slideDown(null,function(){window.location.hash='"+nom+"';});" +
				"}"); 
				//Get current heading
				var my_heading = $('>:nth-child('+h[k-1]+')',this)
				//bind click function evaluating on each iteration "classe" and "nom"
				.click(f)
				//add the div container and set id and class for the heading
				.after('<div></div>').addClass(classe).set('id',nom)
				//append all elements between the current heading and the next one to the div and hide it
				//that are all children of "this" with index lower than h[k]+1
				//(we've just added a div after the heading) and higher than h[k-1]+1
				//NB: the filter selector must not have ">" since we have already all children in the
				//current set of elements
				.find('+div').append($('>*:lt('+(h[k]+1)+')',this).filter('*:gt('+(h[k-1]+1)+')').get()).hide()
				//go back to the heading and get it
				.end().get(0);
				//build table
				table.prepend('<li><a></a></li>').find('li:first-child a').html(my_heading.innerHTML).click(f).end();
			}
		}
	);
}
if(window.location.hash) $(window.location.hash+'+div').show();
}
);
