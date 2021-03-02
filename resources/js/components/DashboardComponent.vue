<template>
<main class="main">    
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">Escritorio</a></li>
    </ol>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">                
            </div>
            <div class="car-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h4>Ingresos</h4>
                            </div>
                            <div class="card-content">
                                <div class="ct-chart">
                                    <canvas id="ingresos">                                                
                                    </canvas>
                                </div>
                            </div>
                            <div class="card-footer">
                                <p>Compras de los últimos meses.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-chart">
                            <div class="card-header">
                                <h4>Ventas</h4>
                            </div>
                            <div class="card-content">
                                <div class="ct-chart">
                                    <canvas id="ventas">                                                
                                    </canvas>
                                </div>
                            </div>
                            <div class="card-footer">
                                <p>Ventas de los últimos meses.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
</template>
<script>
    export default {
        props : ['ruta'],
        data (){
            return {
                varIngreso:null,
                charIngreso:null,
                ingresos:[],
                varTotalIngreso:[],
                varMesIngreso:[],                 
                varVenta:null,
                charVenta:null,
                ventas:[],
                varTotalVenta:[],
                varMesVenta:[],
            }
        },
        created() {
            this.getIngresos();
            this.getVentas();
        },
        methods : {
            getIngresos() {                                
                axios.get(`${this.ruta}/dashboard`)
                    .then(response => {
                        let respuesta = response.data;
                        this.ingresos = respuesta.ingresos;
                        //cargamos los datos del chart
                        this.loadIngresos();
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            getVentas() {                                
                axios.get(`${this.ruta}/dashboard`)
                    .then(response => {
                        let respuesta = response.data;
                        this.ventas = respuesta.ventas;
                        //cargamos los datos del chart
                        this.loadVentas();
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            loadIngresos(){                
                let meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"]; 
                this.ingresos.map(x =>{
                    this.varMesIngreso.push(meses[x.mes-1]);
                    this.varTotalIngreso.push(x.total);
                });
                this.varIngreso=document.getElementById('ingresos').getContext('2d');

                this.charIngreso = new Chart(this.varIngreso, {
                    type: 'bar',
                    data: {
                        labels: this.varMesIngreso,
                        datasets: [{
                            label: 'Ingresos',
                            data: this.varTotalIngreso,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            },
            loadVentas() {                
                let meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"]; 
                this.ventas.map(x => {
                    this.varMesVenta.push(meses[x.mes-1]);
                    this.varTotalVenta.push(x.total);
                });
                this.varVenta=document.getElementById('ventas').getContext('2d');

                this.charVenta = new Chart(this.varVenta, {
                    type: 'bar',
                    data: {
                        labels: this.varMesVenta,
                        datasets: [{
                            label: 'Ventas',
                            data: this.varTotalVenta,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                }
                            }]
                        }
                    }
                });
            }
        },        
    }
</script>
