<template>
    <div id="map"></div>
</template>

<script>
import {FieldValue} from 'laravel-nova'

export default {
    mixins: [FieldValue],

    props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

    mounted() {
        let recaptchaScript = document.createElement('script')
        recaptchaScript.setAttribute('src', 'https://unpkg.com/leaflet@1.6.0/dist/leaflet.js')
        document.head.appendChild(recaptchaScript)

        let script = document.createElement('script')
        script.setAttribute('src', 'https://unpkg.com/leaflet-kmz@latest/dist/leaflet-kmz.js')
        document.head.appendChild(script)

        setTimeout(() => {
            console.log(this.field)
            this.loadMap()
        }, 2000)
    },

    methods: {
        loadMap() {
            var map = L.map('map', {
                preferCanvas: true // recommended when loading large layers.
            });
            console.log('map', map)
            map.setView(new L.LatLng(43.5978, 12.7059), 5);

            var OpenTopoMap = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                maxZoom: 17,
                attribution: 'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
                opacity: 0.90
            });
            OpenTopoMap.addTo(map);

            // Instantiate KMZ layer (async)
            var kmz = L.kmzLayer().addTo(map);

            kmz.on('load', function (e) {
                control.addOverlay(e.layer, e.name);
                // e.layer.addTo(map);
            });

            // Add remote KMZ files as layers (NB if they are 3rd-party servers, they MUST have CORS enabled)
            console.log(`/storage/${this.fieldValue}`)
            kmz.load(`/storage/${this.fieldValue}`);
            var control = L.control.layers(null, null, {collapsed: false}).addTo(map);
        }
    }
}
</script>

<style scoped>
#map {
    height: 500px;
    width: 400px;
}
</style>
