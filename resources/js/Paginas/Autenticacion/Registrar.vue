<script setup>
import DisposicionInvitado from '../../Disposiciones/DisposicionInvitado.vue';
import ErrorEntrada from '../../Componentes/ErrorEntrada.vue';
import EtiquetaEntrada from '../../Componentes/EtiquetaEntrada.vue';
import BotonPrimario from '../../Componentes/BotonPrimario.vue';
import EntradaTexto from '../../Componentes/EntradaTexto.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import CasillaVerificacion from '../../Componentes/CasillaVerificacion.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <DisposicionInvitado>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <EtiquetaEntrada for="name" value="Name" />

                <EntradaTexto
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <ErrorEntrada class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <EtiquetaEntrada for="email" value="Email" />

                <EntradaTexto
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
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
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>

                <BotonPrimario
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </BotonPrimario>
            </div>
        </form>
    </DisposicionInvitado>
</template>
