<template>
    <div>
        <Head title="Map Kmz"/>

        <Heading class="mb-6">KMZ</Heading>

        <Card
            class="flex flex-col items-center justify-center"
            style="min-height: 300px"
        >
            <div class="whitecubes-gmap mt-4" ref="mapkmz"></div>
        </Card>
    </div>
</template>

<script>
export default {
    data() {
        return {
            location: null,
            marker: null,
            map: null
        }
    },

    mounted() {
        this.initGmaps();

        if (this.location) {
            // Add a little delay to fix panTo not registering on update
            setTimeout(() => {
                this.setLocation(this.location);
            }, 100);
        }
    },

    methods: {
        /**
         * Init the gmap
         */
        initGmaps() {
            this.map = new google.maps.Map(this.$refs.mapkmz, {
                center: {lat: -34.397, lng: 150.644},
                zoom: 8
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
                map: this.map
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

            const src = 'http://onlineu.mx/ductodegas/VERACRUZ20201110_161155.kmz'
            console.log(src)
            var kmlLayer = new google.maps.KmlLayer(src, {
                suppressInfoWindows: false,
                preserveViewport: true,
                map: this.map,
            });
            this.map.setZoom(8);
            this.map.setCenter(myLatlng);
            kmlLayer.setZoom(8);
            kmlLayer.setCenter(myLatlng);
        },
    },
}
</script>

<style scoped>
.whitecubes-gmap {
    height: 90vh;
}
</style>
