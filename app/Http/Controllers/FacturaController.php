<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Servicios\SUNAT\NumeroLetra;
use App\Servicios\SUNAT\DocumentoXML;
use App\Servicios\SUNAT\APIFacturacion;

class FacturaController extends Controller
{
    public function enviarSunat() 
    {
        $emisor = 	array(
			'tipodoc'		=> '6',
			'ruc' 			=> '20602814425', 
			'razon_social'	=> 'TAQINI TECHNOLOGY SAC', 
			'nombre_comercial'	=> 'TAQINI TECHNOLOGY SAC', 
			'direccion'		=> '8 DE OCTUBRE N 123 - CHICLAYO - CHICLAYO - LAMBAYEQUE', 
			'pais'			=> 'PE', 
			'departamento'  => 'LAMBAYEQUE',//LAMBAYEQUE 
			'provincia'		=> 'CHICLAYO',//CHICLAYO 
			'distrito'		=> 'CHICLAYO', //CHICLAYO
			'ubigeo'		=> '140101', //CHICLAYO
			'usuario_sol'	=> 'MODDATOS', //USUARIO SECUNDARIO EMISOR ELECTRONICO
			'clave_sol'		=> 'MODDATOS' //CLAVE DE USUARIO SECUNDARIO EMISOR ELECTRONICO
			);


        $cliente = array(
                    'tipodoc'		=> '6',//6->ruc, 1-> dni 
                    'ruc'			=> '20480631286', 
                    'razon_social'  => 'ASOCIACION CENTRO DE ENTRENAMIENTO EN TECNOLOGIAS DE INFORMACION - CETI', 
                    'direccion'		=> 'Cal. Francisco Cuneo-Pataz Nro. 270(Frente al Circulo Departamental de Emple)',
                    'pais'			=> 'PE'
                    );	

        $comprobante =	array(
                    'tipodoc'		=> '01', //01->FACTURA, 03->BOLETA, 07->NC, 08->ND
                    'serie'			=> 'F001',
                    'correlativo'	=> '777',
                    'fecha_emision' => '2020-12-05',
                    'moneda'		=> 'PEN', //PEN->SOLES; USD->DOLARES
                    'total_opgravadas'=> 0, //OP. GRAVADAS
                    'total_opexoneradas'=>0,
                    'total_opinafectas'=>0,
                    'igv'			=> 0,
                    'total'			=> 0,
                    'total_texto'	=> ''
                );

        $detalle = 
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
                    );

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

        $api_facturacion->EnviarComprobanteElectronico($emisor,$nombrexml,$ruta_certificado,$ruta_archivo_xml,$ruta_archivo_cdr);        
        
        return "factura creada satisfactoriamente";
    }
}
