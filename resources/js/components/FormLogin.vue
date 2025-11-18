<template>
  <div class="w-full max-w-sm mx-auto rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Iniciar sesión</h1>

    <form @submit.prevent="login" class="flex flex-col gap-2">
      <!-- Email -->
      <div>
        <label class="block  font-semibold">Email</label>
        <input
          type="email"
          v-model="form.email"
          class="w-full border p-2 rounded"
          required
        >
      </div>

      <!-- Password -->
      <div>
        <label class="block  font-semibold">Contraseña</label>
        <input
          type="password"
          v-model="form.password"
          class="w-full border p-2 rounded"
          required
        >
      </div>

      <a href="/register" class="text-sm font-semibold w-fit self-end hover:underline active:underline">Registrarme</a>
      <!-- Error -->
      <p v-if="error" class="text-pink-600">{{ error }}</p>

      <!-- Button -->
      <button
        type="submit"
        class="w-full bg-neutral-800 text-white p-2 rounded disabled:opacity-50 cursor-pointer hover:bg-neutral-900 active:bg-neutral-700"
        :disabled="loading"
      >
        <span v-if="loading">Ingresando...</span>
        <span v-else>Ingresar</span>
      </button>
    </form>
  </div>
</template>


<script setup>
import { ref } from "vue";

const form = ref({
  email: "",
  password: ""
});

const loading = ref(false);
const error = ref("");

async function login() {
  loading.value = true;
  error.value = "";

  try {
    const response = await fetch("/api/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
      },
      body: JSON.stringify(form.value)
    });

    const data = await response.json();

    if (!response.ok) {
      error.value = data.message || "Error al iniciar sesión";
    } else {

      //Guardar token en localStorage
      localStorage.setItem("token", data.token);

      window.location.href = "/";
    }

  } catch (e) {
    error.value = "Hubo un error en la conexión.";
  }

  loading.value = false;
}
</script>

