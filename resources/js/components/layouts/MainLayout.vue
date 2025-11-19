<template>
    <nav-bar :user="user" />
    <header class="flex justify-between items-center">
        <h2 class="text-lg font-semibold w-full px-6 py-4 border-b border-neutral-300">
            <slot name="header"><h1>{{ props.titleH1 }}</h1></slot>
        </h2>
    </header>
    <main class="container p-6 border-b border-neutral-300" :user="user">
        <slot :user="user" />
    </main>
</template>

<script setup>
import { ref } from 'vue';
import NavBar from '../NavBar.vue';

const props = defineProps({
  titleH1: String,
});

const user = ref({
    name: "",
    email: "",
    role: ""
});

getUserProfile();

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
</script>

<style scoped></style>
