<template>
    <main class="main">
        <!-- Breadcrumb -->
        <ol class="breadcrumb">            
            <li class="breadcrumb-item"><a href="/">Inicio</a></li>
            <li class="breadcrumb-item active">Servicios</li>
        </ol>
        <div class="container-fluid">
            <!-- Ejemplo de tabla Listado -->
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-align-justify"></i> Servicios
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
                        :items="servicios"    
                        :current-page="currentPage"
                        :per-page="perPage"
                        :filter="filter"
                        :sort-by.sync="sortBy"
                        :sort-desc.sync="sortDesc"
                        :sort-direction="sortDirection"
                        @filtered="onFiltered"               
                    >
                        <template slot="opciones" slot-scope="row">
                            <b-button variant="success" size="sm" @click="verServicio(row.item.id)">
                                <i class="icon-eye"></i>
                            </b-button>
                            <template v-if="row.item.tipo_comprobante=='TICKET'">
                                <button type="button" @click="pdfTicket(row.item.id)" class="btn btn-secondary btn-sm">
                                    <i class="icon-doc"></i>
                                </button>
                            </template>
                            <template v-else>
                                <button type="button" @click="pdfServicio(row.item.id)" class="btn btn-secondary btn-sm">
                                    <i class="icon-doc"></i>
                                </button>
                            </template>        
                            <b-button variant="danger" size="sm" v-if="(row.item.estado=='Por recoger' && iduser == row.item.idusuario) || (row.item.estado=='Por recoger' && idrol == 1)" @click="abrirModalAnular(row.item)">
                                <i class="icon-trash"></i>
                            </b-button>                                                        
                        </template>
                        <template slot="estado" slot-scope="row">
                            <span v-if="row.item.estado == 'Por recoger'" class="badge badge-warning">Por recoger</span>
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
                                    v-model="servicio.idcliente"
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
                            <input type="text" class="form-control" v-model="servicio.impuesto">
                            <span v-if="errors.impuesto" class="error">{{ errors.impuesto[0] }}</span>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo Comprobante (*)</label>
                                <select class="form-control" v-model="servicio.tipo_comprobante">
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
                                <input type="text" class="form-control" v-model="servicio.serie_comprobante" placeholder="000x">
                                <span v-if="errors.serie_comprobante" class="error">{{ errors.serie_comprobante[0] }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número Comprobante (*)</label>
                                <input type="text" class="form-control" v-model="servicio.num_comprobante" placeholder="000xx">
                                <span v-if="errors.num_comprobante" class="error">{{ errors.num_comprobante[0] }}</span>
                            </div>
                        </div>                        
                    </div>
                    <div class="form-group row border">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Descripción del servicio <span style="color:red;" v-show="descripcion==''">(*Ingrese)</span></label>                                
                                <input type="text" class="form-control" v-model="descripcion" placeholder="Ingrese descripción">                              
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
                                        <th style="width:35%">Descripción</th>
                                        <th style="width:15%">Precio</th>
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
                                        <td v-text="detalle.descripcion">
                                        </td>
                                        <td>
                                            <input v-model="detalle.precio" type="number" class="form-control">
                                        </td>
                                        <td>                                            
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
                                        <td>S/. {{servicio.totalParcial=(servicio.total-servicio.totalImpuesto).toFixed(2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="5" align="right"><strong>Total I.G.V.:</strong></td>
                                        <td>S/. {{servicio.totalImpuesto=((servicio.total*servicio.impuesto)/(1+servicio.impuesto)).toFixed(2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="5" align="right"><strong>Total Neto:</strong></td>
                                        <td>S/. {{ servicio.total = calcularTotal }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="6">
                                            No hay servicios agregados
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
                            <button type="button" class="btn btn-primary" @click="emitirServicio()">Emitir Servicio</button>
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
                                <p v-text="servicio.cliente"></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Fecha</label>
                                <p v-text="servicio.fecha_hora"></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="">I.G.V.</label>
                            <p v-text="servicio.impuesto"></p>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tipo Comprobante</label>
                                <p v-text="servicio.tipo_comprobante"></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Serie Comprobante</label>
                                <p v-text="servicio.serie_comprobante"></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número Comprobante</label>
                                <p v-text="servicio.num_comprobante"></p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row border">
                        <div class="table-responsive col-md-12">
                            <table class="table table-bordered table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Descripción</th>
                                        <th>Precio</th>
                                        <th>Cantidad</th>
                                        <th>Descuento</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody v-if="arrayDetalle.length">
                                    <tr v-for="detalle in arrayDetalle" :key="detalle.id">
                                        <td v-text="detalle.descripcion">
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
                                        <td>S/. {{servicio.totalParcial=(servicio.total-servicio.totalImpuesto).toFixed(2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total I.G.V.:</strong></td>
                                        <td>S/. {{servicio.totalImpuesto=((servicio.total*servicio.impuesto)).toFixed(2)}}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total Neto:</strong></td>
                                        <td>S/. {{servicio.total}}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan="4">
                                            No hay servicios agregados
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
    </main>
</template>
<script>
    export default {
        props : ['ruta', 'idrol', 'iduser'],
        data() {
            return {                                
                descripcion : '',               
                precio : 0,
                cantidad : 0,     
                descuento : 0,          

                servicio : {
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
                servicios : [],                
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
        created() {
            this.listarServicio();                                
        },
        methods : {
            listarServicio() {                
                axios.get(`${this.ruta}/servicio`)
                    .then(response => {                                    
                        this.servicios = response.data;
                        this.totalRows = this.servicios.length;
                    })
                    .catch(error => {                    
                        console.log(error);
                    });
            },
            selectCliente(search, loading) {                               
                loading(true);                

                axios.get(`${this.ruta}/cliente/selectCliente?filtro=${search}`)
                    .then(response => {                                        
                        this.arrayCliente = response.data;                    
                        loading(false);
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },                                          
            mostrarDetalle() {                
                this.listado = 0;
                this.servicio.idcliente = '';
                this.servicio.tipo_comprobante = 'BOLETA';
                this.servicio.serie_comprobante = '';
                this.servicio.num_comprobante = '';
                this.servicio.impuesto = 0.18;
                this.servicio.total = 0.0;                
                this.descripcion = '';
                this.cantidad = 0;
                this.precio = 0;
                this.descuento = 0;
                this.arrayDetalle = [];
                this.errors = [];
            },
            ocultarDetalle() {
                this.listado = 1;
            },
            verServicio(id) {                
                this.listado = 2;
                
                //Obtener los datos del servicio
                let objServicioT = {};                
                
                axios.get(`${this.ruta}/servicio/obtenerCabecera?id=${id}`)
                    .then(response => {                    
                        objServicioT = response.data;

                        this.servicio.cliente = objServicioT.nombre;
                        this.servicio.fecha_hora = objServicioT.fecha_hora;
                        this.servicio.tipo_comprobante = objServicioT.tipo_comprobante;
                        this.servicio.serie_comprobante = objServicioT.serie_comprobante;
                        this.servicio.num_comprobante = objServicioT.num_comprobante;
                        this.servicio.impuesto = objServicioT.impuesto;
                        this.servicio.total = objServicioT.total;                    
                    })
                    .catch(error => {
                        console.log(error);
                    });

                //Obtener los datos de los detalles                                
                axios.get(`${this.ruta}/servicio/obtenerDetalles?id=${id}`)
                    .then(response => {                    
                        this.arrayDetalle = response.data;
                    })
                    .catch(error => {
                        console.log(error);
                    });               
            },            
            agregarDetalle(){                                
                if(this.descripcion=='' || this.cantidad==0 || this.precio==0) {
                }
                else {                                        
                    this.arrayDetalle.push({                        
                        descripcion: this.descripcion,
                        cantidad: this.cantidad,
                        precio: this.precio,
                        descuento: this.descuento,                        
                    });
                    
                    this.descripcion = '';
                    this.cantidad = 0;
                    this.precio = 0; 
                    this.descuento = 0;                                                
                }   
            },
            eliminarDetalle(index) {                
                this.arrayDetalle.splice(index, 1);
            },                                                      
            validarServicio() {                                               
                this.arrayDetalle.map(x => {
                    
                    if (x.descuento > (x.precio * x.cantidad)) {                        
                        let mensaje = x.descripcion + " con descuento superior al subtotal";                      
                        swal.fire({
                            type: 'error',
                            title: 'Error...',
                            text: mensaje
                        });

                        return;
                    }
                });                              
            },
            emitirServicio() {                                              
                this.validarServicio();

                axios.post(this.ruta + '/servicio/registrar',{
                    'idcliente': this.servicio.idcliente,
                    'tipo_comprobante': this.servicio.tipo_comprobante,
                    'serie_comprobante' : this.servicio.serie_comprobante,
                    'num_comprobante' : this.servicio.num_comprobante,
                    'impuesto' : this.servicio.impuesto,
                    'total' : this.servicio.total,
                    'data': this.arrayDetalle
                }).then(response => {
                    this.listado = 1;
                    this.listarServicio();

                    if (this.servicio.tipo_comprobante == 'TICKET') {
                        window.open(this.ruta + '/servicio/pdfTicket/'+response.data.id, '_blank');
                    }
                    else {
                        window.open(this.ruta + '/servicio/pdf/'+response.data.id, '_blank');
                    }

                    this.servicio.idcliente = '';
                    this.servicio.tipo_comprobante = 'BOLETA';
                    this.servicio.serie_comprobante = '';
                    this.servicio.num_comprobante = '';
                    this.servicio.impuesto = 0.18;
                    this.servicio.total = 0.0;                    

                    this.descripcion = '';
                    this.cantidad = 0;
                    this.precio = 0;                    
                    this.descuento = 0;
                    this.arrayDetalle = [];
                    this.errors = [];                    
                }).catch(error => {                    
                    if (error.response.status==422) {
                        this.errors = error.response.data.errors;
                    }
                    else {                                           
                        this.errorMsg = true;
                        this.txtErrorMsg = 'Error al registrar el servicio.';
                        this.dismissCountDown = this.dismissSecs;
                    }   
                });
            },                                   
            abrirModalAnular(data=[]) {                
                this.servicio.id = data.id;                

                Swal.fire({
                    title: "Anular Servicio",
                    text: "¿Desea anular este servicio?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si',
                    cancelButtonText: 'No',
                }).then((result) => {
                    if (result.value) {                                       
                        this.desactivarServicio()                       
                    }
                })                   
                
            },
            desactivarServicio() {                           
                axios.put(`${this.ruta}/servicio/desactivar/${this.servicio.id}`)
                .then(response => {                                     
                    this.listarServicio();
                    this.successMsg = true;
                    this.txtSuccessMsg = 'El servicio fue anulado con éxito.';
                    this.dismissCountDown = this.dismissSecs;
                }).catch(error => {                                  
                    this.errorMsg = true;
                    this.txtErrorMsg = 'Error al anular el servicio.';
                    this.dismissCountDown = this.dismissSecs;                                        
                });
            },                       
            cargarPdf(){
                window.open(this.ruta + '/servicio/listarPdf','_blank');
            },                   
            pdfServicio(id){
                window.open(this.ruta + '/servicio/pdf/'+id, '_blank');
            },
            pdfTicket(id){
                window.open(this.ruta + '/servicio/pdfTicket/'+ id + ',' + '_blank');
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