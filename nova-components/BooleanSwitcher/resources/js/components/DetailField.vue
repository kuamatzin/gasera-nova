<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <div v-if="field.value">
                <img src="https://files.inovuz.com/files/gasera/cuadro_verde.png" alt="" style="width: 20px;">
            </div>
            <div v-else>
                <img src="https://files.inovuz.com/files/gasera/cuadro_gris.png" alt="" style="width: 20px;">
            </div>
        </template>
    </PanelItem>
</template>

<script>
export default {
    props: ["index", "resource", "resourceName", "resourceId", "field"],

    computed: {
        label() {
            return this.field.value == true ? this.__("Yes") : this.__("No");
        },

        type() {
            return this.field.value == true ? "check-circle" : "x-circle";
        },

        color() {
            return this.field.value == true ? "text-green-500" : "text-red-500";
        },
    },
};
</script>
