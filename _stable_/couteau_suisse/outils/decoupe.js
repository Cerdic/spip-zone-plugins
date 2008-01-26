var onglet_actif = 0;

if (window.jQuery) jQuery(document).ready(function() {
  if(jQuery('div.onglets_bloc_initial').length) {
	bloc = jQuery('div.onglets_bloc_initial');
	bloc.prepend('<div class="onglets_liste"></div>');
	bloc.children('.onglets_contenu').each(function(i) {
			this.id = 'onglets_contenu_' + i;
			jQuery(this).parent().children('.onglets_liste').append(
				'<h2 id="'+'onglets_titre_' + i + '" class="onglets_titre">' + this.firstChild.innerHTML + '</h2>'
			);
		})
		.children('h2').remove();
	jQuery('div.onglets_liste').each(function() {
		this.firstChild.className += ' selected';
		this.nextSibling.className += ' selected';
	});
	jQuery('h2.onglets_titre').hover(
		function(){
			jQuery(this).addClass('hover')
		},function(){
			jQuery(this).removeClass('hover')
		}
	);
	bloc.attr('class','onglets_bloc').each(function(i) {this.id = 'ongl_'+i;});
	// clic du titre...
	jQuery('h2.onglets_titre').click(function(e) {
		var contenu = '#' + this.id;
		contenu = contenu.replace(/titre/,'contenu');
		jQuery(this).parent().parent().find('.selected').removeClass('selected');
		jQuery(contenu).addClass('selected');
		jQuery(this).addClass('selected');
		return false;
	});
	// clic des <a>, au cas ou...
	jQuery('h2.onglets_titre a').click(function(e){
		jQuery(this).parent().click();
		if (e.stopPropagation) e.stopPropagation();
		e.cancelBubble = true;
		return false;
	});
	// activer un onglet grace a l'url
	if(onglet_get) {
		sel=jQuery('#onglets_titre_'+onglet_get);
		sel.click();
	}
  }
});

function get_onglet(url) {
 tab=url.match(/[?&]onglet=([0-9]*)/);
 if (tab==null) return false;
 return tab[1];
}

var onglet_get = get_onglet(window.location.search);