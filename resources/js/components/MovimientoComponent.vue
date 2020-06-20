<template>
    <div>           
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Movimientos
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
                    :items="movimientos"    
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
                                <div class="form-group col-md-5">
                                    <label for="monto">Monto</label>
                                    <input type="text" v-model="movimiento.monto" class="form-control" id="monto">
                                    <span v-if="errors.monto" class="error">{{ errors.monto[0] }}</span>
                                </div>                            
                                <div class="form-group col-md-7">
                                    <label for="tipo_documento">Tipo Movimiento</label>                                    
                                    <select v-model="movimiento.tipo" class="form-control" id="tipo_documento">
                                        <option value="" disabled>Seleccione...</option>
                                        <option value="Entrada">Entrada</option>
                                        <option value="Salida">Salida</option>                                                                                
                                    </select>    
                                    <span v-if="errors.tipo" class="error">{{ errors.tipo[0] }}</span>
                                </div>                                                            
                            </div>    
                            <div class="form-row">     
                                <div class="form-group col-md-12">
                                    <label for="descripcion">Descripción</label>
                                    <textarea v-model="movimiento.descripcion" rows="2" id="descripcion" class="form-control"></textarea>                                                                
                                    <span v-if="errors.descripcion" class="error">{{ errors.descripcion[0] }}</span>
                                </div>                     
                            </div>                                  
                        </form>
                    </div>
                    <div class="modal-footer">
                        <b-button variant="secondary" @click="cerrarModalNuevoEditar">Cancelar</b-button>
                        <b-button v-if="tipoAccion == 1" variant="success" @click="registrarMovimiento">Guardar</b-button>
                        <b-button v-else variant="success" @click="actualizarMovimiento">Actualizar</b-button>
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
                            <b-button variant="success" @click="eliminarMovimiento">Aceptar</b-button>                            
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
                movimiento : {
                    id : 0,
                    monto : '',
                    tipo : '',
                    descripcion : '',
                    idcaja : '',
                },
                columnas: [                    
                    { key: 'opciones', label: 'Opciones', class: 'text-center' },
                    { key : 'monto', label : 'Monto', class: 'text-center', sortable: true },
                    { key : 'tipo', label : 'Tipo', class: 'text-center'},
                    { key : 'created_at', label : 'Fecha - Hora', class: 'text-center'},
                    { key : 'descripcion', label : 'Descripción' },                    
                ], 
                movimientos : [],
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
            listarMovimiento() {
                let me = this;

                axios.get(`${this.ruta}/movimiento`, {
                    params: {
                        'idcaja': this.idcaja
                    }
                })
                .then(function (response) {                                    
                    me.movimientos = response.data;
                    me.totalRows = me.movimientos.length;
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
                        this.tituloModalNuevoEditar = 'Registrar Movimiento';
                        this.movimiento.monto = '';
                        this.movimiento.tipo = '';
                        this.movimiento.descripcion = '';
                        this.tipoAccion = 1; //registrar
                        break;
                    }
                    case 'actualizar':
                    {                        
                        this.tituloModalNuevoEditar = 'Actualizar Movimiento';
                        this.movimiento.id = data.id;
                        this.movimiento.monto = data.monto;
                        this.movimiento.tipo = data.tipo;
                        this.movimiento.descripcion = data.descripcion;
                        this.tipoAccion = 2;
                        break;
                    }
                }
            },
            registrarMovimiento() {
                let me = this;
                this.errors = [];

                axios.post(`${this.ruta}/movimiento/registrar`, {
                    'monto': this.movimiento.monto,
                    'tipo': this.movimiento.tipo,
                    'descripcion': this.movimiento.descripcion,
                    'idcaja' : this.idcaja,
                }).then(function (response) {
                    me.cerrarModalNuevoEditar();                    
                    me.listarMovimiento();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'El movimiento fue agregado satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                                        
                }).catch(function (error) {
                    console.log(error);
                    if (error.response.status==422) {
                        me.errors = error.response.data.errors;
                    }
                    else {
                        me.cerrarModalNuevoEditar();                    
                        me.errorMsg = true;
                        me.txtErrorMsg = 'Error al agregar el movimiento.';
                        me.dismissCountDown = me.dismissSecs;
                    }                    
                });
            },
            actualizarMovimiento(){
                let me = this;
                this.errors = [];

                axios.put(`${this.ruta}/movimiento/actualizar/${this.movimiento.id}`, {                    
                    'monto': this.movimiento.monto,
                    'tipo': this.movimiento.tipo,
                    'descripcion': this.movimiento.descripcion,
                }).then(function (response) {
                    me.cerrarModalNuevoEditar();                    
                    me.listarMovimiento();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'El movimiento fue actualizado satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                }).catch(function (error) {
                    console.log(error);
                    if (error.response.status==422) {
                        me.errors = error.response.data.errors;
                    }
                    else {
                        me.cerrarModalNuevoEditar();                    
                        me.errorMsg = true;
                        me.txtErrorMsg = 'Error al actualizar el movimiento.';
                        me.dismissCountDown = me.dismissSecs;
                    }                    
                });
            },
            cerrarModalNuevoEditar(){
                this.modalNuevoEditar = false;
                this.movimiento.monto = '';
                this.movimiento.tipo = '';
                this.movimiento.descripcion = '';   
                this.errorMsg = false;
                this.successMsg = false;
                this.txtErrorMsg = '';
                this.txtSuccessMsg = '';   
                this.errors = [];                          
            },            
            abrirModalEliminar(data=[]) {
                this.modalEliminar = true;   
                this.movimiento.id = data.id;                                                
                this.tituloModalEliminar = 'Eliminar Movimiento';
                this.mensajeEliminar = '¿Desea eliminar este movimiento?';                                                                                                        
                                
            },
            eliminarMovimiento() {
                let me = this;                

                axios.delete(`${this.ruta}/movimiento/eliminar/${this.movimiento.id}`)
                .then(function (response) {
                    me.cerrarModalEliminar();                    
                    me.listarMovimiento();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'El movimiento fue eliminado satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                }).catch(function (error) {
                    //console.log(error);
                    me.cerrarModalEliminar();         
                    me.errorMsg = true;
                    me.txtErrorMsg = 'Error al eliminar el movimiento.';
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
            this.listarMovimiento();            
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