<template>
    <MainLayout titleH1="Administrar usuarios" v-slot="{user}">
        <ul v-if="user.role !== 'admin'">
            <li class="p-4  rounded-xl border border-neutral-200 overflow-hidden odd:bg-neutral-100 even:bg-neutral-50">
                No tienes acceso a esta sección.
            </li>
        </ul>

        <ul v-else class=" rounded-xl border border-neutral-200 overflow-hidden ">
            <li v-for="user in allusers" :key="user.id"
                class="px-4 py-2 odd:bg-neutral-100 even:bg-neutral-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="font-semibold">{{ user.name }} <span class="text-xs text-neutral-500 font-normal">({{
                        user.roles[0].name }})</span></h2>
                    <p class="text-sm">{{ user.email }}</p>
                </div>
                <div class="flex gap-1 self-end sm:self-auto">
                    <button title="Editar rol" @click="openEditModal(user.id, user.name, user.roles[0].name)"
                        class="border border-neutral-300 px-2 py-1 rounded text-sm cursor-pointer bg-neutral-800 text-white hover:bg-neutral-900 active:bg-neutral-700">
                        <edit-icon class="size-4.5 text-white" />
                    </button>
                    <button title="Eliminar usuario" @click="openDeleteModal(user.id, user.name)"
                        class="border border-neutral-300 px-2 py-1 rounded text-sm cursor-pointer bg-red-600 text-white hover:bg-red-500 active:bg-red-700 ">
                        <delete-icon class="size-4.5 text-white" />
                    </button>
                </div>
            </li>
        </ul>

        <!-- Modal de editar usuario -->
        <dialog ref="editUserModal" class="p-6 rounded-xl border border-neutral-300 w-96 inset-0 m-auto">

            <form @submit.prevent="editRoleUser(selectedUserId)">
                <h2 class="font-semibold text-lg">Editar rol de "{{ selectedUserName }}"</h2>
                <div class="mt-4">
                    <label for="role" class="block text-sm font-medium text-neutral-700">Rol:</label>
                    <select id="role" v-model="selectedUserRole"
                        class="mt-1 block w-full border border-neutral-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-neutral-500 focus:border-neutral-500 sm:text-sm">
                        <option value="admin">Admin</option>
                        <option value="editor">Editor</option>
                        <option value="viewer">Viewer</option>
                    </select>
                </div>

            </form>

            <div class="mt-4 flex justify-end">
                <button @click="editRoleUser(selectedUserId)"
                    class="bg-neutral-800 text-white px-3 py-1 rounded hover:bg-neutral-900 active:bg-neutral-700 cursor-pointer mr-2">Guardar</button>
                <button @click="closeEditModal()"
                    class="bg-neutral-100 px-3 py-1 rounded hover:bg-neutral-200 active:bg-neutral-200 cursor-pointer">Cerrar</button>
            </div>
        </dialog>

        <!-- Modal de eliminar usuario -->
        <dialog ref="deleteUserModal" class="p-6 rounded-xl border border-neutral-300 w-96 inset-0 m-auto">
            <h2 class="font-semibold text-lg text-red-600">¿Eliminar usuario "{{ selectedUserName }}"?</h2>
            <p class="text-pretty text-neutral-800 pt-1">Esto tambien eliminará todas las tareas asignadas a "{{
                selectedUserName }}"</p>
            <div class="mt-4 flex justify-end">
                <button @click="confirmDeleteUser(selectedUserId)"
                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-500 active:bg-red-700 cursor-pointer mr-2">Eliminar</button>
                <button @click="closeDeleteModal()"
                    class="bg-neutral-100 px-3 py-1 rounded hover:bg-neutral-200 active:bg-neutral-200 cursor-pointer">Cerrar</button>
            </div>
        </dialog>

    </MainLayout>
</template>
<script setup>
import { ref } from 'vue';
import MainLayout from '../components/layouts/MainLayout.vue';
import EditIcon from '../components/icons/EditIcon.vue';
import DeleteIcon from '../components/icons/DeleteIcon.vue';

// Obtener perfil de usuario desde MainLayout
const slots = defineSlots();
let user = ref({
    name: "",
    email: "",
    role: ""
});
if (slots.default) {
    const props = slots.default();
    user.value = props?.user;
}

const editUserModal = ref(null); // referencia al dialog edit
const deleteUserModal = ref(null); // referencia al dialog delete
const selectedUserRole = ref(null); // rol seleccionado en el modal de editar

const allusers = ref([]); // lista de todos los usuarios 
const selectedUserId = ref(null);
const selectedUserName = ref(null);

getAllUsers();

// Abrir modal de editar usuario
function openEditModal(userId, userName, userRole) {
    selectedUserRole.value = userRole;
    selectedUserId.value = userId;
    selectedUserName.value = userName;
    editUserModal.value.showModal();
}

function closeEditModal() {
    selectedUserRole.value = null;
    selectedUserId.value = null;
    selectedUserName.value = null;
    editUserModal.value.close();
}

// Abrir modal de eliminar usuario
function openDeleteModal(userId, userName) {
    selectedUserId.value = userId;
    selectedUserName.value = userName;
    deleteUserModal.value.showModal();
}

function closeDeleteModal() {
    selectedUserId.value = null;
    selectedUserName.value = null;
    deleteUserModal.value.close();
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
            // Filtrar para no mostrar el usuario con ID 1 y el usuario actual
            allusers.value = data.filter(
                u => u.id !== 1 && u.id !== user.value.id);
        }
    } catch (error) {
        console.error("Connection error:", error);
    }
}

// Editar rol de usuario
async function editRoleUser(id) {
    try {
        const response = await fetch(`/api/user-update-role/${id}`, {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": `Bearer ${localStorage.getItem("token")}`,
            },
            body: JSON.stringify({
                role: selectedUserRole.value
            })
        });

        const data = await response.json();

        if (response.ok) {
            // Refrescar la lista de usuarios
            closeEditModal()
            getAllUsers();
        } else {
            console.error("Error al actualizar el rol:", data.message);
        }
    } catch (error) {
        console.error("Connection error:", error);
    }
}

// Eliminar usuario
async function confirmDeleteUser(id) {

    try {
        const response = await fetch(`/api/user-delete/${id}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "Authorization": `Bearer ${localStorage.getItem("token")}`,
            }
        });

        const data = await response.json();

        if (response.ok) {
            // Refrescar la lista de usuarios
            closeDeleteModal()
            getAllUsers();
        } else {
            console.error("Error al eliminar el usuario:", data.message);
        }
    } catch (error) {
        console.error("Connection error:", error);
    }
}
</script>
