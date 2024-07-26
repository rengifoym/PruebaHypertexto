<template>
  <div class="container mt-4">
    <h1 class="mb-4">Gestión de Notas</h1>

    <!-- Navegación o pestañas -->
    <ul class="nav nav-tabs mb-4">
      <li class="nav-item">
        <a class="nav-link" href="#" @click.prevent="showTab('estudiantes')">Estudiantes</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" @click.prevent="showTab('cursos')">Cursos</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" @click.prevent="showTab('materias')">Materias</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" @click.prevent="showTab('notas')">Notas</a>
      </li>
    </ul>

    <!-- Contenido de las pestañas -->
    <div v-if="activeTab === 'estudiantes'">
      <estudiantes></estudiantes>
    </div>
    <div v-if="activeTab === 'cursos'">
      <cursos></cursos>
    </div>
    <div v-if="activeTab === 'materias'">
      <materias></materias>
    </div>
    <div v-if="activeTab === 'notas'">
      <notas></notas>
    </div>
  </div>
</template>

<script>
import Estudiantes from './Estudiantes.vue';
import Cursos from './Cursos.vue';
import Materias from './Materias.vue';
import Notas from './Notas.vue';

export default {
  components: { Estudiantes, Cursos, Materias, Notas},
  data() {
    return {
      activeTab: 'estudiantes' // Tab inicial activa
    };
  },
  methods: {
    showTab(tab) {
      this.activeTab = tab;
    }
  }
};
</script>