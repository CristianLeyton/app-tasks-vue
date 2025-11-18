<template>
  <div class="w-full max-w-sm mx-auto rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Registrate</h1>

    <form @submit.prevent="register" class="flex flex-col gap-2">

    <!-- Name -->
    <div>
        <label class="block  font-semibold">Nombre</label>
        <input
          type="text"
          v-model="form.name"
          class="w-full border p-2 rounded"
          required
        >
    </div>
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
          @input="validatePassword"
          class="w-full border p-2 rounded"
          required
        >
      </div>

        <!-- Confirm Password -->
      <div>
        <label class="block  font-semibold">Confirmar contraseña</label>
        <input
          type="password"
          v-model="form.password_confirmation"
          @input="validatePassword"
          class="w-full border p-2 rounded"
          required
        >
      </div>

      <a href="/login" class="text-sm font-semibold w-fit self-end hover:underline active:underline">Ya tengo cuenta</a>
      <!-- Error -->
      <p v-if="error" class="text-pink-600">{{ error }}</p>
      <p v-if="errorPassword" class="text-pink-600">{{ errorPassword }}</p>
      <!-- Button -->
      <button
        type="submit"
        class="w-full bg-neutral-800 text-white p-2 rounded disabled:opacity-50 cursor-pointer hover:bg-neutral-900 active:bg-neutral-700"
        :disabled="loading || disabledButton"
      >
        <span v-if="loading">Creando cuenta...</span>
        <span v-else>Registrarme</span>
      </button>
    </form>
  </div>
</template>


<script setup>
import { ref } from "vue";

const form = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: ""
});

const loading = ref(false);
const disabledButton = ref(false);
const error = ref("");
const errorPassword = ref("");

function validatePassword() {
  if (form.value.password !== form.value.password_confirmation && form.value.password !== "" && form.value.password_confirmation !== "") {
    errorPassword.value = "Las contraseñas no coinciden.";
    disabledButton.value = true;
    return false;
  }
  errorPassword.value = "";
  disabledButton.value = false;
  return true;
}

async function register() {
  loading.value = true;
  error.value = "";

  try {
    const response = await fetch("/api/register", {
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

      window.location.href = "/login";
    }

  } catch (e) {
    error.value = "Hubo un error en la conexión.";
  }

  loading.value = false;
}
</script>

