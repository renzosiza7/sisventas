<template>
    <main class="main">        
        <ol class="breadcrumb">            
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item active">Categorías</li>
        </ol>
        <div class="container-fluid">            
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Categorías
                    <b-button variant="outline-success" @click="abrirModalNuevoEditar('registrar')">                    
                        <i class="fa fa-plus"></i> Nuevo               
                    </b-button>
                    <button type="button" @click="cargarPdf()" class="btn btn-info">
                        <i class="icon-doc"></i>&nbsp;Reporte
                    </button>
                    <!--<button type="button" @click="generar_xml()" class="btn btn-danger">
                        <i class="icon-doc"></i>&nbsp;Crear factura
                    </button>-->
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
                        ref="tbl_categorias"
                        striped 
                        hover 
                        bordered 
                        small
                        responsive                      
                        :fields="columnas"                         
                        :items="myProvider"  
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
                    <form role="form" @submit.prevent="tipoAccion == 1 ? registrarCategoria() : actualizarCategoria()">
                        <div class="modal-body">                        
                            <div class="form-group">
                                <label for="nombre_categoria">Nombre</label>
                                <input type="text" v-model="categoria.nombre" class="form-control" id="nombre_categoria" placeholder="Nombre de categoría"/>                                
                                <span v-if="errors.nombre" class="error">{{ errors.nombre[0] }}</span>
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea v-model="categoria.descripcion" rows="1" id="descripcion" class="form-control"></textarea>                                                                
                                <span v-if="errors.descripcion" class="error">{{ errors.descripcion[0] }}</span>
                            </div>
                            <div class="form-group">
                                <label for="acceso">Acceso</label>
                                <select v-model="categoria.acceso" class="form-control" id="acceso">
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
                            <h5><b>{{ categoria.nombre }}</b></h5>
    					</div>
    					<div class="modal-footer">
                            <b-button variant="secondary" @click="cerrarModalEliminar">Cancelar</b-button>    						    						
                            <b-button v-if="tipoAccionEliminar == 1" variant="success" @click="activarCategoria">Aceptar</b-button>
                            <b-button v-else variant="success" @click="desactivarCategoria">Aceptar</b-button>
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
                categoria : {
                    id : 0,
                    nombre : '',
                    descripcion : '',
                    acceso: 'privado',                    
                },
                columnas: [                                        
                    { key : 'nombre', label : 'Nombre', sortable: true },
                    { key : 'descripcion', label : 'Descripción' },
                    { key : 'acceso', label : 'Acceso', class: 'text-center' },
                    { key : 'condicion', label : 'Condición', class: 'text-center' },                    
                    { key: 'opciones', label: 'Opciones', class: 'text-center' },
                ], 
                //categorias : [],                
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
            refreshTable() {
                this.$refs.tbl_categorias.refresh();
            },     
            myProvider(ctx) {                
                let params = "?page=" + ctx.currentPage + "&size=" + ctx.perPage;

                if (ctx.filter !== "" && ctx.filter !== null) {                    
                    params += "&filter=" + ctx.filter;
                }

                if (ctx.sortBy !== "" && ctx.sortBy !== null) {
                    params += "&sortby=" + ctx.sortBy + "&sortdesc=" + ctx.sortDesc;
                }

                const promise = axios.get(`${this.ruta}/categoria${params}`)
                
                return promise.then(response => {                                        
                    const categorias = response.data.data                                   
                    this.totalRows = response.data.total;

                    return categorias || []
                })
            },               
            abrirModalNuevoEditar(accion, data=[]) {                
                this.modalNuevoEditar = true;

                switch(accion){
                    case 'registrar':
                    {                        
                        this.tituloModalNuevoEditar = 'Registrar Categoría';
                        this.categoria.nombre = '';
                        this.categoria.descripcion = '';
                        this.categoria.acceso = 'privado';
                        this.tipoAccion = 1; //registrar
                        break;
                    }
                    case 'actualizar':
                    {         
                        this.tituloModalNuevoEditar = 'Actualizar Categoría';
                        this.categoria.id = data.id;
                        this.categoria.nombre = data.nombre;
                        this.categoria.descripcion = data.descripcion;
                        this.categoria.acceso = data.acceso;
                        this.tipoAccion = 2;
                        break;
                    }
                }
            },            
            registrarCategoria() {                
                this.errors = [];                           
                
                axios.post(`${this.ruta}/categoria/registrar`, {
                    'nombre': this.categoria.nombre,
                    'descripcion': this.categoria.descripcion,
                    'acceso': this.categoria.acceso
                }).then(response => {
                    this.cerrarModalNuevoEditar();                    
                    this.refreshTable();                    
                    this.successMsg = true;
                    this.txtSuccessMsg = 'La categoría fue agregada satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;                                        
                }).catch(error => {                    
                    if (error.response.status==422) {
                        this.errors = error.response.data.errors;                        
                    }
                    else {
                        this.cerrarModalNuevoEditar()
                        this.errorMsg = true
                        this.txtErrorMsg = 'Error al agregar la categoría.'
                        this.dismissCountDown = this.dismissSecs
                    }                    
                })               
            },
            actualizarCategoria() {                
                this.errors = [];

                axios.put(`${this.ruta}/categoria/actualizar/${this.categoria.id}`, {                    
                    'nombre': this.categoria.nombre,
                    'descripcion': this.categoria.descripcion,
                    'acceso': this.categoria.acceso
                }).then(response => {
                    this.cerrarModalNuevoEditar();  
                    this.refreshTable();                                      
                    this.successMsg = true;
                    this.txtSuccessMsg = 'La categoría fue actualizada satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                }).catch(error => {                    
                    if (error.response.status==422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
                        this.cerrarModalNuevoEditar();                    
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al actualizar la categoría.';
                        this.dismissCountDown = this.dismissSecs;
                    }                    
                });                
            },
            cerrarModalNuevoEditar() {
                this.modalNuevoEditar = false;
                this.categoria.nombre = '';
                this.categoria.descripcion = '';   
                this.categoria.acceso = 'privado';                   
                this.errorMsg = false;
                this.successMsg = false;
                this.txtErrorMsg = '';
                this.txtSuccessMsg = '';   
                this.errors = [];                          
            },            
            abrirModalEliminar(accion, data=[]) {
                this.modalEliminar = true;   
                this.categoria.id = data.id;
                this.categoria.nombre = data.nombre;
                
                switch(accion){
                    case 'activar':
                    {                        
                        this.tituloModalEliminar = 'Activar Categoría';
                        this.mensajeEliminar = '¿Desea activar esta categoría?';                                                                        
                        this.tipoAccionEliminar = 1;                        
                        break;
                    }
                    case 'desactivar':
                    {                        
                        this.tituloModalEliminar = 'Desactivar Categoría';
                        this.mensajeEliminar = '¿Desea desactivar esta categoría?';
                        this.tipoAccionEliminar = 2;                        
                        break;
                    }
                }               
            },
            activarCategoria() {                          
                axios.put(`${this.ruta}/categoria/activar/${this.categoria.id}`)
                .then(response => {
                    this.cerrarModalEliminar();                    
                    this.refreshTable();                    
                    this.successMsg = true;
                    this.txtSuccessMsg = 'La categoría fue activada satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                }).catch(error => {                    
                    this.cerrarModalEliminar();         
                    this.errorMsg = true;
                    this.txtErrorMsg = 'Error al activar la categoría.';
                    this.dismissCountDown = this.dismissSecs;                    
                });
            },
            desactivarCategoria() {              
                axios.put(`${this.ruta}/categoria/desactivar/${this.categoria.id}`)
                .then(response => {
                    this.cerrarModalEliminar();                                        
                    this.refreshTable();
                    this.successMsg = true;
                    this.txtSuccessMsg = 'La categoría fue desactivada satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                }).catch(error => {                    
                    this.cerrarModalEliminar();         
                    this.errorMsg = true;
                    this.txtErrorMsg = 'Error al desactivar la categoría.';
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
                window.open(this.ruta + '/categoria/listarPdf','_blank');
            },
            generar_xml(){                
                window.open(this.ruta + '/facturacion/generar_xml','_blank');
                /*axios.post(`${this.ruta}/factura/registrar`, {                    
                }).then(response => {                    
                    console.log(response.data)                    
                }).catch(error => {
                    
                })*/
            },
            onFiltered(filteredItems) {                
                this.totalRows = filteredItems.length;
                this.currentPage = 1;
            },
            countDownChanged(dismissCountDown) {
                this.dismissCountDown = dismissCountDown;
            },            
        },        
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