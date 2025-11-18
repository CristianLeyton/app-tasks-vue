<template>
  <div>
    <form @submit.prevent="createTask" class="flex flex-col gap-2">
      <!-- Title -->
      <div>
        <label class="block font-semibold">Título *</label>
        <input type="text" v-model="formCreate.title" class="w-full border p-2 rounded" required>
      </div>

      <!-- Description -->
      <div>
        <label class="block font-semibold">Descripción</label>
        <textarea v-model="formCreate.description" class="w-full border p-2 rounded"></textarea>
      </div>

      <!-- Due Date -->
      <div>
        <label class="block font-semibold">Fecha límite</label>
        <input type="date" v-model="formCreate.due_date" class="w-full border p-2 rounded">
      </div>

      <select v-model="formCreate.status" class="border rounded w-full py-2 px-1.5">
        <option value="pending">Pendiente</option>
        <option value="in_progress">En progreso</option>
        <option value="completed">Completada</option>
      </select>

      <!-- Asignar tarea a usuario -->
      <div v-if="allusers.length !== 0 && user.role == 'admin'">
        <label class="block font-semibold">Asignar a</label>
        <select v-model="formCreate.user_id" class="border rounded w-full py-2 px-1.5">
          <option value="" disabled>-- Seleccionar usuario --</option>
          <option v-for="user in allusers" :key="user.id" :value="user.id">{{ user.name }}</option>
        </select>
      </div>

      <!-- Button -->
      <button type="submit"
        class="w-full bg-neutral-800 text-white px-2 py-1.5 rounded disabled:opacity-50 cursor-pointer hover:bg-neutral-900 active:bg-neutral-700">
        Crear tarea
      </button>

    </form>
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";

const props = defineProps({
  user: Object,
});

const formCreate = ref({
  title: "",
  description: "",
  due_date: "",
  status: "pending",   // valor por defecto
  user_id: ""
});

const allusers = ref([]); // lista de todos los usuarios para asignar tareas

async function createTask() {
  try {
    const response = await fetch("/api/create-task", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "Authorization": `Bearer ${localStorage.getItem("token")}`,
      },
      body: JSON.stringify(formCreate.value)
    });

    if (!response.ok) {
      throw new Error("Error al crear la tarea.");
    }

    //Refrescar la lista de tareas
    dispatchEvent(new Event('tasksUpdated'));
    formCreate.value = {
      title: "",
      description: "",
      due_date: "",
      status: "pending",   // valor por defecto
      user_id: ""
    };
  } catch (error) {
    console.error("Error al crear la tarea:", error);
  }
}

onMounted(() => {
  getAllUsers();
});


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
