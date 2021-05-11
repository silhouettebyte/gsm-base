<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-9 my-auto">
                <h5>LTE Signal Strength Map</h5>
            </div>
            <div class="col-3">
                <button type="button" class="btn btn-sm btn-outline-primary float-right">Show History</button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div id='map' style='height: 57vh;'></div>
    </div>
</div>

@push('styles')
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css' rel='stylesheet' />
@endpush

@push('scripts')
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.js'></script>
    <script>
        mapboxgl.accessToken = 'pk.eyJ1IjoiYXJrdmkyIiwiYSI6ImNrbzE1NWoycTA1NTAyb2x5ankxaXh2MTkifQ.rhJzjka_VAEx1p6KOgnsug';
        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/dark-v10',
            center: [123.1948,13.6218],
            zoom: 15
        });

        var url = '/v1/ping'
        map.on('load', function () {
            var request = new XMLHttpRequest();
            window.setInterval(function () {

                request.open('GET', url, true); // make a GET request to parse the GeoJSON at the url
                request.onload = function () {
                    if (this.status >= 200 && this.status < 400) { // retrieve the JSON from the response
                        var json = JSON.parse(this.response);
                        map.getSource('signal').setData(json); // update the drone symbol's location on the map
                        map.flyTo({ // fly the map to the drone's current location
                            center: json.geometry.coordinates,
                            speed: 0.5
                        });
                    }
                };
                request.send();
            }, 2000);
// Add a geojson point source.
// Heatmap layers also work with a vector tile source.
            map.addSource('signal', {
                'type': 'geojson',
                'data': 'http://localhost:5080/test'
            });

            map.addLayer(
                {
                    'id': 'signal-heat',
                    'type': 'heatmap',
                    'source': 'signal',
                    'maxzoom': 15,
                    'paint': {
// Increase the heatmap weight based on frequency and property magnitude
                        'heatmap-weight': {
                            property: 'rssi',
                            type: 'exponential',
                            stops: [
                                [-109,0],
                                [-53, 1]
                            ]
                        },
                        'heatmap-intensity': {
                            stops: [
                                [9, 1],
                                [11, 3]
                            ]
                        },
// Color ramp for heatmap.  Domain is 0 (low) to 1 (high).
// Begin color ramp at 0-stop with a 0-transparancy color
// to create a blur-like effect.
                        'heatmap-color': [
                            'interpolate',
                            ['linear'],
                            ['heatmap-density'],
                            0,
                            'rgb(251, 3, 3)',
                            0.2,
                            'rgb(251, 135, 3)',
                            0.4,
                            'rgb(251, 201, 3)',
                            0.6,
                            'rgb(238, 251, 3)',
                            0.8,
                            'rgb(164, 251, 3)',
                            1,
                            'rgb(16, 251, 3)'
                        ],
// Adjust the heatmap radius by zoom level
                        'heatmap-radius': {
                            stops: [
                                [11, 15],
                                [15, 20]
                            ]
                        },
// Transition from heatmap to circle layer by zoom level
                        'heatmap-opacity': {
                            default: 1,
                            stops: [
                                [14, 1],
                                [15, 0]
                            ]
                        },
                    }
                },
                'waterway-label'
            );

            map.addLayer(
                {
                    'id': 'signal-point',
                    'type': 'circle',
                    'source': 'signal',
                    'minzoom': 9,
                    'paint': {
// Size circle radius by signal magnitude and zoom level
                        'circle-radius': {
                            base: 1.75,
                            stops: [
                                [12, 2],
                                [22, 180]

                            ]
                        },
                        'circle-color': [
                            'match',
                            ['get', 'strength'],
                            'Marginal', '#fb8703',
                            'Ok', '#eefb03',
                            'Good', '#a4fb03',
                            'Excellent', '#10fb03',
                            'rgb(251, 3, 3)',
                        ],
// Color circle by signal magnitude
                        'circle-stroke-color': 'white',
                        'circle-stroke-width': 1,
                        'circle-opacity': {
                            stops: [
                                [14, 0],
                                [15, 1]
                            ]
                        }
                    }
                },
                'waterway-label'
            );

            var popup = new mapboxgl.Popup({
                closeButton: false,
                closeOnClick: false
            });

            map.on('mouseenter', 'signal-point', function (e) {
                map.getCanvas().style.cursor = 'pointer';

                var coordinates = e.features[0].geometry.coordinates.slice();
                var rssi = e.features[0].properties.rssi;
                var network = e.features[0].properties.network;
                var quality = e.features[0].properties.strength;

                while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                    coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
                }

                popup.setLngLat(coordinates).setHTML('<h3>Signal Quality:</h3><h4>' + quality + '</h4><h3>Network:</h3><h4>' + network + '</h4><h3>RSSI:</h3><h4>' + rssi + '</h4>').addTo(map);
            })

            map.on('mouseleave', 'signal-point', function () {
                map.getCanvas().style.cursor = '';
                popup.remove();
            });
            var latest = "{{ $geoloc }}".split(",");
            map.flyTo({
                center: [parseFloat(latest[1]), parseFloat(latest[0])],
                essential: true
            })
            console.log(latest);
        });
    </script>
@endpush
