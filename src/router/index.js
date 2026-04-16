import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/login.vue'
import Dashboard from '../views/main.vue'
import AdminPage from '../views/admin.vue'

const routes = [
  { path: '/', name: 'Login', component: Login },
  { path: '/main', name: 'main', component: Dashboard },
  {
    path: '/admin',
    name: 'Admin',
    component: AdminPage
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Protection des routes (version simplifiée)
router.beforeEach((to, from, next) => {
  console.log('Navigation vers:', to.path)
  
  try {
    const userStr = localStorage.getItem('user')
    let user = null
    
    // Vérifier que userStr n'est pas null/undefined avant de parser
    if (userStr && userStr !== 'undefined' && userStr !== 'null') {
      try {
        user = JSON.parse(userStr)
      } catch (parseError) {
        console.error('Erreur parsing user:', parseError)
        // Nettoyer le localStorage si les données sont corrompues
        localStorage.removeItem('user')
        user = null
      }
    }
    
    console.log('Utilisateur:', user)
    
    // Page de login - toujours accessible
    if (to.path === '/') {
      next()
      return
    }
    
    // Pages protégées
    if (to.path === '/main' || to.path === '/admin') {
      if (!user || !user.id) {
        console.log('Redirection vers login - pas connecté')
        next('/')
        return
      }
      
      // Vérifier les permissions pour la page admin
      if (to.path === '/admin' && parseInt(user.role) !== 1) {
        console.log('Redirection vers main - pas admin')
        next('/main')
        return
      }
      
      console.log('Accès autorisé')
      next()
      return
    }
    
    // Route inconnue
    next('/')
    
  } catch (error) {
    console.error('Erreur dans le router:', error)
    // En cas d'erreur, nettoyer le localStorage et rediriger vers login
    localStorage.removeItem('user')
    if (to.path !== '/') {
      next('/')
    } else {
      next()
    }
  }
})

export default router
