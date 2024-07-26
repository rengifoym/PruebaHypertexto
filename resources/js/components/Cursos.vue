<template>
  <div class="container mt-4">
    <h1 class="mb-4">Gestión de Cursos</h1>

    <!-- Formulario para agregar/editar curso -->
    <div class="card mb-4">
      <div class="card-header">
        {{ isEditing ? 'Editar Curso' : 'Agregar Curso' }}
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

    <!-- Lista de cursos -->
    <div class="card">
      <div class="card-header">
        Lista de Cursos
      </div>
      <ul class="list-group list-group-flush">
        <li v-for="curso in cursos" :key="curso.id" class="list-group-item d-flex justify-content-between align-items-center">
          {{ curso.nombre }}
          <div>
            <button class="btn btn-warning btn-sm me-2" @click="editCurso(curso)">Editar</button>
            <button class="btn btn-danger btn-sm" @click="deleteCurso(curso.id)">Eliminar</button>
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
    const cursos = ref([]);
    const form = reactive({
      id: null,
      nombre: ''
    });
    const isEditing = ref(false);
    const errors = reactive({});

    async function getCursos() {
      try {
        const response = await fetch('/api/cursos');
        if (response.ok) {
          cursos.value = await response.json();
        } else {
          console.error('Error fetching cursos:', response.statusText);
        }
      } catch (error) {
        console.error('Error fetching cursos:', error);
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
        const url = isEditing.value ? `/api/cursos/${form.id}` : '/api/cursos';
        
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
            // Actualiza un objeto en la lista de cursos
            const index = cursos.value.findIndex(curso => curso.id === data.id);
            if (index !== -1) {
              cursos.value[index] = data;
            }
            isEditing.value = false;
          } else {
            cursos.value.push(data); // Añade el nuevo curso
          }
          
          resetForm();
        } else {
          console.error('Error saving curso:', await response.text());
        }
      } catch (error) {
        console.error('Error submitting form:', error);
      }
    }

    function editCurso(curso) {
      form.id = curso.id;
      form.nombre = curso.nombre || '';
      isEditing.value = true;
    }

    async function deleteCurso(id) {
      try {
        await fetch(`/api/cursos/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        cursos.value = cursos.value.filter(curso => curso.id !== id);
      } catch (error) {
        console.error('Error deleting curso:', error);
      }
    }

    function resetForm() {
      form.id = null;
      form.nombre = '';
      errors.nombre = '';
    }

    onMounted(() => {
      getCursos();
    });

    return { cursos, form, isEditing, errors, submitForm, editCurso, deleteCurso };
  }
};
</script>
