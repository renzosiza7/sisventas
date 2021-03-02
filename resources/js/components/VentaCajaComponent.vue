<template>
    <div>                
        <div class="card">
            <div class="card-header">
                <i class="fa fa-align-justify"></i> Caja - Ventas
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
                        :items="ventas"    
                        :current-page="currentPage"
                        :per-page="perPage"
                        :filter="filter"
                        :sort-by.sync="sortBy"
                        :sort-desc.sync="sortDesc"
                        :sort-direction="sortDirection"
                        @filtered="onFiltered"               
                    >
                        <template slot="opciones" slot-scope="row">
                            <b-button v-if="estado=='Abierto'" variant="success" size="sm" @click="verVenta(row.item.id)">
                                <i class="icon-check"></i>
                            </b-button>
                            <b-button v-else variant="primary" size="sm" @click="verVenta(row.item.id)">
                                <i class="icon-eye"></i>
                            </b-button>
                            <template v-if="row.item.tipo_comprobante=='TICKET'">
                                <button type="button" @click="pdfTicket(row.item.id)" class="btn btn-secondary btn-sm">
                                    <i class="icon-doc"></i>
                                </button>
                            </template>
                            <template v-else>
                                <button type="button" @click="pdfVenta(row.item.id)" class="btn btn-secondary btn-sm">
                                    <i class="icon-doc"></i>
                                </button>
                            </template>        
                        </template>
                        <template slot="estado" slot-scope="row">
                            <span v-if="row.item.estado == 'Emitido'" class="badge badge-warning">Emitido</span>                            
                            <span v-if="row.item.estado == 'Registrado'" class="badge badge-success">Registrado</span>                            
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
            <template v-else-if="listado==2">
                <div class="card-body">
                    <div class="form-group row border">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">Cliente</label>
                                <p v-text="venta.cliente"></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Fecha</label>
                                <p v-text="venta.fecha_hora"></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="">I.G.V.</label>
                            <p v-text="venta.impuesto"></p>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo Comprobante</label>
                                <p v-text="venta.tipo_comprobante"></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Serie Comprobante</label>
                                <p v-text="venta.serie_comprobante"></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número Comprobante</label>
                                <p v-text="venta.num_comprobante"></p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row border">
                        <div class="table-responsive col-md-12">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Artículo</th>
                                        <th>Precio venta</th>
                                        <th>Cantidad</th>
                                        <th>Descuento</th>
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
                                        <td v-text="detalle.descuento">
                                        </td>
                                        <td>
                                            {{(detalle.precio*detalle.cantidad-detalle.descuento).toFixed(2)}}
                                        </td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total Parcial:</strong></td>
                                        <td>S/. {{venta.totalParcial=(venta.total-venta.totalImpuesto).toFixed(2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total I.G.V.:</strong></td>
                                        <td>S/. {{venta.totalImpuesto=((venta.total*venta.impuesto)).toFixed(2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total Neto:</strong></td>
                                        <td>S/. {{venta.total}}</td>
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
                            <button type="button" @click="ocultarDetalle" class="btn btn-secondary">Cerrar</button>                            
                            <button v-if="estado=='Abierto'" type="button" @click="actualizarVentaCaja" class="btn btn-success">Registrar Venta</button>                            
                        </div>                        
                    </div>
                </div>
            </template>
        </div><!-- Fin ejemplo de tabla Listado -->                      
    </div>
</template>
<script>
    export default {
        props : ['ruta', 'idcaja', 'estado'],
        data() {
            return {
                objArticulo : {},
                arrayArticulo : [],
                idarticulo : 0,
                codigo : '',
                articulo : '',
                precio : 0,
                stock : 0,
                cantidad : 0,     
                descuento : 0,           
                criterioA:'nombre',
                buscarA: '',     

                venta : {
                    id : 0,
                    idcliente : '', 
                    cliente : '',                    
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
                    { key : 'nombre', label : 'Cliente' },                    
                    { key : 'tipo_comprobante', label : 'Tipo Doc.', class: 'text-center' },         
                    { key : 'serie_comprobante', label : 'Serie', class: 'text-center' },         
                    { key : 'num_comprobante', label : 'Correlativo', class: 'text-center' },         
                    { key : 'fecha_hora', label : 'Fecha Hora', class: 'text-center' },  
                    { key : 'total', label : 'Total', class: 'text-center' },                                 
                    { key : 'estado', label : 'Estado', class: 'text-center' },                                        
                    { key: 'opciones', label: 'Opciones', class: 'text-center' },
                ], 
                ventas : [],                
                arrayCliente: [],
                arrayDetalle : [],
                listado : 1, 
                modal : 0,
                tituloModal : '',
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
        computed : {
            calcularTotal: function(){
                var resultado = 0.0;
                
                for(var i=0; i < this.arrayDetalle.length; i++){
                    resultado = resultado+(this.arrayDetalle[i].precio*this.arrayDetalle[i].cantidad - this.arrayDetalle[i].descuento)
                }

                return resultado.toFixed(2);
            }
        },
        created() {
            this.listarVentaCaja()                        
        },
        methods : {
            listarVentaCaja() {                
                if(this.estado == 'Abierto') {
                    axios.get(`${this.ruta}/venta_caja`)
                        .then(response => {                                    
                            this.ventas = response.data;
                            this.totalRows = this.ventas.length;
                        })
                        .catch(error => {                    
                            console.log(error);
                    });
                }
                else {
                    axios.get(`${this.ruta}/venta_caja_cerrada`, {
                        params: {
                            'idcaja': this.idcaja
                        }
                    })
                        .then(response => {                                    
                            this.ventas = response.data;
                            this.totalRows = this.ventas.length;                            
                        })
                        .catch(error => {                    
                            console.log(error);
                    });
                }      
            },                                                    
            mostrarDetalle() {                
                this.listado = 0;
                this.venta.idcliente = '';
                this.venta.tipo_comprobante = 'BOLETA';
                this.venta.serie_comprobante = '';
                this.venta.num_comprobante = '';
                this.venta.impuesto = 0.18;
                this.venta.total = 0.0;
                this.idarticulo = 0;
                this.articulo = '';
                this.cantidad = 0;
                this.precio = 0;
                this.descuento = 0;
                this.arrayDetalle = [];
                this.errors = [];
            },
            ocultarDetalle() {
                this.listado = 1;
            },
            verVenta(id) {                
                this.venta.id = id;
                this.listado = 2;
                
                //Obtener los datos de la venta
                let objVentaT = {};                
                
                axios.get(`${this.ruta}/venta/obtenerCabecera?id=${id}`)
                    .then(function (response) {                    
                        objVentaT = response.data;

                        this.venta.cliente = objVentaT.nombre;
                        this.venta.fecha_hora = objVentaT.fecha_hora;
                        this.venta.tipo_comprobante = objVentaT.tipo_comprobante;
                        this.venta.serie_comprobante = objVentaT.serie_comprobante;
                        this.venta.num_comprobante = objVentaT.num_comprobante;
                        this.venta.impuesto = objVentaT.impuesto;
                        this.venta.total = objVentaT.total;                    
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                //Obtener los datos de los detalles                                 
                axios.get(`${this.ruta}/venta/obtenerDetalles?id=${id}`)
                    .then(response => {                        
                        this.arrayDetalle = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });               
            },            
            actualizarVentaCaja() {                                                         
                axios.put(`${this.ruta}/venta/actualizar_venta_caja/${this.venta.id}`, {    
                    'idcaja': this.idcaja,                    
                }).then(response => {
                    this.listado = 1;
                    this.listarVentaCaja();

                    if (this.venta.tipo_comprobante == 'TICKET') {
                        window.open(this.ruta + '/venta/pdfTicket/'+ this.venta.id, '_blank');
                    }
                    else {
                        window.open(this.ruta + '/venta/pdf/'+ this.venta.id, '_blank');
                    }

                    this.venta.idcliente = '';
                    this.venta.tipo_comprobante = 'BOLETA';
                    this.venta.serie_comprobante = '';
                    this.venta.num_comprobante = '';
                    this.venta.impuesto = 0.18;
                    this.venta.total = 0.0;
                    this.idarticulo = 0;
                    this.articulo = '';
                    this.cantidad = 0;
                    this.precio = 0;
                    this.stock = 0;
                    this.descuento = 0;
                    this.arrayDetalle = [];
                    this.errors = [];                    
                }).catch(error => {                    
                    if (error.response.status==422) {
                        this.errors = error.response.data.errors;
                    }
                    else {                                           
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al actualizar la venta en caja.';
                        this.dismissCountDown = this.dismissSecs;
                    }   
                });
            },           
            cargarPdf(){
                window.open(this.ruta + '/venta/listarPdf','_blank');
            },                   
            pdfVenta(id){
                window.open(this.ruta + '/venta/pdf/'+id, '_blank');
            },
            pdfTicket(id){
                window.open(this.ruta + '/venta/pdfTicket/'+ id + ',' + '_blank');
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