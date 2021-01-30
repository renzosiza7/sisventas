<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">            
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item active">Proveedores</li>
        </ol>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Proveedores
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
                        :items="proveedores"    
                        :current-page="currentPage"
                        :per-page="perPage"
                        :filter="filter"
                        :sort-by.sync="sortBy"
                        :sort-desc.sync="sortDesc"
                        :sort-direction="sortDirection"
                        @filtered="onFiltered"               
                    >
                        <template slot="documento" slot-scope="row">
                            {{row.item.tipo_documento }} - {{ row.item.num_documento }}                  
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
                                    <input type="text" v-model="proveedor.nombre" class="form-control" id="nombre" placeholder="Nombre o razón social del proveedor">
                                    <span v-if="errors.nombre" class="error">{{ errors.nombre[0] }}</span>
                                </div>
                            </div>  
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label for="tipo_documento">Tipo Documento</label>                                    
                                    <select v-model="proveedor.tipo_documento" class="form-control" id="tipo_documento">
                                        <option value="" disabled>Seleccione...</option>
                                        <option value="RUC">RUC</option>
                                        <option value="DNI">DNI</option>                                        
                                        <option value="PASAPORTE">PASAPORTE</option>
                                    </select>    
                                    <span v-if="errors.tipo_documento" class="error">{{ errors.tipo_documento[0] }}</span>
                                </div>
                                <div class="form-group col-md-7">
                                    <label for="num_documento">Número Documento</label>                                    
                                    <input type="text" v-model="proveedor.num_documento" class="form-control text-center" id="num_documento" placeholder="Nro. Documento">
                                    <span v-if="errors.num_documento" class="error">{{ errors.num_documento[0] }}</span>                                    
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-5">
                                    <label for="direccion">Dirección</label>
                                    <input type="text" v-model="proveedor.direccion" class="form-control" id="direccion" placeholder="Domicilio del proveedor">            
                                    <span v-if="errors.direccion" class="error">{{ errors.direccion[0] }}</span>
                                </div>                                            
                                <div class="form-group col-md-3">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" v-model="proveedor.telefono" class="form-control text-center" id="telefono">
                                    <span v-if="errors.telefono" class="error">{{ errors.telefono[0] }}</span>
                                </div>                                
                                <div class="form-group col-md-4">
                                    <label for="email">E-mail</label>
                                    <input type="text" v-model="proveedor.email" class="form-control text-center" id="email">
                                    <span v-if="errors.email" class="error">{{ errors.email[0] }}</span>
                                </div>
                            </div>                                                          
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label for="contacto">Contacto</label>
                                    <input type="text" v-model="proveedor.contacto" class="form-control text-center" id="contacto">
                                    <span v-if="errors.contacto" class="error">{{ errors.contacto[0] }}</span>
                                </div>                                
                                <div class="form-group col-md-4">
                                    <label for="telefono_contacto">Teléfono Contacto</label>
                                    <input type="text" v-model="proveedor.telefono_contacto" class="form-control text-center" id="telefono_contacto">
                                    <span v-if="errors.telefono_contacto" class="error">{{ errors.telefono_contacto[0] }}</span>
                                </div>
                            </div>                                                          
                        </form>
                    </div>
                    <div class="modal-footer">
                        <b-button variant="secondary" @click="cerrarModalNuevoEditar">Cancelar</b-button>
                        <b-button v-if="tipoAccion == 1" variant="success" @click="registrarProveedor">Guardar</b-button>
                        <b-button v-else variant="success" @click="actualizarProveedor">Actualizar</b-button>
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
                proveedor : {
                    id : 0,
                    nombre : '',                    
                    tipo_documento : '',
                    num_documento : '',                    
                    direccion : '',
                    telefono : '',
                    email : '',
                    contacto : '',
                    telefono_contacto : ''
                },
                columnas: [                    
                    { key: 'opciones', label: 'Opciones', class: 'text-center' },                    
                    { key : 'nombre', label : 'Nombre', sortable: true },                    
                    { key : 'documento', label : 'Documento' },                    
                    { key : 'telefono', label : 'Teléfono', class: 'text-center' },         
                    { key : 'email', label : 'email', class: 'text-center' },  
                    { key : 'contacto', label : 'Contacto' },             
                ], 
                proveedores : [],
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
            listarProveedor() {
                let me = this;

                axios.get(`${this.ruta}/proveedor`)
                .then(function (response) {                                    
                    me.proveedores = response.data;
                    me.totalRows = me.proveedores.length;
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
                        this.tituloModalNuevoEditar = 'Registrar Proveedor';
                        this.proveedor.nombre = '';     
                        this.proveedor.num_documento = '';     
                        this.proveedor.tipo_documento = '';     
                        this.proveedor.direccion = '';     
                        this.proveedor.telefono = '';     
                        this.proveedor.email = '';      
                        this.proveedor.contacto = '';      
                        this.proveedor.telefono_contacto = '';                        
                        this.tipoAccion = 1; //registrar
                        break;
                    }
                    case 'actualizar':
                    {                        
                        this.tituloModalNuevoEditar = 'Actualizar Proveedor';
                        this.proveedor.id = data.id;
                        this.proveedor.nombre = data.nombre;                        
                        this.proveedor.num_documento = data.num_documento;                        
                        this.proveedor.tipo_documento = data.tipo_documento;                        
                        this.proveedor.direccion = data.direccion;                        
                        this.proveedor.telefono = data.telefono;                        
                        this.proveedor.email = data.email;      
                        this.proveedor.contacto = data.contacto;      
                        this.proveedor.telefono_contacto = data.telefono_contacto;                        
                        this.tipoAccion = 2;
                        break;
                    }
                }
            },
            registrarProveedor() {
                let me = this;
                this.errors = [];

                axios.post(`${this.ruta}/proveedor/registrar`, {
                    'nombre': this.proveedor.nombre,          
                    'tipo_documento': this.proveedor.tipo_documento,          
                    'num_documento': this.proveedor.num_documento,                              
                    'direccion': this.proveedor.direccion,          
                    'telefono': this.proveedor.telefono,          
                    'email': this.proveedor.email, 
                    'contacto': this.proveedor.contacto, 
                    'telefono_contacto': this.proveedor.telefono_contacto,                    
                }).then(function (response) {
                    me.cerrarModalNuevoEditar();                    
                    me.listarProveedor();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'El proveedor fue agregado satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                                        
                }).catch(function (error) {
                    console.log(error);
                    if (error.response.status==422) {
                        me.errors = error.response.data.errors;
                    }
                    else {
                        me.cerrarModalNuevoEditar();                    
                        me.errorMsg = true;
                        me.txtErrorMsg = 'Error al agregar el proveedor.';
                        me.dismissCountDown = me.dismissSecs;
                    }                    
                });
            },
            actualizarProveedor(){
                let me = this;
                this.errors = [];

                axios.put(`${this.ruta}/proveedor/actualizar/${this.proveedor.id}`, {                    
                    'nombre': this.proveedor.nombre,          
                    'tipo_documento': this.proveedor.tipo_documento,          
                    'num_documento': this.proveedor.num_documento,                              
                    'direccion': this.proveedor.direccion,          
                    'telefono': this.proveedor.telefono,          
                    'email': this.proveedor.email,                      
                    'contacto': this.proveedor.contacto, 
                    'telefono_contacto': this.proveedor.telefono_contacto,                    
                }).then(function (response) {
                    me.cerrarModalNuevoEditar();                    
                    me.listarProveedor();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'El proveedor fue actualizado satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                }).catch(function (error) {
                    console.log(error);
                    if (error.response.status==422) {
                        me.errors = error.response.data.errors;
                    }
                    else {
                        me.cerrarModalNuevoEditar();                    
                        me.errorMsg = true;
                        me.txtErrorMsg = 'Error al actualizar el proveedor.';
                        me.dismissCountDown = me.dismissSecs;
                    }                    
                });
            },
            cerrarModalNuevoEditar(){
                this.modalNuevoEditar = false;
                this.proveedor.nombre = '';
                this.proveedor.tipo_documento = '';
                this.proveedor.num_documento = '';
                this.proveedor.direccion = '';
                this.proveedor.telefono = '';                
                this.proveedor.email = '';                
                this.proveedor.contacto = '';                
                this.proveedor.telefono_contacto = '';                
                this.errorMsg = false;
                this.successMsg = false;
                this.txtErrorMsg = '';
                this.txtSuccessMsg = '';   
                this.errors = [];                          
            },                       
            cargarPdf(){
                window.open(this.ruta + '/proveedor/listarPdf','_blank');
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
            this.listarProveedor();            
        }
    }
</script>
<style scoped>
    /* Modal styles */
    .modal .modal-dialog {
        max-width: 600px;
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