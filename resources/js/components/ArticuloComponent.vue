<template>
    <main class="main">        
        <ol class="breadcrumb">            
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item active">Artículos</li>
        </ol>
        <div class="container-fluid">            
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
                            <b-button size="sm" @click="row.toggleDetails">
                                <template v-if="row.detailsShowing">
                                    <i class="icon-minus"></i>
                                </template>
                                <template v-else>
                                    <i class="icon-plus"></i>    
                                </template>                                
                            </b-button>
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
                        <template v-slot:row-details="row">
                            <b-card>
                                <ul>                                    
                                    <li><b>Categoría:</b> {{ row.item.categoria }}</li>
                                    <li><b>Marca:</b> {{ row.item.marca }}</li>                                        
                                    <li><b>Precio de compra:</b> {{ row.item.precio_compra }}</li>
                                </ul>
                            </b-card>
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
                    <form role="form" @submit.prevent="tipoAccion == 1 ? registrarArticulo() : actualizarArticulo()">
                    <div class="modal-body">                        
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
                                    <input type="text" v-model="articulo.precio_compra" class="form-control text-center" id="compra">                                    
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
                                <div class="form-group col-md-6">
                                    <label for="descripcion">Descripción</label>
                                    <textarea v-model="articulo.descripcion" rows="1" id="descripcion" class="form-control"></textarea>                                                                
                                    <span v-if="errors.descripcion" class="error">{{ errors.descripcion[0] }}</span>
                                </div>    
                                <div class="form-group col-md-6">
                                    <label for="acceso">Acceso</label>
                                    <select v-model="articulo.acceso" class="form-control" id="acceso">
                                        <option value="privado" selected>Privado</option>
                                        <option value="publico">Público</option>
                                    </select>
                                </div>              
                            </div> 
                            <div class="form-row">
                                <div class="form-group" v-if="articulo.acceso == 'publico'">
                                    <!--<label for="imagen">Elige una imagen</label>-->
                                    <div v-if="articulo.imagen">
                                        <img :src="url_imagen" ref="mostrar_articulo_imagen" class="size_img">
                                    </div>
                                    <input ref="articulo_imagen" @change="adjuntarImagen" type="file" class="form-control-file" id="imagen" required>
                                    <span v-if="errors.imagen" class="error">{{ errors.imagen[0] }}</span>
                                </div>                            
                            </div>                                         
                        
                    </div>
                    <div class="modal-footer">
                        <b-button variant="secondary" @click="cerrarModalNuevoEditar">Cancelar</b-button>
                        <b-button type="submit" v-if="tipoAccion == 1" variant="success">Guardar</b-button>
                        <b-button type="submit" v-else variant="success">Actualizar</b-button>
                    </div>
                    </form>
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
                    acceso: 'privado',
                    imagen : '',
                },      
                categoriasActivas : [],
                categorias : [],
                marcasActivas : [],
                marcas : [],
                columnas: [                                                            
                    { key : 'codigo', label : 'Código', class: 'text-center' },
                    { key : 'nombre', label : 'Artículo', sortable: true },
                    //{ key : 'categoria', label : 'Categoría', sortable: true },
                    //{ key : 'marca', label : 'Marca', sortable: true },        
                    //{ key : 'precio_compra', label : 'P. Compra', class: 'text-center' },
                    { key : 'precio_venta', label : 'P. Venta', class: 'text-center' },            
                    { key : 'stock', label : 'Stock', class: 'text-center' },            
					{ key : 'acceso', label : 'Acceso', class: 'text-center' },            
                    { key : 'condicion', label : 'Condición', class: 'text-center' },            
                    { key: 'opciones', label: 'Opciones', class: 'text-center' },
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
                url_imagen : '',
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
            adjuntarImagen() {
                this.articulo.imagen = this.$refs.articulo_imagen.files[0]
                let reader = new FileReader()
                reader.addEventListener('load', function() {
                    this.$refs.mostrar_articulo_imagen.src = reader.result
                }.bind(this), false)
                reader.readAsDataURL(this.articulo.imagen)
            },
            listarArticulo() {               
                axios.get(`${this.ruta}/articulo`)
                .then(response => {                                                        
                    this.articulos = response.data;
                    this.totalRows = this.articulos.length;
                })
                .catch(error => {                    
                    console.log(error);
                });
            },
            getCategoriasActivas() {                
                axios.get(`${this.ruta}/categoria/select_categoria`)
                .then(response => {                                                        
                    this.categoriasActivas = response.data;                    
                })
                .catch(error => {                    
                    console.log(error);
                });
            },
            getCategorias() {              
                axios.get(`${this.ruta}/categoria`)
                .then(response => {                                                        
                    this.categorias = response.data;                    
                })
                .catch(error => {                    
                    console.log(error);
                });
            },
            getMarcasActivas() {                
                axios.get(`${this.ruta}/marca/select_marca`)
                .then(response => {                                                        
                    this.marcasActivas = response.data;                    
                })
                .catch(error => {                    
                    console.log(error);
                });
            },
            getMarcas() {                
                axios.get(`${this.ruta}/marca`)
                .then(response => {                                                        
                    this.marcas = response.data;                    
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
                        this.articulo.acceso = 'privado';
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
                        this.articulo.acceso = data.acceso;
                        this.articulo.imagen = data.imagen;
                        this.url_imagen = `${this.ruta}/img/articulos/${data.imagen}`;						 
                        this.tipoAccion = 2;
                        break;
                    }
                }
            },
            registrarArticulo() {                
                this.errors = [];
                let formData = new FormData();

                formData.append('codigo', this.articulo.codigo);                
                formData.append('nombre', this.articulo.nombre);                
                formData.append('idcategoria', this.articulo.idcategoria);                
                formData.append('idmarca', this.articulo.idmarca);                
                formData.append('precio_compra', this.articulo.precio_compra);                
                formData.append('precio_venta', this.articulo.precio_venta);                
                formData.append('stock', this.articulo.stock);                
                formData.append('descripcion', this.articulo.descripcion);                
                formData.append('acceso', this.articulo.acceso);     
                formData.append('imagen', this.articulo.imagen);               

                axios.post(`${this.ruta}/articulo/registrar`, formData,
                    {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    this.cerrarModalNuevoEditar();                    
                    this.listarArticulo();
                    this.successMsg = true;
                    this.txtSuccessMsg = 'El artículo fue agregado satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                                        
                }).catch(error => {                    
                    if (error.response.status==422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
                        this.cerrarModalNuevoEditar();                    
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al agregar el artículo.';
                        this.dismissCountDown = this.dismissSecs;
                    }                    
                });
            },
            actualizarArticulo() {                
                this.errors = [];
                let formData = new FormData();

                formData.append('codigo', this.articulo.codigo);                
                formData.append('nombre', this.articulo.nombre);                
                formData.append('idcategoria', this.articulo.idcategoria);                
                formData.append('idmarca', this.articulo.idmarca);                
                formData.append('precio_compra', this.articulo.precio_compra);                
                formData.append('precio_venta', this.articulo.precio_venta);                
                formData.append('stock', this.articulo.stock);                
                formData.append('descripcion', this.articulo.descripcion);                
                formData.append('acceso', this.articulo.acceso);     
                formData.append('imagen', this.articulo.imagen);
                formData.append('url_imagen', this.url_imagen);

                axios.post(`${this.ruta}/articulo/actualizar/${this.articulo.id}`, formData,
                    {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(response => {
                    this.cerrarModalNuevoEditar();                    
                    this.listarArticulo();
                    this.successMsg = true;
                    this.txtSuccessMsg = 'El artículo fue actualizado satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                }).catch(error => {                    
                    if (error.response.status==422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
                        this.cerrarModalNuevoEditar();                    
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al actualizar el artículo.';
                        this.dismissCountDown = this.dismissSecs;
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
                this.articulo.acceso = 'privado';   
                this.articulo.imagen = '';      
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
                axios.put(`${this.ruta}/articulo/activar/${this.articulo.id}`)
                .then(response => {
                    this.cerrarModalEliminar();                    
                    this.listarArticulo();
                    this.successMsg = true;
                    this.txtSuccessMsg = 'El artículo fue activado satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                }).catch(error => {                    
                    this.cerrarModalEliminar();         
                    this.errorMsg = true;
                    this.txtErrorMsg = 'Error al activar el artículo.';
                    this.dismissCountDown = this.dismissSecs;                    
                });
            },
            desactivarArticulo() {               
                axios.put(`${this.ruta}/articulo/desactivar/${this.articulo.id}`)
                .then(response => {
                    this.cerrarModalEliminar();                    
                    this.listarArticulo();
                    this.successMsg = true;
                    this.txtSuccessMsg = 'El artículo fue desactivado satisfactoriamente.';
                    this.dismissCountDown = this.dismissSecs;
                }).catch(error => {                    
                    this.cerrarModalEliminar();         
                    this.errorMsg = true;
                    this.txtErrorMsg = 'Error al desactivar el artículo.';
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
                window.open(this.ruta + '/articulo/listarPdf','_blank');
            },
            onFiltered(filteredItems) {                
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
    .size_img {
        width: 150px;
    }
</style>