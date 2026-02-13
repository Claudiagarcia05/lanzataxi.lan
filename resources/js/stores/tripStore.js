import { defineStore } from 'pinia';
import axios from 'axios';

export const useTripStore = defineStore('trip', {
    state: () => ({
        activeTrip: null,
        trips: [],
        driverLocation: null,
    }),

    actions: {
        async createTrip(tripData) {
            const response = await axios.post('/api/trips', tripData);
            this.activeTrip = response.data;
            return response.data;
        },

        async fetchDriverLocation(tripId) {
            const response = await axios.get(`/api/trips/${tripId}/track`);
            this.driverLocation = response.data.location;
        },

        async acceptTrip(tripId) {
            const response = await axios.patch(`/api/trips/${tripId}/accept`);
            this.activeTrip = response.data;
        },
    },
});
