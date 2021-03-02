<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">            
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item active">Usuarios</li>
        </ol>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Usuarios
                    <b-button variant="outline-success" @click="abrirModalNuevoEditar('registrar')">                    
                        <i class="fa fa-plus"></i> Nuevo               
                    </b-button>
                    <button type="button" @click="cargarPdf()" class="btn btn-info">
                        <i class="icon-doc"></i>&nbsp;Reporte
                    </button>
                </div>
                <div class="card-body"> 
                    <b-alert
                        :show="dismissCountDown"
                        dismissible
                        variant="danger"
                        @dismissed="dismissCountDown=0"
                        @dismiss-count-down="countDownChanged"
                        v-if="errorMsg"
                    >
                        {{ txtErrorMsg }}
                    </b-alert>
                    <b-alert
                        :show="dismissCountDown"
                        dismissible
                        variant="success"
                        @dismissed="dismissCountDown=0"
                        @dismiss-count-down="countDownChanged"
                        v-if="successMsg"
                    >
                        {{ txtSuccessMsg }}
                    </b-alert>

                    <b-row>
                        <b-col sm="12" md="4" lg="4" class="my-1">
                            <b-form-group label-cols-sm="6" label="Registros por página: " class="mb-0">
                                <b-form-select v-model="perPage" :options="pageOptions"></b-form-select>
                            </b-form-group>
                        </b-col>
                        <b-col sm="12" offset-md="3" md="5" lg="5" class="my-1">
                            <b-form-group label-cols-sm="3" label="Buscar: " class="mb-0">
                                <b-input-group>
                                    <b-form-input v-model="filter" placeholder="Escriba el texto a buscar..."></b-form-input>
                                    <b-input-group-append>
                                        <b-button :disabled="!filter" @click="filter = ''">Limpiar</b-button>
                                    </b-input-group-append>
                                </b-input-group>
                            </b-form-group>
                        </b-col>                        
                    </b-row>
                    <b-table 
                        striped 
                        hover 
                        bordered 
                        small
                        responsive
                        :fields="columnas" 
                        :items="users"    
                        :current-page="currentPage"
                        :per-page="perPage"
                        :filter="filter"
                        :sort-by.sync="sortBy"
                        :sort-desc.sync="sortDesc"
                        :sort-direction="sortDirection"
                        @filtered="onFiltered"               
                    >
                        <template slot="documento" slot-scope="row">
                            {{row.item.tipo_documento }} {{ row.item.num_documento }}                  
                        </template>
                        <template slot="condicion" slot-scope="row">
                            <span v-if="row.item.condicion == 1" class="badge badge-success">Activo</span>
                            <span v-else class="badge badge-secondary">Inactivo</span>                            
                        </template>
                        <template slot="opciones" slot-scope="row">
                            <b-button variant="warning" size="sm" @click="abrirModalNuevoEditar('actualizar', row.item)">
                                <i class="icon-pencil"></i>
                            </b-button>
                            <b-button variant="danger" size="sm" v-if="row.item.condicion==1" @click="abrirModalEliminar('desactivar', row.item)">
                                <i class="icon-trash"></i>
                            </b-button>
                            <b-button variant="success" size="sm" v-else @click="abrirModalEliminar('activar', row.item)">
                                <i class="icon-check"></i>
                            </b-button>                                
                        </template>
                    </b-table>
                    <b-row>
                        <b-col offset-md="6" md="6" class="my-1">
                            <b-pagination
                            v-model="currentPage"
                            :total-rows="totalRows"
                            :per-page="perPage"
                            class="my-0"
                            align="right"
                            ></b-pagination>
                        </b-col>
                    </b-row>                   
                </div>
            </div><!-- Fin ejemplo de tabla Listado -->
        </div>       

        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade centered-modal" tabindex="-1" :class="{'mostrar' : modalNuevoEditar}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ tituloModalNuevoEditar }}</h5>
                        <button type="button" class="close" @click="cerrarModalNuevoEditar" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <div class="form-row">                                
                                <div class="form-group col-md-12">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" v-model="user.nombre" class="form-control" id="nombre" placeholder="Nombres y Apellidos">
                                    <span v-if="errors.nombre" class="error">{{ errors.nombre[0] }}</span>
                                </div>
                            </div>  
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label for="tipo_documento">Tipo Documento</label>                                    
                                    <select v-model="user.tipo_documento" class="form-control" id="tipo_documento">                                        
                                        <option value="" disabled>Seleccione...</option>
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>                                        
                                        <option value="PASAPORTE">PASAPORTE</option>
                                    </select>    
                                    <span v-if="errors.tipo_documento" class="error">{{ errors.tipo_documento[0] }}</span>
                                </div>
                                <div class="form-group col-md-7">
                                    <label for="num_documento">Número Documento</label>                                    
                                    <input type="text" v-model="user.num_documento" class="form-control text-center" id="num_documento" placeholder="Nro. Documento">
                                    <span v-if="errors.num_documento" class="error">{{ errors.num_documento[0] }}</span>                                    
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" v-model="user.direccion" class="form-control" id="direccion" placeholder="Dirección de usuario">            
                                    <span v-if="errors.direccion" class="error">{{ errors.direccion[0] }}</span>
                                </div>                                            
                                <div class="form-group col-md-3">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" v-model="user.telefono" class="form-control text-center" id="telefono">
                                    <span v-if="errors.telefono" class="error">{{ errors.telefono[0] }}</span>
                                </div>                                
                                <div class="form-group col-md-4">
                                    <label for="email">E-mail</label>
                                    <input type="text" v-model="user.email" class="form-control text-center" id="email">
                                    <span v-if="errors.email" class="error">{{ errors.email[0] }}</span>
                                </div>
                            </div>                                                          
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label for="rol">Rol</label>                                    
                                    <v-select   v-model="user.idrol"                                                
                                                v-if="modalNuevoEditar"
                                                class="style-chooser"
                                                :options="roles"
                                                :reduce="rol => rol.id" 
                                                label="nombre"           
                                                placeholder="Seleccione..."                                                                                                                                                                                                                                      
                                    ></v-select>
                                    <span v-if="errors.idrol" class="error">{{ errors.idrol[0] }}</span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="usuario">Usuario</label>
                                    <input type="text" v-model="user.usuario" class="form-control text-center" id="usuario" placeholder="Nombre de usuario">
                                    <span v-if="errors.usuario" class="error">{{ errors.usuario[0] }}</span>
                                </div>                                
                                <div class="form-group col-md-3">
                                    <label for="password">Contraseña</label>
                                    <input type="password" v-model="user.password" class="form-control text-center" id="password" placeholder="Contraseña">
                                    <span v-if="errors.password" class="error">{{ errors.password[0] }}</span>
                                </div>
                            </div>                                                          
                        </form>
                    </div>
                    <div class="modal-footer">
                        <b-button variant="secondary" @click="cerrarModalNuevoEditar">Cancelar</b-button>
                        <b-button v-if="tipoAccion == 1" variant="success" @click="registrarUsuario">Guardar</b-button>
                        <b-button v-else variant="success" @click="actualizarUsuario">Actualizar</b-button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!--Fin del modal-->          

        <!-- Delete Modal HTML -->
        <div :class="{ 'mostrar' : modalEliminar }" class="modal fade">
    		<div class="modal-dialog modal-danger">
    			<div class="modal-content">
    				<form>
    					<div class="modal-header">
    						<h5 class="modal-title">{{ tituloModalEliminar }}</h5>
    						<button type="button" class="close" @click="cerrarModalEliminar" aria-hidden="true">&times;</button>
    					</div>
    					<div class="modal-body text-center">
    						<p>{{ mensajeEliminar }}</p>
                            <h5><b>{{ user.nombre }}</b></h5>
    					</div>
    					<div class="modal-footer">
                            <b-button variant="secondary" @click="cerrarModalEliminar">Cancelar</b-button>    						    						
                            <b-button v-if="tipoAccionEliminar == 1" variant="success" @click="activarUsuario">Aceptar</b-button>
                            <b-button v-else variant="success" @click="desactivarUsuario">Aceptar</b-button>
    					</div>
    				</form>
    			</div>
    		</div>
    	</div><!--Fin de modal eliminar-->
    </main>
</template>
<script>
    export default {
        props : ['ruta'],
        data() {
            return {
                user : {
                    id : 0,
                    nombre : '',                    
                    tipo_documento : '',
                    num_documento : '',                    
                    direccion : '',
                    telefono : '',
                    email : '',
                    usuario : '',
                    password : '',
                    idrol : ''
                },
                columnas: [                                        
                    { key : 'nombre', label : 'Nombre', sortable: true },                    
                    { key : 'documento', label : 'Documento' },                    
                    { key : 'telefono', label : 'Teléfono', class: 'text-center' },         
                    { key : 'email', label : 'E-mail', class: 'text-center' },  
                    { key : 'usuario', label : 'Usuario', class: 'text-center' },             
                    { key : 'rol', label : 'Rol', class: 'text-center' },  
                    { key : 'condicion', label : 'Condición', class: 'text-center' },                                        
                    { key: 'opciones', label: 'Opciones', class: 'text-center' },
                ], 
                users : [],
                roles : [],
                tituloModalNuevoEditar : '',   
                tituloModalEliminar : '',             
                modalNuevoEditar : false,  
                modalEliminar : false,              
                tipoAccion : 0,     
                tipoAccionEliminar : 0, 
                mensajeEliminar : '',                              
                errorMsg : false,
                successMsg : false,   
                txtErrorMsg : '',
                txtSuccessMsg : '',
                totalRows: 1,
                currentPage: 1,
                perPage: 5,
                pageOptions: [5, 10, 15],
                sortBy: null,
                sortDesc: false,
                sortDirection: 'asc',
                filter: null,      
                dismissSecs: 3,
                dismissCountDown: 0,
                errors : []                             
            }
        },     
        created() {
            this.listarUser();  
            this.getRolesActivos();          
        },   
        methods : {
            listarUser() {               
                axios.get(`${this.ruta}/user`)
                    .then(response => {                                    
                        this.users = response.data;
                        this.totalRows = this.users.length;
                    })
                    .catch(error => {                    
                        console.log(error);
                    });
            },
            getRolesActivos() {                
                axios.get(`${this.ruta}/rol/getRolesActivos`)
                    .then(response => {                                    
                        this.roles = response.data;                    
                    })
                    .catch(error => {                    
                        console.log(error);
                    });
            },
            abrirModalNuevoEditar(accion, data=[]) {                
                this.modalNuevoEditar = true;

                switch(accion){
                    case 'registrar':
                    {                        
                        this.tituloModalNuevoEditar = 'Registrar Usuario';
                        this.user.nombre = '';     
                        this.user.num_documento = '';     
                        this.user.tipo_documento = '';     
                        this.user.direccion = '';     
                        this.user.telefono = '';     
                        this.user.email = '';      
                        this.user.usuario = '';      
                        this.user.password = '';    
                        this.user.idrol = '';                        
                        this.tipoAccion = 1; //registrar
                        break;
                    }
                    case 'actualizar':
                    {                        
                        this.tituloModalNuevoEditar = 'Actualizar Usuario';
                        this.user.id = data.id;
                        this.user.nombre = data.nombre;                        
                        this.user.num_documento = data.num_documento;                        
                        this.user.tipo_documento = data.tipo_documento;                        
                        this.user.direccion = data.direccion;                        
                        this.user.telefono = data.telefono;                        
                        this.user.email = data.email;      
                        this.user.usuario = data.usuario;      
                        this.user.password = ''; 
                        this.user.idrol = data.idrol;                              
                        this.tipoAccion = 2;
                        break;
                    }
                }
            },
            registrarUsuario() {                
                this.errors = [];

                axios.post(`${this.ruta}/user/registrar`, {
                    'nombre': this.user.nombre,          
                    'tipo_documento': this.user.tipo_documento,          
                    'num_documento': this.user.num_documento,                              
                    'direccion': this.user.direccion,          
                    'telefono': this.user.telefono,          
                    'email': this.user.email, 
                    'usuario': this.user.usuario, 
                    'password': this.user.password,                    
                    'idrol': this.user.idrol,                    
                }).then(response => {
                    this.cerrarModalNuevoEditar();                    
                    this.listarUser();
                    this.successMsg = true;
                    this.txtSuccessMsg = 'El usuario fue agregado satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                                        
                }).catch(error => {                    
                    if (error.response.status==422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
                        this.cerrarModalNuevoEditar();                    
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al agregar el usuario.';
                        this.dismissCountDown = this.dismissSecs;
                    }                    
                });
            },
            actualizarUsuario() {                
                this.errors = [];

                axios.put(`${this.ruta}/user/actualizar/${this.user.id}`, {                    
                    'nombre': this.user.nombre,          
                    'tipo_documento': this.user.tipo_documento,          
                    'num_documento': this.user.num_documento,                              
                    'direccion': this.user.direccion,          
                    'telefono': this.user.telefono,          
                    'email': this.user.email, 
                    'usuario': this.user.usuario, 
                    'password': this.user.password,                    
                    'idrol': this.user.idrol,                    
                }).then(response => {
                    this.cerrarModalNuevoEditar();                    
                    this.listarUser();
                    this.successMsg = true;
                    this.txtSuccessMsg = 'El usuario fue actualizado satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                }).catch(error => {                    
                    if (error.response.status==422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
                        this.cerrarModalNuevoEditar();                    
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al actualizar el usuario.';
                        this.dismissCountDown = this.dismissSecs;
                    }                    
                });
            },
            cerrarModalNuevoEditar(){
                this.modalNuevoEditar = false;
                this.user.nombre = '';
                this.user.tipo_documento = '';
                this.user.num_documento = '';
                this.user.direccion = '';
                this.user.telefono = '';                
                this.user.email = '';                
                this.user.usuario = '';                
                this.user.password = '';                
                this.user.idrol = '';
                this.errorMsg = false;
                this.successMsg = false;
                this.txtErrorMsg = '';
                this.txtSuccessMsg = '';   
                this.errors = [];                          
            },                       
            abrirModalEliminar(accion, data=[]) {
                this.modalEliminar = true;   
                this.user.id = data.id;
                this.user.nombre = data.nombre;
                
                switch(accion){
                    case 'activar':
                    {                        
                        this.tituloModalEliminar = 'Activar Usuario';
                        this.mensajeEliminar = '¿Desea activar este usuario?';                                                                        
                        this.tipoAccionEliminar = 1;                        
                        break;
                    }
                    case 'desactivar':
                    {                        
                        this.tituloModalEliminar = 'Desactivar Usuario';
                        this.mensajeEliminar = '¿Desea desactivar este usuario?';
                        this.tipoAccionEliminar = 2;                        
                        break;
                    }
                }               
            },
            activarUsuario() {                         
                axios.put(`${this.ruta}/user/activar/${this.user.id}`)
                    .then(response => {
                        this.cerrarModalEliminar();                    
                        this.listarUser();
                        this.successMsg = true;
                        this.txtSuccessMsg = 'El usuario fue activado satisfactoriamente.';
                        this.dismissCountDown = this.dismissSecs;
                    }).catch(error => {                        
                        this.cerrarModalEliminar();         
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al activar el usuario.';
                        this.dismissCountDown = this.dismissSecs;                    
                    });
            },
            desactivarUsuario() {                           
                axios.put(`${this.ruta}/user/desactivar/${this.user.id}`)
                    .then(response => {
                        this.cerrarModalEliminar();                    
                        this.listarUser();
                        this.successMsg = true;
                        this.txtSuccessMsg = 'El usuario fue desactivado satisfactoriamente.';
                        this.dismissCountDown = this.dismissSecs;
                    }).catch(error => {                    
                        this.cerrarModalEliminar();         
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al desactivar el usuario.';
                        this.dismissCountDown = this.dismissSecs;                                        
                    });
            },
            cerrarModalEliminar() {
                this.modalEliminar = false;
                this.errorMsg = false;
                this.successMsg = false;
                this.txtErrorMsg = '';
                this.txtSuccessMsg = '';   
            },  
            cargarPdf(){
                window.open(this.ruta + '/user/listarPdf','_blank');
            },                 
            onFiltered(filteredItems) {                
                this.totalRows = filteredItems.length;
                this.currentPage = 1;
            },
            countDownChanged(dismissCountDown) {
                this.dismissCountDown = dismissCountDown;
            },
        }        
    }
</script>
<style scoped>
    /* Modal styles */
    .modal .modal-dialog {
        max-width: 550px;
        margin: 3.75rem auto;
    }
    .modal .modal-header, .modal .modal-body, .modal .modal-footer {
        padding: 10px 20px;
        border-radius: 5px;
    }
    .modal .modal-content {
        border-radius: 5px;
    }
    .modal .modal-footer {
        background: #ecf0f1;
        border-radius: 5px;
    }
    .modal .modal-title {
        display: inline-block;
    }    
    .modal form label {
        font-weight: 600;
    }
    .modal-content {
        width: 100% !important;
        position: absolute !important;
        left: 0%;
        margin-top: 20px !important;
    }
    .mostrar {
        display: list-item !important;
        opacity: 1 !important;
        position: fixed !important;
        background: rgba(0, 0, 0, 0.6) !important;        
    }
    label {
        text-align: right;
        vertical-align: middle;
    }
    .error {
        color: red;
    }
    .overlay {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.6);
    }
    
</style>
<style non-scoped>
    .style-chooser .vs__search::placeholder,
    .style-chooser .vs__dropdown-toggle,
    .style-chooser .vs__dropdown-menu {    
        max-height: 150px;
    }
</style>