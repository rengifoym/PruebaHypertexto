import { createRouter, createWebHistory } from 'vue-router';
import Estudiantes from '../components/Estudiantes.vue';
import Cursos from '../components/Cursos.vue';
import Materias from '../components/Materias.vue';
import Notas from '../components/Notas.vue';

const routes = [
  { path: '/estudiantes', component: Estudiantes },
  { path: '/cursos', component: Cursos },
  { path: '/materias', component: Materias },
  { path: '/notas', component: Notas },
  { path: '/', redirect: '/estudiantes' }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;