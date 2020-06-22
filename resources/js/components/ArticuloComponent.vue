<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">            
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item active">Artículos</li>
        </ol>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Artículos
                    <b-button v-if="idrol == 1 || idrol == 3" variant="outline-success" @click="abrirModalNuevoEditar('registrar')">                    
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
                        :items="articulos"    
                        :current-page="currentPage"
                        :per-page="perPage"
                        :filter="filter"
                        :sort-by.sync="sortBy"
                        :sort-desc.sync="sortDesc"
                        :sort-direction="sortDirection"
                        @filtered="onFiltered"               
                        v-if="idrol == 1 || idrol == 3"
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
                    <b-table 
                        striped 
                        hover 
                        bordered 
                        small
                        responsive
                        :fields="columnas2" 
                        :items="articulos"    
                        :current-page="currentPage"
                        :per-page="perPage"
                        :filter="filter"
                        :sort-by.sync="sortBy"
                        :sort-desc.sync="sortDesc"
                        :sort-direction="sortDirection"
                        @filtered="onFiltered"               
                        v-else
                    >
                        <template slot="condicion" slot-scope="row">
                            <span v-if="row.item.condicion == 1" class="badge badge-success">Activo</span>
                            <span v-else class="badge badge-secondary">Inactivo</span>                            
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
                                <div class="form-group col-md-4">
                                    <label for="codigo">Código</label>
                                    <input type="text" v-model="articulo.codigo" class="form-control text-center" id="codigo" placeholder="Ingrese código">
                                    <!--<barcode :value="articulo.codigo" height="40" display-value="false" width="1">
                                        Código de barras.    
                                    </barcode>-->
                                    <span v-if="errors.codigo" class="error">{{ errors.codigo[0] }}</span>
                                </div>
                                <div class="form-group col-md-8">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" v-model="articulo.nombre" class="form-control" id="nombre" placeholder="Nombre del artículo">
                                    <span v-if="errors.nombre" class="error">{{ errors.nombre[0] }}</span>
                                </div>
                            </div>  
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="categoria">Categoría</label>                                    
                                    <template v-if="tipoAccion == 1">
                                        <v-select   v-model="articulo.idcategoria"
                                                    v-if="modalNuevoEditar"
                                                    class="style-chooser"                                              
                                                    :options="categoriasActivas"                                              
                                                    :reduce="categoria => categoria.id" 
                                                    label="nombre"
                                                    placeholder="Seleccione..."                                               
                                        >
                                            <template slot="no-options">
                                                Opción no encontrada...
                                            </template>
                                        </v-select>                                        
                                    </template>
                                    <template v-else-if="tipoAccion == 2">
                                        <v-select   v-model="articulo.idcategoria"
                                                    v-if="modalNuevoEditar"
                                                    class="style-chooser"                                              
                                                    :options="categorias"                                              
                                                    :reduce="categoria => categoria.id" 
                                                    label="nombre"
                                                    placeholder="Seleccione..."                                               
                                        >
                                            <template slot="no-options">
                                                Opción no encontrada...
                                            </template>
                                        </v-select>                             
                                    </template>                                   
                                    <span v-if="errors.idcategoria" class="error">{{ errors.idcategoria[0] }}</span>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="marca">Marca</label>                                    
                                    <template v-if="tipoAccion == 1">
                                        <v-select v-model="articulo.idmarca"                                                
                                                  v-if="modalNuevoEditar"
                                                  class="style-chooser"
                                                  :options="marcasActivas"
                                                  :reduce="marca => marca.id" 
                                                  label="nombre"           
                                                  placeholder="Seleccione..."                                                                                                                                                                                                                                      
                                        >
                                            <template slot="no-options">
                                                Opción no encontrada...
                                            </template>
                                        </v-select>
                                    </template>
                                    <template v-else-if="tipoAccion == 2">
                                        <v-select v-model="articulo.idmarca"                                                
                                                  v-if="modalNuevoEditar"
                                                  class="style-chooser"
                                                  :options="marcas"
                                                  :reduce="marca => marca.id" 
                                                  label="nombre"           
                                                  placeholder="Seleccione..."                                                                                                                                                                                                                                      
                                        >
                                            <template slot="no-options">
                                                Opción no encontrada...
                                            </template>
                                        </v-select>
                                    </template>                                  
                                    <span v-if="errors.idmarca" class="error">{{ errors.idmarca[0] }}</span>
                                </div>
                            </div>  
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="precio_venta">Precio Compra</label>
                                    <input type="text" v-model="articulo.precio_compra" class="form-control text-center" id="precio_venta">                                    
                                </div>     
                                <div class="form-group col-md-4">
                                    <label for="precio_venta">Precio Venta</label>
                                    <input type="text" v-model="articulo.precio_venta" class="form-control text-center" id="precio_venta">
                                    <span v-if="errors.precio_venta" class="error">{{ errors.precio_venta[0] }}</span>
                                </div>                                
                                <div class="form-group col-md-4">
                                    <label for="stock">Stock</label>
                                    <input type="text" v-model="articulo.stock" class="form-control text-center" id="stock">
                                    <span v-if="errors.stock" class="error">{{ errors.stock[0] }}</span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="descripcion">Descripción</label>
                                    <textarea v-model="articulo.descripcion" rows="1" id="descripcion" class="form-control"></textarea>                                                                
                                    <span v-if="errors.descripcion" class="error">{{ errors.descripcion[0] }}</span>
                                </div>                
                            </div>                                        
                        </form>
                    </div>
                    <div class="modal-footer">
                        <b-button variant="secondary" @click="cerrarModalNuevoEditar">Cancelar</b-button>
                        <b-button v-if="tipoAccion == 1" variant="success" @click="registrarArticulo">Guardar</b-button>
                        <b-button v-else variant="success" @click="actualizarArticulo">Actualizar</b-button>
                    </div>
                </div>
            </div>
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
                            <h5><b>{{ articulo.nombre }}</b></h5>
    					</div>
    					<div class="modal-footer">
                            <b-button variant="secondary" @click="cerrarModalEliminar">Cancelar</b-button>    						    						
                            <b-button v-if="tipoAccionEliminar == 1" variant="success" @click="activarArticulo">Aceptar</b-button>
                            <b-button v-else variant="success" @click="desactivarArticulo">Aceptar</b-button>
    					</div>
    				</form>
    			</div>
    		</div>
    	</div><!--Fin de modal eliminar-->
    </main>
</template>
<script>   
    import VueBarcode from 'vue-barcode';

    export default {
        props : ['ruta', 'idrol'],
        data() {
            return {                            
                articulo : {
                    id : 0,
                    idcategoria: '',
                    idmarca : '',                    
                    codigo : '',                    
                    nombre : '',
                    precio_compra : 0,
                    precio_venta : 0,
                    stock : 0,                    
                    descripcion : '',
                },      
                categoriasActivas : [],
                categorias : [],
                marcasActivas : [],
                marcas : [],
                columnas: [                                        
                    { key: 'opciones', label: 'Opciones', class: 'text-center' },
                    { key : 'codigo', label : 'Código', class: 'text-center' },
                    { key : 'nombre', label : 'Artículo', sortable: true },
                    { key : 'categoria', label : 'Categoría', sortable: true },
                    { key : 'marca', label : 'Marca', sortable: true },        
                    { key : 'precio_compra', label : 'P. Compra', class: 'text-center' },
                    { key : 'precio_venta', label : 'P. Venta', class: 'text-center' },            
                    { key : 'stock', label : 'Stock', class: 'text-center' },            
                    { key : 'condicion', label : 'Condición', class: 'text-center' },            
                ], 
                columnas2: [// columnas para vendedor y cajero sin opciones                    
                    { key : 'codigo', label : 'Código', class: 'text-center' },
                    { key : 'nombre', label : 'Artículo', sortable: true },
                    { key : 'categoria', label : 'Categoría', sortable: true },
                    { key : 'marca', label : 'Marca', sortable: true },        
                    { key : 'precio_compra', label : 'P. Compra', class: 'text-center' },
                    { key : 'precio_venta', label : 'P. Venta', class: 'text-center' },            
                    { key : 'stock', label : 'Stock', class: 'text-center' },            
                    { key : 'condicion', label : 'Condición', class: 'text-center' },            
                ], 
                articulos : [],
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
        components: {
            'barcode': VueBarcode
        },
        methods : {
            listarArticulo() {
                let me = this;

                axios.get(`${this.ruta}/articulo`)
                .then(function (response) {                                                        
                    me.articulos = response.data;
                    me.totalRows = me.articulos.length;
                })
                .catch(function (error) {                    
                    console.log(error);
                });
            },
            getCategoriasActivas() {
                let me = this;

                axios.get(`${this.ruta}/categoria/select_categoria`)
                .then(function (response) {                                                        
                    me.categoriasActivas = response.data;                    
                })
                .catch(function (error) {                    
                    console.log(error);
                });
            },
            getCategorias() {
                let me = this;

                axios.get(`${this.ruta}/categoria`)
                .then(function (response) {                                                        
                    me.categorias = response.data;                    
                })
                .catch(function (error) {                    
                    console.log(error);
                });
            },
            getMarcasActivas() {
                let me = this;

                axios.get(`${this.ruta}/marca/select_marca`)
                .then(function (response) {                                                        
                    me.marcasActivas = response.data;                    
                })
                .catch(function (error) {                    
                    console.log(error);
                });
            },
            getMarcas() {
                let me = this;

                axios.get(`${this.ruta}/marca`)
                .then(function (response) {                                                        
                    me.marcas = response.data;                    
                })
                .catch(function (error) {                    
                    console.log(error);
                });
            },
            abrirModalNuevoEditar(accion, data=[]) {                     
                this.modalNuevoEditar = true;                                
                //this.getCategoriasActivas();
                //this.getMarcasActivas();      

                switch(accion){
                    case 'registrar':
                    {                        
                        this.tituloModalNuevoEditar = 'Registrar Artículo';
                        this.articulo.codigo = '';
                        this.articulo.nombre = '';
                        this.articulo.idcategoria = '';
                        this.articulo.idmarca = '';
                        this.articulo.precio_compra = '';
                        this.articulo.precio_venta = '';
                        this.articulo.stock = '';
                        this.articulo.descripcion = '';
                        this.tipoAccion = 1; //registrar
                        break;
                    }
                    case 'actualizar':
                    {                        
                        this.tituloModalNuevoEditar = 'Actualizar Artículo';
                        this.articulo.id = data.id;
                        this.articulo.codigo = data.codigo;
                        this.articulo.nombre = data.nombre;
                        this.articulo.idcategoria = data.idcategoria;                        
                        this.articulo.idmarca = data.idmarca;                        
                        this.articulo.precio_compra = data.precio_compra;
                        this.articulo.precio_venta = data.precio_venta;
                        this.articulo.stock = data.stock;
                        this.articulo.descripcion = data.descripcion;
                        this.tipoAccion = 2;
                        break;
                    }
                }
            },
            registrarArticulo() {
                let me = this;
                this.errors = [];

                axios.post(`${this.ruta}/articulo/registrar`, {
                    'codigo': this.articulo.codigo,
                    'nombre': this.articulo.nombre,
                    'idcategoria': this.articulo.idcategoria,
                    'idmarca': this.articulo.idmarca,
                    'precio_compra': this.articulo.precio_compra,
                    'precio_venta': this.articulo.precio_venta,
                    'stock': this.articulo.stock,
                    'descripcion': this.articulo.descripcion,
                }).then(function (response) {
                    me.cerrarModalNuevoEditar();                    
                    me.listarArticulo();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'El artículo fue agregado satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                                        
                }).catch(function (error) {
                    console.log(error);
                    if (error.response.status==422) {
                        me.errors = error.response.data.errors;
                    }
                    else {
                        me.cerrarModalNuevoEditar();                    
                        me.errorMsg = true;
                        me.txtErrorMsg = 'Error al agregar el artículo.';
                        me.dismissCountDown = me.dismissSecs;
                    }                    
                });
            },
            actualizarArticulo() {
                let me = this;
                this.errors = [];

                axios.put(`${this.ruta}/articulo/actualizar/${this.articulo.id}`, {                    
                    'codigo': this.articulo.codigo,
                    'nombre': this.articulo.nombre,
                    'idcategoria': this.articulo.idcategoria,
                    'idmarca': this.articulo.idmarca,
                    'precio_compra': this.articulo.precio_compra,
                    'precio_venta': this.articulo.precio_venta,
                    'stock': this.articulo.stock,
                    'descripcion': this.articulo.descripcion,
                }).then(function (response) {
                    me.cerrarModalNuevoEditar();                    
                    me.listarArticulo();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'El artículo fue actualizado satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                }).catch(function (error) {
                    console.log(error);
                    if (error.response.status==422) {
                        me.errors = error.response.data.errors;
                    }
                    else {
                        me.cerrarModalNuevoEditar();                    
                        me.errorMsg = true;
                        me.txtErrorMsg = 'Error al actualizar el artículo.';
                        me.dismissCountDown = me.dismissSecs;
                    }                    
                });
            },
            cerrarModalNuevoEditar(){
                this.modalNuevoEditar = false;
                this.articulo.codigo = '';                
                this.articulo.nombre = '';
                this.articulo.idcategoria = 0;
                this.articulo.idmarca = 0;
                this.articulo.precio_compra = '';
                this.articulo.precio_venta = '';
                this.articulo.stock = '';
                this.articulo.descripcion = '';   
                this.errorMsg = false;
                this.successMsg = false;
                this.txtErrorMsg = '';
                this.txtSuccessMsg = '';   
                this.errors = [];                          
            },   
            abrirModalEliminar(accion, data=[]) {
                this.modalEliminar = true;   
                this.articulo.id = data.id;
                this.articulo.nombre = data.nombre;
                
                switch(accion){
                    case 'activar':
                    {                        
                        this.tituloModalEliminar = 'Activar Artículo';
                        this.mensajeEliminar = '¿Desea activar este artículo?';                                                                        
                        this.tipoAccionEliminar = 1;                        
                        break;
                    }
                    case 'desactivar':
                    {                        
                        this.tituloModalEliminar = 'Desactivar Artículo';
                        this.mensajeEliminar = '¿Desea desactivar este artículo?';
                        this.tipoAccionEliminar = 2;                        
                        break;
                    }
                }               
            },
            activarArticulo() {
                let me = this;                

                axios.put(`${this.ruta}/articulo/activar/${this.articulo.id}`)
                .then(function (response) {
                    me.cerrarModalEliminar();                    
                    me.listarArticulo();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'El artículo fue activado satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                }).catch(function (error) {
                    //console.log(error);
                    me.cerrarModalEliminar();         
                    me.errorMsg = true;
                    me.txtErrorMsg = 'Error al activar el artículo.';
                    me.dismissCountDown = me.dismissSecs;                    
                });
            },
            desactivarArticulo() {
                let me = this;                

                axios.put(`${this.ruta}/articulo/desactivar/${this.articulo.id}`)
                .then(function (response) {
                    me.cerrarModalEliminar();                    
                    me.listarArticulo();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'El artículo fue desactivado satisfactoriamente.';
                    me.dismissCountDown = me.dismissSecs;
                }).catch(function (error) {
                    //console.log(error);
                    me.cerrarModalEliminar();         
                    me.errorMsg = true;
                    me.txtErrorMsg = 'Error al desactivar el artículo.';
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
                window.open(this.ruta + '/articulo/listarPdf','_blank');
            },
            onFiltered(filteredItems) {
                // Trigger pagination to update the number of buttons/pages due to filtering
                this.totalRows = filteredItems.length;
                this.currentPage = 1;
            },
            countDownChanged(dismissCountDown) {
                this.dismissCountDown = dismissCountDown;
            }
        },
        mounted() {
            this.listarArticulo();        
            this.getCategoriasActivas();
            this.getCategorias();
            this.getMarcasActivas();                
            this.getMarcas();                
        }
    }
</script>
<style scoped>
    /* Modal styles */
    .modal .modal-dialog {
        max-width: 500px;
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