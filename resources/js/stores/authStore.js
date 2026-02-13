import { defineStore } from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isDriver: (state) => state.user?.role === 'driver',
        isPassenger: (state) => state.user?.role === 'passenger',
        isAdmin: (state) => state.user?.role === 'admin',
    },

    actions: {
        async login(credentials) {
            try {
                const response = await axios.post('/api/login', credentials);
                this.token = response.data.token;
                this.user = response.data.user;
                axios.defaults.headers.common.Authorization = `Bearer ${this.token}`;
                return { success: true };
            } catch (error) {
                return {
                    success: false,
                    error: error.response?.data?.message,
                };
            }
        },

        logout() {
            this.token = null;
            this.user = null;
            delete axios.defaults.headers.common.Authorization;
        },
    },

    persist: true,
});
