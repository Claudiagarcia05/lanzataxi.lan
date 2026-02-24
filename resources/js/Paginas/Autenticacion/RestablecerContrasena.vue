<script setup>
import DisposicionInvitado from '../../Disposiciones/DisposicionInvitado.vue';
import ErrorEntrada from '../../Componentes/ErrorEntrada.vue';
import EtiquetaEntrada from '../../Componentes/EtiquetaEntrada.vue';
import BotonPrimario from '../../Componentes/BotonPrimario.vue';
import EntradaTexto from '../../Componentes/EntradaTexto.vue';
import { Head, useForm } from '@inertiajs/vue3';
import CasillaVerificacion from '../../Componentes/CasillaVerificacion.vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
  <DisposicionInvitado>
        <Head title="Reset Password" />

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

            <div class="mt-4">
                <EtiquetaEntrada for="password" value="Password" />

                <EntradaTexto
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />

                <ErrorEntrada class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <EtiquetaEntrada
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <EntradaTexto
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <ErrorEntrada
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <BotonPrimario
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Reset Password
                </BotonPrimario>
            </div>
        </form>
  </DisposicionInvitado>
</template>
