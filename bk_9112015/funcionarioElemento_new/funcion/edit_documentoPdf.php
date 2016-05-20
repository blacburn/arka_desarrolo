<?php

namespace inventarios\gestionElementos\funcionarioElemento\funcion;

use inventarios\gestionElementos\funcionarioElemento\funcion\redireccion;

$ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );

$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/plugin/html2pfd/";

include ($ruta . "/plugin/html2pdf/html2pdf.class.php");

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class RegistradorOrden {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miFuncion;
	var $miSql;
	var $conexion;
	function __construct($lenguaje, $sql, $funcion) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miFuncion = $funcion;
	}
	function documento() {
		// echo "pdf";
		// var_dump($_REQUEST);exit;
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$funcionario = $_REQUEST ['funcionario'];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarElemento', $funcionario );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'jefe_recursos_fisicos' );
// 		echo $cadenaSql;
		$jefe = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$jefe = $jefe [0];
		
// 		var_dump($jefe);exit;
// 		var_dump($resultado);exit;
		
		foreach ( $resultado as $valor ) {
			
			if ($valor ['tipo_bien'] == 2) {
				
				$elementos_consumo_controlado [] = $valor;
			}
			
			if ($valor ['tipo_bien'] == 3) {
				
				$elementos_devolutivos [] = $valor;
			}
		}
		
		// var_dump($elementos_devolutivos);exit;
		
		$directorio = $this->miConfigurador->getVariableConfiguracion ( 'rutaUrlBloque' );
		
		$contenidoPagina = '';
		
		if (isset ( $elementos_consumo_controlado )) {
			
			$contenidoPagina .= "
<style type=\"text/css\">
    table { 
        color:#333; /* Lighten up font color */
        font-family:Helvetica, Arial, sans-serif; /* Nicer font */

        border-collapse:collapse; border-spacing: 3px; 
    }

    table.page_header {width: 100%; border: none; background-color: #DDDDFF; border-bottom: solid 1mm #AAAADD; padding: 2mm; margin-top: 1cm; }
    table.page_footer {width: 100%; border: none; background-color: #DDDDFF; border-top: solid 1mm #AAAADD; padding: 2mm}

    td, th { 
        border: 1px solid #CCC; 
        height: 13px;
    } /* Make cells a bit taller */

    th {
        background: #F3F3F3; /* Light grey background */
        font-weight: bold; /* Make sure they're bold */
        text-align: center;
        font-size:10px
    }

    td {
        background: #FAFAFA; /* Lighter grey background */
        text-align: left;
        font-size:10px
    }
</style>		
				
				
<page backtop='35mm' backbottom='20mm' backleft='5mm' backright='5mm' pagegroup='new'>
	
<page_header>
 <table align='left' style='width:100%;' >
            <tr>
                <td align='center' style='width:12%;border=none;' >
                    <img src='" . $directorio . "/css/images/escudo.png'  width='80' height='100'>
                </td>
                <td align='center' style='width:88%;border=none;' >
                    <font size='9px'><b>UNIVERSIDAD DISTRITAL FRANCISCO JOSÉ DE CALDAS </b></font>
                     <br>
                    <font size='7px'><b>NIT: 899.999.230-7</b></font>
                     <br>
                      <br>
                     <font size='9px'><b>Almacén General e Inventarios</b></font>
                    <br>		
                    <font size='7px'><b>Acta Inventario Individualizado</b></font>
                    <br>									
       
                    <font size='4px'>" . date ( "Y-m-d" ) . "</font>
                   			
                </td>
            </tr>
        </table>
           	<table style='width:100%;border=none;'>
            <tr> 
			td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>NOMBRE FUNCIONARIO : " . $resultado [0] ['nombre_funcionario'] . "</td>
			td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>CC :</b> " . $_REQUEST ['funcionario'] . "</td> 			
 		 	</tr>
			</table>
			
         
</page_header>
         	<table style='width:100%;'>
            <tr> 
			<th style='width:100%;border=none;text-align:left;'>TIPO DE BIEN CONSUMO CONTROLADO</th> 			
 		 	</tr>
			</table>  
			 <br>		
			<table style='width:100%;'>
			<tr> 
			<th style='width:10%;text-align=center;'>Placa</th>
			<th style='width:10%;text-align=center;'>Dependencia</th>
			<th style='width:10%;text-align=center;'>Sede</th>
			<th style='width:35%;text-align=center;'>Descripción</th>
			<th style='width:10%;text-align=center;'>Marca</th>
			<th style='width:10%;text-align=center;'>Serie</th>
			<th style='width:5%;text-align=center;'>Estado</th>
			<th style='width:10%;text-align=center;'>Verificación</th>
			</tr>";
			
			foreach ( $elementos_consumo_controlado as $valor ) {
				
				$contenidoPagina .= "<tr>
                    			<td style='width:10%;text-align=center;'>" . $valor ['placa'] . "</td>
                    			<td style='width:10%;text-align=center;'><font size='0.5px'>" . $valor ['dependencia'] . "</font></td>
                    			<td style='width:10%;text-align=center;'><font size='0.5px'>" . $valor ['sede'] . "</font></td>
                    			<td style='width:35%;text-align=center;'>" . $valor ['descripcion_elemento'] . "</td>
                    			<td style='width:10%;text-align=center;'>" . $valor ['marca'] . "</td>
                    			<td style='width:10%;text-align=center;'>" . $valor ['serie'] . "</td>
                    			<td style='width:5%;text-align=center;'>" . $valor ['estado_bien'] . "</td>
                    			<td style='width:10%;text-align=center;'>" . $valor ['marca_existencia'] . "</td>
                    			</tr>";
			}
			
			
			
			
			$contenidoPagina .= "</table>";
			
			$contenidoPagina .= "<table style='width:100%;'>
											<tr>
											<td style='width:100%;border=none;'><font size='5px'>Nota: Antes de firmar, verifique que los bienes que se encuentran en el presente listado corresponden a los que usted se hace responsable.</font></td>
											</tr>
											</table>";
			
			$contenidoPagina .= "	
												<br>
												<table style='width:100%; background:#FFFFFF ; border: 0px  #FFFFFF;'>
												<tr>
												<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>_______________________________________________________</td>
												<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>_______________________________________________________</td>
												</tr>
												<tr>
												<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>" . $jefe ['nombre'] . "</td>
												<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF; text-transform:capitalize;'>".$resultado [0] ['nombre_funcionario']."</td>
												</tr>
												<tr>
												<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF; text-transform:capitalize;'>Almacenista General</td>
												<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>CC : " . $_REQUEST ['funcionario'] . "</td>
												</tr>
												</table>";
			
			$contenidoPagina .= "		<br>
												<table style='width:100%; background:#FFFFFF ; border: 0px  #FFFFFF;'>
												<tr>
												<td style='width:100%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>Realizo y Verificó Existencia Fìsica:</td>
												</tr>
												</table>											
				
												<table style='width:100%; background:#FFFFFF ; border: 0px  #FFFFFF;'>
												<tr>
												<td style='width:20%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>Nombre : </td>
												<td style='width:30%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>________________________________________</td>
												<td style='width:20%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>Actualizado a : </td>
												<td style='width:30%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>" . date ( 'Y - m - d    H:i:s' ) . "</td>
												</tr>
												</table>";
			
			$contenidoPagina .= "<page_footer>
			<table style='width:100%; background:#FFFFFF ; border: 0px  #FFFFFF;'>
				<tr>
				<td style='width:100%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>
									                    <font size='1px'>Para mayor información y solicitud de inventarios: almacen@udistrital.edu.co</font>
									                    <br>
									                    <font size='0.5px'>Ley 734 del 2002 :Delos deberes del Servidor Pùblico. Vigilar y salvaguardar los bienes y valores que le han sido encomendados y cuidar que sean utilizados debida y racionalmente, de conformidad con los fines a que han sido destinados.</font>
														</td>
														</tr>
														</table>
                                                                                                                   <p style='text-align: right; font-size:10px;'>[[page_cu]]/[[page_nb]]</p>
									    </page_footer> ";
			
			$contenidoPagina .= "</page>";
		}
		
		if ( isset($elementos_devolutivos)) {
			
			$contenidoPagina .= "
<style type=\"text/css\">
    table { 
        color:#333; /* Lighten up font color */
        font-family:Helvetica, Arial, sans-serif; /* Nicer font */

        border-collapse:collapse; border-spacing: 3px; 
    }

    table.page_header {width: 100%; border: none; background-color: #DDDDFF; border-bottom: solid 1mm #AAAADD; padding: 2mm; margin-top: 1cm; }
    table.page_footer {width: 100%; border: none; background-color: #DDDDFF; border-top: solid 1mm #AAAADD; padding: 2mm}

    td, th { 
        border: 1px solid #CCC; 
        height: 13px;
    } /* Make cells a bit taller */

    th {
        background: #F3F3F3; /* Light grey background */
        font-weight: bold; /* Make sure they're bold */
        text-align: center;
        font-size:10px
    }

    td {
        background: #FAFAFA; /* Lighter grey background */
        text-align: left;
        font-size:10px
    }
</style>		
				
				
<page backtop='35mm' backbottom='20mm' backleft='5mm' backright='5mm' pagegroup='new'>
	
<page_header>
 <table align='left' style='width:100%;' >
            <tr>
                <td align='center' style='width:12%;border=none;' >
                    <img src='" . $directorio . "/css/images/escudo.png'  width='80' height='100'>
                </td>
                <td align='center' style='width:88%;border=none;' >
                    <font size='9px'><b>UNIVERSIDAD DISTRITAL FRANCISCO JOSÉ DE CALDAS </b></font>
                     <br>
                    <font size='7px'><b>NIT: 899.999.230-7</b></font>
                     <br>
                      <br>
                     <font size='9px'><b>Almacén General e Inventarios</b></font>
                    <br>	     <br>		
                    <font size='7px'><b>Acta Inventario Individualizado</b></font>
                       <br>
                    <font size='4px'>" . date ( "Y-m-d" ) . "</font>
                   			
                </td>
            </tr>
        </table>
           	<table style='width:100%;border=none;'>
            <tr> 
			<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'><b>NOMBRE FUNCIONARIO :</b> " . $resultado [0] ['nombre_funcionario'] . "</td>
			<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'><b>CC : </b>" . $_REQUEST ['funcionario'] . "</td> 			
 		 	</tr>
			</table>
			
</page_header>
			
           	<table style='width:100%;'>
            <tr>
			<th style='width:100%;border=none;text-align:left;'>TIPO DE BIEN DEVOLUTIVO</th>
 		 	</tr>
			</table>
			 <br>
			<table style='width:100%;'>
			<tr>
			<th style='width:10%;text-align=center;'>Placa</th>
			<th style='width:10%;text-align=center;'>Dependencia</th>
			<th style='width:10%;text-align=center;'>Sede</th>
			<th style='width:35%;text-align=center;'>Descripción</th>
			<th style='width:10%;text-align=center;'>Marca</th>
			<th style='width:10%;text-align=center;'>Serie</th>
			<th style='width:5%;text-align=center;'>Estado</th>
			<th style='width:10%;text-align=center;'>Verificación</th>
			</tr>";
			
			foreach ( $elementos_devolutivos as $valor ) {
				
				$contenidoPagina .= "<tr>
                    			<td style='width:10%;text-align=center;'>" . $valor ['placa'] . "</td>
                    			<td style='width:10%;text-align=center;'><font size='0.5px'>" . $valor ['dependencia'] . "</font></td>
                    			<td style='width:10%;text-align=center;'><font size='0.5px'>" . $valor ['sede'] . "</font></td>
                    			<td style='width:35%;text-align=center;'>" . $valor ['descripcion_elemento'] . "</td>
                    			<td style='width:10%;text-align=center;'>" . $valor ['marca'] . "</td>
                    			<td style='width:10%;text-align=center;'>" . $valor ['serie'] . "</td>
                    			<td style='width:5%;text-align=center;'>" . $valor ['estado_bien'] . "</td>
                    			<td style='width:10%;text-align=center;'>" . $valor ['marca_existencia'] . "</td>
                    			</tr>";
			}
			
			$contenidoPagina .= "</table>";
			
			$contenidoPagina .= "<table style='width:100%;'>
											<tr>
											<td style='width:100%;border=none;'><font size='5px'>Nota: Antes de firmar, verifique que los bienes que se encuentran en el presente listado corresponden a los que usted se hace responsable.</font></td>
											</tr>
											</table>";
			
			$contenidoPagina .= "
												<br>
												<table style='width:100%; background:#FFFFFF ; border: 0px  #FFFFFF;'>
												<tr>
												<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>_______________________________________________________</td>
												<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>_______________________________________________________</td>
												</tr>
												<tr>
												<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>" . $jefe ['nombre'] . "</td>
												<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF; text-transform:capitalize;'>".$resultado [0] ['nombre_funcionario']."</td>
												</tr>
												<tr>
												<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF; text-transform:capitalize;'>Almacenista General</td>
												<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>CC : " . $_REQUEST ['funcionario'] . "</td>
												</tr>
												</table>";
			
			$contenidoPagina .= "		<br>
												<table style='width:100%; background:#FFFFFF ; border: 0px  #FFFFFF;'>
												<tr>
												<td style='width:100%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>Realizo y Verificó Existencia Fìsica:</td>
												</tr>
												</table>
		
												<table style='width:100%; background:#FFFFFF ; border: 0px  #FFFFFF;'>
												<tr>
												<td style='width:20%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>Nombre : </td>
												<td style='width:30%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>________________________________________</td>
												<td style='width:20%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>Actualizado a : </td>
												<td style='width:30%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>" . date ( 'Y - m - d    H:i:s' ) . "</td>
												</tr>
												</table>";
			
			$contenidoPagina .= "<page_footer>
														<table style='width:100%; background:#FFFFFF ; border: 0px  #FFFFFF;'>
														<tr>
														<td style='width:100%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>
									                    <font size='1px'>Para mayor información y solicitud de inventarios: almacen@udistrital.edu.co</font>
									                    <br>
									                    <font size='0.5px'>Ley 734 del 2002 :Delos deberes del Servidor Pùblico. Vigilar y salvaguardar los bienes y valores que le han sido encomendados y cuidar que sean utilizados debida y racionalmente, de conformidad con los fines a que han sido destinados.</font>
														</td>
														</tr>
														</table>
                                                                                                                   <p style='text-align: right; font-size:10px;'>[[page_cu]]/[[page_nb]]</p>
									    </page_footer> ";
			
			$contenidoPagina .= "</page>";
		}
// 		echo $contenidoPagina;exit;
		return $contenidoPagina;
	}
}

$miRegistrador = new RegistradorOrden ( $this->lenguaje, $this->sql, $this->funcion );

$textos = $miRegistrador->documento ();

ob_start ();
$html2pdf = new \HTML2PDF ( 'L', 'LETTER', 'es', true, 'UTF-8', array (1,	1,	1,	1) );
$html2pdf->pdf->SetDisplayMode ( 'fullpage' );
$html2pdf->WriteHTML ( $textos );

$html2pdf->Output ( 'Certificado  	' . date ( 'Y-m-d' ) . '.pdf', 'D' );

?>




