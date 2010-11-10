$(document).ready(function() {
	//Met la premiere image en active
	$(".choix_image").show();
	$(".choix_image a:first-child").addClass("active");
	//Cherche la largeur et la quantite des images 
	var imageWidthPa = $(".fenetrepa").width();
	var imageWidthPo = $(".fenetrepo").width();
	var imageSumPa = $(".cadre_imagepa img").size();
	var imageSumPo = $(".cadre_imagepo img").size();
	var imageReelWidthPa = imageWidthPa * imageSumPa;
	var imageReelWidthPo = imageWidthPo * imageSumPo;
	//Donne la largeur a cadre_image.
	$(".cadre_imagepa").css({'width' : imageReelWidthPa});
	$(".cadre_imagepo").css({'width' : imageReelWidthPo});
	//choix_image + diapos function
	rotate = function(){	
		var triggerID = $active.attr("rel") - 1; //Declencheur de la rotation (active moins une image)
		var cadre_imagePositionPa = triggerID * imageWidthPa; //Determine la largeur a cadre_image
		var cadre_imagePositionPo = triggerID * imageWidthPo; //Determine la largeur a cadre_image
		$(".choix_image a").removeClass('active'); //Nettoie
		$active.addClass('active'); //Ajoute la classe active
		//Animation
		$(".cadre_imagepa").animate({ 
			left: -cadre_imagePositionPa
		}, 500 );
		$(".cadre_imagepo").animate({ 
			left: -cadre_imagePositionPo
		}, 500 );
	}; 
	//Rotation et temps
	rotateSwitch = function(){		
		play = setInterval(function(){ //Temps de vision
			$active = $('.choix_image a.active').next();
			if ( $active.length === 0) { //Et a la fin...
				$active = $('.choix_image a:first-child'); //On prend les memes et on recommence ;)
			}
			rotate();
		}, 7000); //On peut ajuster le tems en milisecondes
	};
	rotateSwitch(); //Demarre au chargement de la page
	//Survoler une image arrete le diapos
	$(".cadre_imagepa a").hover(function() {
		clearInterval(play);
	}, function() {
		rotateSwitch();
	});	
	$(".cadre_imagepo a").hover(function() {
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