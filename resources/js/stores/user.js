// stores/user.js
import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import axios from 'axios';

export const useUserStore = defineStore('user', () => {
    const currentUser = ref(null);

    // Fetch the current user
    const fetchCurrentUser = async () => {
        try {
            const response = await axios.get('/api/user');
            currentUser.value = response.data.data;
            currentUser.value.team = response.data.data.teams[0];
        } catch (error) {
            console.error('Failed to fetch current user:', error);
        }
    };

    return {
        currentUser,
        fetchCurrentUser
    };
});
