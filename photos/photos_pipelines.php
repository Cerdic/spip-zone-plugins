<?php
	function photos_header_prive($flux){
		$flux .= "";
	return $flux;
	}
 function photos_insert_head($flux){
 $flux .="<!-- Debut header photos -->
<script type='text/javascript' src='plugins/photos/js/highslide-full.js'></script>
<link rel='stylesheet' type='text/css' href='plugins/photos/css/highslide.css' />
<!--[if lt IE 7]>
<link rel='stylesheet' type='text/css' href='plugins/photos/js/highslide-ie6.css' />
<![endif]-->



<!--
    2) Optionally override the settings defined at the top
    of the highslide.js file. The parameter hs.graphicsDir is important!
    Add the slideshow and do some adaptations to this example.
-->

<script type='text/javascript'><!--
	hs.graphicsDir = 'squelettes/graphics/';
	hs.transitions = ['expand', 'crossfade'];
	hs.restoreCursor = null;
	hs.lang.restoreTitle = 'Click pour changer d\'image';

	// Add the slideshow providing the controlbar and the thumbstrip
	hs.addSlideshow({
		//slideshowGroup: 'group1',
		interval: 5000,
		repeat: true,
		useControls: true,
		overlayOptions: {
			position: 'bottom right',
			offsetY: 50
		},
		thumbstrip: {
			position: 'above',
			mode: 'horizontal',
			relativeTo: 'expander'
		}
	});

	// Options for the in-page items
	var inPageOptions = {
		//slideshowGroup: 'group1',
		outlineType: null,
		allowSizeReduction: false,
		wrapperClassName: 'in-page controls-in-heading',
		useBox: true,
		width:500,
		height: 400,
		targetX: 'gallery-area 10px',
		targetY: 'gallery-area',
		captionEval: 'this.thumb.alt',
		numberPosition: 'caption'
	}

	// Open the first thumb on page load
	hs.addEventListener(window, 'load', function() {
		document.getElementById('thumb1').onclick();
	});

	// Cancel the default action for image click and do next instead
	hs.Expander.prototype.onImageClick = function() {
		if (/in-page/.test(this.wrapper.className))	return hs.next();
	}

	// Under no circumstances should the static popup be closed
	hs.Expander.prototype.onBeforeClose = function() {
		if (/in-page/.test(this.wrapper.className))	return false;
	}
	// ... nor dragged
	hs.Expander.prototype.onDrag = function() {
		if (/in-page/.test(this.wrapper.className))	return true;
	}

	// Keep the position after window resize
    hs.addEventListener(window, 'resize', function() {
		var i, exp;
		hs.page = hs.getPageSize();

		for (i = 0; i < hs.expanders.length; i++) {
			exp = hs.expanders[i];
			if (exp) {
				var x = exp.x,
					y = exp.y;

				// get new thumb positions
				exp.tpos = hs.getPosition(exp.el);
				x.calcThumb();
				y.calcThumb();

				// calculate new popup position
		 		x.pos = x.tpos - x.cb + x.tb;
				x.scroll = hs.page.scrollLeft;
				x.clientSize = hs.page.width;
				y.pos = y.tpos - y.cb + y.tb;
				y.scroll = hs.page.scrollTop;
				y.clientSize = hs.page.height;
				exp.justify(x, true);
				exp.justify(y, true);

				// set new left and top to wrapper and outline
				exp.moveTo(x.pos, y.pos);
			}
		}
	});
//-->
</script>


<style type='text/css'>
	.highslide-image {
		border: 1px solid black;
	}
	.highslide-controls {
		width: 90px !important;
	}
	.highslide-controls .highslide-close {
		display: none;
	}
	.highslide-caption {
		padding: .5em 0;
	}
</style>";
return $flux;
 }
?>
