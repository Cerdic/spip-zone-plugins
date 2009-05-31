$.fn.slideshow = function(options) {
	var settings = {
		timeout: '2000',
		type: 'sequence'
	}
	if(options)
		$.extend(settings, options);
	
	this.css('position', 'relative');
	var slides = this.find('img').get();
	for ( var i = 0; i < slides.length; i++ ) {
		$(slides[i]).css('zIndex', slides.length - i).css('position', 'absolute').css('top', '0').css('left', '0');
	}
	if ( settings.type == 'sequence' ) {
		setTimeout(function(){
			$.slideshow.next(slides, settings, 1, 0);
		}, settings.timeout);
	}
	else if ( settings.type == 'random' ) {
		setTimeout(function(){
			do { current = Math.floor ( Math.random ( ) * ( slides.length ) ); } while ( current == 0 )
			$.slideshow.next(slides, settings, current, 0);
		}, settings.timeout);
	}
	else {
		alert('type must either be \'sequence\' or \'random\'');
	}
};
$.slideshow = function() {}
$.slideshow.next = function (slides, settings, current, last) {
	for (var i = 0; i < slides.length; i++) {
		$(slides[i]).css('display', 'none');
	}
	$(slides[last]).css('display', 'block').css('zIndex', '0');
	$(slides[current]).css('zIndex', '1').fadeIn('slow');
	
	if ( settings.type == 'sequence' ) {
		if ( ( current + 1 ) < slides.length ) {
			current = current + 1;
			last = current - 1;
		}
		else {
			current = 0;
			last = slides.length - 1;
		}
	}
	else if ( settings.type == 'random' ) {
		last = current;
		while (	current == last ) {
			current = Math.floor ( Math.random ( ) * ( slides.length ) );
		}
	}
	else {
		alert('type must either be \'sequence\' or \'random\'');
	}
	setTimeout((function(){$.slideshow.next(slides, settings, current, last);}), settings.timeout);
}