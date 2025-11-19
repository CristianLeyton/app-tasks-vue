<template>
  <nav class="flex items-center justify-end gap-4 p-4 border-b border-neutral-300">
    <router-link v-if="!loggedIn" to="/login"
      class="hover:bg-neutral-900 active:bg-neutral-700 cursor-pointer flex items-center gap-1 px-3 py-1 text-sm text-white rounded bg-neutral-800">Iniciar
      sesión</router-link>
    <router-link v-if="!loggedIn" to="/register"
      class="hover:bg-neutral-900 active:bg-neutral-700 cursor-pointer flex items-center gap-1 px-3 py-1 text-sm text-white rounded bg-neutral-800">Registrarme</router-link>
    <p v-if="props.user.name && loggedIn" class="">Bienvenido, {{ props.user.name }} ({{ props.user.role }})</p>
    <p v-if="!props.user.name" class="w-xs h-7 bg-neutral-200 animate-pulse rounded"></p>

    <div class="flex gap-2" v-if="loggedIn">
      <div v-if="user.role == 'admin'" class="flex gap-2">
      <router-link to="/"
        class="hover:bg-neutral-900 active:bg-neutral-700 cursor-pointer flex items-center gap-1 px-3 py-1 text-sm text-white rounded bg-neutral-800">
        <pad-icon class="text-white size-5" />Tareas
      </router-link>
      <router-link to="/panel"
        class="hover:bg-neutral-900 active:bg-neutral-700 cursor-pointer flex items-center gap-1 px-3 py-1 text-sm text-white rounded bg-neutral-800">
        <users-icon class="text-white size-5" />Usuarios
      </router-link>
      </div>
      <button @click="logout"
        class="hover:bg-neutral-900 active:bg-neutral-700 cursor-pointer flex items-center gap-1 px-3 py-1 text-sm text-white rounded bg-neutral-800">
        <log-out-icon class="text-white size-5" />Salir</button>
    </div>
  </nav>
</template>

<script setup>
import { ref } from "vue";
import LogOutIcon from "./icons/LogOutIcon.vue";
import PadIcon from "./icons/PadIcon.vue";
import UsersIcon from "./icons/UsersIcon.vue";

const props = defineProps({
  user: Object,
});

const loggedIn = ref(!!localStorage.getItem("token"));

//Cerrar sesión
function logout() {
  try {
    fetch("/api/logout", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "Authorization": `Bearer ${localStorage.getItem("token")}`,
      }
    });
  } catch (error) {
    error.value = "Hubo un error en la conexión.";
  }
  localStorage.removeItem("token");
  loggedIn.value = false;
  window.location.href = "/login"; // Redirige a la página de login
}
</script>
