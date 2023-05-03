<template>
    <PanelItem :index="index" :field="field">
        <template #value>
            <ImageLoader
                v-if="shouldShowLoader"
                :src="imageUrl"
                :maxWidth="field.maxWidth || field.detailWidth"
                :rounded="field.rounded"
                :aspect="field.aspect"
            />

            <span v-if="fieldValue && !imageUrl" class="break-words">
      </span>

            <span v-if="!fieldValue && !imageUrl">&mdash;</span>

            <p v-if="shouldShowToolbar" class="flex items-center text-sm">
                <a
                    v-if="field.downloadable"
                    @keydown.enter.prevent="download"
                    @click.prevent="download"
                    tabindex="0"
                    class="cursor-pointer text-gray-500 inline-flex items-center"
                >
                    <Icon
                        class="mr-2"
                        type="eye"
                        view-box="0 0 24 24"
                        width="16"
                        height="16"
                    />
                    <span class="class mt-1">Previsualizar</span>
                </a>
            </p>
        </template>
    </PanelItem>

    <Modal :show="showModal" :size="'7xl'" :modalStyle="'fullscreen'">
        <div style="background: white">
            <ModalHeader>
                <div class="flex justify-between items-center mb-2">
                    <h1 class="text-xl font-bold">Previsualizar archivo</h1>
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
                        <iframe
                            :src="`/storage/${this.fieldValue}`"
                            frameborder="0"
                            width="100%"
                            height="85%"
                        ></iframe>
                    </div>
                </div>
            </ModalContent>
        </div>
    </Modal>
</template>

<script>
import {FieldValue} from 'laravel-nova'

export default {
    mixins: [FieldValue],

    props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

    data() {
        return {
            showModal: false,
        }
    },

    methods: {
        /**
         * Download the linked file
         */
        download() {
            this.showModal = true;
            return;
            let link = document.createElement('a')
            link.target = '_blank'
            link.href = `/storage/${this.fieldValue}`
            document.body.appendChild(link)
            link.click()
            document.body.removeChild(link)
        },
    },

    computed: {
        hasValue() {
            return Boolean(this.field.value || this.imageUrl)
        },

        shouldShowLoader() {
            return this.imageUrl
        },

        shouldShowToolbar() {
            return Boolean(this.field.downloadable && this.hasValue)
        },

        imageUrl() {
            return this.field.previewUrl || this.field.thumbnailUrl
        },

        isVaporField() {
            return this.field.component === 'vapor-file-field'
        },
    },
}
</script>
