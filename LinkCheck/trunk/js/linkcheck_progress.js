

function linkcheck_verifier_liens(adresse){
	
	
	$.ajax({
			dataType: "json",
			url: adresse,
			success: function(msg){
				$('#nb_lien_mort').text(msg["nb_lien_mort"])
				$('#nb_lien_malade').text(msg["nb_lien_malade"])
				$('#nb_lien_deplace').text(msg["nb_lien_deplace"])
				$('#nb_lien_ok').text(msg["nb_lien_ok"])
				$('#nb_lien_inconnu').text(msg["nb_lien_inconnu"])
				$('#bar_lien_mort').css("width",msg["pct_lien_mort"]+"%")
				$('#bar_lien_malade').css("width",msg["pct_lien_malade"]+"%")
				$('#bar_lien_deplace').css("width",msg["pct_lien_deplace"]+"%")
				$('#bar_lien_ok').css("width",msg["pct_lien_ok"]+"%")
				if(msg["nb_lien_inconnu"]==0)
					window.location.href = adresse;
				else
					linkcheck_verifier_liens(adresse)
				}
			});
	
	
}
$(document).ready(function(){
	var adresse ='?'+$('#btn_linkcheck_tests form input').serialize();
	
	$('#btn_linkcheck_tests .submit').click(function(){linkcheck_verifier_liens(adresse);
		$('#btn_linkcheck_tests').hide().before('<div class="patience_la_fontaine">« Patience et longueur de temps font plus que force ni que rage. »</div>');
		return false;})
		
		
	})
