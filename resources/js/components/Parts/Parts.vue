<template>
    <div class="container">
        <parts-navbar @export-csv="exportCSV" @show-upload="showUploadModal" />
        <parts-datatable ref="dt" />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import PartsDatatable from "./PartsDatatable.vue";
import PartsNavbar from "./PartsNavbar.vue";
import {usePartsStore} from "../../stores/parts.js";

// Reactive states
const partsStore = usePartsStore();
const dt = ref(null);

// Handle CSV export
const exportCSV = () => {
    dt.value.exportCSV();
};

// Upload modal
const showUploadModal = () => {
    partsStore.isUploadModalVisible = true;
};
</script>
