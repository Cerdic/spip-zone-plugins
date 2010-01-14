var onglet_actif = 0;

// compatibilite Ajax : ajouter "this" a "jQuery" pour mieux localiser les actions 
function onglets_init() {
  var cs_bloc = jQuery('div.onglets_bloc_initial', this);
  if(cs_bloc.length) {
	cs_bloc.prepend('<div class="onglets_liste"></div>')
		.children('.onglets_contenu').each(function(i) {
			this.id = 'onglets_contenu_' + i;
			jQuery(this).parent().children('.onglets_liste').append(
				'<h2 id="'+'onglets_titre_' + i + '" class="onglets_titre">' + this.firstChild.innerHTML + '</h2>'
			);
		})
		.children('h2').remove();
	jQuery('div.onglets_liste', this).each(function() {
		this.firstChild.className += ' selected';
		this.nextSibling.className += ' selected';
	});
	jQuery('h2.onglets_titre', this).hover(
		function(){
			jQuery(this).addClass('hover')
		},function(){
			jQuery(this).removeClass('hover')
		}
	);
	jQuery('div.onglets_bloc_initial', this)
		.attr('class','onglets_bloc').each(function(i) {this.id = 'ongl_'+i;});
	// clic du titre...
	jQuery('h2.onglets_titre', this).click(function(e) {
		var contenu = '#' + this.id;
		contenu = contenu.replace(/titre/,'contenu');
		var bloc = jQuery(this).parent().parent();
		bloc.children('.selected').removeClass('selected').end()
			.children('.onglets_liste').children('.selected').removeClass('selected');
		jQuery(contenu).addClass('selected');
		jQuery(this).addClass('selected');
		return false;
	});
	// clic des <a>, au cas ou...
	jQuery('h2.onglets_titre a', this).click(function(e){
		jQuery(this).parents('h2').click();
		if (e.stopPropagation) e.stopPropagation();
		e.cancelBubble = true;
		return false;
	});
	// activation d'un onglet grace a l'url
	if(onglet_get && (this==document)) {
		sel=jQuery('#onglets_titre_'+onglet_get);
		sel.click();
	}
  }
}

function get_onglet(url) {
 tab=url.search.match(/[?&]onglet=([0-9]*)/) || url.hash.match(/#onglet([0-9]*)/);
 if (tab==null) return false;
 return tab[1];
}

var onglet_get = get_onglet(window.location);