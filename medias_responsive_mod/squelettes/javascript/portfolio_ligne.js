
if (typeof portfolio_ligne_marge === 'undefined') var portfolio_ligne_marge = 10;
if (typeof portfolio_ligne_taille === 'undefined') var portfolio_ligne_taille = 300;



function calculer_portfolio_ligne () {
	
	/* Concaténer les ul.portfolio_ligne qui se suivent */
	/* Rendu inutile par post_echappe_html_propre */
	$("ul.portfolio_ligne + ul.portfolio_ligne").each(function() {
		var t = $(this);
		var contenu = t.html();
		t.prev("ul.portfolio_ligne").append(contenu);
		t.remove();
	});

	/* Fonction principale: aligner les images sur des lignes */
	$(".portfolio_ligne").each(function() {
	
		var m_right = "margin-right";
		var m_left = "margin-left";
		if ($(this).css("direction") == "rtl") {
			m_right = "margin-left";
			m_left = "margin-right";
		}
	
		$(this).css("padding", 0).css("padding-bottom", -1*portfolio_ligne_marge+"px")
			.find("li").css(m_right, portfolio_ligne_marge+"px")
			.css("margin-bottom", portfolio_ligne_marge+"px")
			.css("clear", "");
		
		// Eviter les problèmes d'arrondi…
		$(this).width("auto");
		var l_max = Math.floor($(this).width());
		$(this).width(l_max);
		var taille_max = portfolio_ligne_taille;
		
		if (l_max > 1400) taille_max *= 1.2;
		else if (l_max <= 1024 && l_max > 768) taille_max *= 0.9;
		else if (l_max <= 768 && l_max > 520) taille_max *= 0.8;
		else if (l_max <= 520 && l_max > 420) taille_max *= 0.7;
		else if (l_max <= 420) taille_max *= 0.6;
		
		var num_deb = 1;
		var num = 0;
		var l_total = 0;
		var l_temp = 200;
		var l_cont = 200;
		var h_cont = 200;
		var rapport = 1;
		var r_cont = new Array();
		
		$(this).find("li").each(function() {
			num++
						
			l_cont = $(this).attr("data-width");
			h_cont = $(this).attr("data-height");
			r_cont[num] = l_cont / h_cont;
			
			l_temp = r_cont[num] * taille_max;
			
			
			if (l_total + l_temp > l_max - ((num-1)-num_deb)*portfolio_ligne_marge) {
				
				rapport = ( l_max - ((num-1)-num_deb)*portfolio_ligne_marge) / ( l_total );
				hauteur = taille_max * rapport;
				hauteur_boite = Math.round(hauteur);
								
				var total_ligne  = 0;				
				for (i = num_deb; i < num; i++) {
				
				
					var t = $(this).parent("ul").find("li:nth-child("+i+")");
					

					if (total_ligne == 0) t.css("clear", "both");

					
					var h = Math.round(hauteur * r_cont[i]);
					
					if (i == num-1) {
						h = l_max  - total_ligne;
						t.css(m_right, "0");
						total_ligne = 0;
						
					} else {
						total_ligne += h + portfolio_ligne_marge;
					}
					
					t.width(h);
					t.find(".fond").height(hauteur_boite);
				}
				l_total = 0;
				num_deb = num;
			}
			
			l_total += l_temp;
			
			if ($(this).is(":last-child")) {
				$(this).css(m_right, 0);
				
				var rapport = false;
				if ( l_total + ((num-1)-num_deb)*portfolio_ligne_marge > 0.3*l_max) {
					rapport = ( l_max -  ((num)-num_deb)*portfolio_ligne_marge ) / l_total;
					hauteur = taille_max * rapport;
					hauteur_boite = Math.round(hauteur);
				}
				
				var total_ligne  = 0;				
				for (i = num_deb; i <= num; i++) {
					var t = $(this).parent("ul").find("li:nth-child("+i+")");
					
					if (total_ligne == 0) t.css("clear", "both");

					if (!hauteur_boite) {
						if (rapport) {
							hauteur_boite = taille_max * rapport * 1.3;
						} else {
							var hauteur_boite = taille_max * 1.3;
						}
					}
					//hauteur_boite = Math.round(hauteur);

					var h = Math.round(hauteur_boite * r_cont[i]);
						
						
					if (i == num && rapport) {
						h = l_max  - total_ligne;
						total_ligne = 0;
						
					} else {
						total_ligne += h + portfolio_ligne_marge;
					}
					console.log(total_ligne);
										
					if (i ==  num_deb && !rapport) {
						t.addClass("forcer_centrer");
						t.css(m_left,  "" );
						t.css("clear", "both");
					} else {
						t.removeClass("forcer_centrer").css(m_left, "");
					}

					t.width(h);
		
					t.find(".fond").height(hauteur_boite);
				}

			}
			
			
		});
		
	});
}


$(document).ready( calculer_portfolio_ligne );
$(window).on("resize", calculer_portfolio_ligne );