<template>
    <div>           
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Gastos
                <b-button v-if="estado == 'Abierto'" variant="outline-success" @click="abrirModalNuevoEditar('registrar')">                    
                    <i class="fa fa-plus"></i> Nuevo               
                </b-button>                
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
                    :items="gastos"    
                    :current-page="currentPage"
                    :per-page="perPage"
                    :filter="filter"
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc"
                    :sort-direction="sortDirection"
                    @filtered="onFiltered"               
                >                    
                    <template slot="opciones" slot-scope="row">
                        <b-button :disabled="estado == 'Cerrado'" variant="warning" size="sm" @click="abrirModalNuevoEditar('actualizar', row.item)">
                            <i class="icon-pencil"></i>
                        </b-button>
                        <b-button :disabled="estado == 'Cerrado'" variant="danger" size="sm" @click="abrirModalEliminar(row.item)">
                            <i class="icon-trash"></i>
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
                                <div class="form-group col-md-4">
                                    <label for="importe">Importe</label>
                                    <input type="text" v-model="gasto.importe" class="form-control" id="importe">
                                    <span v-if="errors.importe" class="error">{{ errors.importe[0] }}</span>
                                </div>                            
                                <div class="form-group col-md-8">
                                    <label for="idtipo_gasto">Tipo de Gasto</label>                                    
                                    <v-select  
                                        v-model="gasto.idtipo_gasto"
                                        v-if="modalNuevoEditar"
                                        class="style-chooser"                                              
                                        :options="tipoGastoActivos"                                              
                                        :reduce="tipo_gasto => tipo_gasto.id" 
                                        label="nombre"
                                        placeholder="Seleccione..."                                               
                                    >
                                        <template slot="no-options">
                                            Opción no encontrada...
                                        </template>
                                    </v-select>  
                                    <span v-if="errors.idtipo_gasto" class="error">{{ errors.idtipo_gasto[0] }}</span>
                                </div>                                                            
                            </div>    
                            <div class="form-row">     
                                <div class="form-group col-md-12">
                                    <label for="descripcion">Descripción</label>
                                    <textarea v-model="gasto.descripcion" rows="2" id="descripcion" class="form-control"></textarea>                                                                
                                    <span v-if="errors.descripcion" class="error">{{ errors.descripcion[0] }}</span>
                                </div>                     
                            </div>                                  
                        </form>
                    </div>
                    <div class="modal-footer">
                        <b-button variant="secondary" @click="cerrarModalNuevoEditar">Cancelar</b-button>
                        <b-button v-if="tipoAccion == 1" variant="success" @click="registrarGasto">Guardar</b-button>
                        <b-button v-else variant="success" @click="actualizarGasto">Actualizar</b-button>
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
    					</div>
    					<div class="modal-footer">
                            <b-button variant="secondary" @click="cerrarModalEliminar">Cancelar</b-button>    						    						
                            <b-button variant="success" @click="eliminarGasto">Aceptar</b-button>                            
    					</div>
    				</form>
    			</div>
    		</div>
    	</div><!--Fin de modal eliminar-->
    </div>
</template>
<script>
    export default {
        props : ['idcaja', 'ruta', 'estado'],
        data() {
            return {
                gasto : {
                    id : 0,
                    importe : '',
                    idtipo_gasto : '',
                    descripcion : '',
                    idcaja : '',
                },
                columnas: [                                        
                    { key : 'importe', label : 'Importe', class: 'text-center', sortable: true },
                    { key : 'tipo_gasto', label : 'Tipo Gasto', class: 'text-center'},
                    { key : 'created_at', label : 'Fecha - Hora', class: 'text-center', sortable: true},
                    { key : 'descripcion', label : 'Descripción' },                    
                    { key: 'opciones', label: 'Opciones', class: 'text-center' },
                ], 
                gastos : [],
                tipoGastoActivos : [],
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
            listarGasto() {                
                axios.get(`${this.ruta}/gasto`, {
                    params: {
                        'idcaja': this.idcaja
                    }
                })
                .then(response => {                                    
                    this.gastos = response.data;
                    this.totalRows = this.gastos.length;
                })
                .catch(error => {                    
                    console.log(error);
                });
            },
            getTiposGastoActivos() {               
                axios.get(`${this.ruta}/gasto/select_tipo_gasto`)
                .then(response => {                                                        
                    this.tipoGastoActivos = response.data;                    
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
                        this.tituloModalNuevoEditar = 'Registrar Gasto';
                        this.gasto.importe = '';
                        this.gasto.idtipo_gasto = '';
                        this.gasto.descripcion = '';
                        this.tipoAccion = 1; //registrar
                        break;
                    }
                    case 'actualizar':
                    {                        
                        this.tituloModalNuevoEditar = 'Actualizar Gasto';
                        this.gasto.id = data.id;
                        this.gasto.importe = data.importe;
                        this.gasto.idtipo_gasto = data.idtipo_gasto;
                        this.gasto.descripcion = data.descripcion;
                        this.tipoAccion = 2;
                        break;
                    }
                }
            },
            registrarGasto() {                
                this.errors = [];

                axios.post(`${this.ruta}/gasto/registrar`, {
                    'importe': this.gasto.importe,
                    'idtipo_gasto': this.gasto.idtipo_gasto,
                    'descripcion': this.gasto.descripcion,
                    'idcaja' : this.idcaja,
                }).then(response => {
                    this.cerrarModalNuevoEditar();                    
                    this.listarGasto();
                    this.successMsg = true;
                    this.txtSuccessMsg = 'El gasto fue agregado satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                                        
                }).catch(error => {                    
                    if (error.response.status==422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
                        this.cerrarModalNuevoEditar();                    
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al agregar el gasto.';
                        this.dismissCountDown = this.dismissSecs;
                    }                    
                });
            },
            actualizarGasto() {                
                this.errors = [];

                axios.put(`${this.ruta}/gasto/actualizar/${this.gasto.id}`, {                    
                    'importe': this.gasto.importe,
                    'idtipo_gasto': this.gasto.idtipo_gasto,
                    'descripcion': this.gasto.descripcion,
                }).then(response => {
                    this.cerrarModalNuevoEditar();                    
                    this.listarGasto();
                    this.successMsg = true;
                    this.txtSuccessMsg = 'El gasto fue actualizado satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                }).catch(error => {                    
                    if (error.response.status==422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
                        this.cerrarModalNuevoEditar();                    
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al actualizar el gasto.';
                        this.dismissCountDown = this.dismissSecs;
                    }                    
                });
            },
            cerrarModalNuevoEditar(){
                this.modalNuevoEditar = false;
                this.gasto.importe = '';
                this.gasto.idtipo_gasto = '';
                this.gasto.descripcion = '';   
                this.errorMsg = false;
                this.successMsg = false;
                this.txtErrorMsg = '';
                this.txtSuccessMsg = '';   
                this.errors = [];                          
            },            
            abrirModalEliminar(data=[]) {
                this.modalEliminar = true;   
                this.gasto.id = data.id;                                                
                this.tituloModalEliminar = 'Eliminar Gasto';
                this.mensajeEliminar = '¿Desea eliminar este gasto?';                                                                                                        
                                
            },
            eliminarGasto() {                 
                axios.delete(`${this.ruta}/gasto/eliminar/${this.gasto.id}`)
                    .then(response => {
                        this.cerrarModalEliminar();                    
                        this.listarGasto();
                        this.successMsg = true;
                        this.txtSuccessMsg = 'El gasto fue eliminado satisfactoriamente.';
                        this.dismissCountDown = this.dismissSecs;
                    }).catch(error => {                        
                        this.cerrarModalEliminar();         
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al eliminar el gasto.';
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
            onFiltered(filteredItems) {                
                this.totalRows = filteredItems.length;
                this.currentPage = 1;
            },
            countDownChanged(dismissCountDown) {
                this.dismissCountDown = dismissCountDown;
            },
        },
        mounted() {
            this.listarGasto()
            this.getTiposGastoActivos()       
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
<style non-scoped>
    .style-chooser .vs__search::placeholder,
    .style-chooser .vs__dropdown-toggle,
    .style-chooser .vs__dropdown-menu {    
        max-height: 150px;
    }
</style>