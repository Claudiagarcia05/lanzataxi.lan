<template>
	<form @submit.prevent="eliminarCuenta">
		<div>
			<EtiquetaEntrada for="password">Contraseña</EtiquetaEntrada>
			<EntradaTexto id="password" v-model="password" type="password" required />
			<ErrorEntrada v-if="error" :mensaje="error" />
		</div>
		<BotonPrimario type="submit">Eliminar cuenta</BotonPrimario>
	</form>
</template>

<script>
import ErrorEntrada from '../../../Componentes/ErrorEntrada.vue';
import EtiquetaEntrada from '../../../Componentes/EtiquetaEntrada.vue';
import BotonPrimario from '../../../Componentes/BotonPrimario.vue';
import EntradaTexto from '../../../Componentes/EntradaTexto.vue';

export default {
	components: {
		ErrorEntrada,
		EtiquetaEntrada,
		BotonPrimario,
		EntradaTexto
	},
	data() {
		return {
			password: '',
			error: ''
		};
	},
	methods: {
		eliminarCuenta() {
			// Lógica para eliminar la cuenta
			// Si hay error:
			// this.error = 'Contraseña incorrecta';
		}
	}
};
</script>