// notessurgissantes:
// -----------------
// quand on survole un appel de note, ajouter '.notessurgissantes'
// sur la note concernee ; la css la fera flotter en bas
//   in_spip_note indique si la souris est sur les notes
//   (pour eviter un clignotement)

$(function() {

	var in_spip_note = false; 
	$('a.spip_note[rel=footnote]')
	.hover(
		function() {
			var idnote = $(this).attr('href').replace(/^.*#/,'#');
			$(idnote).addClass('notessurgissantes');
		},
		function(){
			setTimeout(function() {
				if (!in_spip_note)
					$('.notessurgissantes')
					.removeClass('notessurgissantes');
			}, 50);
		}
	).click(function(){
		$('.notessurgissantes')
		.removeClass('notessurgissantes');
	});
	$('.spip_note[rev=footnote]').parent()
	.hover(
		function() {
			in_spip_note = true;
		},
		function(){
			in_spip_note = false;
			$('.notessurgissantes')
			.removeClass('notessurgissantes');
		}
	);

});
