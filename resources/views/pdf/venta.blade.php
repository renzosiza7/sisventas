<!DOCTYPE>
<html>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de Venta</title>
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
        @foreach ($venta as $v)
        <header><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <div id="logo">
                <img src="{{ base_path() }}/public/img/logo.png" alt="logo" id="imagen">
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
                    {{$v->tipo_comprobante}}<br>
                    {{$v->serie_comprobante}}-{{$v->num_comprobante}}                
                </b>
            </div>
        </header>
        <br>
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
                                    <b>Sr(a). {{$v->nombre}}</b><br>
                                    @if($v->num_documento != '')
                                        <b>{{$v->tipo_documento}}:</b> {{$v->num_documento}}<br>
                                    @endif
                                    @if($v->direccion != '')
                                        <b>Dirección:</b> {{$v->direccion}}<br>
                                    @endif
                                    @if($v->telefono != '')
                                        <b>Teléfono:</b> {{$v->telefono}}<br>
                                    @endif
                                    @if($v->email != '')
                                        <b>Email:</b> {{$v->email}}
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
                            <td align="center">{{$v->usuario}}</td>
                            <td align="center">{{date('d-m-Y', strtotime($v->fecha_hora))}}</td>
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
                            <td align="center">{{$det->cantidad}}</td>
                            <td>{{$det->articulo}}</td>
                            <td align="center">{{$det->precio}}</td>
                            <td align="center">{{$det->descuento}}</td>
                            <td align="right">{{number_format($det->cantidad*$det->precio-$det->descuento,2)}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @foreach ($venta as $v)
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th align="right">SUBTOTAL</th>
                            <td align="right">S/. {{number_format($v->total-($v->total*$v->impuesto),2)}}</td>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th align="right">I.G.V. ({{$v->impuesto*100}}%)</th>
                            <td align="right">S/. {{number_format($v->total*$v->impuesto,2)}}</td>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th align="right">TOTAL</th>
                            <td align="right">S/. {{$v->total}}</td>
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
                <p><b>Gracias por su compra!</b></p>
            </div>
        </footer>
    </body>
</html>