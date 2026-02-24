<script setup>
import EntradaTexto from '../../../Componentes/EntradaTexto.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import ErrorEntrada from '../../../Componentes/ErrorEntrada.vue';
import EtiquetaEntrada from '../../../Componentes/EtiquetaEntrada.vue';
import BotonPrimario from '../../../Componentes/BotonPrimario.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    estado: {
        type: String,
    },
});

const usuario = usePage().props.auth.usuario;

const form = useForm({
    name: usuario.name,
    email: usuario.email,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's perfil information and email address.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('perfil.update'))"
            class="mt-6 space-y-6"
        >
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

            <div>
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

            <div v-if="mustVerifyEmail && usuario.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="estado === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <BotonPrimario :disabled="form.processing">Save</BotonPrimario>

                <Transition
                    enter-activo-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-activo-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
