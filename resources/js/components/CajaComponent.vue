<template>
    <main class="main">        
        <ol class="breadcrumb">            
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item active">Caja</li>
        </ol>
        <div class="container-fluid">
            <template v-if="listado==0">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-align-justify"></i> Cajas
                        <b-button v-if="idrol == 1" variant="outline-success" @click="abrirModalNuevoEditar('registrar')">                    
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
                            :items="cajas"    
                            :current-page="currentPage"
                            :per-page="perPage"
                            :filter="filter"
                            :sort-by.sync="sortBy"
                            :sort-desc.sync="sortDesc"
                            :sort-direction="sortDirection"
                            @filtered="onFiltered"               
                        >
                            <template slot="opciones" slot-scope="row">
                                <b-button v-if="row.item.estado == 'Abierto' && iduser == row.item.idcajero" variant="success" size="sm" @click="verCaja(row.item.id, row.item.estado)">
                                    <i class="icon-check"></i>
                                </b-button>                           
                                <b-button v-if="row.item.estado == 'Cerrado'" variant="primary" size="sm" @click="verCaja(row.item.id, row.item.estado)">
                                    <i class="icon-eye"></i>
                                </b-button>                           
                                <b-button v-if="row.item.estado == 'Abierto' && idrol == 1" variant="warning" size="sm" @click="abrirModalNuevoEditar('actualizar', row.item)">
                                    <i class="icon-pencil"></i>
                                </b-button>      
                                <b-button v-if="row.item.estado == 'Abierto' && idrol == 1" variant="danger" size="sm" @click="abrirModalEliminar(row.item)">
                                    <i class="icon-trash"></i>
                                </b-button>                                                        
                            </template>
                            <template slot="estado" slot-scope="row">
                                <span v-if="row.item.estado == 'Abierto'" class="badge badge-success">Abierto</span>
                                <span v-else class="badge badge-danger">Cerrado</span>                            
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
                </div>
            </template>
            <template v-else-if="listado==1">
                <b-card no-body>
                    <b-tabs pills card>
                        <b-tab title="Ventas" @click="actualizarPagina">
                            <venta-caja-component :key="renderKey" :ruta="ruta" :idcaja="idcaja" :estado="estado"/>
                        </b-tab>
                        <b-tab title="Servicios">
                            <servicio-caja-component :key="renderKey" :ruta="ruta" :idcaja="idcaja" :estado="estado"/>
                        </b-tab>   
                        <b-tab title="Movimientos" @click="actualizarPagina">                    
                            <movimiento-component :key="renderKey" :ruta="ruta" :idcaja="idcaja" :estado="estado"/>
                        </b-tab>               
                        <b-tab title="Gastos" @click="actualizarPagina">                    
                            <gasto-component :key="renderKey" :ruta="ruta" :idcaja="idcaja" :estado="estado"/>
                        </b-tab>            
                        <b-tab title="Cierre de Caja" @click="actualizarPagina" class="">                    
                            <cierre-caja-component :key="renderKey" :ruta="ruta" :idcaja="idcaja" :estado="estado"/>
                        </b-tab>               
                        <div class="text-center mb-4">
                            <b-button type="button" @click="ocultarDetalle()" variant="outline-warning">
                                <i class="fa fa-arrow-left"></i> Volver
                            </b-button>
                        </div>   
                    </b-tabs>                                      
                </b-card>                
            </template>            
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
                            <div class="form-group">
                                <label for="importe">Monto Inicial</label>
                                <input type="text" v-model="caja.monto_inicial" class="form-control" id="monto_inicial">
                                <span v-if="errors.monto_inicial" class="error">{{ errors.monto_inicial[0] }}</span>
                            </div>                            
                            <div class="form-group" v-if="tipoAccion == 1">
                                <label for="idtipo_gasto">Cajero</label>                                    
                                <v-select  
                                    v-model="caja.idcajero"
                                    v-if="modalNuevoEditar"
                                    class="style-chooser"                                              
                                    :options="cajeros"                                              
                                    :reduce="cajero => cajero.id" 
                                    label="nombre"
                                    placeholder="Seleccione..."                                               
                                >
                                    <template slot="no-options">
                                        Opción no encontrada...
                                    </template>
                                </v-select>  
                                <span v-if="errors.idcajero" class="error">{{ errors.idcajero[0] }}</span>
                            </div>                                                                                        
                        </form>
                    </div>
                    <div class="modal-footer">
                        <b-button variant="secondary" @click="cerrarModalNuevoEditar">Cancelar</b-button>
                        <b-button v-if="tipoAccion == 1" variant="success" @click="registrarCaja">Guardar</b-button>
                        <b-button v-else variant="success" @click="actualizarCaja">Actualizar</b-button>
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
                            <b-button variant="success" @click="cerrarCaja">Aceptar</b-button>                            
    					</div>
    				</form>
    			</div>
    		</div>
    	</div><!--Fin de modal eliminar-->          
    </main>
</template>
<script>
    export default {
        props : ['ruta', 'idrol', 'iduser'],
        data() {
            return {
                renderKey: 1,
                listado : 0, 
                idcaja : '',
                estado : '',
                caja : {
                    id : 0,
                    monto_inicial : 0,
                    total_ventas : 0,
                    total_servicios : 0,
                    total_mov_entrada : 0,
                    total_mov_salida : 0,
                    total_gastos : 0,
                    saldo_sistema : 0,
                    saldo_caja : 0,
                    diferencia : 0,
                    descripcion : '',
                    idcajero: '',
                },
                columnas: [                                        
                    { key : 'nombre_cajero', label : 'Cajero' },
                    { key : 'monto_inicial', label : 'Monto Inicial', class: 'text-center' },
                    { key : 'created_at', label : 'Fecha creación', class: 'text-center' },
                    { key : 'updated_at', label : 'Fecha actualización', class: 'text-center' },                    
                    { key : 'estado', label : 'Estado', class: 'text-center' },                                        
                    { key: 'opciones', label: 'Opciones', class: 'text-center' },
                ], 
                cajas : [],
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
            this.listarCaja() 
            this.getCajeros()
        },        
        methods : {
            actualizarPagina() {                
                this.renderKey++;
            },
            verCaja(idcaja, estado) {                
                this.idcaja = idcaja
                this.estado = estado
                this.listado = 1
            },
            ocultarDetalle(){
                this.listado = 0
            },
            listarCaja() {                
                axios.get(`${this.ruta}/caja`)
                    .then(response => {                                    
                        this.cajas = response.data;
                        this.totalRows = this.cajas.length;
                    })
                    .catch(error => {                    
                        console.log(error);
                    });
            },       
            getCajeros() {                
                axios.get(`${this.ruta}/caja/get_cajeros`)
                    .then(response => {                                    
                        this.cajeros = response.data;                    
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
                        this.tituloModalNuevoEditar = 'Abrir Caja';
                        this.caja.monto_inicial = ''; 
                        this.caja.idcajero = '';                        
                        this.tipoAccion = 1; //registrar
                        break;
                    }
                    case 'actualizar':
                    {                        
                        this.tituloModalNuevoEditar = 'Actualizar Caja';
                        this.caja.id = data.id;
                        this.caja.monto_inicial = data.monto_inicial; 
                        this.caja.idcajero = data.idcajero;                      
                        this.tipoAccion = 2;
                        break;
                    }                    
                }
            },
            registrarCaja() {                
                this.errors = [];

                axios.post(`${this.ruta}/caja/registrar`, {
                    'monto_inicial': this.caja.monto_inicial,                    
                    'idcajero': this.caja.idcajero,                    
                }).then(response => {
                    this.cerrarModalNuevoEditar();                    
                    this.listarCaja();
                    this.successMsg = true;
                    this.txtSuccessMsg = 'Caja agregada satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                                        
                }).catch(error => {                    
                    if (error.response.status==422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
                        this.cerrarModalNuevoEditar();                    
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al agregar la caja.';
                        this.dismissCountDown = this.dismissSecs;
                    }                    
                });
            },
            actualizarCaja(){                
                this.errors = [];

                axios.put(`${this.ruta}/caja/actualizar/${this.caja.id}`, {                    
                    'monto_inicial': this.caja.monto_inicial,                    
                }).then(response => {
                    this.cerrarModalNuevoEditar();                    
                    this.listarCaja();
                    this.successMsg = true;
                    this.txtSuccessMsg = 'Caja actualizada satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                }).catch(error => {                    
                    if (error.response.status==422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
                        this.cerrarModalNuevoEditar();                    
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al actualizar la caja.';
                        this.dismissCountDown = this.dismissSecs;
                    }                    
                });
            },
            cerrarModalNuevoEditar(){
                this.modalNuevoEditar = false;
                this.caja.monto_inicial = '';                
                this.errorMsg = false;
                this.successMsg = false;
                this.txtErrorMsg = '';
                this.txtSuccessMsg = '';   
                this.errors = [];                          
            },            
            abrirModalEliminar(data=[]) {
                this.modalEliminar = true;   
                this.caja.id = data.id;                                                
                this.tituloModalEliminar = 'Cerrar Caja';
                this.mensajeEliminar = '¿Desea cerrar caja?';                                                                                                        
                                
            },
            cerrarCaja() {                             
                axios.put(`${this.ruta}/caja/cerrar/${this.caja.id}`, {                    
                    'total_ventas' : this.caja.total_ventas,                    
                    'total_servicios' : this.caja.total_servicios,                    
                    'total_mov_entrada' : this.caja.total_mov_entrada,                    
                    'total_mov_salida' : this.caja.total_mov_salida,                    
                    'total_gastos' : this.caja.total_gastos,                    
                    'saldo_sistema' : this.caja.saldo_sistema,                    
                    'saldo_caja' : this.caja.saldo_caja,                    
                    'diferencia' : this.caja.diferencia,                    
                })
                .then(response => {
                    this.cerrarModalEliminar();                    
                    this.listarCaja();
                    this.successMsg = true;
                    this.txtSuccessMsg = 'Caja cerrada satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                }).catch(error => {                    
                    this.cerrarModalEliminar();         
                    this.errorMsg = true;
                    this.txtErrorMsg = 'Error al cerrar la caja.';
                    this.dismissCountDown = this.dismissSecs;                    
                });
            },           
            cerrarModalEliminar() {
                this.modalEliminar = false;
                this.caja.monto_inicial = 0;
                this.caja.total_ventas = 0;
                this.caja.total_servicios = 0;
                this.caja.total_mov_entrada = 0;
                this.caja.total_mov_salida = 0;
                this.caja.total_gastos = 0;
                this.caja.saldo_sistema = 0;
                this.caja.saldo_caja = 0;
                this.caja.diferencia = 0;
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