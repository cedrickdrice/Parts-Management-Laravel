<template>
    <nav class="navbar navbar-light bg-light">
        <a class="navbar-brand">Parts</a>
        <form class="form-inline">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button class="btn btn-outline-success my-2 my-sm-0" type="button" @click="$emit('show-upload')">Upload</button>
                <button class="btn btn-outline-info my-2 my-sm-0" type="button" @click="$emit('export-csv')">Download</button>
            </div>
        </form>

        <!-- Upload Modal (this modal logic can also be handled in the parent) -->
        <Dialog header="Upload CSV" v-model:visible="partStore.isUploadModalVisible" :modal="true">
            <div class="card flex flex-col gap-6 items-center justify-center">
                <FileUpload mode="advanced"
                            name="file"
                            custom-upload
                            accept=".csv"
                            :maxFileSize="1000000"
                            @uploader="partStore.handleUpload"
                />
            </div>
        </Dialog>
    </nav>
</template>

<script setup>
import { usePartsStore } from "../../stores/parts.js";

// Store
const partStore = usePartsStore();
</script>

<style scoped>
.p-fileupload-file-thumbnail {
    display: none;
}
</style>
