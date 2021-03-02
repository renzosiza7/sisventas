<template>
    <div>           
        <div class="card p-3">            
            <b-card-group deck>
                <b-card>
                    <table class="table table-sm">
                          <caption>Inicio Caja</caption>
                        <tbody>                            
                            <tr>
                                <th>Monto inicial</th>
                                <td v-text="monto_inicial"></td>                               
                            </tr>                            
                        </tbody>
                    </table>
                    <table class="table table-sm">
                          <caption>Ingresos</caption>
                        <tbody>                            
                            <tr>
                                <th>Ventas</th>
                                <td v-text="data_caja.total_ventas"></td>                               
                            </tr>
                            <tr>
                                <th>Servicios</th>
                                <td v-text="data_caja.total_servicios"></td>                               
                            </tr>
                            <tr>
                                <th>Mov. Entradas</th>
                                <td v-text="data_caja.total_mov_entrada"></td>                               
                            </tr>                            
                        </tbody>
                    </table>
                    <table class="table table-sm">
                          <caption>Egresos</caption>
                        <tbody>                            
                            <tr>
                                <th>Mov. Salidas</th>
                                <td v-text="data_caja.total_mov_salida"></td>                               
                            </tr>
                            <tr>
                                <th>Gastos</th>
                                <td v-text="data_caja.total_gastos"></td>                                             
                            </tr>                            
                        </tbody>
                    </table>                    
                </b-card>

                <b-card v-if="estado == 'Abierto'">
                    <table class="table table-bordered table-sm">
                          <caption>Calculadora</caption>
                        <thead>
                            <tr>
                                <th class="text-center">Denominación</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-center">Total</th>
                            </tr>    
                        </thead>      
                        <tbody>                         
                            <tr>
                                <th class="text-right">1 sol</th>
                                <td><input @change="calcularTotal" type="number" min="0" v-model="cantidades[0]" class="form-control form-control-sm text-center"></td>                                             
                                <td class="text-center" v-text="cantidades[0] * valores[0]"></td>                                             
                            </tr>
                            <tr>
                                <th class="text-right">2 soles</th>
                                <td><input @change="calcularTotal" type="number" min="0" v-model="cantidades[1]" class="form-control form-control-sm text-center"></td>                                             
                                <td class="text-center" v-text="cantidades[1] * valores[1]"></td>                                             
                            </tr>
                            <tr>
                                <th class="text-right">5 soles</th>
                                <td><input @change="calcularTotal" type="number" min="0" v-model="cantidades[2]" class="form-control form-control-sm text-center"></td>                                             
                                <td class="text-center" v-text="cantidades[2] * valores[2]"></td>                                             
                            </tr>
                            <tr>
                                <th class="text-right">10 soles</th>
                                <td><input @change="calcularTotal" type="number" min="0" v-model="cantidades[3]" class="form-control form-control-sm text-center"></td>                                             
                                <td class="text-center" v-text="cantidades[3] * valores[3]"></td>                                             
                            </tr>
                            <tr>
                                <th class="text-right">20 soles</th>
                                <td><input @change="calcularTotal" type="number" min="0" v-model="cantidades[4]" class="form-control form-control-sm text-center"></td>                                             
                                <td class="text-center" v-text="cantidades[4] * valores[4]"></td>                                             
                            </tr>
                            <tr>
                                <th class="text-right">50 soles</th>
                                <td><input @change="calcularTotal" type="number" min="0" v-model="cantidades[5]" class="form-control form-control-sm text-center"></td>                                             
                                <td class="text-center" v-text="cantidades[5] * valores[5]"></td>                                             
                            </tr>
                            <tr>
                                <th class="text-right">100 soles</th>
                                <td><input @change="calcularTotal" type="number" min="0" v-model="cantidades[6]" class="form-control form-control-sm text-center"></td>                                             
                                <td class="text-center" v-text="cantidades[6] * valores[6]"></td>                                             
                            </tr>             
                            <tr>
                                <th class="text-right">200 soles</th>
                                <td><input @change="calcularTotal" type="number" min="0" v-model="cantidades[7]" class="form-control form-control-sm text-center"></td>                                             
                                <td class="text-center" v-text="cantidades[7] * valores[7]"></td>                                             
                            </tr>                            
                        </tbody>
                    </table>                                        
                </b-card>
                <b-card v-if="estado == 'Abierto'">                    
                    <table class="table">
                          <caption>Saldos</caption>
                        <tbody>                            
                            <tr>
                                <th>Saldo Sistema</th>
                                <td v-text="saldo_sistema.toFixed(2)"></td>                                             
                            </tr>
                            <tr>
                                <th>Saldo Caja</th>
                                <td v-text="saldo_caja.toFixed(2)" style="font-weight:bold"></td>                                
                            </tr>             
                            <tr>
                                <th>Diferencia</th>
                                <td v-text="diferencia"></td>                                
                            </tr>                            
                        </tbody>
                    </table>                    
                </b-card>
                <b-card v-else>                    
                    <table class="table">
                          <caption>Saldos</caption>
                        <tbody>                            
                            <tr>
                                <th>Saldo Sistema</th>
                                <td v-text="saldo_sistema_cerrada"></td>                                             
                            </tr>
                            <tr>
                                <th>Saldo Caja</th>
                                <td v-text="saldo_caja_cerrada" style="font-weight:bold"></td>                                
                            </tr>             
                            <tr>
                                <th>Diferencia</th>
                                <td v-text="diferencia_cerrada"></td>                                
                            </tr>                            
                        </tbody>
                    </table>                    
                </b-card>
            </b-card-group>
            <div class="text-center mt-3" v-if="estado == 'Abierto'">
                <b-button variant="success" @click="cerrarCaja">Cerrar Caja</b-button>
            </div>
        </div>    
    </div>
</template>
<script>
    export default {
        props : ['idcaja', 'ruta', 'estado'],
        data() {
            return {
                monto_inicial : 0.0,
                saldo_sistema : 0.0,
                saldo_caja : 0.0,                
                data_caja : [],
                cantidades : [0, 0, 0, 0, 0, 0, 0, 0],
                valores : [1, 2, 5, 10, 20, 50, 100, 200],    
                saldo_sistema_cerrada : 0.0,
                saldo_caja_cerrada : 0.0,
                diferencia_cerrada : 0.0,
            }
        },   
        computed: {
            diferencia: function () {
                var diferencia = this.saldo_sistema - this.saldo_caja

                return diferencia.toFixed(2)
            }
        },     
        created() {
            this.getDataCaja()            
        },
        methods : {            
            getDataCaja() {               
                axios.get(`${this.ruta}/caja/get_data`, {
                    params: {
                        'idcaja': this.idcaja
                    }
                })
                .then(response => {                                                        
                    this.data_caja = response.data; 
                    
                    this.monto_inicial = this.data_caja.inicio_caja.monto_inicial
                    
                    if (this.data_caja.inicio_caja.estado == 'Abierto') {
                        this.saldo_sistema = parseFloat(this.data_caja.inicio_caja.monto_inicial) +
                                           parseFloat(this.data_caja.total_ventas) +
                                           parseFloat(this.data_caja.total_servicios) + 
                                           parseFloat(this.data_caja.total_mov_entrada) - 
                                           parseFloat(this.data_caja.total_mov_salida) -
                                           parseFloat(this.data_caja.total_gastos);                    
                    }
                    else {
                        this.saldo_sistema_cerrada = this.data_caja.inicio_caja.saldo_sistema;
                        this.saldo_caja_cerrada = this.data_caja.inicio_caja.saldo_caja;
                        this.diferencia_cerrada = this.data_caja.inicio_caja.diferencia;
                    }                    
                })
                .catch(error => {                    
                    console.log(error);
                });
            },
            calcularTotal() {
                let total = 0;

                for(let i = 0; i < this.valores.length; i++) {
                    total += this.cantidades[i] * this.valores[i];
                }

                this.saldo_caja = total;
            },
            cerrarCaja() {
                Swal.fire({
                  title: "Cerrar Caja",
                  text: "¿Esta seguro de cerrar la caja?",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Si',
                  cancelButtonText: 'No',
               }).then((result) => {
                    if (result.value) {                                         
                        axios.put(`${this.ruta}/caja/cerrar/${this.idcaja}`, {                    
                            'total_ventas': this.data_caja.total_ventas,                    
                            'total_servicios': this.data_caja.total_servicios,                    
                            'total_mov_entrada': this.data_caja.total_mov_entrada,                    
                            'total_mov_salida': this.data_caja.total_mov_salida,                    
                            'total_gastos': this.data_caja.total_gastos,                    
                            'saldo_sistema': this.saldo_sistema,                    
                            'saldo_caja': this.saldo_caja,                    
                            'diferencia': this.diferencia,                    
                        }).then(response => {                               
                            Swal.fire(
                              'Éxito!',
                              'Caja cerrada con éxito',
                              'success'
                           ).then((result) => {
                              window.location.href = this.ruta + "/main";                                                            
                           })                           
                           
                        }).catch(error => {
                            Swal.fire(
                               'Error!',
                               'No se pudo cerrar la caja, Intentelo más tarde',
                               'error'
                           )                   
                        });
                    }
               })   
            }
            
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
    caption {
        color:red;
        caption-side: top;
    }
    
</style>
<style non-scoped>
    .style-chooser .vs__search::placeholder,
    .style-chooser .vs__dropdown-toggle,
    .style-chooser .vs__dropdown-menu {    
        max-height: 150px;
    }
</style>