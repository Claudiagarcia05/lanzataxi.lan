<script setup>
import DisposicionInvitado from '../../Disposiciones/DisposicionInvitado.vue';
import ErrorEntrada from '../../Componentes/ErrorEntrada.vue';
import EtiquetaEntrada from '../../Componentes/EtiquetaEntrada.vue';
import BotonPrimario from '../../Componentes/BotonPrimario.vue';
import EntradaTexto from '../../Componentes/EntradaTexto.vue';
import { Head, useForm } from '@inertiajs/vue3';
import CasillaVerificacion from '../../Componentes/CasillaVerificacion.vue';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <DisposicionInvitado>
        <Head title="Confirm Password" />

        <div class="mb-4 text-sm text-gray-600">
            This is a secure area of the application. Please confirm your
            password before continuing.
        </div>

        <form @submit.prevent="submit">
            <div>
                <EtiquetaEntrada for="password" value="Password" />
                <EntradaTexto
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                />
                <ErrorEntrada class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 flex justify-end">
                <BotonPrimario
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Confirm
                </BotonPrimario>
            </div>
        </form>
    </DisposicionInvitado>
</template>
