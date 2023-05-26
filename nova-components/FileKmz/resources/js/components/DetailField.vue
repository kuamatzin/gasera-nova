<template>
    <div>
        <button @click="showModalMap()">asdsad</button>
        <Modal :show="showModal" :size="'7xl'" :modalStyle="'fullscreen'">
            <div style="background: white">
                <ModalHeader>
                    <div class="flex justify-between items-center mb-2">
                        <h1 class="text-xl font-bold">Mapa KMZ</h1>
                        <button
                            @click="showModal = false"
                            class="cursor-pointer text-3xl leading-none"
                        >
                            &times;
                        </button>
                    </div>
                </ModalHeader>
                <ModalContent>
                    <div class="flex flex-wrap h-screen">
                        <div class="w-full h-screen">
                            <div class="whitecube-gmap mt-4" ref="map"></div>
                        </div>
                    </div>
                </ModalContent>
            </div>
        </Modal>
    </div>
</template>

<script>
import { FieldValue } from "laravel-nova";

export default {
    mixins: [FieldValue],

    props: ["index", "resource", "resourceName", "resourceId", "field"],

    data() {
        return {
            showModal: false,
            location: null,
            marker: null,
            map: null,
        };
    },

    mounted() {
        //this.location = JSON.parse(this.field.value);
    },

    methods: {
        initGmaps() {
            console.log(this.$refs.map);
            this.map = new google.maps.Map(this.$refs.map, {
                center: this.field.defaultCoordinates || {
                    lat: -34.397,
                    lng: 150.644,
                },
                zoom: this.field.zoom || 8,
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

            const src = window.location.origin + "/storage/" + this.fieldValue;

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
        showModalMap() {
            this.showModal = true;

            setTimeout(() => {
                this.initGmaps();

                if (this.location) {
                    // Add a little delay to fix panTo not registering on update
                    setTimeout(() => {
                        this.setLocation(this.location);
                    }, 100);
                }

                setTimeout(() => {
                    this.loadMap();
                }, 500);
            }, 200);
        },
    },
};
</script>

<style scoped>
.whitecube-gmap {
    height: 90vh;
}
</style>
