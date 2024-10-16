<template>
    <div>
        <DataTable filterDisplay="menu"
                   class="datatable"
                   ref="dt"
                   dataKey="part_id"
                   paginator showGridlines
                   :lazy="true"
                   :loading="partsStore.loading"
                   :rows="pagination.per_page"
                   :row-hover="partsStore.parts.length > 0"
                   :total-records="partsStore.pagination.total"
                   :value="partsStore.parts"
                   @page="getPartsFromApi">
            <template #header>
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="text-xl font-bold">Parts</span>
                </div>
            </template>
            <Column field="part_id" header="Part Id"/>
            <Column field="part_type" header="Part Type"/>
            <Column field="manufacturer" header="Manufacturer"/>
            <Column field="model_number" header="Model Number"/>
            <Column field="list_price" header="List Price"/>
        </DataTable>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { usePartsStore } from '../../stores/parts.js';

// Store
const partsStore = usePartsStore();
const pagination = ref({
    total: 0,
    per_page: 25,
});

// Fetch parts from the API
const getPartsFromApi = async (event) => {
    await partsStore.fetchParts(0, event.page + 1, pagination.value.per_page);
};

// On Mounted life hook
onMounted(async () => {
    await partsStore.fetchParts(0);
});
</script>

<style scoped>
/* Add styles here */
</style>
