<template>
    <main class="main">        
        <ol class="breadcrumb">            
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item active">Clientes</li>
        </ol>
        <div class="container-fluid">            
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Clientes
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
                        :items="clientes"    
                        :current-page="currentPage"
                        :per-page="perPage"
                        :filter="filter"
                        :sort-by.sync="sortBy"
                        :sort-desc.sync="sortDesc"
                        :sort-direction="sortDirection"
                        @filtered="onFiltered"               
                    >
                        <template slot="documento" slot-scope="row">
                            {{ row.item.tipo_documento }} - {{ row.item.num_documento }}
                        </template>
                        <template slot="opciones" slot-scope="row">
                            <b-button variant="warning" size="sm" @click="abrirModalNuevoEditar('actualizar', row.item)">
                                <i class="icon-pencil"></i>
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
                                    <input type="text" v-model="cliente.nombre" class="form-control" id="nombre" placeholder="Nombre del cliente">
                                    <span v-if="errors.nombre" class="error">{{ errors.nombre[0] }}</span>
                                </div>
                            </div>  
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label for="tipo_documento">Tipo Documento</label>                                    
                                    <select v-model="cliente.tipo_documento" class="form-control" id="tipo_documento">
                                        <option value="" disabled>Seleccione...</option>
                                        <option value="DNI">DNI</option>
                                        <option value="RUC">RUC</option>                                        
                                        <option value="PASAPORTE">PASAPORTE</option>
                                    </select>    
                                    <span v-if="errors.tipo_documento" class="error">{{ errors.tipo_documento[0] }}</span>
                                </div>
                                <div class="form-group col-md-7">
                                    <label for="num_documento">Número Documento</label>                                    
                                    <input type="text" v-model="cliente.num_documento" class="form-control text-center" id="num_documento" placeholder="Nro. Documento">
                                    <span v-if="errors.num_documento" class="error">{{ errors.num_documento[0] }}</span>                                    
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" v-model="cliente.direccion" class="form-control" id="direccion" placeholder="Domicilio del cliente">            
                                    <span v-if="errors.direccion" class="error">{{ errors.direccion[0] }}</span>
                                </div>                
                            </div>  
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" v-model="cliente.telefono" class="form-control text-center" id="telefono">
                                    <span v-if="errors.telefono" class="error">{{ errors.telefono[0] }}</span>
                                </div>                                
                                <div class="form-group col-md-7">
                                    <label for="email">E-mail</label>
                                    <input type="text" v-model="cliente.email" class="form-control text-center" id="email">
                                    <span v-if="errors.email" class="error">{{ errors.email[0] }}</span>
                                </div>
                            </div>                                                          
                        </form>
                    </div>
                    <div class="modal-footer">
                        <b-button variant="secondary" @click="cerrarModalNuevoEditar">Cancelar</b-button>
                        <b-button v-if="tipoAccion == 1" variant="success" @click="registrarCliente">Guardar</b-button>
                        <b-button v-else variant="success" @click="actualizarCliente">Actualizar</b-button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!--Fin del modal-->          
    </main>
</template>
<script>
    export default {
        props : ['ruta'],
        data() {
            return {
                cliente : {
                    id : 0,
                    nombre : '',                    
                    tipo_documento : '',
                    num_documento : '',                    
                    direccion : '',
                    telefono : '',
                    email : '',
                },
                columnas: [                    
                    { key: 'opciones', label: 'Opciones', class: 'text-center' },
                    { key : 'nombre', label : 'Nombre', sortable: true },
                    { key : 'documento', label : 'Documento' },                    
                    { key : 'direccion', label : 'Dirección' },         
                    { key : 'telefono', label : 'Teléfono', class: 'text-center' },         
                    { key : 'email', label : 'email', class: 'text-center' },                                                               
                ], 
                clientes : [],
                tituloModalNuevoEditar : '',                
                modalNuevoEditar : false,                
                tipoAccion : 0,                                
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
            listarCliente() {
                let me = this;

                axios.get(`${this.ruta}/cliente`)
                .then(function (response) {                                    
                    me.clientes = response.data;
                    me.totalRows = me.clientes.length;
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
                        this.tituloModalNuevoEditar = 'Registrar Cliente';
                        this.cliente.nombre = '';     
                        this.cliente.num_documento = '';     
                        this.cliente.tipo_documento = '';     
                        this.cliente.direccion = '';     
                        this.cliente.telefono = '';     
                        this.cliente.email = '';                        
                        this.tipoAccion = 1; //registrar
                        break;
                    }
                    case 'actualizar':
                    {                        
                        this.tituloModalNuevoEditar = 'Actualizar Cliente';
                        this.cliente.id = data.id;
                        this.cliente.nombre = data.nombre;                        
                        this.cliente.num_documento = data.num_documento;                        
                        this.cliente.tipo_documento = data.tipo_documento;                        
                        this.cliente.direccion = data.direccion;                        
                        this.cliente.telefono = data.telefono;                        
                        this.cliente.email = data.email;                        
                        this.tipoAccion = 2;
                        break;
                    }
                }
            },
            registrarCliente() {
                let me = this;
                this.errors = [];

                axios.post(`${this.ruta}/cliente/registrar`, {
                    'nombre': this.cliente.nombre,          
                    'tipo_documento': this.cliente.tipo_documento,          
                    'num_documento': this.cliente.num_documento,                              
                    'direccion': this.cliente.direccion,          
                    'telefono': this.cliente.telefono,          
                    'email': this.cliente.email,                    
                }).then(function (response) {
                    me.cerrarModalNuevoEditar();                    
                    me.listarCliente();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'El cliente fue agregado satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                                        
                }).catch(function (error) {
                    console.log(error);
                    if (error.response.status==422) {
                        me.errors = error.response.data.errors;
                    }
                    else {
                        me.cerrarModalNuevoEditar();                    
                        me.errorMsg = true;
                        me.txtErrorMsg = 'Error al agregar el cliente.';
                        me.dismissCountDown = me.dismissSecs;
                    }                    
                });
            },
            actualizarCliente(){
                let me = this;
                this.errors = [];

                axios.put(`${this.ruta}/cliente/actualizar/${this.cliente.id}`, {                    
                    'nombre': this.cliente.nombre,          
                    'tipo_documento': this.cliente.tipo_documento,          
                    'num_documento': this.cliente.num_documento,                              
                    'direccion': this.cliente.direccion,          
                    'telefono': this.cliente.telefono,          
                    'email': this.cliente.email,                      
                }).then(function (response) {
                    me.cerrarModalNuevoEditar();                    
                    me.listarCliente();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'El cliente fue actualizado satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                }).catch(function (error) {
                    console.log(error);
                    if (error.response.status==422) {
                        me.errors = error.response.data.errors;
                    }
                    else {
                        me.cerrarModalNuevoEditar();                    
                        me.errorMsg = true;
                        me.txtErrorMsg = 'Error al actualizar el cliente.';
                        me.dismissCountDown = me.dismissSecs;
                    }                    
                });
            },
            cerrarModalNuevoEditar(){
                this.modalNuevoEditar = false;
                this.cliente.nombre = '';
                this.cliente.tipo_documento = '';
                this.cliente.num_documento = '';
                this.cliente.direccion = '';
                this.cliente.telefono = '';                
                this.cliente.email = '';                
                this.errorMsg = false;
                this.successMsg = false;
                this.txtErrorMsg = '';
                this.txtSuccessMsg = '';   
                this.errors = [];                          
            },       
            cargarPdf(){
                window.open(this.ruta + '/cliente/listarPdf','_blank');
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
            this.listarCliente();            
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
    
</style>