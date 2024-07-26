<template>
  <div class="container mt-4">
    <h1 class="mb-4">Gestión de Materias</h1>

    <!-- Formulario para agregar/editar materia -->
    <div class="card mb-4">
      <div class="card-header">
        {{ isEditing ? 'Editar Materia' : 'Agregar Materia' }}
      </div>
      <div class="card-body">
        <form @submit.prevent="submitForm">
          <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" v-model="form.nombre" placeholder="Nombre" required>
            <div v-if="errors.nombre" class="text-danger">{{ errors.nombre }}</div>
          </div>
          <button type="submit" class="btn btn-primary">{{ isEditing ? 'Actualizar' : 'Guardar' }}</button>
        </form>
      </div>
    </div>

    <!-- Lista de materias -->
    <div class="card">
      <div class="card-header">
        Lista de Materias
      </div>
      <ul class="list-group list-group-flush">
        <li v-for="materia in materias" :key="materia.id" class="list-group-item d-flex justify-content-between align-items-center">
          {{ materia.nombre }}
          <div>
            <button class="btn btn-warning btn-sm me-2" @click="editMateria(materia)">Editar</button>
            <button class="btn btn-danger btn-sm" @click="deleteMateria(materia.id)">Eliminar</button>
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
    const materias = ref([]);
    const form = reactive({
      id: null,
      nombre: ''
    });
    const isEditing = ref(false);
    const errors = reactive({});

    async function getMaterias() {
      try {
        const response = await fetch('/api/materias');
        if (response.ok) {
          materias.value = await response.json();
        } else {
          console.error('Error fetching materias:', response.statusText);
        }
      } catch (error) {
        console.error('Error fetching materias:', error);
      }
    }

    function validateForm() {
      let valid = true;
      
      errors.nombre = form.nombre.trim() ? '' : 'El nombre es requerido.';
      
      return valid && !errors.nombre;
    }

    async function submitForm() {
      if (!validateForm()) return;

      try {
        const method = isEditing.value ? 'PUT' : 'POST';
        const url = isEditing.value ? `/api/materias/${form.id}` : '/api/materias';
        
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
            // Actualiza un objeto en la lista de materias
            const index = materias.value.findIndex(materia => materia.id === data.id);
            if (index !== -1) {
              materias.value[index] = data;
            }
            isEditing.value = false;
          } else {
            materias.value.push(data);
          }
          
          resetForm();
        } else {
          console.error('Error saving materia:', await response.text());
        }
      } catch (error) {
        console.error('Error submitting form:', error);
      }
    }

    function editMateria(materia) {
      form.id = materia.id;
      form.nombre = materia.nombre || '';
      isEditing.value = true;
    }

    async function deleteMateria(id) {
      try {
        await fetch(`/api/materias/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        materias.value = materias.value.filter(materia => materia.id !== id);
      } catch (error) {
        console.error('Error deleting materia:', error);
      }
    }

    function resetForm() {
      form.id = null;
      form.nombre = '';
      errors.nombre = '';
    }

    onMounted(() => {
      getMaterias();
    });

    return { materias, form, isEditing, errors, submitForm, editMateria, deleteMateria };
  }
};
</script>
