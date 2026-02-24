<script setup>
import DisposicionAutenticada from '../../Disposiciones/DisposicionAutenticada.vue';
import EliminarUsuarioFormulario from './Parciales/EliminarUsuarioFormulario.vue';
import ActualizarInformacionPerfilFormulario from './Parciales/ActualizarInformacionPerfilFormulario.vue';
import ActualizarContrasenaFormulario from './Parciales/ActualizarContrasenaFormulario.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    estado: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <DisposicionAutenticada>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Profile
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <ActualizarInformacionPerfilFormulario
                        :must-verify-email="mustVerifyEmail"
                        :estado="estado"
                        class="max-w-xl"
                    />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <ActualizarContrasenaFormulario class="max-w-xl" />
                </div>

                <div
                    class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <EliminarUsuarioFormulario class="max-w-xl" />
                </div>
            </div>
        </div>
    </DisposicionAutenticada>
</template>
