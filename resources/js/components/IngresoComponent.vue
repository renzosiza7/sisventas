<template>
    <main class="main">        
        <ol class="breadcrumb">            
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item active">Ingresos</li>
        </ol>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Ingresos
                    <b-button variant="outline-success" @click="mostrarDetalle()">                    
                        <i class="fa fa-plus"></i> Nuevo               
                    </b-button>
                    <button type="button" @click="cargarPdf()" class="btn btn-info">
                        <i class="icon-doc"></i>&nbsp;Reporte
                    </button>
                </div>
                <template v-if="listado==1">
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
                        :items="ingresos"    
                        :current-page="currentPage"
                        :per-page="perPage"
                        :filter="filter"
                        :sort-by.sync="sortBy"
                        :sort-desc.sync="sortDesc"
                        :sort-direction="sortDirection"
                        @filtered="onFiltered"               
                    >
                        <template slot="opciones" slot-scope="row">
                            <b-button variant="warning" size="sm" @click="verIngreso(row.item.id)">
                                <i class="icon-eye"></i>
                            </b-button>
                            <button type="button" @click="pdfIngreso(row.item.id)" class="btn btn-info btn-sm">
                                <i class="icon-doc"></i>
                            </button>
                            <b-button variant="danger" size="sm" v-if="(row.item.estado=='Registrado' && iduser == row.item.idusuario) || (row.item.estado=='Registrado' && idrol == 1)" @click="abrirModalAnular(row.item)">
                                <i class="icon-trash"></i>
                            </b-button>                                                        
                        </template>
                        <template slot="estado" slot-scope="row">
                            <span v-if="row.item.estado == 'Registrado'" class="badge badge-success">Registrado</span>
                            <span v-else class="badge badge-danger">Anulado</span>                            
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
                </template>
                <template v-else-if="listado==0">
                <div class="card-body">
                    <div class="form-group row border">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="">Proveedor (*)</label>
                                <v-select
                                    v-model="ingreso.idproveedor"
                                    @search="selectProveedor"
                                    class="style-chooser" 
                                    :filterable="true"
                                    label="nombre"                                    
                                    :options="arrayProveedor"
                                    placeholder="Buscar Proveedores..."                
                                    :reduce="proveedor => proveedor.id"                 
                                >
                                    <template slot="no-options">
                                        Lo sentimos, no hay resultados de coincidencia.
                                    </template>
                                </v-select>
                                <span v-if="errors.idproveedor" class="error">{{ errors.idproveedor[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="">I.G.V.(*)</label>
                            <input type="text" class="form-control" v-model="ingreso.impuesto">
                            <span v-if="errors.impuesto" class="error">{{ errors.impuesto[0] }}</span>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo Comprobante (*)</label>
                                <select class="form-control" v-model="ingreso.tipo_comprobante">
                                    <option value="">Seleccione...</option>
                                    <option value="BOLETA">Boleta</option>
                                    <option value="FACTURA">Factura</option>                                    
                                </select>
                                <span v-if="errors.tipo_comprobante" class="error">{{ errors.tipo_comprobante[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Serie Comprobante</label>
                                <input type="text" class="form-control" v-model="ingreso.serie_comprobante" placeholder="000x">
                                <span v-if="errors.serie_comprobante" class="error">{{ errors.serie_comprobante[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número Comprobante (*)</label>
                                <input type="text" class="form-control" v-model="ingreso.num_comprobante" placeholder="000xx">
                                <span v-if="errors.num_comprobante" class="error">{{ errors.num_comprobante[0] }}</span>
                            </div>
                        </div>                        
                    </div>
                    <div class="form-group row border">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Artículo <span style="color:red;" v-show="idarticulo==0">(*Seleccione)</span></label>
                                <div class="form-inline">
                                    <input type="text" class="form-control" v-model="codigo" @keyup.enter="buscarArticulo()" placeholder="Ingrese artículo">
                                    <button @click="abrirModal()" class="btn btn-primary">...</button>
                                    <input type="text" readonly class="form-control" v-model="articulo">
                                </div>                                    
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Precio <span style="color:red;" v-show="precio==0">(*Ingrese)</span></label>
                                <input type="number" value="0" step="any" class="form-control" v-model="precio">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Cantidad <span style="color:red;" v-show="cantidad==0">(*Ingrese)</span></label>
                                <input type="number" value="0" class="form-control" v-model="cantidad">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button @click="agregarDetalle()" class="btn btn-success form-control btnagregar"><i class="icon-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row border">
                        <div class="table-responsive col-md-12">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th style="width:10%" class="text-center">Opciones</th>
                                        <th style="width:35%">Artículo</th>
                                        <th style="width:20%">Precio compra</th>
                                        <th style="width:20%">Cantidad</th>
                                        <th style="width:15%">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody v-if="arrayDetalle.length">
                                    <tr v-for="(detalle, index) in arrayDetalle" :key="detalle.id">
                                        <td class="text-center">
                                            <button @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm">
                                                <i class="icon-close"></i>
                                            </button>
                                        </td>
                                        <td v-text="detalle.articulo">
                                        </td>
                                        <td>
                                            <input v-model="detalle.precio" type="number" class="form-control">
                                        </td>
                                        <td>
                                            <input v-model="detalle.cantidad" type="number" class="form-control">
                                        </td>
                                        <td>
                                            {{ (detalle.precio*detalle.cantidad).toFixed(2) }}
                                        </td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total Parcial:</strong></td>
                                        <td>S/. {{ingreso.totalParcial=(ingreso.total-ingreso.totalImpuesto).toFixed(2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total I.G.V.:</strong></td>
                                        <td>S/. {{ingreso.totalImpuesto=((ingreso.total*ingreso.impuesto)/(1+ingreso.impuesto)).toFixed(2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total Neto:</strong></td>
                                        <td>S/. {{ ingreso.total = calcularTotal }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="5">
                                            No hay artículos agregados
                                        </td>
                                    </tr>
                                </tbody>                                    
                            </table>
                            <span v-if="errors.data" class="error">{{ errors.data[0] }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="button" @click="ocultarDetalle()" class="btn btn-secondary">Cerrar</button>
                            <button type="button" class="btn btn-primary" @click="registrarIngreso()">Registrar Compra</button>
                        </div>
                    </div>
                </div>
                </template>
                <template v-else-if="listado==2">
                <div class="card-body">
                    <div class="form-group row border">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Proveedor</label>
                                <p v-text="ingreso.proveedor"></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Fecha</label>
                                <p v-text="ingreso.fecha_hora"></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="">I.G.V.</label>
                            <p v-text="ingreso.impuesto"></p>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo Comprobante</label>
                                <p v-text="ingreso.tipo_comprobante"></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Serie Comprobante</label>
                                <p v-text="ingreso.serie_comprobante"></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número Comprobante</label>
                                <p v-text="ingreso.num_comprobante"></p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row border">
                        <div class="table-responsive col-md-12">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Artículo</th>
                                        <th>Precio compra</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody v-if="arrayDetalle.length">
                                    <tr v-for="detalle in arrayDetalle" :key="detalle.id">
                                        <td v-text="detalle.articulo">
                                        </td>
                                        <td v-text="detalle.precio">
                                        </td>
                                        <td v-text="detalle.cantidad">
                                        </td>
                                        <td>
                                            {{(detalle.precio*detalle.cantidad).toFixed(2)}}
                                        </td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="3" align="right"><strong>Total Parcial:</strong></td>
                                        <td>S/. {{ingreso.totalParcial=(ingreso.total-ingreso.totalImpuesto).toFixed(2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="3" align="right"><strong>Total I.G.V.:</strong></td>
                                        <td>S/. {{ingreso.totalImpuesto=((ingreso.total*ingreso.impuesto)).toFixed(2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="3" align="right"><strong>Total Neto:</strong></td>
                                        <td>S/. {{ingreso.total}}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="4">
                                            No hay artículos agregados
                                        </td>
                                    </tr>
                                </tbody>                                    
                            </table>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <button type="button" @click="ocultarDetalle()" class="btn btn-secondary">Cerrar</button>                            
                        </div>
                    </div>
                </div>
                </template>
            </div><!-- Fin ejemplo de tabla Listado -->
        </div>       

        <!--Inicio del modal agregar/actualizar-->
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-primary modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <select class="form-control col-md-4" v-model="criterioA">
                                        <option value="nombre">Nombre</option>                                        
                                        <option value="codigo">Código</option>
                                        <option value="descripcion">Descripción</option>
                                    </select>
                                    <input type="text" v-model="buscarA" @keyup.enter="listarArticulo(buscarA,criterioA)" class="form-control" placeholder="Texto a buscar">
                                    <button type="submit" @click="listarArticulo(buscarA,criterioA)" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Acción</th>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Marca</th>
                                        <th>Precio Venta</th>
                                        <th>Stock</th>                                            
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="articulo in arrayArticulo" :key="articulo.id">
                                        <td class="text-center">
                                            <button type="button" @click="agregarDetalleModal(articulo)" class="btn btn-success btn-sm">
                                            <i class="icon-check"></i>
                                            </button>
                                        </td>
                                        <td v-text="articulo.codigo"></td>
                                        <td v-text="articulo.nombre"></td>
                                        <td v-text="articulo.categoria"></td>
                                        <td v-text="articulo.marca"></td>
                                        <td class="text-center" v-text="articulo.precio_venta"></td>
                                        <td class="text-center" v-text="articulo.stock"></td>                                            
                                    </tr>                                
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>                            
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--Fin del modal-->         
    </main>
</template>
<script>
    export default {
        props : ['ruta', 'idrol', 'iduser'],
        data() {
            return {
                objArticulo : {},
                arrayArticulo : [],
                idarticulo : 0,
                codigo : '',
                articulo : '',
                precio : 0,
                cantidad : 0,           
                criterioA:'nombre',
                buscarA: '',
                ingreso : {
                    id : 0,
                    idproveedor : '', 
                    proveedor : '',                    
                    fecha_hora:'',                                     
                    tipo_comprobante : 'BOLETA',
                    serie_comprobante : '',
                    num_comprobante : '',                    
                    impuesto : 0.18,
                    total : 0.0,                    
                    totalImpuesto: 0.0,
                    totalParcial: 0.0,
                },
                columnas: [                                                           
                    { key : 'usuario', label : 'Usuario', sortable: true },                    
                    { key : 'proveedor', label : 'Proveedor' },                    
                    { key : 'tipo_comprobante', label : 'Tipo Doc.', class: 'text-center' },         
                    { key : 'serie_comprobante', label : 'Serie', class: 'text-center' },         
                    { key : 'num_comprobante', label : 'Correlativo', class: 'text-center' },         
                    { key : 'fecha_hora', label : 'Fecha Hora', class: 'text-center' },  
                    { key : 'total', label : 'Total', class: 'text-center' },                                 
                    { key : 'estado', label : 'Estado', class: 'text-center' },                    
                    { key: 'opciones', label: 'Opciones', class: 'text-center' }, 
                ], 
                ingresos : [],                
                arrayProveedor: [],
                arrayDetalle : [],
                listado : 1, 
                modal : 0,
                tituloModal : '',
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
        computed : {
            calcularTotal: function(){
                var resultado = 0.0;
                
                for(var i=0; i < this.arrayDetalle.length; i++){
                    resultado = resultado+(this.arrayDetalle[i].precio*this.arrayDetalle[i].cantidad)
                }

                return resultado.toFixed(2);
            }
        },
        created() {
            this.listarIngreso();                          
        },
        methods : {
            listarIngreso() {                
                axios.get(`${this.ruta}/ingreso`)
                .then(response => {                                    
                    this.ingresos = response.data;
                    this.totalRows = this.ingresos.length;
                })
                .catch(error => {                    
                    console.log(error);
                });
            },
            selectProveedor(search, loading) {                     
                loading(true)

                axios.get(`${this.ruta}/proveedor/selectProveedor?filtro=${search}`)
                    .then(response => {                                        
                        this.arrayProveedor = response.data;                    
                        loading(false);
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },   
            buscarArticulo() {                                
                axios.get(`${this.ruta}/articulo/buscarArticulo?filtro=${this.codigo}`)
                    .then(response => {                    
                        this.objArticulo = response.data;

                        if (this.objArticulo != null){
                            this.idarticulo = this.objArticulo.id;
                            this.articulo = this.objArticulo.nombre;                        
                        }
                        else{
                            this.idarticulo=0;
                            this.articulo='No existe artículo';                        
                        }                    
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },                             
            mostrarDetalle() {                
                this.listado = 0;
                this.ingreso.idproveedor = '';
                this.ingreso.tipo_comprobante = 'BOLETA';
                this.ingreso.serie_comprobante = '';
                this.ingreso.num_comprobante = '';
                this.ingreso.impuesto = 0.18;
                this.ingreso.total = 0.0;
                this.idarticulo = 0;
                this.articulo = '';
                this.cantidad = 0;
                this.precio = 0;
                this.arrayDetalle = [];
                this.errors = [];
            },
            ocultarDetalle(){
                this.listado = 1;
            },
            verIngreso(id) {                
                this.listado = 2;
                
                //Obtener los datos del ingreso
                let objIngresoT = {};                
                
                axios.get(`${this.ruta}/ingreso/obtenerCabecera?id=${id}`)
                    .then(response => {                    
                        objIngresoT = response.data;

                        this.ingreso.proveedor = objIngresoT.nombre;
                        this.ingreso.fecha_hora = objIngresoT.fecha_hora;
                        this.ingreso.tipo_comprobante = objIngresoT.tipo_comprobante;
                        this.ingreso.serie_comprobante = objIngresoT.serie_comprobante;
                        this.ingreso.num_comprobante = objIngresoT.num_comprobante;
                        this.ingreso.impuesto = objIngresoT.impuesto;
                        this.ingreso.total = objIngresoT.total;                    
                    })
                    .catch(error => {
                        console.log(error);
                    });

                //Obtener los datos de los detalles                             
                axios.get(`${this.ruta}/ingreso/obtenerDetalles?id=${id}`)
                    .then(response => {                        
                        this.arrayDetalle = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });               
            },
            encuentra(id) {
                let sw = 0;

                for(let i = 0; i < this.arrayDetalle.length; i++){
                    if(this.arrayDetalle[i].idarticulo == id){
                        sw = true;
                    }
                }

                return sw;
            },
            agregarDetalle() {                                
                if(this.idarticulo==0 || this.cantidad==0 || this.precio==0) {
                }
                else {
                    if (this.encuentra(this.idarticulo)){
                        swal.fire({
                            type: 'error',
                            title: 'Error...',
                            text: 'Ese artículo ya se encuentra agregado!',
                        });
                    }
                    else {
                       this.arrayDetalle.push({
                            idarticulo: this.idarticulo,
                            articulo: this.articulo,
                            cantidad: this.cantidad,
                            precio: this.precio
                        });

                        this.codigo = '';
                        this.idarticulo = 0;
                        this.articulo = '';
                        this.cantidad = 0;
                        this.precio = 0; 
                    }                    
                }   
            },
            eliminarDetalle(index) {                
                this.arrayDetalle.splice(index, 1);
            },
            cerrarModal() {
                this.modal=0;
                this.tituloModal='';
                this.criterioA = 'nombre';
                this.buscarA = ''                
            }, 
            abrirModal(){          
                this.arrayArticulo = [];                     
                this.modal = 1;
                this.tituloModal = 'Seleccione uno o varios artículos';
            },            
            listarArticulo(buscar,criterio) {                                     
                axios.get(`${this.ruta}/articulo/listarArticulo?buscar=${buscar}&criterio=${criterio}`)
                    .then(response => {
                        this.arrayArticulo = response.data;                     
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            agregarDetalleModal(data = []) {                
                if (this.encuentra(data['id'])) {
                    swal.fire({
                            type: 'error',
                            title: 'Error...',
                            text: 'Ese artículo ya se encuentra agregado!',
                    });
                }
                else {
                    this.arrayDetalle.push({
                        idarticulo: data['id'],
                        articulo: data['nombre'],
                        cantidad: 1,
                        precio: 1
                    }); 
                }
            },            
            registrarIngreso() {                                              
                axios.post(this.ruta + '/ingreso/registrar',{
                    'idproveedor': this.ingreso.idproveedor,
                    'tipo_comprobante': this.ingreso.tipo_comprobante,
                    'serie_comprobante' : this.ingreso.serie_comprobante,
                    'num_comprobante' : this.ingreso.num_comprobante,
                    'impuesto' : this.ingreso.impuesto,
                    'total' : this.ingreso.total,
                    'data': this.arrayDetalle
                }).then(response => {
                    this.listado = 1;
                    this.listarIngreso();
                    window.open(this.ruta + '/ingreso/pdf/'+response.data.id, '_blank');
                    this.ingreso.idproveedor = '';
                    this.ingreso.tipo_comprobante = 'BOLETA';
                    this.ingreso.serie_comprobante = '';
                    this.ingreso.num_comprobante = '';
                    this.ingreso.impuesto = 0.18;
                    this.ingreso.total = 0.0;
                    this.idarticulo = 0;
                    this.articulo = '';
                    this.cantidad = 0;
                    this.precio = 0;
                    this.arrayDetalle = [];
                    this.errors = [];
                }).catch(error => {                    
                    if (error.response.status==422) {
                        this.errors = error.response.data.errors;
                    }
                    else {                                           
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al registrar el ingreso.';
                        this.dismissCountDown = this.dismissSecs;
                    }   
                });
            },                                   
            abrirModalAnular(data=[]) {
                this.ingreso.id = data.id;      
                
                swal.fire({
                    title: "Anular Ingreso",
                    text: "¿Desea anular este ingreso?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si',
                    cancelButtonText: 'No',
                }).then((result) => {
                    if (result.value) {                                       
                        this.desactivarIngreso()                       
                    }
                })                
                                                              
            },
            desactivarIngreso() {                         
                axios.put(`${this.ruta}/ingreso/desactivar/${this.ingreso.id}`)
                    .then(response => {                    
                        this.listarIngreso();
                        this.successMsg = true;
                        this.txtSuccessMsg = 'El ingreso fue anulado con éxito.';
                        this.dismissCountDown = this.dismissSecs;
                    }).catch(error => {                               
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al anular el ingreso.';
                        this.dismissCountDown = this.dismissSecs;                                        
                    });
            },                                           
            cargarPdf(){
                window.open(this.ruta + '/ingreso/listarPdf','_blank');
            },     
            pdfIngreso(id){
                window.open(this.ruta + '/ingreso/pdf/'+ id + ',' + '_blank');
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
        max-width: 800px;
        margin: 3.75rem auto;
    }
    .modal2 .modal-dialog {
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
        font-weight: 600;
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
    @media (min-width: 600px) {
        .btnagregar {
            margin-top: 2rem;
        }
    }
</style>