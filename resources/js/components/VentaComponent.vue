<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">            
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item active">Ventas</li>
        </ol>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Ventas
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
                            <b-button variant="success" size="sm" @click="verVenta(row.item.id)">
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
                            <b-button variant="danger" size="sm" v-if="(row.item.estado=='Emitido' && iduser == row.item.idusuario) || (row.item.estado=='Emitido' && idrol == 1)" @click="abrirModalAnular(row.item)">
                                <i class="icon-trash"></i>
                            </b-button>                                                        
                        </template>
                        <template slot="estado" slot-scope="row">
                            <span v-if="row.item.estado == 'Emitido'" class="badge badge-warning">Emitido</span>
                            <span v-else-if="row.item.estado == 'Anulado'" class="badge badge-danger">Anulado</span>                            
                            <span v-else class="badge badge-success">Registrado</span>                            
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
                                <label for="">Cliente (*)</label>
                                <v-select
                                    v-model="venta.idcliente"
                                    @search="selectCliente"
                                    class="style-chooser" 
                                    :filterable="false"
                                    label="nombre"                                    
                                    :options="arrayCliente"
                                    placeholder="Buscar Clientes..."                
                                    :reduce="cliente => cliente.id"                 
                                >
                                    <template slot="no-options">
                                        Lo sentimos, no hay resultados de coincidencia.
                                    </template>
                                </v-select>
                                <span v-if="errors.idcliente" class="error">{{ errors.idcliente[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="">I.G.V.(*)</label>
                            <input type="text" class="form-control" v-model="venta.impuesto">
                            <span v-if="errors.impuesto" class="error">{{ errors.impuesto[0] }}</span>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo Comprobante (*)</label>
                                <select class="form-control" v-model="venta.tipo_comprobante">
                                    <option value="">Seleccione...</option>
                                    <option value="BOLETA">Boleta</option>
                                    <option value="FACTURA">Factura</option>
                                    <option value="TICKET">Ticket</option>
                                </select>
                                <span v-if="errors.tipo_comprobante" class="error">{{ errors.tipo_comprobante[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Serie Comprobante</label>
                                <input type="text" class="form-control" v-model="venta.serie_comprobante" placeholder="000x">
                                <span v-if="errors.serie_comprobante" class="error">{{ errors.serie_comprobante[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número Comprobante (*)</label>
                                <input type="text" class="form-control" v-model="venta.num_comprobante" placeholder="000xx">
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
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>Dcto.</label>
                                <input type="number" value="0" class="form-control" v-model="descuento">
                            </div>
                        </div>
                        <div class="col-md-1">
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
                                        <th style="width:15%">Precio venta</th>
                                        <th style="width:15%">Cantidad</th>
                                        <th style="width:10%">Descuento</th>
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
                                            <span style="color:red;" v-show="detalle.cantidad>detalle.stock">Stock: {{detalle.stock}}</span>
                                            <input v-model="detalle.cantidad" type="number" class="form-control">
                                        </td>
                                        <td>
                                            <span style="color:red;" v-show="detalle.descuento>(detalle.precio*detalle.cantidad)">Descuento superior</span>
                                            <input v-model="detalle.descuento" type="number" class="form-control">
                                        </td>
                                        <td>
                                            {{ (detalle.precio*detalle.cantidad - detalle.descuento).toFixed(2) }}
                                        </td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="5" align="right"><strong>Total Parcial:</strong></td>
                                        <td>S/. {{venta.totalParcial=(venta.total-venta.totalImpuesto).toFixed(2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="5" align="right"><strong>Total I.G.V.:</strong></td>
                                        <td>S/. {{venta.totalImpuesto=((venta.total*venta.impuesto)/(1+venta.impuesto)).toFixed(2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="5" align="right"><strong>Total Neto:</strong></td>
                                        <td>S/. {{ venta.total = calcularTotal }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="6">
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
                            <button type="button" class="btn btn-primary" @click="registrarVenta()">Emitir Venta</button>
                        </div>
                    </div>
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
                    resultado = resultado+(this.arrayDetalle[i].precio*this.arrayDetalle[i].cantidad - this.arrayDetalle[i].descuento)
                }

                return resultado.toFixed(2);
            }
        },
        methods : {
            listarVenta() {
                let me = this;

                axios.get(`${this.ruta}/venta`)
                .then(function (response) {                                    
                    me.ventas = response.data;
                    me.totalRows = me.ventas.length;
                })
                .catch(function (error) {                    
                    console.log(error);
                });
            },
            selectCliente(search, loading) {
                let me=this;                
                loading(true);
                var url= this.ruta + '/cliente/selectCliente?filtro=' + search;

                axios.get(url).then(function (response) {                                        
                    me.arrayCliente = response.data;                    
                    loading(false);
                })
                .catch(function (error) {
                    console.log(error);
                });
            },   
            buscarArticulo() {
                let me=this;
                var url= this.ruta + '/articulo/buscarArticuloVenta?filtro=' + me.codigo;

                axios.get(url).then(function (response) {                    
                    me.objArticulo = response.data;

                    if (me.objArticulo != null){
                        me.idarticulo = me.objArticulo.id;
                        me.articulo = me.objArticulo.nombre;                        
                        me.precio = me.objArticulo.precio_venta;
                        me.stock = me.objArticulo.stock;
                    }
                    else{
                        me.idarticulo=0;
                        me.articulo='No existe artículo';                        
                    }                    
                })
                .catch(function (error) {
                    console.log(error);
                });
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
            encuentra(id) {
                var sw = 0;

                for(var i = 0; i < this.arrayDetalle.length; i++){
                    if(this.arrayDetalle[i].idarticulo == id){
                        sw = true;
                    }
                }

                return sw;
            },
            agregarDetalle(){
                let me=this;
                
                if(me.idarticulo==0 || me.cantidad==0 || me.precio==0) {
                }
                else {
                    if (me.encuentra(me.idarticulo)){
                        swal({
                            type: 'error',
                            title: 'Error...',
                            text: 'Ese artículo ya se encuentra agregado!',
                        });
                    }
                    else {
                       if(me.cantidad > me.stock) {
                           swal({
                                type: 'error',
                                title: 'Error...',
                                text: 'No hay stock disponible!',
                            });
                       }
                       else {
                            me.arrayDetalle.push({
                                idarticulo: me.idarticulo,
                                articulo: me.articulo,
                                cantidad: me.cantidad,
                                precio: me.precio,
                                descuento: me.descuento,
                                stock: me.stock
                            });

                            me.codigo = '';
                            me.idarticulo = 0;
                            me.articulo = '';
                            me.cantidad = 0;
                            me.precio = 0; 
                            me.descuento = 0;
                            me.stock = 0;
                       }
                    }                    
                }   
            },
            eliminarDetalle(index) {
                let me = this;
                me.arrayDetalle.splice(index, 1);
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
                let me=this;                
                var url= this.ruta + '/articulo/listarArticuloVenta?buscar='+ buscar + '&criterio='+ criterio;
                axios.get(url).then(function (response) {
                    me.arrayArticulo = response.data;                     
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            agregarDetalleModal(data = []) {
                let me=this;

                if (me.encuentra(data['id'])) {
                    swal({
                            type: 'error',
                            title: 'Error...',
                            text: 'Ese artículo ya se encuentra agregado!',
                    });
                }
                else {
                    me.arrayDetalle.push({
                        idarticulo: data['id'],
                        articulo: data['nombre'],
                        cantidad: 1,
                        precio: data['precio_venta'],
                        descuento:0,
                        stock: data['stock']
                    }); 
                }
            },            
            validarVenta() {                
                var mensaje = '';
                let me = this;

                me.arrayDetalle.map(function(x) {

                    if (x.cantidad > x.stock) {
                        var mensaje = x.articulo + " con stock insuficiente";                        
                        swal({
                            type: 'error',
                            title: 'Error...',
                            text: mensaje
                        });

                        return;                
                    }
                    if (x.descuento > (x.precio * x.cantidad)) {                        
                        var mensaje = x.articulo + " con descuento superior al subtotal";                      
                        swal({
                            type: 'error',
                            title: 'Error...',
                            text: mensaje
                        });

                        return;
                    }
                });                              
            },
            registrarVenta() {                              
                let me = this;
                me.validarVenta();

                axios.post(this.ruta + '/venta/registrar',{
                    'idcliente': this.venta.idcliente,
                    'tipo_comprobante': this.venta.tipo_comprobante,
                    'serie_comprobante' : this.venta.serie_comprobante,
                    'num_comprobante' : this.venta.num_comprobante,
                    'impuesto' : this.venta.impuesto,
                    'total' : this.venta.total,
                    'data': this.arrayDetalle
                }).then(function (response) {
                    me.listado = 1;
                    me.listarVenta();

                    if (me.venta.tipo_comprobante == 'TICKET') {
                        window.open(me.ruta + '/venta/pdfTicket/'+response.data.id, '_blank');
                    }
                    else {
                        window.open(me.ruta + '/venta/pdf/'+response.data.id, '_blank');
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
                        me.txtErrorMsg = 'Error al registrar el venta.';
                        me.dismissCountDown = me.dismissSecs;
                    }   
                });
            },                                   
            abrirModalAnular(data=[]) {                
                this.venta.id = data.id;        
                
                Swal.fire({
                    title: "Anular Venta",
                    text: "¿Desea anular esta venta?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si',
                    cancelButtonText: 'No',
                }).then((result) => {
                    if (result.value) {                                       
                        this.desactivarVenta()                       
                    }
                })     
            },
            desactivarVenta() {
                let me = this;                

                axios.put(`${this.ruta}/venta/desactivar/${this.venta.id}`)
                .then(function (response) {                                    
                    me.listarVenta();
                    me.successMsg = true;
                    me.txtSuccessMsg = 'El venta fue anulado con éxito.';
                    me.dismissCountDown = me.dismissSecs;
                }).catch(function (error) {
                    //console.log(error);                    
                    me.errorMsg = true;
                    me.txtErrorMsg = 'Error al anular el venta.';
                    me.dismissCountDown = me.dismissSecs;                                        
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
            this.listarVenta();                          
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