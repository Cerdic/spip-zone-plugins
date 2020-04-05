
$(document).ready(function() {

  $(".panorama-panolens").each(function(index,el){

      panolens_options.container = el

      var $el = $(el)
          , panorama
          , src = $el.data("src")

      if (!src) return true

      if (!$el.is(".panorama-video")) {

        panorama = new PANOLENS.ImagePanorama(src)
        console.log('image');

      } else {
        panorama = new PANOLENS.VideoPanorama(src, panolens_options.video)
        var poster = $('el').data('poster');
        if (poster) panorama.setLinkingImage(poster);

      }

      var viewer = new PANOLENS.Viewer(panolens_options)
          viewer.add(panorama)

      if (PANOLENS.Utils.checkTouchSupported())
        viewer.enableControl(1)


    })

})
