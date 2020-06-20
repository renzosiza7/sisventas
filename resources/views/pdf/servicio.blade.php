<!DOCTYPE>
<html>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de Servicio</title>
    <style>
        body {
        /*position: relative;*/
        /*width: 16cm;  */
        /*height: 29.7cm; */
        /*margin: 0 auto; */
        /*color: #555555;*/
        /*background: #FFFFFF; */
        font-family: Arial, sans-serif; 
        font-size: 14px;
        /*font-family: SourceSansPro;*/
        }

        #logo{
        float: left;
        margin-top: 1%;
        margin-left: 2%;
        margin-right: 2%;
        }

        #imagen{
        width: 150px;
        }

        #datos{
        float: left;
        margin-top: 0%;
        margin-left: 2%;
        margin-right: 2%;
        /*text-align: justify;*/
        }

        #encabezado{
        text-align: center;        
        margin-right: 50%;
        font-size: 15px;
        }

        #fact{
        /*position: relative;*/
        float: right;        
        margin-left: 2%;
        margin-right: 2%;
        font-size: 15px;
        }

        section{
        clear: left;
        }

        #cliente{
        text-align: left;        
        }

        #facliente{
        width: 40%;
        border-collapse: collapse;
        border-spacing: 0;        
        margin-top: 15px;
        }

        #fac, #fv, #fa{
        color: #FFFFFF;
        font-size: 15px;
        }

        #facliente thead{
        padding: 20px;
        background: #2183E3;
        text-align: left;
        border-bottom: 1px solid #FFFFFF;  
        }

        #facvendedor{
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 15px;
        }

        #facvendedor thead{
        padding: 20px;
        background: #2183E3;
        text-align: center;
        border-bottom: 1px solid #FFFFFF;  
        }

        #facarticulo{
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 15px;
        }

        #facarticulo thead{
        padding: 20px;
        background: #2183E3;
        text-align: center;
        border-bottom: 1px solid #FFFFFF;  
        }

        #gracias{
        text-align: center; 
        }
    </style>
    <body>
        @foreach ($servicio as $s)
        <header><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <div id="logo">
                <img src="{{ base_path() }}/img/logo.png" alt="incanatoIT" id="imagen">
            </div>
            <div id="datos">
                <div id="encabezado">
                    <b>GRUPO CARDEÑA S.A.C.</b><br>
                    Francisco Bolognesi Nº 712 Chivay-Arequipa<br>
                    Teléfono: (+51) 962985509<br>
                    Email: rafael.cr@grupocarden.com
                </div>
            </div>
            <div id="fact" align="center">      
                <b>          
                    R.U.C. 20539375866<br>
                    {{$s->tipo_comprobante}}<br>
                    {{$s->serie_comprobante}}-{{$s->num_comprobante}}                
                </b>
            </div>
        </header>        
        <section>
            <div>
                <table id="facliente">
                    <thead>                        
                        <tr>
                            <th id="fac">Cliente</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p id="cliente">
                                    <b>Sr(a). {{$s->nombre}}</b><br>
                                    @if($s->num_documento != '')
                                        <b>{{$s->tipo_documento}}:</b> {{$s->num_documento}}<br>
                                    @endif
                                    @if($s->direccion != '')
                                        <b>Dirección:</b> {{$s->direccion}}<br>
                                    @endif
                                    @if($s->telefono != '')
                                        <b>Teléfono:</b> {{$s->telefono}}<br>
                                    @endif
                                    @if($s->email != '')
                                        <b>Email:</b> {{$s->email}}
                                    @endif
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        @endforeach
        <br>
        <section>
            <div>
                <table id="facvendedor">
                    <thead>
                        <tr id="fv">
                            <th>VENDEDOR</th>
                            <th>FECHA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td align="center">{{ $s->usuario }}</td>
                            <td align="center">{{ date('d-m-Y', strtotime($s->fecha_hora)) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <br>
        <section>
            <div>
                <table id="facarticulo">
                    <thead>
                        <tr id="fa">
                            <th>CANT</th>
                            <th>DESCRIPCION</th>
                            <th>PRECIO UNIT</th>
                            <th>DESC.</th>
                            <th>IMPORTE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalles as $det)
                        <tr>
                            <td align="center">{{ $det->cantidad }}</td>
                            <td>{{ $det->descripcion }}</td>
                            <td align="center">{{ $det->precio }}</td>
                            <td align="center">{{ $det->descuento }}</td>
                            <td align="right">{{ number_format($det->cantidad*$det->precio-$det->descuento, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @foreach ($servicio as $s)
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th align="right">SUBTOTAL</th>
                            <td align="right">S/. {{ number_format($s->total-($s->total*$s->impuesto), 2) }}</td>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th align="right">I.G.V. ({{$s->impuesto*100}}%)</th>
                            <td align="right">S/. {{ number_format($s->total*$s->impuesto, 2) }}</td>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th align="right">TOTAL</th>
                            <td align="right">S/. {{$s->total}}</td>
                        </tr>
                        @endforeach
                    </tfoot>
                </table>
            </div>
        </section>
        <br>
        <br>
        <footer>
            <div id="gracias">
                <p><b>Gracias por solicitar nuestros servicios!</b></p>
            </div>
        </footer>
    </body>
</html>