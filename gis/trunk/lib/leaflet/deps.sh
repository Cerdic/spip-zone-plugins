#!/bin/bash
declare -a js=(
    "https://raw.githubusercontent.com//shramov/leaflet-plugins/master/layer/tile/Bing.js"
    "https://raw.githubusercontent.com//brunob/leaflet.fullscreen/master/Control.FullScreen.js"
    "https://raw.githubusercontent.com/Norkart/Leaflet-MiniMap/master/src/Control.MiniMap.js"
    "https://gitlab.com/IvanSanchez/Leaflet.GridLayer.GoogleMutant/-/raw/master/Leaflet.GoogleMutant.js"
    "https://raw.githubusercontent.com/shramov/leaflet-plugins/master/layer/vector/GPX.js"
    "https://raw.githubusercontent.com/shramov/leaflet-plugins/master/layer/vector/GPX.Speed.js"
    "https://raw.githubusercontent.com/shramov/leaflet-plugins/master/layer/vector/KML.js"
    "https://raw.githubusercontent.com/Leaflet/Leaflet.markercluster/v1.4.1/dist/leaflet.markercluster-src.js"
    "https://raw.githubusercontent.com/leaflet-extras/leaflet-providers/master/leaflet-providers.js"
    "https://raw.githubusercontent.com/shramov/leaflet-plugins/master/layer/Marker.Rotate.js"
    "https://raw.githubusercontent.com/shramov/leaflet-plugins/master/layer/vector/TOPOJSON.js"
);
for i in "${js[@]}"
do
	wget -N $i -P plugins
done
declare -a images=(
    "https://raw.githubusercontent.com/brunob/leaflet.fullscreen/master/icon-fullscreen.png"
    "https://raw.githubusercontent.com/brunob/leaflet.fullscreen/master/icon-fullscreen-2x.png"
    "https://raw.githubusercontent.com/Norkart/Leaflet-MiniMap/master/src/images/toggle.png"
    "https://raw.githubusercontent.com/Norkart/Leaflet-MiniMap/master/src/images/toggle.svg"
);
for i in "${images[@]}"
do
	wget -N $i -P plugins/images
done

wget -O plugins/leaflet.markercluster.css https://raw.githubusercontent.com/Leaflet/Leaflet.markercluster/v1.4.1/dist/MarkerCluster.css https://raw.githubusercontent.com/Leaflet/Leaflet.markercluster/v1.4.1/dist/MarkerCluster.Default.css

# todo dans plugins/leaflet-plugins.css avec ajout de images/ aux url(xxx)
# https://raw.githubusercontent.com/brunob/leaflet.fullscreen/master/Control.FullScreen.css
# https://raw.githubusercontent.com/Norkart/Leaflet-MiniMap/master/src/Control.MiniMap.css