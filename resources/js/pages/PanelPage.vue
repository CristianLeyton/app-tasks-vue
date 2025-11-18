<template>
    <nav-bar :user="user" />
    <div class="container p-6 border-b border-neutral-300">
        <header class="flex justify-between items-center">
            <h2 class="text-lg font-semibold">Administrar usuarios</h2>
        </header>

        <ul v-if="user.role !== 'admin'">
            <li
                class="p-4 mt-4 rounded-xl border border-neutral-200 overflow-hidden odd:bg-neutral-100 even:bg-neutral-50">
                No tienes acceso a esta sección.
            </li>
        </ul>

        <ul v-else class="mt-4 rounded-xl border border-neutral-200 overflow-hidden ">
            <li v-for="user in allusers" :key="user.id"
                class="px-4 py-2 odd:bg-neutral-100 even:bg-neutral-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="font-semibold">{{ user.name }} <span class="text-xs text-neutral-500 font-normal">({{
                        user.roles[0].name }})</span></h2>
                    <p class="text-sm">{{ user.email }}</p>
                </div>
                <div class="flex gap-1 self-end sm:self-auto">
                     <button class="border border-neutral-300 px-2 py-1 rounded text-sm cursor-pointer bg-neutral-800 text-white hover:bg-neutral-900 active:bg-neutral-700">Editar</button>
                     <button class="border border-neutral-300 px-2 py-1 rounded text-sm cursor-pointer bg-red-600 text-white hover:bg-red-500 active:bg-red-700 ">Eliminar</button>
                </div>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import NavBar from '../components/NavBar.vue';

const user = ref({
    name: "",
    email: "",
    role: ""
});

const allusers = ref([]); // lista de todos los usuarios 

onMounted(() => {
    getUserProfile();
    getAllUsers();
});

// Obtener perfil de usuario
async function getUserProfile() {
    try {
        const response = await fetch("/api/user-profile", {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": `Bearer ${localStorage.getItem("token")}`,
            }
        });

        const data = await response.json();

        /*     console.log(data); */
        if (response.ok) {
            user.value.name = data.userProfile.name;
            user.value.email = data.userProfile.email;
            user.value.role = data.role;
        }
    } catch (error) {
        console.error("Connection error:", error);
    }

}

// Obtener lista de todos los usuarios (para admin)
async function getAllUsers() {
    try {
        const response = await fetch("/api/get-users", {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": `Bearer ${localStorage.getItem("token")}`,
            }
        });

        const data = await response.json();

        if (response.ok) {
            allusers.value = data;
            //console.log(allusers.value);
        }
    } catch (error) {
        console.error("Connection error:", error);
    }
}
</script>
