<template>
    <div class="card">
        <DataTable filterDisplay="menu"
                   v-model:filters="filters"
                   v-model:selection="partsStore.selectedParts"
                   ref="dt"
                   :value="partsStore.parts"
                   paginator showGridlines
                   :rows="partsStore.pagination.per_page" dataKey="part_id"
                   :row-hover="partsStore.parts.length > 0"
                   :loading="partsStore.loading"
                   :globalFilterFields="['part_type', 'manufacturer', 'model_number', 'multiplier', 'team_price']"
                   :total-records="partsStore.pagination.total">

            <template #header>
                <div class="d-flex justify-content-between">
                    <Button type="button" icon="pi pi-filter-slash" label="Clear" outlined @click="clearFilter()" />
                    <IconField>
                        <InputIcon>
                            <i class="pi pi-search" />
                        </InputIcon>
                        <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                    </IconField>
                </div>
                <div class="row mt-3">
                    <ButtonGroup>
                        <Button
                            icon="pi pi-user-plus"
                            label="Add to Team"
                            @click="partsStore.associateSelectedParts"
                            :disabled="hasSelectedParts"
                        />
                        <Button
                            severity="info"
                            icon="pi pi-upload"
                            label="Upload Price"
                            @click="showUploadModal"
                        />
                        <Button
                            severity="help"
                            icon="pi pi-download"
                            label="Export Pricing"
                            @click="exportCSV"
                        />
                    </ButtonGroup>
                </div>
            </template>
            <template #empty> No parts found. </template>
            <template #loading> Loading parts data. Please wait. </template>

            <Column selection-mode="multiple" style="width: 3em" />

            <!-- Column for Part Type -->
            <Column field="part_type" header="Part Type" style="min-width: 12rem">
                <template #body="{ data }">
                    {{ data.part_type }}
                </template>
                <template #filter="{ filterModel }">
                    <InputText v-model="filterModel.value" type="text" placeholder="Search by Part Type" />
                </template>
            </Column>

            <!-- Column for Manufacturer -->
            <Column field="manufacturer" header="Manufacturer" style="min-width: 12rem">
                <template #body="{ data }">
                    {{ data.manufacturer }}
                </template>
                <template #filter="{ filterModel }">
                    <InputText v-model="filterModel.value" type="text" placeholder="Search by Manufacturer" />
                </template>
            </Column>

            <!-- Column for Model Number -->
            <Column field="model_number" header="Model Number" style="min-width: 12rem">
                <template #body="{ data }">
                    {{ data.model_number }}
                </template>
                <template #filter="{ filterModel }">
                    <InputText v-model="filterModel.value" type="text" placeholder="Search by Model Number" />
                </template>
            </Column>

            <!-- Column for Multiplier -->
            <Column field="multiplier" header="Multiplier" style="min-width: 10rem">
                <template #body="{ data }">
                    {{ data.multiplier }}
                </template>
                <template #filter="{ filterModel }">
                    <InputNumber v-model="filterModel.value" mode="decimal" placeholder="Filter by Multiplier" />
                </template>
            </Column>

            <!-- Column for Team Price -->
            <Column field="team_price" header="Team Price" style="min-width: 12rem">
                <template #body="{ data }">
                    {{ data.team_price }}
                </template>
                <template #filter="{ filterModel }">
                    <InputNumber v-model="filterModel.value" mode="currency" currency="USD" locale="en-US" placeholder="Filter by Team Price" />
                </template>
            </Column>

        </DataTable>
    </div>


    <!-- Upload Modal -->
    <Dialog header="Upload CSV" v-model:visible="isUploadModalVisible" :modal="true">
        <div class="card flex flex-col gap-6 items-center justify-center">
            <FileUpload mode="advanced"
                        name="file"
                        custom-upload
                        accept=".csv"
                        :maxFileSize="1000000"
                        @uploader="handleUpload"
            />
        </div>
    </Dialog>
</template>

<script async setup>
import {ref, onMounted, computed} from 'vue';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import { usePartsStore } from '../../../stores/parts.js';
import { useUserStore } from '../../../stores/user.js';
import Swal from 'sweetalert2/dist/sweetalert2';

const partsStore = usePartsStore();
const userStore = useUserStore();
const filters = ref();
const dt = ref();

// Reactive states
const isUploadModalVisible = ref(false);
const showUploadModal = () => {
    isUploadModalVisible.value = true;
};

// Mock service call to fetch team parts
onMounted(async () => {
    const currentUserTeamId = userStore.currentUser.team.id;
    await partsStore.fetchAllParts(currentUserTeamId);
});

// Initialize DT filters
const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        part_type: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        manufacturer: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        model_number: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        multiplier: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
        team_price: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] }
    };
};
initFilters();

// Clear all filters
const clearFilter = () => {
    initFilters();
};

// Export CSV
const exportCSV = () => {
    dt.value.exportCSV();
};

// Handle Pricing CSV file upload
const handleUpload = async (event) => {
    const currentUserTeamId = userStore.currentUser.team.id;
    const files = Array.isArray(event.files) ? event.files : [event.files];
    const formData = new FormData();

    for (const file of files) {
        formData.append('file', file);
        formData.append('team_id', currentUserTeamId);
    }

    try {
        const response = await axios.post('/api/parts/team/upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
        });
        isUploadModalVisible.value = false;
        await partsStore.fetchAllParts(currentUserTeamId);

        Swal.fire({
            toast: true,
            position: 'bottom-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            icon: 'success',
            title: 'Upload CSV',
            text: 'CSV has been uploaded.'
        });
    } catch (error) {
        console.error('Error uploading file:', error);
    }
};

const hasSelectedParts = computed(() => partsStore.selectedParts.length === 0);
</script>
