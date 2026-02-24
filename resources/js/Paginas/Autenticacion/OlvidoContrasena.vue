<script setup>
import DisposicionInvitado from '../../Disposiciones/DisposicionInvitado.vue';
import ErrorEntrada from '../../Componentes/ErrorEntrada.vue';
import EtiquetaEntrada from '../../Componentes/EtiquetaEntrada.vue';
import BotonPrimario from '../../Componentes/BotonPrimario.vue';
import EntradaTexto from '../../Componentes/EntradaTexto.vue';
import { Head, useForm } from '@inertiajs/vue3';
import CasillaVerificacion from '../../Componentes/CasillaVerificacion.vue';

defineProps({
    estado: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
  <DisposicionInvitado>
        <Head title="Forgot Password" />

        <div class="mb-4 text-sm text-gray-600">
            Forgot your password? No problem. Just let us know your email
            address and we will email you a password reset link that will allow
            you to choose a new one.
        </div>

        <div
            v-if="estado"
            class="mb-4 text-sm font-medium text-green-600"
        >
            {{ estado }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <EtiquetaEntrada for="email" value="Email" />

                <EntradaTexto
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <ErrorEntrada class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <BotonPrimario
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Email Password Reset Link
                </BotonPrimario>
            </div>
        </form>
  </DisposicionInvitado>
</template>
