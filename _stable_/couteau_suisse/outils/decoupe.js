var onglet_actif = 0;

jQuery(document).ready(function() {
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
	bloc.attr('class','onglets_bloc');
	jQuery('h2.onglets_titre').click(
		function(e) {
			var contenu = '#' + this.id;
			contenu = contenu.replace(/titre/,'contenu');
			jQuery(this).parent().parent().find('.selected').removeClass('selected');
			jQuery(contenu).addClass('selected');
			jQuery(this).addClass('selected');
		}
	);
  }
});
