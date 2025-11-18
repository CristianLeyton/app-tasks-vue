<template>
  <nav-bar :user="user" />
  <div class="container p-6 border-b border-neutral-300">
    <header class="flex justify-between items-center">
      <h2 class="text-lg font-semibold">Listado de tareas</h2>
    </header>

    <div class="mt-2 flex justify-between items-center">
      <div class="text-sm">
        Filtrar por estado:
        <select v-model="filterStatus" class="border rounded py-1 px-1.5 ml-2">
          <option value="all">Todas</option>
          <option value="pending">Pendientes</option>
          <option value="in_progress">En progreso</option>
          <option value="completed">Completadas</option>
        </select>
      </div>
      <button v-if="user.role !== 'viewer'"
        class="bg-neutral-800 text-white px-3 py-1 rounded hover:bg-neutral-900 active:bg-neutral-700 cursor-pointer"
        @click="openCreateModal()">
        Nueva
      </button>
    </div>
    <ul v-if="filteredTasks.length === 0">
      <li class="p-4 mt-4 rounded-xl border border-neutral-200 overflow-hidden odd:bg-neutral-100 even:bg-neutral-50">
        No hay tareas disponibles
      </li>
    </ul>

    <ul v-else class="mt-4 rounded-xl border border-neutral-200 overflow-hidden ">
      <!-- Tareas  -->
      <li v-for="task in filteredTasks" :key="task.id"
        class="px-4 py-2 odd:bg-neutral-100 even:bg-neutral-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
          <h2 class="font-semibold" :class="{
            'text-green-500': task.status === 'completed',
            'text-orange-400': task.status === 'in_progress'
          }">{{ task.title }} <span class="text-xs text-neutral-500 font-normal">
              ({{ statusLabels[task.status] }})</span>
            <span v-if="task.status === 'completed'"> ✔ </span>
            <span v-if="task.status === 'in_progress'"> ⏳ </span>
          </h2>
          <p class="text-sm">{{ task.description || 'Sin descripción' }}
          </p>
        </div>
        <div class="flex gap-1 self-end sm:self-auto">
          <button class="border border-neutral-300 px-2 py-1 rounded text-sm cursor-pointer hover:bg-white"
            @click="openViewModal(task)">
            Ver
          </button>
          <button v-if="user.role !== 'viewer'"
            class="border border-neutral-300 px-2 py-1 rounded text-sm cursor-pointer bg-neutral-800 text-white hover:bg-neutral-900 active:bg-neutral-700"
            @click="openEditModal(task)">
            Editar
          </button>
          <button v-if="user.role !== 'viewer'"
            class="border border-neutral-300 px-2 py-1 rounded text-sm cursor-pointer bg-red-600 text-white hover:bg-red-500 active:bg-red-700 "
            @click="openDeleteModal(task)">
            Eliminar
          </button>
        </div>
      </li>
    </ul>

    <!-- Modal de crear tarea -->
    <dialog ref="taskCreateModal" class="p-6 rounded-xl border border-neutral-300 w-96 inset-0 m-auto">
      <create-task-form :user="user" />
      <div class="mt-4 flex justify-end">
        <button @click="closeCreateModal()"
          class="bg-neutral-100 px-3 py-1 rounded hover:bg-neutral-200 active:bg-neutral-200 cursor-pointer">Cerrar</button>
      </div>
    </dialog>

    <!-- Modal de ver detalles de la tarea -->
    <dialog ref="taskViewModal" class="p-6 rounded-xl border border-neutral-300 w-96 inset-0 m-auto">
      <h2 class="font-semibold text-lg">{{ selectedTask.title }}</h2>
      <p class="mt-2">{{ selectedTask.description }}</p>
      <p class="mt-1 text-sm">Estado: {{ statusLabels[selectedTask.status] }}</p>
      <p v-if="selectedTask.due_date" class="mt-1 text-sm">Fecha límite: {{ selectedTask.due_date }}</p>
      <p v-if="selectedTask.user?.name" class="mt-2 text-sm"> Asignado a: {{ selectedTask.user?.name }} ({{
        selectedTask.user?.email }})</p>

      <div class="mt-4 flex justify-end">
        <button @click="closeViewModal()"
          class="bg-neutral-100 px-3 py-1 rounded hover:bg-neutral-200 active:bg-neutral-200 cursor-pointer">Cerrar</button>
      </div>
    </dialog>

    <!-- Modal de editar tarea -->
    <dialog ref="taskEditModal" class="p-6 rounded-xl border border-neutral-300 w-96 inset-0 m-auto">
      <edit-task-form :task="selectedTask" :key="selectedTask.id" />
      <div class="mt-4 flex justify-end">
        <button @click="closeEditModal()"
          class="bg-neutral-100 px-3 py-1 rounded hover:bg-neutral-200 active:bg-neutral-200 cursor-pointer">Cerrar</button>
      </div>
    </dialog>

    <!-- Modal de eliminar tarea -->
    <dialog ref="taskDeleteModal" class="p-6 rounded-xl border border-neutral-300 w-96 inset-0 m-auto">
      <h2 class="font-semibold text-lg text-red-600">¿Eliminar "{{ selectedTask.title }}"?</h2>
      <p class="mt-2">{{ selectedTask.description }}</p>
      <p class="mt-1 text-sm">Estado: {{ statusLabels[selectedTask.status] }}</p>
      <p v-if="selectedTask.due_date" class="mt-1 text-sm">Fecha límite: {{ selectedTask.due_date }}</p>
      <p v-if="selectedTask.user?.name" class="mt-2 text-sm"> Asignado a: {{ selectedTask.user?.name }} ({{
        selectedTask.user?.email }})</p>

      <div class="mt-4 flex justify-end">
        <button @click="confirmDelete()"
          class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-500 active:bg-red-700 cursor-pointer mr-2">Eliminar</button>
        <button @click="closeDeleteModal()"
          class="bg-neutral-100 px-3 py-1 rounded hover:bg-neutral-200 active:bg-neutral-200 cursor-pointer">Cerrar</button>
      </div>
    </dialog>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import NavBar from '../components/NavBar.vue';
import CreateTaskForm from '../components/CreateTaskForm.vue';
import EditTaskForm from '../components/EditTaskForm.vue';

const tasks = ref([]);
const user = ref({
  name: "",
  email: "",
  role: ""
});

const selectedTask = ref({}); // tarea seleccionada para el modal
const taskCreateModal = ref(null); // referencia al dialog create
const taskViewModal = ref(null); // referencia al dialog view
const taskEditModal = ref(null); // referencia al dialog edit
const taskDeleteModal = ref(null); // referencia al dialog delete
const filterStatus = ref('all');


onMounted(() => {
  getUserProfile();
  fetchTasks();
});

const statusLabels = { pending: "Pendiente", in_progress: "En progreso", completed: "Completada" };

// Abrir modal de crear Tarea
function openCreateModal() {
  taskCreateModal.value.showModal();
}
function closeCreateModal() {
  taskCreateModal.value.close();
}

// Abrir modal de ver Tarea
function openViewModal(task) {
  selectedTask.value = task;
  taskViewModal.value.showModal();
}
function closeViewModal() {
  taskViewModal.value.close();
}

// Abrir modal de editar Tarea
function openEditModal(task) {
  selectedTask.value = task;
  taskEditModal.value.showModal();
}
function closeEditModal() {
  taskEditModal.value.close();
}

// Abrir modal de eliminar Tarea
function openDeleteModal(task) {
  selectedTask.value = task;
  taskDeleteModal.value.showModal();
}
function closeDeleteModal() {
  taskDeleteModal.value.close();
}

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

// Obtener lista de tareas
async function fetchTasks() {
  try {
    const response = await fetch('/api/list-tasks', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
      },
    });

    const data = await response.json();
    if (response.ok) {
      tasks.value = data;
    } else {
      console.error('Error fetching tasks:', data.message);
    }
  } catch (error) {
    console.error('Connection error:', error);
  }

  /*   console.log(tasks.value); */
}

// Confirmar eliminación de tarea
async function confirmDelete() {
  try {
    const response = await fetch(`/api/delete-task/${selectedTask.value.id}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
      },
    });

    const data = await response.json();
    if (response.ok) {
      // Eliminar tarea de la lista localmente
      tasks.value = tasks.value.filter(task => task.id !== selectedTask.value.id);
      closeDeleteModal();
    } else {
      console.error('Error deleting task:', data.message);
    }
  } catch (error) {
    console.error('Connection error:', error);
  }
}

window.addEventListener('tasksUpdated', () => {
  fetchTasks();
  closeEditModal();
  closeCreateModal();
});


const filteredTasks = computed(() => {
  if (filterStatus.value === 'all') return tasks.value;
  return tasks.value.filter(t => t.status === filterStatus.value);
});

</script>
