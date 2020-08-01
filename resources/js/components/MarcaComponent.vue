<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">            
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item active">Marcas</li>
        </ol>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Marcas
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
                        <b-col md="4" class="my-1">
                            <b-form-group label-cols-sm="6" label="Registros por página: " class="mb-0">
                                <b-form-select v-model="perPage" :options="pageOptions"></b-form-select>
                            </b-form-group>
                        </b-col>
                        <b-col offset-md="4" md="4" class="my-1">
                            <b-form-group label-cols-sm="3" label="Buscar: " class="mb-0">
                                <b-input-group>
                                    <b-form-input v-model="filter" placeholder="Escriba el texto a buscar..."></b-form-input>
                                    <!--<b-input-group-append>
                                        <b-button :disabled="!filter" @click="filter = ''">Clear</b-button>
                                    </b-input-group-append>-->
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
                        :items="marcas"    
                        :current-page="currentPage"
                        :per-page="perPage"
                        :filter="filter"
                        :sort-by.sync="sortBy"
                        :sort-desc.sync="sortDesc"
                        :sort-direction="sortDirection"
                        @filtered="onFiltered"               
                    >
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
                    <form role="form" @submit.prevent="guardarMarca">
                        <div class="modal-body">                        
                            <div class="form-group">
                                <label for="nombre_marca">Nombre</label>
                                <input type="text"  v-model="marca.nombre" class="form-control" id="nombre_marca" placeholder="Nombre de marca"/>                                
                                <span v-if="errors.nombre" class="error">{{ errors.nombre[0] }}</span>
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea v-model="marca.descripcion" rows="2" id="descripcion" class="form-control"></textarea>                                                                
                                <span v-if="errors.descripcion" class="error">{{ errors.descripcion[0] }}</span>
                            </div> 
                            <div class="form-group">
                                <label for="acceso">Acceso</label>
                                <select v-model="marca.acceso" class="form-control" id="acceso">
                                    <option value="privado" selected>Privado</option>
                                    <option value="publico">Público</option>
                                </select>
                            </div>                                                  
                        </div>
                        <div class="modal-footer">
                            <b-button variant="secondary" @click="cerrarModalNuevoEditar">Cancelar</b-button>
                            <b-button type="submit" v-if="tipoAccion == 1" variant="success">Registrar</b-button>
                            <b-button type="submit" v-else variant="success">Actualizar</b-button>
                        </div>
                    </form>
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
                            <h5><b>{{ marca.nombre }}</b></h5>
    					</div>
    					<div class="modal-footer">
                            <b-button variant="secondary" @click="cerrarModalEliminar">Cancelar</b-button>    						    						
                            <b-button v-if="tipoAccionEliminar == 1" variant="success" @click="activarMarca">Aceptar</b-button>
                            <b-button v-else variant="success" @click="desactivarMarca">Aceptar</b-button>
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
                marca : {
                    id : 0,
                    nombre : '',
                    descripcion : '',
                    acceso: 'privado',                    
                },
                columnas: [                    
                    { key: 'opciones', label: 'Opciones', class: 'text-center' },
                    { key : 'nombre', label : 'Nombre', sortable: true },
                    { key : 'descripcion', label : 'Descripción' },
                    { key : 'acceso', label : 'Acceso', class: 'text-center' },
                    { key : 'condicion', label : 'Condición', class: 'text-center' },                    
                ], 
                marcas : [],                
                tieneArticulos : false,
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
        methods : {            
            listarMarca() {
                let me = this;

                axios.get(`${this.ruta}/marca`)
                .then(function (response) {                                    
                    me.marcas = response.data;
                    me.totalRows = me.marcas.length;
                })
                .catch(function (error) {                    
                    console.log(error);
                });
            },
            abrirModalNuevoEditar(accion, data=[]) {                
                this.modalNuevoEditar = true;

                switch(accion){
                    case 'registrar':
                    {                        
                        this.tituloModalNuevoEditar = 'Registrar Marca';
                        this.marca.nombre = '';
                        this.marca.descripcion = '';
                        this.marca.acceso = 'privado';
                        this.tipoAccion = 1; //registrar
                        break;
                    }
                    case 'actualizar':
                    {                        
                        this.tituloModalNuevoEditar = 'Actualizar Marca';
                        this.marca.id = data.id;
                        this.marca.nombre = data.nombre;
                        this.marca.descripcion = data.descripcion;
                        this.marca.acceso = data.acceso;                        
                        this.tipoAccion = 2;
                        break;
                    }
                }
            },
            guardarMarca() {
                if (this.tipoAccion == 1) {                    
                    this.registrarMarca()
                }
                else {
                    this.actualizarMarca()
                }
            },
            registrarMarca() {
                let me = this;
                this.errors = [];
                
                axios.post(`${this.ruta}/marca/registrar`, {
                    'nombre': this.marca.nombre,
                    'descripcion': this.marca.descripcion,
                    'acceso': this.marca.acceso
                }).then(function (response) {
                    me.cerrarModalNuevoEditar();                    
                    me.listarMarca();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'La marca fue agregada satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                                        
                }).catch(function (error) {
                    console.log(error);
                    if (error.response.status==422) {
                        me.errors = error.response.data.errors;
                    }
                    else {
                        me.cerrarModalNuevoEditar();                    
                        me.errorMsg = true;
                        me.txtErrorMsg = 'Error al agregar la marca.';
                        me.dismissCountDown = me.dismissSecs;
                    }                    
                });
                
            },
            actualizarMarca(){
                let me = this;
                this.errors = [];
                
                axios.put(`${this.ruta}/marca/actualizar/${this.marca.id}`, {                    
                    'nombre': this.marca.nombre,
                    'descripcion': this.marca.descripcion,
                    'acceso': this.marca.acceso
                }).then(function (response) {
                    me.cerrarModalNuevoEditar();                    
                    me.listarMarca();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'La marca fue actualizada satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                }).catch(function (error) {
                    console.log(error);
                    if (error.response.status==422) {
                        me.errors = error.response.data.errors;
                    }
                    else {
                        me.cerrarModalNuevoEditar();                    
                        me.errorMsg = true;
                        me.txtErrorMsg = 'Error al actualizar la marca.';
                        me.dismissCountDown = me.dismissSecs;
                    }                    
                });                
            },
            cerrarModalNuevoEditar(){
                this.modalNuevoEditar = false;
                this.marca.nombre = '';
                this.marca.descripcion = '';  
                this.marca.acceso = 'privado';                   
                this.errorMsg = false;
                this.successMsg = false;
                this.txtErrorMsg = '';
                this.txtSuccessMsg = '';   
                this.errors = [];                          
            },            
            abrirModalEliminar(accion, data=[]) {
                this.modalEliminar = true;   
                this.marca.id = data.id;
                this.marca.nombre = data.nombre;
                
                switch(accion){
                    case 'activar':
                    {                        
                        this.tituloModalEliminar = 'Activar Marca';
                        this.mensajeEliminar = '¿Desea activar esta marca?';                                                                        
                        this.tipoAccionEliminar = 1;                        
                        break;
                    }
                    case 'desactivar':
                    {                        
                        this.tituloModalEliminar = 'Desactivar Marca';
                        this.mensajeEliminar = '¿Desea desactivar esta marca?';
                        this.tipoAccionEliminar = 2;                               
                        break;
                    }
                }               
            },
            activarMarca() {
                let me = this;                

                axios.put(`${this.ruta}/marca/activar/${this.marca.id}`)
                .then(function (response) {
                    me.cerrarModalEliminar();                    
                    me.listarMarca();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'La marca fue activada satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                }).catch(function (error) {
                    //console.log(error);
                    me.cerrarModalEliminar();         
                    me.errorMsg = true;
                    me.txtErrorMsg = 'Error al activar la marca.';
                    me.dismissCountDown = me.dismissSecs;                    
                });
            },            
            desactivarMarca() {
                let me = this;                

                axios.put(`${this.ruta}/marca/desactivar/${this.marca.id}`)
                .then(function (response) {
                    me.cerrarModalEliminar();                    
                    me.listarMarca();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'La marca fue desactivada satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                }).catch(function (error) {
                    //console.log(error);
                    me.cerrarModalEliminar();         
                    me.errorMsg = true;
                    me.txtErrorMsg = 'Error al desactivar la marca.';
                    me.dismissCountDown = me.dismissSecs;                                        
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
                window.open(this.ruta + '/marca/listarPdf','_blank');
            },
            onFiltered(filteredItems) {
                // Trigger pagination to update the number of buttons/pages due to filtering
                this.totalRows = filteredItems.length;
                this.currentPage = 1;
            },
            countDownChanged(dismissCountDown) {
                this.dismissCountDown = dismissCountDown;
            },            
        },
        mounted() {
            this.listarMarca();            
        }
    }
</script>
<style scoped>
    /* Modal styles */
    .modal .modal-dialog {
        max-width: 400px;
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
    .size_img {
        width: 150px;
    }
    
</style>