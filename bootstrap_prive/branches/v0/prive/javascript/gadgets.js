function init_gadgets(url_menu_rubrique){
	jQuery('#boutonbandeautoutsite').one('mouseover',function(){
		jQuery(this).siblings('ul').find('li:first>a').animeajax();
		jQuery.ajax({
			url: url_menu_rubrique,
			success: function(c){
				jQuery('#boutonbandeautoutsite').siblings('ul').remove();
				jQuery('#boutonbandeautoutsite').after(c);
			}
		});
	});
}

function focus_zone(selecteur){
	jQuery(selecteur).eq(0).find('a,input:visible').get(0).focus();
	return false;
}


jQuery(document).ready(function(){
	init_gadgets(url_menu_rubrique);

	// [...]

	jQuery('#bandeau_haut #formRecherche input').hover(function(){
		jQuery('#bandeau_haut ul.actif').trigger('mouseout');
	});
	jQuery('#bando_liens_rapides a')
		.focus(function(){
			jQuery('#bando_liens_rapides').addClass('actif');
		})
		.blur(function(){
			jQuery('#bando_liens_rapides').removeClass('actif');
		});
	if (typeof window.test_accepte_ajax != "undefined")
		test_accepte_ajax();
});

/*
 * Project: Twitter Bootstrap Hover Dropdown
 * Author: Cameron Spear
 * Contributors: Mattia Larentis
 *
 * Dependencies?: Twitter Bootstrap's Dropdown plugin
 *
 * A simple plugin to enable twitter bootstrap dropdowns to active on hover and provide a nice user experience.
 *
 * No license, do what you want. I'd love credit or a shoutout, though.
 *
 * http://cameronspear.com/blog/twitter-bootstrap-dropdown-on-hover-plugin/
 */
;(function($, window, undefined) {

    var shouldHover = function() {
        return $('#cwspear-is-awesome').is(':visible');
    };

    // outside the scope of the jQuery plugin to
    // keep track of all dropdowns
    var $allDropdowns = $();

    // if instantlyCloseOthers is true, then it will instantly
    // shut other nav items when a new one is hovered over
    $.fn.dropdownHover = function(options) {

        // the element we really care about
        // is the dropdown-toggle's parent
        $allDropdowns = $allDropdowns.add(this.parent());

        return this.each(function() {
            var $this = $(this).parent(),
                defaults = {
                    delay: 500,
                    instantlyCloseOthers: true
                },
                data = {
                    delay: $(this).data('delay'),
                    instantlyCloseOthers: $(this).data('close-others')
                },
                settings = $.extend(true, {}, defaults, options, data),
                timeout;

            $this.hover(function() {
                if(shouldHover()) {
                    if(settings.instantlyCloseOthers === true)
                        $allDropdowns.removeClass('open');

                    window.clearTimeout(timeout);
                    $(this).addClass('open');
                }
            }, function() {
                if(shouldHover()) {
                    timeout = window.setTimeout(function() {
                        $this.removeClass('open');
                    }, settings.delay);
                }
            });
        });
    };

    // apply dropdownHover to all elements with the data-hover="dropdown" attribute
    $(document).ready(function() {
        // pure win here: we create these spans so we can test if we have the responsive css loaded
        // this is my attempt to hopefully make sure the IDs are unique
        $('<span class="visible-desktop" style="font-size:1px !important" id="cwspear-is-awesome">.</span>').appendTo('body');

        $('[data-hover="dropdown"]').dropdownHover();
    });

})(jQuery, this);
