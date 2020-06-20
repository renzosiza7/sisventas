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
                    { key: 'opciones', label: 'Opciones', class: 'text-center' },
                    { key : 'usuario', label : 'Usuario', sortable: true },                    
                    { key : 'nombre', label : 'Cliente' },                    
                    { key : 'tipo_comprobante', label : 'Tipo Doc.', class: 'text-center' },         
                    { key : 'serie_comprobante', label : 'Serie', class: 'text-center' },         
                    { key : 'num_comprobante', label : 'Correlativo', class: 'text-center' },         
                    { key : 'fecha_hora', label : 'Fecha Hora', class: 'text-center' },  
                    { key : 'total', label : 'Total', class: 'text-center' },                                 
                    { key : 'estado', label : 'Estado', class: 'text-center' },                                        
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
        methods : {
            listarVentaCaja() {
                let me = this;

                if(this.estado == 'Abierto') {
                    axios.get(`${this.ruta}/venta_caja`)
                        .then(function (response) {                                    
                            me.ventas = response.data;
                            me.totalRows = me.ventas.length;
                        })
                        .catch(function (error) {                    
                            console.log(error);
                    });
                }
                else {
                    axios.get(`${this.ruta}/venta_caja_cerrada`, {
                        params: {
                            'idcaja': this.idcaja
                        }
                    })
                        .then(function (response) {                                    
                            me.ventas = response.data;
                            me.totalRows = me.ventas.length;                            
                        })
                        .catch(function (error) {                    
                            console.log(error);
                    });
                }      
            },                                                    
            mostrarDetalle(){
                let me = this;
                me.listado = 0;

                me.venta.idcliente = '';
                me.venta.tipo_comprobante = 'BOLETA';
                me.venta.serie_comprobante = '';
                me.venta.num_comprobante = '';
                me.venta.impuesto = 0.18;
                me.venta.total = 0.0;
                me.idarticulo = 0;
                me.articulo = '';
                me.cantidad = 0;
                me.precio = 0;
                me.descuento = 0;
                me.arrayDetalle = [];
                me.errors = [];
            },
            ocultarDetalle() {
                this.listado = 1;
            },
            verVenta(id) {
                let me = this;
                me.venta.id = id;
                me.listado = 2;
                
                //Obtener los datos de la venta
                var objVentaT = {};
                var url = this.ruta + '/venta/obtenerCabecera?id=' + id;
                
                axios.get(url).then(function (response) {                    
                    objVentaT = response.data;

                    me.venta.cliente = objVentaT.nombre;
                    me.venta.fecha_hora = objVentaT.fecha_hora;
                    me.venta.tipo_comprobante = objVentaT.tipo_comprobante;
                    me.venta.serie_comprobante = objVentaT.serie_comprobante;
                    me.venta.num_comprobante = objVentaT.num_comprobante;
                    me.venta.impuesto = objVentaT.impuesto;
                    me.venta.total = objVentaT.total;                    
                })
                .catch(function (error) {
                    console.log(error);
                });

                //Obtener los datos de los detalles 
                var urld= this.ruta + '/venta/obtenerDetalles?id=' + id;
                
                axios.get(urld).then(function (response) {
                    //console.log(response);                    
                    me.arrayDetalle = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });               
            },            
            actualizarVentaCaja() {                              
                let me = this;                

                axios.put(`${this.ruta}/venta/actualizar_venta_caja/${this.venta.id}`, {    
                    'idcaja': this.idcaja,                    
                }).then(function (response) {
                    me.listado = 1;
                    me.listarVentaCaja();

                    if (me.venta.tipo_comprobante == 'TICKET') {
                        window.open(me.ruta + '/venta/pdfTicket/'+ me.venta.id, '_blank');
                    }
                    else {
                        window.open(me.ruta + '/venta/pdf/'+ me.venta.id, '_blank');
                    }

                    me.venta.idcliente = '';
                    me.venta.tipo_comprobante = 'BOLETA';
                    me.venta.serie_comprobante = '';
                    me.venta.num_comprobante = '';
                    me.venta.impuesto = 0.18;
                    me.venta.total = 0.0;
                    me.idarticulo = 0;
                    me.articulo = '';
                    me.cantidad = 0;
                    me.precio = 0;
                    me.stock = 0;
                    me.descuento = 0;
                    me.arrayDetalle = [];
                    me.errors = [];                    
                }).catch(function (error) {
                    console.log(error);
                    if (error.response.status==422) {
                        me.errors = error.response.data.errors;
                    }
                    else {                                           
                        me.errorMsg = true;
                        me.txtErrorMsg = 'Error al actualizar la venta en caja.';
                        me.dismissCountDown = me.dismissSecs;
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
                // Trigger pagination to update the number of buttons/pages due to filtering
                this.totalRows = filteredItems.length;
                this.currentPage = 1;
            },
            countDownChanged(dismissCountDown) {
                this.dismissCountDown = dismissCountDown;
            },
        },
        mounted() {
            this.listarVentaCaja();                          
        }
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