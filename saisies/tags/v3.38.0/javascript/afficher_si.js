$(function(){
	$('form').each(function(){
		afficher_si = $(this).find('[data-afficher_si]').each(function(){
			form = $(this).parents('form');
			verifier_afficher_si(form, $(this), true);
			}
		);
		$(this).find('texteara, input, select').change(function(){
				form = $(this).parents('form');
				name = $(this).attr('name').replace(/\\[.*\\]/,'');
				form.find('[data-afficher_si*=\''+name+'\']').each(function(){
					verifier_afficher_si(form, $(this));
				})
		})
	})
})
function verifier_afficher_si(form, saisie, chargement) {
	var condition = saisie.attr('data-afficher_si');
	condition = eval(condition);
	if (condition) {
		afficher_si_show(saisie);
		saisie.removeClass('afficher_si_masque').addClass('afficher_si_visible');
		saisie.find('[data-afficher-si-required]').attr('required', true).attr('data-afficher-si-required',false);
	} else {
		if (chargement) {
			afficher_si_hide(saisie);
			saisie.css('display','none');
		} else {
			afficher_si_hide(saisie);
		}
		saisie.addClass('afficher_si_masque').removeClass('afficher_si_visible');
		saisie.find('[required]').attr('required', false).attr('data-afficher-si-required', null);
	}
}
