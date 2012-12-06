$(document).ready(function(){
	
	// preparer le chargement ajax
	$('body').append('<div class="charger_ticketspublics"></div>');
	
	// afficher/masquer la feuille de route
	$('#btn_ticketspublics').live("click",function(){
		var url_ticketspublics = $(this).attr('href');
		$('.charger_ticketspublics').load(url_ticketspublics,function(){
			$(this).toggle(200);
		});
		return false;
	});
	// + un bouton special pour refermer
	$('.fermer').live("click",function(){
		$('.charger_ticketspublics').toggle(function(){
			$(this).empty();
		});
	});
	
	// des boutons pour trier
	$('a.trier_ticketspublics').live("click",function(){
		$('.liste').animate({opacity:'.5'});
		var trier = $(this).attr('href');
		$('.liste').load(trier,function(){
			$('.liste').animate({opacity:'1'});
		});
		return false;
	});
// 	// sans ajaxReload http://api.jquery.com/change/
// 	$("#id_parent").change(function () {
// 		$('.charge_communes').animate({opacity:'.5'});
// 			var str = "";
// 			$("select option:selected").each(function () {
// 				var communes = $(this).attr('onclick');
// 				$('.charge_communes').load(communes,function(){
// 					$('.charge_communes').animate({opacity:'1'});
// 				});
// 			});
// 	})
// 	.change();
	
	// afficher/masquer des tiroirs avec des poignees
	$('a.poignee').live("click",function(){
		$(this).toggleClass('ouvert').siblings('.tiroir').slideToggle();
		return false;
	});
	
});