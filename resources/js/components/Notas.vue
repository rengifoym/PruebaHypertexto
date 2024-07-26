<template>
  <div class="container mt-4">
    <h1 class="mb-4">Gestión de Notas</h1>

    <!-- Formulario para agregar/editar nota -->
    <div class="card mb-4">
      <div class="card-header">
        {{ isEditing ? 'Editar Nota' : 'Agregar Nota' }}
      </div>
      <div class="card-body">
        <form @submit.prevent="submitForm">
          <div class="mb-3">
            <label for="estudiante" class="form-label">Estudiante</label>
            <select id="estudiante" v-model="form.estudiante_id" class="form-select" required>
              <option value="" disabled>Seleccionar estudiante</option>
              <option v-for="estudiante in estudiantes" :key="estudiante.id" :value="estudiante.id">
                {{ estudiante.nombre }} {{ estudiante.apellido }}
              </option>
            </select>
            <div v-if="errors.estudiante_id" class="text-danger">{{ errors.estudiante_id }}</div>
          </div>
          <div class="mb-3">
            <label for="curso" class="form-label">Curso</label>
            <select id="curso" v-model="form.curso_id" class="form-select" required>
              <option value="" disabled>Seleccionar curso</option>
              <option v-for="curso in cursos" :key="curso.id" :value="curso.id">
                {{ curso.nombre }}
              </option>
            </select>
            <div v-if="errors.curso_id" class="text-danger">{{ errors.curso_id }}</div>
          </div>
          <div class="mb-3">
            <label for="materia" class="form-label">Materia</label>
            <select id="materia" v-model="form.materia_id" class="form-select" required>
              <option value="" disabled>Seleccionar materia</option>
              <option v-for="materia in materias" :key="materia.id" :value="materia.id">
                {{ materia.nombre }}
              </option>
            </select>
            <div v-if="errors.materia_id" class="text-danger">{{ errors.materia_id }}</div>
          </div>
          <div class="mb-3">
            <label for="nota" class="form-label">Nota</label>
            <input type="number" id="nota" v-model="form.nota" class="form-control" placeholder="Nota" step="0.01" min="0" max="10" required>
            <div v-if="errors.nota" class="text-danger">{{ errors.nota }}</div>
          </div>
          <button type="submit" class="btn btn-primary">{{ isEditing ? 'Actualizar' : 'Guardar' }}</button>
        </form>
      </div>
    </div>

    <!-- Lista de notas -->
    <div class="card">
      <div class="card-header">
        Lista de Notas
      </div>
      <ul class="list-group list-group-flush">
        <li v-for="nota in notas" :key="nota.id" class="list-group-item d-flex justify-content-between align-items-center">
          {{ nota.estudiante.nombre }} {{ nota.estudiante.apellido }} - {{ nota.curso.nombre }} - {{ nota.materia.nombre }}: {{ nota.nota }}
          <div>
            <button class="btn btn-warning btn-sm me-2" @click="editNota(nota)">Editar</button>
            <button class="btn btn-danger btn-sm" @click="deleteNota(nota.id)">Eliminar</button>
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
    const notas = ref([]);
    const estudiantes = ref([]);
    const cursos = ref([]);
    const materias = ref([]);
    
    const form = reactive({
      id: null,
      estudiante_id: '',
      curso_id: '',
      materia_id: '',
      nota: ''
    });

    const isEditing = ref(false);
    const errors = reactive({});

    function updateNota(nota) {
      const index = notas.value.findIndex(n => n.id === nota.id);
      if (index !== -1) {
        notas.value[index] = nota;
      }
    }

    async function getNotas() {
      try {
        const response = await fetch('/api/notas');
        if (response.ok) {
          notas.value = await response.json();
        } else {
          console.error('Error fetching notas:', response.statusText);
        }
      } catch (error) {
        console.error('Error fetching notas:', error);
      }
    }

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

      errors.estudiante_id = form.estudiante_id ? '' : 'El estudiante es requerido.';
      errors.curso_id = form.curso_id ? '' : 'El curso es requerido.';
      errors.materia_id = form.materia_id ? '' : 'La materia es requerida.';
      errors.nota = form.nota >= 0 && form.nota <= 10 ? '' : 'La nota debe estar entre 0 y 10.';

      return valid && !errors.estudiante_id && !errors.curso_id && !errors.materia_id && !errors.nota;
    }

    async function submitForm() {
      if (!validateForm()) return;

      try {
        const method = isEditing.value ? 'PUT' : 'POST';
        const url = isEditing.value ? `/api/notas/${form.id}` : '/api/notas';
        
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
            updateNota(data); // Actualiza la nota en el estado local
            isEditing.value = false;
          } else {
            notas.value.push(data); // Añade la nueva nota al estado local
          }
          
          resetForm();
        } else {
          const errorText = await response.text(); // Obtener texto del error
          console.error('Error saving nota:', response.statusText, errorText);
        }
      } catch (error) {
        console.error('Error submitting form:', error);
      }
    }

    function editNota(nota) {
      form.id = nota.id;
      form.estudiante_id = nota.estudiante_id;
      form.curso_id = nota.curso_id;
      form.materia_id = nota.materia_id;
      form.nota = nota.nota;
      isEditing.value = true;
    }

    async function deleteNota(id) {
      try {
        await fetch(`/api/notas/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        notas.value = notas.value.filter(nota => nota.id !== id);
      } catch (error) {
        console.error('Error deleting nota:', error);
      }
    }

    function resetForm() {
      form.id = null;
      form.estudiante_id = '';
      form.curso_id = '';
      form.materia_id = '';
      form.nota = '';
      errors.estudiante_id = '';
      errors.curso_id = '';
      errors.materia_id = '';
      errors.nota = '';
    }

    // Cargar los datos al montar el componente
    onMounted(() => {
      getNotas();
      getEstudiantes();
      getCursos();
      getMaterias();
    });

    return { notas, estudiantes, cursos, materias, form, isEditing, errors, submitForm, editNota, deleteNota };
  }
};
</script>
