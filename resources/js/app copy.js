import { createApp } from 'vue';
import App from './views/components/primary/App';
import Login from './views/components/primary/Login';
import Register from './views/components/primary/Register';
import Home from './views/components/usercomponents/Home';
import Settings from './views/components/usercomponents/Settings';
import RiskCalculator from './views/components/usercomponents/RiskCalculator';
import NewTrades from './views/components/usercomponents/NewTrades';
import TradeHistory from './views/components/usercomponents/TradeHistory';
import LivePortfolio from './views/components/usercomponents/LivePortfolio';
import {createRouter, createWebHistory} from 'vue-router';
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap"
import Datepicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faUserSecret } from '@fortawesome/free-solid-svg-icons';

const routes = [
    {
      path: '/',
      name: 'Login',
      component: Login,
      meta: { requiresVisitor: true },
      alias: '/login'
    },
    {
      path: '/register',
      name: 'Register',
      component: Register,
      meta: { requiresVisitor: true }
    },
    {
      path: '/home',
      name: 'Home',
      component: Home,  
      meta: { requiresAuth: true, isAdmin: true, isUser: true}
    },
    {
      path: '/settings',
      name: 'Settings',
      component: Settings,  
      meta: { requiresAuth: true, isAdmin: true, isUser: true}
    },
    {
      path: '/riskcalculator',
      name: 'RiskCalculator',
      component: RiskCalculator,  
      meta: { requiresAuth: true, isAdmin: true, isUser: true}
    },
    {
      path: '/newtrades',
      name: 'NewTrades',
      component: NewTrades,  
      meta: { requiresAuth: true, isAdmin: true, isUser: true}
    },
    {
      path: '/tradehistory',
      name: 'TradeHistory',
      component: TradeHistory,  
      meta: { requiresAuth: true, isAdmin: true, isUser: true}
    },
    {
      path: '/liveportfolio',
      name: 'LivePortfolio',
      component: LivePortfolio,  
      meta: { requiresAuth: true, isAdmin: true, isUser: true}
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
      // this route requires auth, check if logged in
      // if not, redirect to login page.
      console.log(isLoggedIn());
      if (isLoggedIn() === '\'false\'') {
        next({
          name: 'Login',
        })
      } 
      else {
        var role = getRole();
        if(to.matched.some(record => record.meta.isAdmin)){
          if(role === '\'Admin\''){
            routeToAuthenticatedLink("'Admin'", role, next);
          }
          else if(to.matched.some(record => record.meta.isUser) && role === '\'User\''){
            routeToAuthenticatedLink("'User'", role, next);
          }
        }
        else if(to.matched.some(record => record.meta.isUser)){
          if(role === '\'User\''){
            routeToAuthenticatedLink("'User'", role, next);
          }
        }
        else{
          next()
        }
      }
    }
    else if (to.matched.some(record => record.meta.requiresVisitor)) {
        // this route requires auth, check if logged in
        // if logged in redirect to dashboard
        if (isLoggedIn() === '\'true\'' && getRole() === '\'User\'') {
          next({
            name: 'Home',
          })
        }
        else if (isLoggedIn() === '\'true\'' && getRole() === '\'Admin\'') {
          next({
            name: 'Home',
          })
        }
        else {
          next()
        }
    }
    else {
      next() // make sure to always call next()!
    }
  })

  function isLoggedIn(){
    if (sessionStorage.getItem("isLoggedIn") === null) {
      return  '\'false\'';
    }
    return sessionStorage.getItem('isLoggedIn');
  }

  function getRole(){
    return sessionStorage.getItem('role');
  }

  function routeToAuthenticatedLink(permittedRole, role, next){
    if(role === permittedRole){
      next()
    }
    else if(role === '\'Admin\''){
      next({
        name: 'Login',
      })
    }
    else if(role === '\'User\''){
      next({
        name: 'Login',
      })
    }
  }

export default router;

library.add(faUserSecret);

const app = createApp(App).use(router);

app.component('font-awesome-icon', FontAwesomeIcon);

app.component('Datepicker', Datepicker);

app.mount('#app');
