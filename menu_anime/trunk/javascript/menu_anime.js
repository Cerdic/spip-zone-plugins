/* function by Catalin Rosu for mobile display */
$(function() {
		if ($.browser.msie && $.browser.version.substr(0,1)<7)
		{
		$('li').has('ul').mouseover(function(){
			$(this).children('ul').css('visibility','visible');
			}).mouseout(function(){
			$(this).children('ul').css('visibility','hidden');
			})
		}

		/* Mobile */
		$('.menu-conteneur').prepend('<div class="menu_anime-trigger">Menu</div>');		
		$(".menu_anime-trigger").on("click", function(){
			$(".menu_anime").slideToggle();
		});

		// iPad
		var isiPad = navigator.userAgent.match(/iPad/i) != null;
		if (isiPad) $('.menu_anime ul').addClass('no-transition');      
    });