<template>
  <div>
    <form @submit.prevent="editTask" class="flex flex-col gap-2">
      <!-- Title -->
      <div>
        <label class="block font-semibold">Título *</label>
        <input type="text" v-model="formEdit.title" class="w-full border p-2 rounded" required>
      </div>

      <!-- Description -->
      <div>
        <label class="block font-semibold">Descripción</label>
        <textarea v-model="formEdit.description" class="w-full border p-2 rounded"></textarea>
      </div>

      <!-- Due Date -->
      <div>
        <label class="block font-semibold">Fecha límite</label>
        <input type="date" v-model="formEdit.due_date" class="w-full border p-2 rounded">
      </div>

      <select v-model="formEdit.status" class="border rounded w-full py-2 px-1.5">
        <option value="pending">Pendiente</option>
        <option value="in_progress">En progreso</option>
        <option value="completed">Completada</option>
      </select>

      <!-- Button -->
      <button type="submit"
        class="w-full bg-neutral-800 text-white px-2 py-1.5 rounded disabled:opacity-50 cursor-pointer hover:bg-neutral-900 active:bg-neutral-700">
        Guardar cambios
      </button>

    </form>
  </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
  task: Object,
});

const formEdit = ref({
  title: props.task.title || "",
  description: props.task.description || "",
  due_date: props.task.due_date || "",
  status: props.task.status || "pending",   // valor por defecto
});

async function editTask() {
  try {
    const response = await fetch(`/api/edit-task/${props.task.id}`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "Authorization": `Bearer ${localStorage.getItem("token")}`,
      },
      body: JSON.stringify(formEdit.value)
    });

    const data = await response.json(); 
    if (!response.ok) {
      throw data;
    }

    //Refrescar la lista de tareas
    dispatchEvent(new Event('tasksUpdated'));
  } catch (error) {
    console.error("Error al editar la tarea:", error.message);
  }
}
</script>
