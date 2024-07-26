<template>
  <div class="container mt-4">
    <h1 class="mb-4">Gestión de Estudiantes</h1>

    <!-- Formulario para agregar/editar estudiante -->
    <div class="card mb-4">
      <div class="card-header">
        {{ isEditing ? 'Editar Estudiante' : 'Agregar Estudiante' }}
      </div>
      <div class="card-body">
        <form @submit.prevent="submitForm">
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" v-model="form.nombre" placeholder="Nombre" required>
            <div v-if="errors.nombre" class="text-danger">{{ errors.nombre }}</div>
          </div>
          <div class="mb-3">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" class="form-control" id="apellido" v-model="form.apellido" placeholder="Apellido" required>
            <div v-if="errors.apellido" class="text-danger">{{ errors.apellido }}</div>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" v-model="form.email" placeholder="Email" required>
            <div v-if="errors.email" class="text-danger">{{ errors.email }}</div>
          </div>
          <button type="submit" class="btn btn-primary">{{ isEditing ? 'Actualizar' : 'Guardar' }}</button>
        </form>
      </div>
    </div>

    <!-- Lista de estudiantes -->
    <div class="card">
      <div class="card-header">
        Lista de Estudiantes
      </div>
      <ul class="list-group list-group-flush">
        <li v-for="estudiante in estudiantes" :key="estudiante.id" class="list-group-item d-flex justify-content-between align-items-center">
          {{ estudiante.nombre }} {{ estudiante.apellido }} - {{ estudiante.email }}
          <div>
            <button class="btn btn-warning btn-sm me-2" @click="editEstudiante(estudiante)">Editar</button>
            <button class="btn btn-danger btn-sm" @click="deleteEstudiante(estudiante.id)">Eliminar</button>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';

export default {
  setup() {
    const estudiantes = ref([]);
    const form = reactive({
      id: null,
      nombre: '',
      apellido: '',
      email: ''
    });
    const isEditing = ref(false);
    const errors = reactive({});

    async function getEstudiantes() {
      try {
        const response = await fetch('/api/estudiantes');
        if (response.ok) {
          estudiantes.value = await response.json();
        } else {
          console.error('Error fetching estudiantes:', response.statusText);
        }
      } catch (error) {
        console.error('Error fetching estudiantes:', error);
      }
    }

    function validateForm() {
      let valid = true;

      errors.nombre = form.nombre.trim() ? '' : 'El nombre es requerido.';
      errors.apellido = form.apellido.trim() ? '' : 'El apellido es requerido.';

      if (!form.email.trim()) {
        errors.email = 'El email es requerido.';
        valid = false;
      } else if (!isValidEmail(form.email)) {
        errors.email = 'El formato del email es inválido.';
        valid = false;
      } else {
        errors.email = '';
      }

      return valid;
    }

    function isValidEmail(email) {
      // Expresión regular para validar el formato del correo electrónico
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailPattern.test(email);
    }

    async function submitForm() {
      if (!validateForm()) return;

      try {
        const method = isEditing.value ? 'PUT' : 'POST';
        const url = isEditing.value ? `/api/estudiantes/${form.id}` : '/api/estudiantes';
        
        const response = await fetch(url, {
          method: method,
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(form)
        });

        if (response.ok) {
          const data = await response.json();

          if (isEditing.value) {
            // Actualiza un objeto en la lista de estudiantes
            const index = estudiantes.value.findIndex(estudiante => estudiante.id === data.id);
            if (index !== -1) {
              estudiantes.value[index] = data;
            }
            isEditing.value = false;
          } else {
            estudiantes.value.push(data); // Añade el nuevo estudiante
          }
          
          resetForm();
        } else {
          console.error('Error saving estudiante:', await response.text());
        }
      } catch (error) {
        console.error('Error submitting form:', error);
      }
    }

    function editEstudiante(estudiante) {
      form.id = estudiante.id;
      form.nombre = estudiante.nombre || '';
      form.apellido = estudiante.apellido || '';
      form.email = estudiante.email || '';
      isEditing.value = true;
    }

    async function deleteEstudiante(id) {
      try {
        await fetch(`/api/estudiantes/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        estudiantes.value = estudiantes.value.filter(estudiante => estudiante.id !== id);
      } catch (error) {
        console.error('Error deleting estudiante:', error);
      }
    }

    function resetForm() {
      form.id = null;
      form.nombre = '';
      form.apellido = '';
      form.email = '';
      errors.nombre = '';
      errors.apellido = '';
      errors.email = '';
    }

    onMounted(() => {
      getEstudiantes();
    });

    return { estudiantes, form, isEditing, errors, submitForm, editEstudiante, deleteEstudiante };
  }
};
</script>