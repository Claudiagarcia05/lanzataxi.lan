<script setup>
import CasillaVerificacion from '../../Componentes/CasillaVerificacion.vue';
import DisposicionInvitado from '../../Disposiciones/DisposicionInvitado.vue';
import ErrorEntrada from '../../Componentes/ErrorEntrada.vue';
import EtiquetaEntrada from '../../Componentes/EtiquetaEntrada.vue';
import BotonPrimario from '../../Componentes/BotonPrimario.vue';
import EntradaTexto from '../../Componentes/EntradaTexto.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    estado: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
  <DisposicionInvitado>
        <Head title="Log in" />

        <div v-if="estado" class="mb-4 text-sm font-medium text-green-600">
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

            <div class="mt-4">
                <EtiquetaEntrada for="password" value="Password" />

                <EntradaTexto
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <ErrorEntrada class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <CasillaVerificacion name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>

                <BotonPrimario
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </BotonPrimario>
            </div>
        </form>
  </DisposicionInvitado>
</template>
