import { defineStore } from 'pinia'
import { useAuthStore } from './authStore'
import axios from 'axios'

export const useTripStore = defineStore('viaje', {
    state: () => ({
        viajes: [],
        viajeActivo: null,
        cargando: false,
        error: null,
        ubicacionConductor: null
    }),

    getters: {
        viajesPasajero: (state) => {
            const auth = useAuthStore()
            return state.viajes.filter(t => t.pasajeroId === auth.usuario?.id)
        },
        viajesConductor: (state) => {
            const auth = useAuthStore()
            return state.viajes.filter(t => t.conductorId === auth.usuario?.id)
        },
        viajesPendientes: (state) => state.viajes.filter(t => t.estado === 'pendiente'),
        completedviajes: (state) => state.viajes.filter(t => t.estado === 'completed'),
        viajesHoy: (state) => {
            const today = new Date().toDateString()
            return state.viajes.filter(t => new Date(t.date).toDateString() === today)
        }
    },

    actions: {
        mapearViaje(viaje) {
            return {
                id: viaje.id,
                pasajeroId: viaje.pasajero_id,
                pasajeroName: viaje.pasajero?.name || 'Pasajero',
                conductorId: viaje.conductor?.user?.id || null,
                conductorEntityId: viaje.conductor_id,
                conductorName: viaje.conductor?.user?.name || null,
                conductor: viaje.conductor || null,
                taxiId: viaje.taxi_id,
                pickup: viaje.pickup_address || `(${viaje.pickup_lat}, ${viaje.pickup_lng})`,
                dropoff: viaje.dropoff_address || `(${viaje.dropoff_lat}, ${viaje.dropoff_lng})`,
                pickup_address: viaje.pickup_address,
                dropoff_address: viaje.dropoff_address,
                pickupLat: Number(viaje.pickup_lat),
                pickupLng: Number(viaje.pickup_lng),
                dropoffLat: Number(viaje.dropoff_lat),
                dropoffLng: Number(viaje.dropoff_lng),
                date: viaje.created_at,
                created_at: viaje.created_at,
                endTime: viaje.end_time,
                end_time: viaje.end_time,
                scheduled_for: viaje.scheduled_for,
                estado: viaje.status,
                price: viaje.price ? Number(viaje.price) : 0,
                distance: viaje.distance ? Number(viaje.distance) : 0,
                co2Saved: viaje.co2_saved ? Number(viaje.co2_saved) : 0,
                valoracion: viaje.rating,
                comment: viaje.comment,
                pasajeros: viaje.pasajeros || 1,
                luggage: viaje.luggage || 0,
                pago_method: viaje.pago_method || 'cash',
                notes: viaje.notes,
                pago: viaje.pago || null,
            }
        },

        async fetchTrips() {
            this.cargando = true
            this.error = null
            try {
                const auth = useAuthStore()
                let endpoint = '/api/user/viajes'

                if (auth.isconductor) {
                    endpoint = '/api/conductor/viajes'
                } else if (auth.isAdmin) {
                    endpoint = '/api/admin/viajes'
                }

                const response = await axios.get(endpoint)
                this.viajes = response.data.map(this.mapearViaje)
            } catch (error) {
                this.error = error.response?.data?.message || 'No se pudieron cargar los viajes'
            } finally {
                this.cargando = false
            }
        },

        async createTrip(datosViaje) {
            this.cargando = true
            try {
                const response = await axios.post('/api/viajes', {
                    pickup_lat: datosViaje.pickup_lat || datosViaje.pickupLat,
                    pickup_lng: datosViaje.pickup_lng || datosViaje.pickupLng,
                    dropoff_lat: datosViaje.dropoff_lat || datosViaje.dropoffLat,
                    dropoff_lng: datosViaje.dropoff_lng || datosViaje.dropoffLng,
                    pickup_address: datosViaje.pickup_address || datosViaje.pickup,
                    dropoff_address: datosViaje.dropoff_address || datosViaje.dropoff,
                    distance: datosViaje.distance,
                    scheduled_for: datosViaje.scheduled_for || null,
                    pasajeros: datosViaje.pasajeros || 1,
                    luggage: datosViaje.luggage || 0,
                    pago_method: datosViaje.pago_method || 'cash',
                    notes: datosViaje.notes || null,
                })

                const viajeNuevo = this.mapearViaje(response.data)

                this.viajes.unshift(viajeNuevo)
                this.viajeActivo = viajeNuevo

                return { success: true, viaje: viajeNuevo }
            } catch (error) {
                const message = error.response?.data?.message || 'No se pudo crear el viaje'
                this.error = message
                return { success: false, error: message }
            } finally {
                this.cargando = false
            }
        },

        async aceptarViaje(viajeId) {
            const response = await axios.patch(`/api/viajes/${viajeId}/accept`)
            const viajeActualizado = this.mapearViaje(response.data)
            this.viajes = this.viajes.map(t => t.id === viajeId ? viajeActualizado : t)
            this.viajeActivo = viajeActualizado
        },

        async startTrip(viajeId) {
            const response = await axios.patch(`/api/viajes/${viajeId}/start`)
            const viajeActualizado = this.mapearViaje(response.data)
            this.viajes = this.viajes.map(t => t.id === viajeId ? viajeActualizado : t)
            this.viajeActivo = viajeActualizado
        },

        async completeTrip(viajeId, valoracion = null, comment = '') {
            const response = await axios.patch(`/api/viajes/${viajeId}/complete`, {
                rating: valoracion,
                comment,
            })

            const viajeActualizado = this.mapearViaje(response.data)
            this.viajes = this.viajes.map(t => t.id === viajeId ? viajeActualizado : t)
            this.viajeActivo = null
        },

        async cancelTrip(viajeId) {
            const response = await axios.patch(`/api/viajes/${viajeId}/cancel`)
            const viajeActualizado = this.mapearViaje(response.data)
            this.viajes = this.viajes.map(t => t.id === viajeId ? viajeActualizado : t)
            this.viajeActivo = null
        },

        simularMovimientoConductor(viajeId) {
            const viaje = this.viajes.find(t => t.id === viajeId)
            if (!viaje) return

            let progress = 0
            const interval = setInterval(() => {
                progress += 0.1
                if (progress >= 1) {
                    clearInterval(interval)
                    this.ubicacionConductor = {
                        lat: viaje.dropoffLat,
                        lng: viaje.dropoffLng
                    }
                } else {
                    this.ubicacionConductor = {
                        lat: viaje.pickupLat + (viaje.dropoffLat - viaje.pickupLat) * progress,
                        lng: viaje.pickupLng + (viaje.dropoffLng - viaje.pickupLng) * progress
                    }
                }
            }, 2000)
        },

        async rateTrip(viajeId, valoracion, comment = '') {
            const viaje = this.viajes.find(t => t.id === viajeId)
            if (!viaje) return

            const response = await axios.patch(`/api/viajes/${viajeId}/rate`, {
                rating: valoracion,
                comment,
            })

            const viajeActualizado = this.mapearViaje(response.data)
            this.viajes = this.viajes.map(t => t.id === viajeId ? viajeActualizado : t)
        }
    }
})


