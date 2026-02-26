// ...existing code...
// Eliminar bloque actions duplicado, dejar solo el que está dentro de defineStore
import { defineStore } from 'pinia'
import { useAuthStore } from './almacenAutenticacion'
import { useViajeStore } from './almacenViaje'
import axios from 'axios'

export const useConductorStore = defineStore('conductor', {
  state: () => ({
    perfil: null,
    estaEnLinea: false,
    estadisticas: {
      viajesHoy: 0,
      gananciasHoy: 0,
      gananciasSemanales: 0,
      gananciasMensuales: 0,
      valoracion: 4.92,
      totalViajes: 156
    },
    ubicacionActual: null,
    observadorUbicacion: null,
    cargando: false
  }),

  getters: {
    valoracionFormateada: (state) => state.estadisticas.valoracion.toFixed(2),
    gananciasHoyFormateadas: (state) => `${state.estadisticas.gananciasHoy.toFixed(2)} €`,
    estaDisponible: (state) => state.estaEnLinea && state.perfil?.verified,
    // isOnline y isAvailable eliminados, solo dejar para Mi Perfil si es necesario
    formattedRating: (state) => state.estadisticas.valoracion.toFixed(2),
    todayEarningsFormatted: (state) => `${state.estadisticas.gananciasHoy.toFixed(2)} €`
  },

  actions: {
    async obtenerPerfilConductor() {
      this.cargando = true
      try {
        const [respuestaPerfil, respuestaEstado] = await Promise.all([
          axios.get('/api/conductor/profile'),
          axios.get('/api/conductor/status'),
        ])

        const auth = useAuthStore()
        const datosPerfil = respuestaPerfil.data
        const taxi = datosPerfil.taxi || {}
        // Guardar avatar si existe en user
        const avatar = datosPerfil.user?.avatar || null

        // Sincronizar datos de usuario autenticado
        if (datosPerfil.user) {
          auth.usuario = {
            ...auth.usuario,
            name: datosPerfil.user.name,
            email: datosPerfil.user.email,
            phone: datosPerfil.user.phone,
            avatar: datosPerfil.user.avatar
          }
          localStorage.setItem('usuario', JSON.stringify(auth.usuario))
        }

        this.perfil = {
          id: auth.usuario?.id,
          name: datosPerfil.user?.name || auth.usuario?.name,
          email: datosPerfil.user?.email || auth.usuario?.email,
          phone: datosPerfil.user?.phone || '',
          avatar: avatar,
          licenseNumber: datosPerfil.license_number,
          vehicle: {
            model: taxi.model || 'Sin modelo',
            plate: taxi.plate || 'Sin matrícula',
            year: taxi.year || new Date().getFullYear(),
            color: taxi.color || 'Sin color',
            capacity: taxi.capacity || 4
          },
          verified: true,
          fechaRegistro: datosPerfil.created_at?.split('T')[0] || null
        }

        this.estaEnLinea = Boolean(respuestaEstado.data.is_active)
        this.estadisticas = {
          ...this.estadisticas,
          valoracion: Number(respuestaEstado.data.rating || this.estadisticas.valoracion),
        }
      } catch (error) {
        console.error('Error fetching conductor perfil:', error)
      } finally {
        this.cargando = false
      }
    },

    cambiarEstadoEnLinea() {
      this.establecerEstadoEnLinea(!this.estaEnLinea)
    },

    async establecerEstadoEnLinea(valor) {
      try {
        this.estaEnLinea = valor
        await axios.patch('/api/conductor/status', { is_active: valor })
        if (this.estaEnLinea) {
          this.iniciarSeguimientoUbicacion()
        } else {
          this.detenerSeguimientoUbicacion()
        }
      } catch (error) {
        console.error('Error al cambiar el estado laboral:', error)
        throw error
      }
    },

    iniciarSeguimientoUbicacion() {
      if (navigator.geolocation) {
        this.observadorUbicacion = navigator.geolocation.watchPosition(
          (position) => {
            this.ubicacionActual = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            }

            axios.post('/api/conductor/ubicacion', {
              lat: this.ubicacionActual.lat,
              lng: this.ubicacionActual.lng,
            }).catch(() => {})
          },
          (error) => console.error('Error getting ubicacion:', error),
          { enableHighAccuracy: true, timeout: 10000 }
        )
      }
    },

    detenerSeguimientoUbicacion() {
      if (this.observadorUbicacion) {
        navigator.geolocation.clearWatch(this.observadorUbicacion)
        this.observadorUbicacion = null
      }
    },

    async actualizarEstadisticas() {
      const storeViaje = useTripStore()
      const viajesHoy = storeViaje.viajesHoy.filter(t => t.conductorId === this.perfil?.id)
      const gananciasHoy = viajesHoy.reduce((suma, viaje) => suma + (viaje.price || 0), 0)

      this.estadisticas = {
        ...this.estadisticas,
        viajesHoy: viajesHoy.length,
        gananciasHoy,
        totalViajes: storeViaje.viajesConductor.length,
      }
    },

    async aceptarViaje(viajeId) {
      const storeViaje = useTripStore()
      await storeViaje.aceptarViaje(viajeId)
      this.actualizarEstadisticas()
    },

    // async obtenerPerfilConductor() { return this.obtenerPerfilConductor() },
    // toggleOnlineStatus eliminado, solo dejar para Mi Perfil si es necesario
    async setOnlineStatus(valor) { return this.establecerEstadoEnLinea(valor) },
    startubicacionTracking() { return this.iniciarSeguimientoUbicacion() },
    stopubicacionTracking() { return this.detenerSeguimientoUbicacion() },
    async actualizarEstadisticas() { return this.actualizarEstadisticas() },
    async aceptarViaje(viajeId) { return this.aceptarViaje(viajeId) }
  }
})

export const useDriverStore = useConductorStore

