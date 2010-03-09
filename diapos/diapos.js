$(document).ready(function() {

	//Met la premiere image en active
	$(".choix_image").show();
	$(".choix_image a:first").addClass("active");
		
	//Cherche la largeur et la quantite des images 
	var imageWidth = $(".fenetre").width();
	var imageSum = $(".cadre_image img").size();
	var imageReelWidth = imageWidth * imageSum;
	
	//Donne la largeur a cadre_image.
	$(".cadre_image").css({'width' : imageReelWidth});
	
	//choix_image + diapos function
	rotate = function(){	
		var triggerID = $active.attr("rel") - 1; //Quantite des images
		var cadre_imagePosition = triggerID * imageWidth; //Determine la largeur a cadre_image

		$(".choix_image a").removeClass('active'); //Nettoie
		$active.addClass('active'); //Ajoute la classe active
		
		//Animation
		$(".cadre_image").animate({ 
			left: -cadre_imagePosition
		}, 500 );
		
	}; 
	
	//Rotation et temps
	rotateSwitch = function(){		
		play = setInterval(function(){ //Temps de vision
			$active = $('.choix_image a.active').next();
			if ( $active.length === 0) { //Et a la fin...
				$active = $('.choix_image a:first'); //On prend les memes et on recommence ;)
			}
			rotate();
		}, 7000); //On peut ajuster le tems en milisecondes
	};
	
	rotateSwitch(); //Demarre au chargement de la page
	
	//Survoler une image arrete le diapos
	$(".cadre_image a").hover(function() {
		clearInterval(play);
	}, function() {
		rotateSwitch();
	});	
	
	//Choisir l'image
	$(".choix_image a").click(function() {	
		$active = $(this); //Active l'imageimage
		clearInterval(play); //Arrete
		rotate(); //Et demmarre ensuite
		rotateSwitch(); 
		return false;
	});	
	
});