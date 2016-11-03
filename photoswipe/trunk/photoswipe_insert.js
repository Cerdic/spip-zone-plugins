function photoshow_hover() {
    var me = this;
    var a = photoshow_identify(me);
    if (a) {
        $(me).addClass('photoshow');
        $(me).removeClass('cboxElement'); // remove mediabox!
    }
}
function photoshow() {
    var imgs = [],
        g = [];
    var a = photoshow_identify(this),
        index = 0;

    if (!a) return;
    
    // gallery
    var idx = 0;
    if (photoswipe.gallery) {
        $(photoswipe.selector)
            .each(function (i, e) {
                var b = photoshow_identify(e);
                if (b) {
                    if (!a.rel || b.rel == a.rel) {
                        imgs.push(b);
                        if (b.src == a.src) index = idx;
                        idx ++;
                    }
                }
            });
    } else {
        imgs.push(a);
    }

    if (photoswipe.debug) {
        console.log(JSON.stringify(imgs));
    }

    photoshow_gallery(imgs, index);
    return false; // interdire l'action d'un <a> englobant
}

function photoshow_identify(me) {
    var me = $(me), a = {};
    if (me.is('a')) {
        if (!me.attr('type').match(/image\/(jpeg|gif|png)/)) {
            return null;
        }
        a.src = me.attr('href');
        a.w = me.attr('data-photo-w');
        a.h = me.attr('data-photo-h');
        if (!(a.src && a.w && a.h)) {
            return;
        }
        a.thumbnail = me.find('img');
        a.rel = me.attr('rel') || a.thumbnail.attr('rel');
    } else if (me.is('img')) {
        var photosrc = me.attr('data-photo');
        a.thumbnail = me;
        if (photosrc) {
            a.src = photosrc.replace(/__\.__/g, '.');
            a.w = parseInt(me.attr('data-photo-w'));
            a.h = parseInt(me.attr('data-photo-h'));
        } else {
            a.src = me.attr('src');
            a.w = parseInt(me.attr('naturalWidth'));
            a.h = parseInt(me.attr('naturalHeight'));
        }
        a.rel = me.attr('rel');

    } else {
        // cas non prevu !
        return;
    }

    // recuperer la legende
    a.title = "";

    // 1. figure / figcaption
    var p = me.parents('figure').find('figcaption');
    if (p.length) {
      a.title = p.html();
    }
    // 2. dl/dt (modèle spip…)
    if (!a.title) {
      me
      .parents('dt')
      .parents('dl')
      .find('dt.spip_doc_titre, dd.spip_doc_descriptif')
      .each(function(i,e) {
        a.title += e.outerHTML; 
      });
    }
    // 3. title
    if (!a.title) {
      a.title = me.attr('title');
    }

    // on verifie que la taille du fichier grand est superieure a celle du fichier petit
    // -> dans tous les cas, si c'est un lien hypertexte, il faut l'activer
    if ( me.is('a') || a.w > me.width() || a.w > $(window).width() || a.h > $(window).height()) {
        return a;
    }
}

function photoshow_gallery(items, index) {
    var pswpElement = document.querySelectorAll('.pswp')[0];

    // define options (if needed)
    var options = {
        // optionName: 'option value'
        // for example:
        index: index, // start slide,
        shareEl: false, // no "share on pinterest!"
        fullscreenEl: false,
        loop: false,
        addCaptionHTMLFn: function (item, captionEl, isFake) {
            // item      - slide object
            // captionEl - caption DOM element
            // isFake    - true when content is added to fake caption container
            //             (used to get size of next or previous caption)

            if (!item.title) {
                captionEl.children[0].innerHTML = '';
                return false;
            }
            captionEl.children[0].innerHTML = item.title;
            return true;
        },
        getThumbBoundsFn: function (index) {
            // find thumbnail element
            var thumbnail = items[index].thumbnail[0];

            if (!thumbnail) return;

            // get window scroll Y
            var pageYScroll = window.pageYOffset || document.documentElement.scrollTop;
            // optionally get horizontal scroll

            // get position of element relative to viewport
            var rect = thumbnail.getBoundingClientRect();

            // w = width
            return {
                x: rect.left,
                y: rect.top + pageYScroll,
                w: rect.width
            };
        }
    };

    // Initializes and opens PhotoSwipe
    gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
    gallery.init();
}



function photoswipe_init() {

    $.ajaxSetup({ cache: true });
    $('<div>')
        .html('<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true"> <div class="pswp__bg"></div> <div class="pswp__scroll-wrap"> <div class="pswp__container"> <div class="pswp__item"></div> <div class="pswp__item"></div> <div class="pswp__item"></div> </div> <div class="pswp__ui pswp__ui--hidden"> <div class="pswp__top-bar"> <div class="pswp__counter"></div> <button class="pswp__button pswp__button--close" title="Close (Esc)"></button> <button class="pswp__button pswp__button--share" title="Share"></button> <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button> <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button> <div class="pswp__preloader"> <div class="pswp__preloader__icn"> <div class="pswp__preloader__cut"> <div class="pswp__preloader__donut"></div> </div> </div> </div> </div> <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap"> <div class="pswp__share-tooltip"></div> </div> <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"> </button> <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"> </button> <div class="pswp__caption"> <div class="pswp__caption__center"></div> </div> </div> </div></div>')
        .appendTo('body');
}

$(function() {
    photoswipe_init();
    if (!!$.fn.on) {
      $(document).on("mouseover", photoswipe.selector, photoshow_hover);
      $(document).on("click", photoswipe.selector, photoshow);
    } else if (!!$.fn.live) {
      $(photoswipe.selector).live("mouseover", photoshow_hover);
      $(photoswipe.selector).live("click", photoshow);
    }
});
