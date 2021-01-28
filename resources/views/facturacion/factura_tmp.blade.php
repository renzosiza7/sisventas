<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    @include('facturacion.cantidad_en_letras')
    <?php 
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
			'correlativo'	=> '334',
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
$comprobante['total_texto'] = CantidadEnLetra($total);

//dd($comprobante);
?>
@include('facturacion.xml')
<?php

$xml = new GeneradorXML();

//RUC DEL EMISOR - TIPO DE COMPROBANTE - SERIE DEL DOCUMENTO - CORRELATIVO
//01-> FACTURA, 03-> BOLETA, 07-> NOTA DE CREDITO, 08-> NOTA DE DEBITO, 09->GUIA DE REMISION
$nombrexml = $emisor['ruc'].'-'.$comprobante['tipodoc'].'-'.$comprobante['serie'].'-'.$comprobante['correlativo'];

$ruta = $nombrexml;
$xml->CrearXMLFactura($ruta, $emisor, $cliente, $comprobante, $detalle);
?>
@include('facturacion.signature')
<?php
	//firma del documento
	$objSignature = new Signature();

	$flg_firma = "0";
	$ruta_xml_firmar = public_path() . '/' . $ruta . '.XML';

	$ruta_certificado = public_path() . '/certificado_gyg_tuning.pfx';
	$pass_certificado = "dani3l2021";

	$resp = $objSignature->signature_xml($flg_firma, $ruta_xml_firmar, $ruta_certificado, $pass_certificado);

	//Generar el .zip
	$zip = new ZipArchive();

	$nombrezip = $nombrexml.".ZIP";
	$rutazip = $ruta.".ZIP";

	if($zip->open($rutazip,ZIPARCHIVE::CREATE)===true){
		$zip->addFile($ruta_xml_firmar, $nombrexml.'.XML');
		$zip->close();
	}

	//Enviamos el archivo a sunat

	$ws = "https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService";

	$ruta_archivo = $rutazip;
	$nombre_archivo = $nombrezip;

	$contenido_del_zip = base64_encode(file_get_contents($ruta_archivo));


	$xml_envio ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasisopen.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
			 <soapenv:Header>
				 <wsse:Security>
					 <wsse:UsernameToken>
						 <wsse:Username>'.$emisor['ruc'].$emisor['usuario_sol'].'</wsse:Username>
						 <wsse:Password>'.$emisor['clave_sol'].'</wsse:Password>
					 </wsse:UsernameToken>
				 </wsse:Security>
			 </soapenv:Header>
			 <soapenv:Body>
				 <ser:sendBill>
					 <fileName>'.$nombre_archivo.'</fileName>
					 <contentFile>'.$contenido_del_zip.'</contentFile>
				 </ser:sendBill>
			 </soapenv:Body>
			</soapenv:Envelope>';


		$header = array(
					"Content-type: text/xml; charset=\"utf-8\"",
					"Accept: text/xml",
					"Cache-Control: no-cache",
					"Pragma: no-cache",
					"SOAPAction: ",
					"Content-lenght: ".strlen($xml_envio)
				);


		$ch = curl_init();
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,1);
		curl_setopt($ch,CURLOPT_URL,$ws);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_HTTPAUTH,CURLAUTH_ANY);
		curl_setopt($ch,CURLOPT_TIMEOUT,30);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml_envio);
		curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
		//para ejecutar los procesos de forma local en windows
		//enlace de descarga del cacert.pem https://curl.haxx.se/docs/caextract.html
		curl_setopt($ch, CURLOPT_CAINFO, public_path()."/cacert.pem");
		
		$response = curl_exec($ch);	

		$httpcode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
		$estadofe = "0";

		if($httpcode == 200){ //200->La comunicación fue satisfactoria
			$doc = new DOMDocument();
			$doc->loadXML($response);

				if(isset($doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue)){
					$cdr = $doc->getElementsByTagName('applicationResponse')->item(0)->nodeValue;
					$cdr = base64_decode($cdr);
					
					file_put_contents(public_path()."/cdr/R-".$nombrezip, $cdr);

					$zip = new ZipArchive;
					if($zip->open(public_path()."/cdr/R-".$nombrezip)===true){
						$zip->extractTo(public_path()."/cdr/", "R-".$nombrexml.'.XML');
						$zip->close();
					}
					$estadofe ="1";
					echo "TODO OK";
				}else{		
					$estadofe = "2";
					$codigo = $doc->getElementsByTagName("faultcode")->item(0)->nodeValue;
					$mensaje = $doc->getElementsByTagName("faultstring")->item(0)->nodeValue;
					echo "error ".$codigo.": ".$mensaje; 
				}		

		}else{ //hay problemas comunicacion
				$estadofe = "3";
				echo curl_error($ch);
				echo "Problema de conexión";

		}

		curl_close($ch);
?>

</body>
</html>