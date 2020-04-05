// on ajoute les titres de section au sommaire du aside pour faire des liens vers les sections

$(document).ready(function (){
	infosites_formatage();
	function infosites_sommaire(target){
		if ($('#aside').length>0 && $('#aside .block.sommaire').length==0){
			$('#aside').append('<div class="block sommaire hidden-sm hidden-xs"><div class="list-group"></div></div>');
		}
		if (target.length>0){
			target.each(function (){
				if ($("#aside .sommaire div.list-group a[href='#"+$(this).attr('id')+"']").length==0){
					$('#aside .sommaire div.list-group').append(
						'<a class="list-group-item" href="#'
						+$(this).attr('id')+'">'
						+$(this).text()
						+'</a>');
				}
			});

		}
	}
	 function infosites_formatage(){
		 // On active les tooltips pour la sidebar.
		 $('#aside a').tooltip({
			 placement: 'top',
			 trigger: 'hover'
		 });

		 $('[type=submit]').each(function (event){
			 if (!$(this).hasClass('btn')){
				 $(this).addClass('btn btn-default');
			 }
		 });

		 $('.actions .editbox').each(function (event){
			 if (!$(this).hasClass('btn')){
				 $(this).addClass('btn btn-default');
			 }
		 });

		 $('#content .icone.s24 a').each(function (event){
			 $(this).addClass('btn btn-default');
		 });

		 $('.liste-objets.coordonnees .action a').each(function (event){
			 if (!$(this).hasClass('btn')){
				 $(this).addClass('btn btn-default');
			 }
		 });

		 $('table.spip.liste').each(function (event){
			 $(this).addClass('table table-striped table-bordered');
		 });

		 var rows = $(".page_diagnostic_iso #content table tr.data");
		 rows.each(function (){
			 var cells = $(this).find('td');

			 for (var i = 1; i<cells.length; i++){
				 if (cells.eq(1).html()!=cells.eq(i).html()){
					 cells.eq(1).addClass('bg-warning');
					 cells.eq(i).addClass('bg-warning');
				 }
			 }
			 $(this).find("table tr td").each(function (){
				 $(this).removeClass('bg-warning');
			 });
		 });

		 $('.liste-objets.commits .commit span.titre').click(function (event){
			 event.preventDefault();
			 var target = $(this);
			 var fiche = target.closest('tr').next('.fiche_commit');
			 if (fiche.hasClass('hidden')){
				 fiche.addClass('visible').removeClass('hidden');
				 target.addClass('ouvert').removeClass('ferme');
				 target.find('i').addClass('fa-angle-double-down').removeClass('fa-angle-double-right');
			 } else if (fiche.hasClass('visible')){
				 fiche.addClass('hidden').removeClass('visible');
				 target.addClass('ferme').removeClass('ouvert');
				 target.find('i').addClass('fa-angle-double-right').removeClass('fa-angle-double-down');
			 }
		 });
	 }

	$(document).ajaxComplete(function (){
		infosites_sommaire($('.contenu .legend'));
		infosites_sommaire($('#extra .legend'));
		infosites_formatage();
	});
});
