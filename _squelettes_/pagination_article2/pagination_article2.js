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

if ($('.'+BLOC+'>'+HEADING).size() > 1) {
	$('.'+BLOC).each(
		function (j) {
			
			//prepend table to the texte block 
			$(this).prepend(
				'<div class="tdm" style="float: right; width: 120px;">'
				+ '<ul></ul>'
				+ '</div>'
				);
			//get the <ul> for later use		
			var table=$('div.tdm>ul',this);

			var h = [];
			//search for indexes of headers
			childrens = $('>',this).each(
				function(k) {
					if(this.tagName == HEADING.toUpperCase()) h[h.length] = k; 
				}
			)
			h[h.length] = childrens.size();
			classe = 'inter-'+j;
			//reverse iteration not to change live collection indexes of the elements before the current
			for (k=h.length-1; k>0; k--) {
				nom = classe+'-'+k;
				//create onclick function
				eval('var f = function() {'+
					'$(\'.'+classe+':visible\').slideUp();$(\'#'+nom+'\').filter(\':hidden\').slideDown();' +
				'}'); 
				//Get current heading
				var my_heading = $(':nth-child('+h[k-1]+')',this)
				//bind click function evaluating on each iteration "classe" and "nom"
				.click(f)
				//add the div container and get the heading for later use
				.after('<div id="'+nom+'" class="'+classe+'"></div>').get(0);
				//append all elements between the current heading and the next one to the div and hide it
				//that are all children of "this" with index lower then h[k]+1 
				//(we've just added a div after the heading) and higher than h[k-1]+1
				//NB: the filter selector must not have ">" since we have already all children in the
				//current set of elements
				$('#'+nom).append($('>*:lt('+(h[k]+1)+')',this).filter('*:gt('+(h[k-1]+1)+')').get()).hide();
				//build table
				table = table.prepend('<li><a></a></li>').find('li:first-child a').click(f).html(my_heading.innerHTML).end();
				//manage link inside the page to the blocks
				//$('a[@href$="#'+nom+'"]').click(f);
			}
		}
	);
}
if(window.location.hash) $(window.location.hash).show();
}
);
