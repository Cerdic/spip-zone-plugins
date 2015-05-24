function photoshow() {
    var imgs = [],
        g = [];
    var a = photoshow_identify(this),
        index = 0;

    // gallery ? il y a un glitch
    if (photoswipe.gallery) {
        $('img[data-photo]')
            .each(function (i, e) {
                var b = photoshow_identify(e);
                imgs.push(b);
                if (b.src == a.src) index = i;
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
    var photosrc = $(me).attr('data-photo');

    if (photosrc) {
        a = {
            thumbnail: me,
            src: photosrc.replace(/__\.__/g, '.'),
            w: parseInt($(me).attr('data-photo-w')),
            h: parseInt($(me).attr('data-photo-h')),
            title: $(me).attr('title'), // legende
        };
    } else {
        a = {
            thumbnail: me,
            src: me.src,
            w: parseInt(me.naturalWidth),
            h: parseInt(me.naturalHeight),
            title: $(me).attr('title'),
        };
    }

    return a;
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
            var thumbnail = items[index].thumbnail;

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

    $('head')
        .append('<link rel="stylesheet" href="' + photoswipe.path + 'photoswipe.css"> \r\r<!-- Skin CSS file (styling of UI - buttons, caption, etc.)\r     In the folder of skin CSS file there are also:\r     - .png and .svg icons sprite, \r     - preloader.gif (for browsers that do not support CSS animations) -->\r<link rel="stylesheet" href="' + photoswipe.path + '/default-skin/default-skin.css"> \r\r<!-- Core JS file -->\r<script src="' + photoswipe.path + '/photoswipe.min.js"></script> \r\r<!-- UI JS file -->\r<script src="' + photoswipe.path + '/photoswipe-ui-default.min.js"></script> ');

    $('<div>')
        .html('<!-- Root element of PhotoSwipe. Must have class pswp. -->\r<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">\r\r    <!-- Background of PhotoSwipe. \r         It’s a separate element as animating opacity is faster than rgba(). -->\r    <div class="pswp__bg"></div>\r\r    <!-- Slides wrapper with overflow:hidden. -->\r    <div class="pswp__scroll-wrap">\r\r        <!-- Container that holds slides. \r            PhotoSwipe keeps only 3 of them in the DOM to save memory.\r            Don’t modify these 3 pswp__item elements, data is added later on. -->\r        <div class="pswp__container">\r            <div class="pswp__item"></div>\r            <div class="pswp__item"></div>\r            <div class="pswp__item"></div>\r        </div>\r\r        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->\r        <div class="pswp__ui pswp__ui--hidden">\r\r            <div class="pswp__top-bar">\r\r                <!--  Controls are self-explanatory. Order can be changed. -->\r\r                <div class="pswp__counter"></div>\r\r                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>\r\r                <button class="pswp__button pswp__button--share" title="Share"></button>\r\r                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>\r\r                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>\r\r                <!-- Preloader demo http://codepen.io/dimsemenov/pen/yyBWoR -->\r                <!-- element will get class pswp__preloader--active when preloader is running -->\r                <div class="pswp__preloader">\r                    <div class="pswp__preloader__icn">\r                      <div class="pswp__preloader__cut">\r                        <div class="pswp__preloader__donut"></div>\r                      </div>\r                    </div>\r                </div>\r            </div>\r\r            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">\r                <div class="pswp__share-tooltip"></div> \r            </div>\r\r            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">\r            </button>\r\r            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">\r            </button>\r\r            <div class="pswp__caption">\r                <div class="pswp__caption__center"></div>\r            </div>\r\r        </div>\r\r    </div>\r\r</div>')
        .appendTo('body');



}