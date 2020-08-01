/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');



window.Vue = require('vue');

import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue)

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

import vSelect from 'vue-select'
Vue.component('v-select', vSelect)

import 'vue-select/dist/vue-select.css'
import Axios from 'axios';
import Echo from 'laravel-echo';
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('categoria-component', require('./components/CategoriaComponent.vue').default);
Vue.component('marca-component', require('./components/MarcaComponent.vue').default);
Vue.component('articulo-component', require('./components/ArticuloComponent.vue').default);
Vue.component('tipo-gasto-component', require('./components/TipoGastoComponent.vue').default);
Vue.component('cliente-component', require('./components/ClienteComponent.vue').default);
Vue.component('proveedor-component', require('./components/ProveedorComponent.vue').default);
Vue.component('rol-component', require('./components/RolComponent.vue').default);
Vue.component('usuario-component', require('./components/UsuarioComponent.vue').default);
Vue.component('ingreso-component', require('./components/IngresoComponent.vue').default);
Vue.component('venta-component', require('./components/VentaComponent.vue').default);
Vue.component('venta-caja-component', require('./components/VentaCajaComponent.vue').default);
Vue.component('servicio-caja-component', require('./components/ServicioCajaComponent.vue').default);
Vue.component('servicio-component', require('./components/ServicioComponent.vue').default);
Vue.component('caja-component', require('./components/CajaComponent.vue').default);
Vue.component('movimiento-component', require('./components/MovimientoComponent.vue').default);
Vue.component('gasto-component', require('./components/GastoComponent.vue').default);
Vue.component('cierre-caja-component', require('./components/CierreCajaComponent.vue').default);
Vue.component('consulta-ingreso-component', require('./components/ConsultaIngresoComponent.vue').default);
Vue.component('consulta-venta-component', require('./components/ConsultaVentaComponent.vue').default);
Vue.component('dashboard-component', require('./components/DashboardComponent.vue').default);
Vue.component('notification-component', require('./components/Notification.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    data: {
        menu: 'inicio',
        //ruta: '//127.0.0.1:8000',
        ruta: '//localhost:3000',
        //ruta: '//rs7.app/sisventas',
        //ruta: 'http://www.grupocarden.com/sisventas/public',
        //ruta: '//10.20.10.4:8001',
        renderKey: 1,
        notifications : []
    },
    created() {
        let me = this;

        axios.post('notification/get').then(function(response){
          me.notifications = response.data;
        }).catch(function(error) {
          console.log(error);
        });

        var userId = $('meta[name="userId"]').attr('content');

        window.Echo.private('App.User.' + userId).notification((notification) => {
          me.notifications.unshift(notification);          
        });
    },
    methods: {
        cambiarMenu(nuevo_menu) {                          
          this.menu = nuevo_menu;
          this.actualizarPagina();           
        },
        actualizarPagina() {
          this.renderKey++;
        }
    }
});
