<template>
    <div>
        <Head title="Map Kmz" />
        <Heading class="mb-6">Mapa Chihuahua</Heading>
        <div class="flex">
            <div>
                <input
                    class="relative m-0 block w-full min-w-0 flex-auto rounded border border-solid border-neutral-300 bg-clip-padding px-3 py-[0.32rem] text-base font-normal text-neutral-700 transition duration-300 ease-in-out file:-mx-3 file:-my-[0.32rem] file:overflow-hidden file:rounded-none file:border-0 file:border-solid file:border-inherit file:bg-neutral-100 file:px-3 file:py-[0.32rem] file:text-neutral-700 file:transition file:duration-150 file:ease-in-out file:[border-inline-end-width:1px] file:[margin-inline-end:0.75rem] hover:file:bg-neutral-200 focus:border-primary focus:text-neutral-700 focus:shadow-te-primary focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:file:bg-neutral-700 dark:file:text-neutral-100 dark:focus:border-primary"
                    type="file"
                    id="formFile"
                    accept=".kmz, .kml"
                />
            </div>
            <div class="ml-4">
                <button
                    size="md"
                    class="flex-shrink-0 h-9 px-4 focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring text-white dark:text-gray-800 inline-flex items-center font-bold shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-4 h-9 text-sm flex-shrink-0 h-9 px-4 focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring text-white dark:text-gray-800 inline-flex items-center font-bold"
                    @click="submitKmz()"
                    ><span class="hidden md:inline-block">Actualizar KMZ</span>
                </button>
            </div>
        </div>

        <div class="whitecubes-gmap mt-6" ref="mapkmz"></div>
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
            const { data } = await Nova.request().get("/kmz/1");
            this.markers = data;

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

        submitKmz() {
            const file = document.getElementById("formFile").files[0];
            const formData = new FormData();
            formData.append("file", file);
            Nova.request()
                .post("/kmz/1", formData)
                .then((response) => {
                    this.getKmz();
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        loadMap() {
            const myLatlng = new google.maps.LatLng(
                29.283187136943198,
                -110.31398065149865
            );

            const src = window.location.origin + "/storage/" + this.markers.mapa_afectacion_path;
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
