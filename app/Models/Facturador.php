<?php

namespace App\Models;
use App\Servicios\SUNAT\NumeroLetra;
use App\Servicios\SUNAT\DocumentoXML;
use App\Servicios\SUNAT\APIFacturacion;

use Illuminate\Database\Eloquent\Model;

class Facturador extends Model
{
    public function enviarSunat($cliente_venta, $serie, $correlativo, $fecha_venta, $hora_venta, $detalles_venta) 
    {
        $emisor = 	array(
			'tipodoc'		=> '6',
			'ruc' 			=> '20539375866', 
			'razon_social'	=> 'GRUPO CARDEÑA S.A.C.', 
			'nombre_comercial'	=> 'GRUPO CARDEÑA S.A.C.', 
			'direccion'		=> 'Cal. Francisco Bolognesi Nro. 712', 
			'pais'			=> 'PE', 
			'departamento'  => 'AREQUIPA',
			'provincia'		=> 'CAYLLOMA',
			'distrito'		=> 'CHIVAY',
			'ubigeo'		=> '040201',
			'usuario_sol'	=> 'RAFAEL21', //USUARIO SECUNDARIO EMISOR ELECTRONICO
			'clave_sol'		=> 'Rafael2021' //CLAVE DE USUARIO SECUNDARIO EMISOR ELECTRONICO
			);


        $cliente = array(
                    'tipodoc'		=> '6',//6->ruc, 1-> dni 
                    'ruc'			=> $cliente_venta['num_documento'], 
                    'razon_social'  => $cliente_venta['nombre'], 
                    'direccion'		=> $cliente_venta['direccion'], 
                    'pais'			=> 'PE'
                    );	

        $comprobante =	array(
                    'tipodoc'		=> '01', //01->FACTURA, 03->BOLETA, 07->NC, 08->ND
                    'serie'			=> $serie,
                    'correlativo'	=> $correlativo,
                    'fecha_emision' => $fecha_venta,
                    'hora_venta' => $hora_venta,
                    'moneda'		=> 'PEN', //PEN->SOLES; USD->DOLARES
                    'total_opgravadas'=> 0, //OP. GRAVADAS
                    'total_opexoneradas'=>0,
                    'total_opinafectas'=>0,
                    'igv'			=> 0,
                    'total'			=> 0,
                    'total_texto'	=> ''
                );

        $detalle= array();

        foreach ($detalles_venta as $indice => $detalle_venta) {
            $valor_unitario = ($detalle_venta['precio'] * 100) / 118;

            $detalle[$indice]['item'] = $indice + 1;
            $detalle[$indice]['codigo'] = $detalle_venta['idarticulo'];            
            $detalle[$indice]['descripcion'] = $detalle_venta['articulo'];
            $detalle[$indice]['cantidad'] = $detalle_venta['cantidad'];
            $detalle[$indice]['valor_unitario'] = number_format($valor_unitario, 2);
            $detalle[$indice]['precio_unitario'] = number_format($detalle_venta['precio'], 2);
            $detalle[$indice]['tipo_precio'] = "01"; //ya incluye igv
            $detalle[$indice]['igv'] = number_format(($detalle_venta['precio'] - $valor_unitario) * $detalle_venta['cantidad'], 2);
            $detalle[$indice]['porcentaje_igv'] = 18; //de 0 a 100
            $detalle[$indice]['valor_total'] = number_format($detalle_venta['cantidad'] * $valor_unitario, 2);
            $detalle[$indice]['importe_total'] = number_format($detalle_venta['cantidad'] * $detalle_venta['precio'], 2);
            $detalle[$indice]['unidad'] = 'NIU'; //unidad,
            $detalle[$indice]['codigo_afectacion_alt'] = '10'; // Catálogo No. 07 - Anexo V
            $detalle[$indice]['codigo_afectacion'] = 1000;
            $detalle[$indice]['nombre_afectacion'] = 'IGV';
            $detalle[$indice]['tipo_afectacion'] = 'VAT'; //GRAVADAS
        }               

        /*$detalle = 
                    array(
                        array(
                            'item' 				=> 1,
                            'codigo'			=> '11',
                            'descripcion'		=> 'ACEITE CAPRI',
                            'cantidad'			=> 1,
                            'valor_unitario'	=> 50,
                            'precio_unitario'	=> 59,
                            'tipo_precio'		=> "01", //ya incluye igv
                            'igv'				=> 9,
                            'porcentaje_igv'	=> 18, //de 0 a 100
                            'valor_total'		=> 50, 
                            'importe_total'		=> 59,
                            'unidad'			=> 'NIU',//unidad,
                            'codigo_afectacion_alt'	=> '10', // Catálogo No. 07 - Anexo V
                            'codigo_afectacion'	=> 1000,
                            'nombre_afectacion'	=>'IGV',
                            'tipo_afectacion'	=> 'VAT' //GRAVADAS				 
                        ),
                        array(
                            'item' 				=> 2,
                            'codigo'			=> '22',
                            'descripcion'		=> 'LIBRO ABC',
                            'cantidad'			=> 1,
                            'valor_unitario'	=> 50,
                            'precio_unitario'	=> 50,
                            'tipo_precio'		=> "01", //ya incluye igv
                            'igv'				=> 0,
                            'porcentaje_igv'	=> 18,
                            'valor_total'		=> 50,
                            'importe_total'		=> 50,
                            'unidad'			=> 'NIU',//unidad,
                            'codigo_afectacion_alt'	=> '20', // Catálogo No. 07 - Anexo V
                            'codigo_afectacion'	=> 9997,
                            'nombre_afectacion'	=>	'EXO',  //EXONERADOS
                            'tipo_afectacion'	=> 'VAT' 			 
                        ),
                        array(
                            'item' 				=> 3,
                            'codigo'			=> '33',
                            'descripcion'		=> 'TOMATE ABC',
                            'cantidad'			=> 1,
                            'valor_unitario'	=> 50,
                            'precio_unitario'	=> 50,
                            'tipo_precio'		=> "01", //ya incluye igv
                            'igv'				=> 0,
                            'porcentaje_igv'	=> 18,
                            'valor_total'		=> 50,
                            'importe_total'		=> 50,
                            'unidad'			=> 'NIU',//unidad,
                            'codigo_afectacion_alt'	=> '30', // Catálogo No. 07 - Anexo V
                            'codigo_afectacion'	=> 9998,
                            'nombre_afectacion'	=>	'INA',  // INAFECTAS
                            'tipo_afectacion'	=> 'FRE'  
                        )								
                    );*/

        $op_gravadas = 0;
        $op_inafectas = 0;
        $op_exoneradas = 0;
        $igv = 0;
        $total = 0; 

        foreach ($detalle as $k => $v) {
            if($v['codigo_afectacion_alt']=='10'){
                $op_gravadas = $op_gravadas + $v['valor_total'];
            }

            if($v['codigo_afectacion_alt']=='20'){
                $op_exoneradas = $op_exoneradas + $v['valor_total'];
            }

            if($v['codigo_afectacion_alt']=='30'){
                $op_inafectas = $op_inafectas + $v['valor_total'];
            }

            $igv = $igv + $v['igv'];
            $total = $total + $v['importe_total'];
        }

        $comprobante['total_opgravadas'] = $op_gravadas;
        $comprobante['total_opexoneradas'] = $op_exoneradas;
        $comprobante['total_opinafectas'] = $op_inafectas;
        $comprobante['igv'] = $igv;
        $comprobante['total'] = $total;        
        $numero_letra = new NumeroLetra();              
        $comprobante['total_texto'] = $numero_letra->ValorEnLetras($total);

        $documento_xml = new DocumentoXML();

        //RUC DEL EMISOR - TIPO DE COMPROBANTE - SERIE DEL DOCUMENTO - CORRELATIVO
        //01-> FACTURA, 03-> BOLETA, 07-> NOTA DE CREDITO, 08-> NOTA DE DEBITO, 09->GUIA DE REMISION
        $nombrexml = $emisor['ruc'].'-'.$comprobante['tipodoc'].'-'.$comprobante['serie'].'-'.$comprobante['correlativo'];

        $ruta = public_path() . '/documentos/facturas/xml/' . $nombrexml;
        $documento_xml->CrearXMLFactura($ruta, $emisor, $cliente, $comprobante, $detalle);

        //ENVIO DE COMPROBANTE A SUNAT
        $ruta_certificado = public_path();
        $ruta_archivo_xml = public_path() . '/documentos/facturas/xml/';
        $ruta_archivo_cdr = public_path() . '/documentos/facturas/cdr/';

        $api_facturacion = new APIFacturacion();

        $result = $api_facturacion->EnviarComprobanteElectronico($emisor,$nombrexml,$ruta_certificado,$ruta_archivo_xml,$ruta_archivo_cdr);        
        
        return $result;
    }
}
