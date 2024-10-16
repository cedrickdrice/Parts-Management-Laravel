import { ref } from 'vue';
import { defineStore } from 'pinia';
import { useUserStore } from './user.js';

import Swal from 'sweetalert2/dist/sweetalert2';
import axios from 'axios';

export const usePartsStore = defineStore('parts', () => {
    const parts = ref([]);
    const loading = ref(false);
    const selectedParts = ref([]);
    const isUploadModalVisible = ref(false);
    const pagination = ref({
        per_page: 25,
        total: 0,
    });


    // Fetch parts based on team ID
    const fetchParts = async (teamId = 0, page = 1, perPage = 25) => {
        loading.value = true;
        const url = `/api/parts/team/${teamId}?page=${page}&per_page=${perPage}`;

        try {
            const response = await axios.get(url);
            parts.value = response.data.data;
            pagination.value.total = response.data.meta.total;
        } catch (error) {
            console.error('Error fetching parts:', error);
        } finally {
            loading.value = false;
        }
    };

    // Fetch parts based on team ID
    const fetchAllParts = async (teamId = 0) => {
        loading.value = true;
        const url = `/api/parts/team/${teamId}`;

        try {
            const response = await axios.get(url);
            parts.value = response.data.data;
            pagination.value.total = response.data.meta.total;
        } catch (error) {
            console.error('Error fetching parts:', error);
        } finally {
            loading.value = false;
        }
    };

    // Associate selected parts with the team
    const associateSelectedParts = async () => {
        const userStore = useUserStore();
        const currentUserTeamId = userStore.currentUser.team.id;
        loading.value = true;

        try {
            const response = await axios.post(`/api/parts/team/${currentUserTeamId}/associate`, {
                parts: selectedParts.value.map(part => part.part_id),
            });

            Swal.fire({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                icon: 'success',
                title: 'Team Parts Associated',
                text: 'Successfully associated parts.'
            });

            selectedParts.value = [];
            await fetchParts(currentUserTeamId);
        } catch (error) {
            console.error('Error associating parts:', error);
        }
    };

    // Handle file upload for CSV
    const handleUpload = async (event) => {
        console.log('tes')
        const files = Array.isArray(event.files) ? event.files : [event.files];
        const formData = new FormData();

        for (const file of files) {
            formData.append('file', file);
        }

        try {
            const response = await axios.post('/api/parts/upload', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                },
            });
            isUploadModalVisible.value = false;

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

    return {
        parts,
        loading,
        pagination,
        selectedParts,
        isUploadModalVisible,
        fetchParts,
        fetchAllParts,
        associateSelectedParts,
        handleUpload,
    };
});
