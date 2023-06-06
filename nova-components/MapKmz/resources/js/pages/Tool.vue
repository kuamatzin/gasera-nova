<template>
    <div>
        <Head title="Map Kmz" />

        <Heading class="mb-6">Mapa KMZ</Heading>

        <div class="whitecubes-gmap mt-4" ref="mapkmz"></div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            location: null,
            markers: [],
            marker: null,
            map: null,
        };
    },

    mounted() {
        this.getKmz();
    },

    methods: {
        async getKmz() {
            //const { data } = await Nova.request().get("/kmz");
            //this.markers = data;
            this.initGmaps();
            this.loadMap();
        },
        /**
         * Init the gmap
         */
        initGmaps() {
            this.map = new google.maps.Map(this.$refs.mapkmz, {
                center: { lat: -34.397, lng: 150.644 },
                zoom: 8,
            });
        },

        /**
         * Set an active location
         */
        setLocation(location) {
            this.clearMarker();
            this.map.panTo(this.location.latlng);
            this.marker = new google.maps.Marker({
                position: this.location.latlng,
                map: this.map,
            });
        },

        /**
         * Clear the gmap's marker
         */
        clearMarker() {
            if (!this.marker) return;

            this.marker.setMap(null);
            this.marker = null;
        },

        loadMap() {
            const myLatlng = new google.maps.LatLng(
                29.283187136943198,
                -110.31398065149865
            );

            const src = window.location.origin + "/storage/" + 'mapa.kmz';
            const kmz = new google.maps.KmlLayer(src, {
                suppressInfoWindows: false,
                preserveViewport: true,
                map: this.map,
            });
            console.log(kmz);

            this.map.setZoom(8);
            this.map.setCenter(myLatlng);
        },
    },
};
</script>

<style scoped>
.whitecubes-gmap {
    height: 80vh;
}
</style>
