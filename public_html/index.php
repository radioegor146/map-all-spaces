<!DOCTYPE html>
<? require '../private/init.php';?>
<html lang="en">
<head>
    <title>Map all spaces</title>

    <meta charset="UTF-8">
    <meta name="Map hackerspaces/fablabs/makerspaces " content="Dynamic map with all hackerspace, fablabs and makerspaces">
    <link rel="stylesheet" type="text/css" href="/css/style.css">

    <!-- If IE use the latest rendering engine -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Set the page to the width of the device and set the zoon level -->
    <meta name="viewport" content="width = device-width, initial-scale = 1">

    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Leaflet v1.0.1 -->
    <link rel="stylesheet" href="//unpkg.com/leaflet@1.6.0/dist/leaflet.css" />
    <script src="//unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
    <!--script src="leaflet.featuregroup.subgroup.js"></script-->
    <script src="https://unpkg.com/leaflet.featuregroup.subgroup@1.0.2/dist/leaflet.featuregroup.subgroup.js"></script>

    <!-- Leaflet loading spinner-->
    <script src="/dist/spin.min.js" charset="utf-8"></script>
    <script src="/dist/leaflet.spin.min.js" charset="utf-8"></script>

    <!-- Leaflet clusters / groeps -->
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/"></script>

    <link rel="stylesheet" type="text/css" href="/dist/MarkerCluster.Default.css">
    <link rel="stylesheet" type="text/css" href="/dist/MarkerCluster.css">

    <!-- Leaflet search -->
    <link rel="stylesheet" href="/css/leaflet-search.css" />
    <script src="/dist/leaflet-search.js"></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-2M9QVB70G3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-2M9QVB70G3');
    </script>

</head>
<body>
    <div id="header">
        <? include $PRIVATE.'/layout/navigate.php' ?>
    </div>
    <div class="container">
        <div id="map"></div>
    </div>
    <script>
    var pos = [51, 12];

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(getPos);
    };
   
    //Callback geolocation
    function getPos(geoPos) {
        pos = [geoPos.coords.latitude, geoPos.coords.longitude];
    };

        var map = L.map('map').setView(pos,4); 
        
        // var map = L.map('map').locate({ setView: true, maxZoom: 8 });
        //var pos = L.GeoIP.getPosition();
        //51.7491824,12.3034407,4.3z
        //L.GeoIP.centerMapOnPosition(map);

        //attributes for basemap credit (lower right hand corner annotation)
        var streetsAttr = 'Map tiles by Carto, under CC BY 3.0. Data by <a href="https://openstreetmap.org">OpenStreetMap</a>, under ODbL.';
        var OpenStreetMap_MapnikAttr = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';

        var streets = L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png',  { 
            id: 'MapID', 
            attribution: streetsAttr
        }).addTo(map);

         var OpenStreetMap_Mapnik = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            id: 'MapID', 
            attribution: OpenStreetMap_MapnikAttr
        });

        //create baseMaps variable to store basemap layer switcher
        var baseMaps = {
          "Carto Streets": streets,
          "OpenStreetMap": OpenStreetMap_Mapnik,
        };

        var masClusGroup = new L.markerClusterGroup({disableClusteringAtZoom: 7, chunkedLoading: true}).addTo(map);

        var layerSpaceApi = L.featureGroup.subGroup(masClusGroup).addTo(map);
        var layerSpaceFablab = L.featureGroup.subGroup(masClusGroup).addTo(map);
        var layerSpaceWiki = L.featureGroup.subGroup(masClusGroup).addTo(map);       

        map.spin(true);

        //spaceapi
        $.getJSON('/api.geojson', function(cartodbdata) {
            geojsonlayer= L.geoJson(cartodbdata, {
                onEachFeature: function (feature, layer) {
                    if (feature.properties.name) {
                            var html = '<b>'+feature.properties.name+'</b><br/>'+
                                feature.properties.address+'<br/>'+
                                feature.properties.zip+' '+feature.properties.city+'<br/>'+
                                "<a href='"+feature.properties.url+"' target='_blank' >website</a>  "+
                                "<a href='"+feature.properties.source+"' target='_blank' >source</a><br/>"
                            layer.bindPopup(html).addTo(layerSpaceApi);
                        };
                },
                pointToLayer: function (feature, latlon) {
                var iconurl = feature.properties['marker-symbol'];
                return new L.Marker(latlon, {
                    icon: new L.icon({
                        iconUrl: iconurl,
                        iconSize: [30, 70],
                        iconAnchor: [15, 35],
                        popupAnchor: [0, -25]
                                               
                    }),
                    zIndexOffset: 5000
                });
            }
            });
         });


        //fablab
        $.getJSON('/fablab.geojson', function(cartodbdata) {
            geojsonlayer= L.geoJson(cartodbdata, {
                onEachFeature: function (feature, layer) {
                    if (feature.properties.name) {
                            var html = '<b>'+feature.properties.name+'</b><br/>'+
                                feature.properties.address+'<br/>'+
                                feature.properties.zip+' '+feature.properties.city+'<br/>'+
                                "<a href='"+feature.properties.url+"' target='_blank' >website</a>  "+
                                "<a href='"+feature.properties.source+"' target='_blank' >source</a><br/>"
                            layer.bindPopup(html).addTo(layerSpaceFablab);
                        };
                },
                pointToLayer: function (feature, latlon) {
                var iconurl = feature.properties['marker-symbol'];
                return new L.Marker(latlon, {
                    icon: new L.icon({
                        iconUrl: iconurl,
                        iconSize: [30, 70],
                        iconAnchor: [15, 35],
                        popupAnchor: [0, -25]
                    })
                });
            }
            });
         });

        //fablabq
        $.getJSON('/fablabq.geojson', function(cartodbdata) {
            geojsonlayer= L.geoJson(cartodbdata, {
                onEachFeature: function (feature, layer) {
                    if (feature.properties.name) {
                            var html = '<b>'+feature.properties.name+'</b><br/>'+
                                feature.properties.address+'<br/>'+
                                feature.properties.zip+' '+feature.properties.city+'<br/>'+
                                "<a href='"+feature.properties.url+"' target='_blank' >website</a>  "+
                                "<a href='"+feature.properties.source+"' target='_blank' >source</a><br/>"
                            layer.bindPopup(html).addTo(layerSpaceFablab);
                        };
                },
                pointToLayer: function (feature, latlon) {
                var iconurl = feature.properties['marker-symbol'];
                return new L.Marker(latlon, {
                    icon: new L.icon({
                        iconUrl: iconurl,
                        iconSize: [30, 70],
                        iconAnchor: [15, 35],
                        popupAnchor: [0, -25]
                    })
                });
            }
            });
         });



        //wiki
        $.getJSON('/wiki.geojson', function(cartodbdata) {
            geojsonlayer= L.geoJson(cartodbdata, {
                onEachFeature: function (feature, layer) {
                    if (feature.properties.name) {
                            var html = '<b>'+feature.properties.name+'</b><br/>'+
                                feature.properties.address+'<br/>'+
                                feature.properties.zip+' '+feature.properties.city+'<br/>'+
                                "<a href='"+feature.properties.url+"' target='_blank' >website</a>  "+
                                "<a href='"+feature.properties.source+"' target='_blank' >source</a><br/>"
                            layer.bindPopup(html).addTo(layerSpaceWiki);
                        };
                },
                pointToLayer: function (feature, latlon) {
                var iconurl = feature.properties['marker-symbol'];
                return new L.Marker(latlon, {
                    icon: new L.icon({
                        iconUrl: iconurl,
                        iconSize: [30, 70],
                        iconAnchor: [15, 35],
                        popupAnchor: [0, -25]
                    }),
                    zIndexOffset: -3000,  
                });
            }
            })/*.addTo(layerSpaceApi)*/;
         });


        var overLayMap = {
            "Hackerspace (SpaceAPI)": layerSpaceApi,
            "FabLab.io": layerSpaceFablab,
            "Hackerspace (wiki)": layerSpaceWiki,
        };

        L.control.layers(baseMaps, overLayMap).addTo(map);

        var poiLayers = L.layerGroup([
            layerSpaceApi,
            layerSpaceFablab,
            layerSpaceWiki
        ])
        .addTo(map);

        L.control.search({
            layer: poiLayers,
            initial: false,
            propertyName: 'name',
            minLength: 2,
            buildTip: function(text, val) {
                var type = val.layer.feature.properties.sourcetype;
                var url = val.layer.feature.properties.source;
                var city = val.layer.feature.properties.city;
                return '<a href="#" class="'+url+'">'+text+' - '+city+'  ('+type+')</a>';
            },
            moveToLocation: function(latlng, title, map) {
                map.setView(latlng, 8); // access the zoom
            }   
        })
        .addTo(map);


        $(document).ajaxComplete(function (event, xhr, settings) {
            map.spin(false);
        });

    </script>
    <div class="legend">
        <ul>
        <li>Source </li>
        <li><img src="/image/hs_open.png" alt="source spaceapi">API open</li>
        <li><img src="/image/hs_closed.png" alt="source spaceapi">API closed</li>
        <li><img src="/image/hs.png" alt="source spaceapi">API (unknown)</li>
        <li><img src="/image/fablab.png" alt="source fablab.oi">Fablab</li>
        <li><img src="/image/hs_black.png" alt="source wiki.hackerspaces.org">Wiki</li>
        </ul>
    </div>
</body>
</html>