<?php
/**  Programa para el manejo de gestion documental, oficios, memorandus, circulares, acuerdos
*    Desarrollado y en otros Modificado por la SubSecretaría de Informática del Ecuador
*    Quipux    www.gestiondocumental.gov.ec
*------------------------------------------------------------------------------
*    This program is free software: you can redistribute it and/or modify
*    it under the terms of the GNU Affero General Public License as
*    published by the Free Software Foundation, either version 3 of the
*    License, or (at your option) any later version.
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU Affero General Public License for more details.
*
*    You should have received a copy of the GNU Affero General Public License
*    along with this program.  If not, see http://www.gnu.org/licenses.
*------------------------------------------------------------------------------
**/
/**
*       Basado Sistema de Compras Públicas y Búsqueda Avanzada (Anterior) Quipux 
*	Autor			Iniciales		Fecha (dd/mm/aaaa)
*       David Gamboa            DG, josedavo@gmail.com  2014-11-28
*       Se evita envier like para consultas
*
**/

$ruta_raiz = "..";
session_start();
include_once "$ruta_raiz/rec_session.php";
if (isset ($replicacion) && $replicacion && $config_db_replica_busqueda!="") $db = new ConnectionHandler($ruta_raiz,$config_db_replica_busqueda);

include_once "$ruta_raiz/funciones_interfaz.php";
include "$ruta_raiz/obtenerdatos.php";
echo "<html>".html_head();
include_once "$ruta_raiz/js/ajax.js";
//if ($version_light && $_SESSION["inst_codi"]!=2) {
//    die ("<br/><center><font size='3' color='blue'><b>Lo sentimos, esta funcionalidad se encuentra actualmente en mantenimiento.<br>Por favor vuelva a intentarlo m&aacute;s tarde. </b></font>");
//}
$paginador = new ADODB_Pager_Ajax($ruta_raiz, "div_buscar_documentos", "busqueda_paginador.php",
                  "txt_nume_documento,rad_nume_docu_exacto,txt_nume_referencia".
                  ",txt_usua_destinatario,txt_usua_destinatario_h,txt_de_para_copia,txt_inst_codi,txt_tipo_documento,txt_categoria,txt_tipificacion".
                  ",txt_fecha_desde,txt_fecha_hasta,txt_estado,txt_usua_codi,txt_de_para_ex,txt_usua_para,txt_usua_para_h".
                  ",txt_tipo_fecha,txt_campo_metadato,txt_sino_firma,txt_reporte,txt_depe_codi,txt_texto",
                  "txt_tipo_busqueda=avanzada");
//$buscar_inst  = limpiar_sql($_POST["buscar_inst"]);
$txt_estado = limpiar_sql($_POST["txt_estado"]);
$txt_reporte = limpiar_sql($_POST["txt_reporte"]);
$txt_sino_firma = limpiar_sql($_POST["txt_sino_firma"]);
$txt_nume_documento  = limpiar_sql($_POST["txt_nume_documento"]);
$txt_nume_referencia = limpiar_sql($_POST["txt_nume_referencia"]);
//$txt_usua_remitente = limpiar_sql($_POST["txt_usua_remitente"]);
$txt_usua_destinatario = limpiar_sql($_POST["txt_usua_destinatario"]);

$txt_texto = limpiar_sql($_POST["txt_texto"]);
$txt_tipo_documento = limpiar_sql($_POST["txt_tipo_documento"]);
$txt_categoria = limpiar_sql($_POST["txt_categoria"]);
$txt_tipificacion = limpiar_sql($_POST["txt_tipificacion"]);
$txt_campo_metadato = limpiar_sql($_POST["txt_campo_metadato"]);
//tipo: de para copia
$txt_de_para_copia = 0+limpiar_sql($_POST["txt_de_para_copia"]);
if (!isset($_POST["txt_de_para_copia"]))
    $txt_de_para_copia=1;
//id_destinatario
$txt_usua_destinatario_h = 0+limpiar_sql($_POST["txt_usua_destinatario_h"]);

$txt_de_para_ex = 0+limpiar_sql($_POST["txt_de_para_ex"]);
if (!isset($_POST["txt_de_para_ex"]))
    $txt_de_para_ex=2;
$txt_usua_para = limpiar_sql($_POST["txt_usua_para"]);
$txt_usua_para_h = 0+limpiar_sql($_POST["txt_usua_para_h"]);


//institucion
$txt_inst_codi =  limpiar_numero($_POST["txt_inst_codi"]);

if (isset($_POST["txt_inst_codi"]) && $_SESSION["perm_buscar_doc_adscritas"] == 1 && (0+$_POST["txt_inst_codi"])!=$_SESSION["inst_codi"]) {
    $txt_inst_codi = 0+limpiar_numero($_POST["txt_inst_codi"]);
    
    
} else {
    $txt_inst_codi = $_SESSION["inst_codi"];
    
   
}
$rs = $db->conn->query("select inst_nombre from institucion where inst_codi=$txt_inst_codi");
    $span_inst_nombre = $rs->fields["INST_NOMBRE"];

 
if (isset($_POST["txt_depe_codi"])) {
    $txt_depe_codi = limpiar_sql($_POST["txt_depe_codi"]);
    $txt_usua_codi = limpiar_sql($_POST["txt_usua_codi"]);
    $txt_fecha_desde = limpiar_sql($_POST["txt_fecha_desde"]);
    $txt_fecha_hasta = limpiar_sql($_POST["txt_fecha_hasta"]);
} else {
    $txt_depe_codi = $_SESSION["depe_codi"];
    $txt_usua_codi = $_SESSION["usua_codi"];
    if ($_SESSION["ver_todos_docu"]==1) { // Si tiene permiso de bandeja de entrada coja todas las areas por defecto
        $txt_depe_codi = 0;
        $txt_usua_codi = 0;
    }
}
if (!isset($_POST["txt_fecha_desde"])){
    
    if ($config_numero_meses < 3) $txt_fecha_desde = date("Y-m-d", strtotime(date("Y-m-d")." - 1 month"));
    else $txt_fecha_desde = date("Y-m-d", strtotime(date("Y-m-d")." - 3 month"));
    $txt_fecha_hasta = date("Y-m-d");
}else{
        $txt_fecha_desde = limpiar_sql($_POST["txt_fecha_desde"]);
    $txt_fecha_hasta = limpiar_sql($_POST["txt_fecha_hasta"]);
}
$txt_buscar = 0+$_POST["txt_buscar"];

if ($txt_depe_codi==$_SESSION["depe_codi"]) $span_depe_nombre = $_SESSION["depe_nomb"];
elseif ($txt_depe_codi == 0) $span_depe_nombre = "&lt;&lt Todas las &aacute;reas &gt;&gt;";
else {
    $rs = $db->conn->query("select depe_nomb from dependencia where depe_codi=$txt_depe_codi");
    $span_depe_nombre = $rs->fields["DEPE_NOMB"];
}

if ($txt_usua_codi == 0) $span_usua_nombre = "&lt;&lt Todos los usuarios &gt;&gt;";
else {
    $rs = $db->conn->query("select usua_apellido, usua_nomb from usuarios where usua_codi=$txt_usua_codi");
    $span_usua_nombre = $rs->fields["USUA_APELLIDO"]." ".$rs->fields["USUA_NOMB"];
}

?>
<script type='text/JavaScript' src='<?=$ruta_raiz?>/js/shortcut.js'></script>
<script type="text/javascript">
    var timer_id_activar_boton_buscar = 0;
    var flag_activar_boton_buscar = false; //Permite ejecutar busqueda_buscar_documento() al dat click sobre buscar
    function Start(URL, tipo) {
        var x = (screen.width - 1100) / 2;
        var y = (screen.height - 740) / 2;
         windowprops = "top=100,left=100,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=1000,height=500";
//        windowprops = "top=0,left=0,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=1100,height=740";
//        URL = URL + '?documento_us1=' + document.formulario.documento_us1.value + '&documento_us2=' + document.formulario.documento_us2.value + '&concopiaa=' + document.formulario.concopiaa.value + "&ent=<?=$ent?>"
//              + "&radi_lista_dest=" + document.formulario.radi_lista_dest.value
//              + "&radi_lista_nombre=" + document.formulario.radi_lista_nombre.value
//              + "&lista_modificada="+document.formulario.hidden_lista_modificada.value;
        preview = window.open(URL+"?pres="+tipo , "preview", windowprops);
        preview.moveTo(x, y);
        preview.focus();
    }
    function busqueda_buscar_documento(tipo) {
        if (!flag_activar_boton_buscar) {
            alert("Espere por favor, su consulta ya se está procesando.");
            return;
        }
        if (!validar_fechas()) return;
        numeroCaracteres=parseInt('<?=0+$numeroCaracteresTexto?>');;
        if (document.getElementById('txt_nombre_texto_error').value==''){
            document.getElementById("txt_buscar").value = "1";
            document.getElementById("txt_reporte").value = tipo;
            flag_activar_boton_buscar = false;
            document.formulario.action = "busqueda.php";
            //Validacion
            numdoc = document.getElementById("txt_nume_documento").value;
            numref = document.getElementById("txt_nume_referencia").value;
            usdest = document.getElementById("txt_usua_destinatario_h").value;
            metadt = document.getElementById("txt_campo_metadato").value;
            uscod = document.getElementById('txt_usua_codi').value;
            para = document.getElementById('txt_usua_para_h').value;
            asunto = document.getElementById('txt_texto').value;
            tipi = document.getElementById('txt_tipificacion').value; 
            depe = document.getElementById('txt_depe_codi').value;
        
            if (depe>=0 || tipi>0 || para>0 || uscod>0 || numdoc!='' || numref!='' || usdest>0 || metadt!='' || asunto!='')
                document.formulario.submit();
            else{
                alert("Favor ingrese un parámetro de búsqueda");
                flag_activar_boton_buscar = true;
            }
        }else{
            alert("Se requiere más información en los campos ingresados, debe ser al menos "+numeroCaracteres+ " caracteres");
        }
    }

    function realizar_busqueda() {
        paginador_reload_div('');
        fjs_timer_activar_boton_buscar();
    }

    function fjs_timer_activar_boton_buscar() {
        try {
            document.getElementById("hid_flag_activar_boton_buscar").value = "0";
            flag_activar_boton_buscar = true;
        } catch(e) {
            setTimeout("fjs_timer_activar_boton_buscar()", 500);
        }
        return;
    }

    function mostrar_documento(numdoc, txtdoc)
    {
	var_envio='<?=$ruta_raiz?>/verradicado.php?verrad='+numdoc+'&textrad='+txtdoc+'&menu_ver_tmp=3&tipo_ventana=popup';
	window.open(var_envio,numdoc,"height=450,width=750,scrollbars=yes");
    }
    function reportes_generar_guardar_como(tipo) {
        nuevoAjax('div_reporte_guardar_como', 'POST', 'busqueda_generar_guardar_como.php', 'tipo='+tipo);
    }

    // Llama a cuerpo segun los shortcuts
    function llamarListado(nombreCarpeta, codigoCarpeta){
         location.href= '<?=$ruta_raiz?>/cuerpo.php?nomcarpeta='+nombreCarpeta+'&carpeta='+codigoCarpeta+'&adodb_next_page=1';
    }

    function validar_fechas () {
        function convertir_texto_a_fecha(cadena) {
            try {
                var cad = cadena.split('-');
                var fecha = new Date(cad[0],cad[1],cad[2]);
            } catch (e) {
                fecha = 0;
            }
            return fecha;
        }
        var fecha_desde = document.getElementById('txt_fecha_desde').value;
        var fecha_hasta = document.getElementById('txt_fecha_hasta').value;

        var tiempo1 = convertir_texto_a_fecha(fecha_hasta) - convertir_texto_a_fecha(fecha_desde);
        if (tiempo1 < 0) {
            alert ('La fecha de inicio no puede superar a la fecha final.\nPor favor modifique las fechas antes de continuar.')
            return false;
        }
        var tiempo2 = convertir_texto_a_fecha('<?=date("Y-m-d")?>') - convertir_texto_a_fecha('<?=date("Y-m-d", strtotime(date("Y-m-d")." - $config_numero_meses month"))?>');
        if (tiempo1 > tiempo2) {
            alert ('El rango de fechas no puede superar los <?=$config_numero_meses?> meses.\nPor favor modifique las fechas antes de continuar.')
            return false;
        }
        return true;
    }

    function fjs_buscar_cambiar_combo(combo) {
        document.getElementById("txt_usua_destinatario_h").value=0;
        document.getElementById("txt_usua_destinatario").value="";
         document.getElementById("txt_usua_para_h").value=0;
        document.getElementById("txt_usua_para").value="";
        var inst_codi = document.getElementById("txt_inst_codi").value;
        var depe_codi = document.getElementById("txt_depe_codi").value;
        var usua_codi = document.getElementById("txt_usua_codi").value;

        document.getElementById('img_'+combo+'_cambiar').style.display = 'none';
        document.getElementById('span_'+combo+'_nombre').style.display = 'none';
        document.getElementById('div_'+combo+'_combo').style.display = '';

        if (document.getElementById('div_'+combo+'_combo').innerHTML == '') {
            
            nuevoAjax('div_'+combo+'_combo', 'POST', 'busqueda_cargar_combos.php', 'tipo_combo='+combo+'&txt_inst_codi='+inst_codi+'&txt_depe_codi='+depe_codi+'&txt_usua_codi='+usua_codi);
        }
    }

    function fjs_buscar_cargar_combo(combo, objeto){
        document.getElementById('img_'+combo+'_cambiar').style.display = '';
        document.getElementById('span_'+combo+'_nombre').style.display = '';
        document.getElementById('div_'+combo+'_combo').style.display = 'none';

        document.getElementById('span_'+combo+'_nombre').innerHTML = objeto.options[objeto.selectedIndex].text;
        document.getElementById('txt_'+combo+'_codi').value = objeto.value;
        switch (combo) {
            case 'inst':
                document.getElementById('txt_depe_codi').value = '0';
                document.getElementById('span_depe_nombre').innerHTML = '&lt;&lt Todas las &aacute;reas &gt;&gt;';
                document.getElementById('div_depe_combo').innerHTML = '';
                document.getElementById('txt_usua_codi').value = '0';
                document.getElementById('span_usua_nombre').innerHTML = '&lt;&lt Todos los usuarios &gt;&gt;';
                document.getElementById('div_usua_combo').innerHTML = '';

                document.getElementById('img_depe_cambiar').style.display = '';
                document.getElementById('span_depe_nombre').style.display = '';
                document.getElementById('div_depe_combo').style.display = 'none';
                document.getElementById('img_usua_cambiar').style.display = '';
                document.getElementById('span_usua_nombre').style.display = '';
                document.getElementById('div_usua_combo').style.display = 'none';
                break;
            case 'depe':
                document.getElementById('txt_usua_codi').value = '0';
                document.getElementById('span_usua_nombre').innerHTML = '&lt;&lt Todos los usuarios &gt;&gt;';
                document.getElementById('div_usua_combo').innerHTML = '';

                document.getElementById('img_usua_cambiar').style.display = '';
                document.getElementById('span_usua_nombre').style.display = '';
                document.getElementById('div_usua_combo').style.display = 'none';
                break;
        }

    }
function borrar_texto(obj){
    obj.value="";
   
}
function cambiarDePara(tipo){
 if (tipo==1){
    if(document.getElementById('txt_de_para_copia').value==2)
        document.getElementById('txt_de_para_ex').value=1;
    else
        document.getElementById('txt_de_para_ex').value=2;
 }else{
    if (document.getElementById('txt_de_para_ex').value==1)
        document.getElementById('txt_de_para_copia').value=2;
    else
     document.getElementById('txt_de_para_copia').value=1;
 }
}


function limpiar(tipo){
    if (tipo==1){
        document.getElementById('txt_usua_destinatario').value='';
        document.getElementById('txt_usua_destinatario_h').value=0;
    }else{
        document.getElementById('txt_usua_para').value='';
        document.getElementById('txt_usua_para_h').value=0;
    }
   
}
</script>
<body onload="shortcuts_busqueda(); this.focus();">
   
    <center>
    <form id="formulario" name="formulario" method="post" >
        <table border="0" cellpadding="0" cellspacing="0" width="99%">
            <tr>
                <td width="100%" align="right">
                   <input type="hidden" name="txt_nombre_texto_error" id="txt_nombre_texto_error" class="tex_area" value=""/>
                    <a href="./busqueda_tramites.php">Busqueda de Tr&aacute;mites</a>
                </td>                               
            </tr>
        </table>
         
        <table width="90%" border="0" class="borde_tab">
            <tr>
                <td class="titulos4" colspan="3"><center><b>B&uacute;squeda Avanzada de Documentos</b></center></td>
            </tr>
            <tr>
                <td class="titulos2">No. <?=$descRadicado?>:</td>
                <td class="listado2">                    
                    <input type="text" name="txt_nume_documento" id="txt_nume_documento" class="tex_area" value='<?=$txt_nume_documento?>' onblur="numeroCarecteresDiv(this,<?=$numeroCaracteresTexto?>,'txt_nume_documento',1)"
                            size="70" title="Ingrese el n&uacute;mero o parte del n&uacute;mero del documento.">
                </td>
                <td width="30%" class="listado2"  title="B&uacute;squeda exacta: Ingrese el n&uacute;mero del documento.">Búsqueda exacta
                    <input style='display:none' type="radio" name="rad_nume_docu_exacto" id="rad_nume_docu_exacto" value="0" checked>

                    <br><?php echo dibujarDiv($ruta_raiz,'div_txt_nume_documento',$numeroCaracteresTexto);?>
                </td>
            </tr>
            <tr>
                <td class="titulos2"><?=$descReferencia?>:</td>
                <td class="listado2">                    
                    <input type="text" name="txt_nume_referencia" id="txt_nume_referencia" class="tex_area" value='<?=$txt_nume_referencia?>' onblur="numeroCarecteresDiv(this,<?=$numeroCaracteresTexto?>,'txt_nume_referencia',1)"
                            size="70" title="Ingrese el n&uacute;mero o parte del n&uacute;mero de referencia del documento.">
                </td>
                <td width="30%" class="listado2">&nbsp;
                <?php echo dibujarDiv($ruta_raiz,'div_txt_nume_referencia',$numeroCaracteresTexto);?>
                </td>
            </tr>

            <?php //DE ?>
            <tr>
                <td class="titulos2">
                    <table>
                        <tr>
                            <td>
                                
                            <?php
                              $htmlc="<select id='txt_de_para_copia' name='txt_de_para_copia' class='select' onchange='cambiarDePara(1)'>";
                            
                            for ($i=1;$i<=3;$i++){
                                if ($i==$txt_de_para_copia)
                                    $selected="selected";
                                else
                                    $selected="";
                                if ($i==1)
                                   $htmlc.="<option value='$i' $selected>De</option>";
                                if ($i==2)
                                   $htmlc.="<option value='$i' $selected>Para/Copia</option>";
                               
                              }
                            $htmlc .="</select>";
                            echo $htmlc;
                           
                                ?>
                                </td>
                        </tr>
                    </table>
                    </td>
                <td class="listado2">    
                     <input readonly type="text" name="txt_usua_destinatario" id="txt_usua_destinatario" class="tex_area" value='<?=$txt_usua_destinatario?>' 
                           size="70"  title="Click en la lupa para buscar.">
                        <a href='javascript:;' onclick="limpiar(1);" title="borrar" alt="borrar">
                         <IMG SRC="../imagenes/close_button.gif" ALT="limpiar" />
                        </a>
                    <input  type="hidden" id="txt_usua_destinatario_h" name="txt_usua_destinatario_h" value='<?=$txt_usua_destinatario_h?>'>
                     
                         
                    <?php // $inst_codi = $_SESSION["inst_codi"];?>
                    <!--<input  type="text" id="txt_inst_codi"  name="txt_inst_codi" value='<?=$inst_codi;?>'>-->
                    
                    
                     
                </td>
                
                
                <td width="30%" class="listado2">&nbsp;
                    <a href='javascript:;' onclick="Start('buscar_usuario_nuevo.php',1);" title="Buscar De" alt="Buscar De">
                    <IMG SRC="../imagenes/zoom_in.png" ALT="buscar (De/Para/Copia)" />
                    </a>
                </td>
            </tr>
            <?php //fin para?>
                        <?php //Para ?>
            <tr>
                <td class="titulos2">
                    <table>
                        <tr>
                            <td>
                    
                            <?php
                           
                             $htmlc="<select id='txt_de_para_ex' name='txt_de_para_ex' class='select' onchange='cambiarDePara(2)'>";
                            
                            for ($i=1;$i<=3;$i++){
                                if ($i==$txt_de_para_ex)
                                    $selected="selected";
                                else
                                    $selected="";
                                 if ($i==1)
                                   $htmlc.="<option value='$i' $selected>De</option>";
                                if ($i==2)
                                   $htmlc.="<option value='$i' $selected>Para/Copia</option>";
                               
                              }
                                $htmlc .="</select>";
                                echo $htmlc;
                          
                                ?>
                                </td>
                                <input disabled="false" type="text" id='div_de_para_copia' name="div_de_para_copia" value='Usuario:' class="titulos2" style="display:none; border: none;"> 
                        </tr>
                    </table>
                    </td>
                <td class="listado2">    
                    <input readonly type="text" name="txt_usua_para" id="txt_usua_para" class="tex_area" value='<?=$txt_usua_para?>' 
                           size="70"  title="Click en la lupa para buscar.">
                    <a href='javascript:;' onclick="limpiar(2);" title="borrar" alt="borrar">
                    <IMG SRC="../imagenes/close_button.gif" ALT="limpiar" />
                    </a>
                    <input  type="hidden" id="txt_usua_para_h" name="txt_usua_para_h" value='<?=$txt_usua_para_h?>'>
                   
                </td>
                
                
                <td width="30%" class="listado2">&nbsp;
                   <a href='javascript:;' onclick="Start('buscar_usuario_nuevo.php',2);" title="Buscar Para/Copia" alt="Buscar Para/Copia">
                   <IMG SRC="../imagenes/zoom_in.png" ALT="buscar (De/Para/Copia)" />
                   </a>
                </td>
            </tr>
           <?php //fin de?>
            <?php //if (in_array($_SESSION["inst_codi"], $config_array_permisos_asunto)) {?>
            <tr>
                <td class="titulos2">Buscar en el texto (asunto/notas):</td>
                <td class="listado2">                    
                    <input type="text" name="txt_texto" id="txt_texto" class="tex_area" value='<?=$txt_texto?>' maxlength="70" size="70" onblur="numeroCarecteresDiv(this,<?=$numeroCaracteresTexto?>,'txt_texto',1)"
                           title="Ingrese parte del asunto o del texto del documento.">
                </td>
                <td width="30%" class="listado2">&nbsp;
                <?php echo dibujarDiv($ruta_raiz,'div_txt_texto',$numeroCaracteresTexto);?>
                </td>
            </tr>
            <?php //}else{
                 //echo "<input type='hidden' name='txt_texto' id='txt_texto'>";
            //} ?>
            <tr style='display:none'>
                <td class="titulos2">Buscar en el texto (<?=$labelmetadatos?>):</td>
                <td class="listado2">                    
                    <input type="text" name="txt_campo_metadato" id="txt_campo_metadato" class="tex_area" value='<?=$txt_campo_metadato?>' maxlength="70" size="70" onblur="numeroCarecteresDiv(this,<?=$numeroCaracteresTexto?>,'txt_campo_metadato',1)"
                           title="Ingrese parte del texto del <?=$labelmetadatos?>.">
                </td>
                <td width="30%" class="listado2">&nbsp;
                <?php echo dibujarDiv($ruta_raiz,'div_txt_texto',$numeroCaracteresTexto);?>
                </td>
            </tr>
            
            <tr>
		<td  align="left" class="titulos2">
                    <span class="titulos2">
                        Estado del Documento: </span></td>
                <td class="listado2">
             <?php
                //Combo para generar estado del documento.
                $sql_estado="SELECT ESTA_DESC, ESTA_CODI FROM ESTADO where esta_codi <> 1 ORDER BY 1";
                $rsE = $db->query($sql_estado);
                echo $rsE->GetMenu2("txt_estado", $txt_estado, "999:&lt;&lt Todos &gt;&gt;", false,""," id='txt_estado' class='select'" );
               // echo $rs->GetMenu2("txt_tipo_documento", "$txt_tipo_documento", "0:&lt;&lt Todos &gt;&gt;", false,"","class='select' id ='txt_tipo_documento'");
                ?>
                </td>
                <td width="30%" class="listado2">&nbsp;
                    &nbsp;
                </td>
             </tr>
            <tr>
                <td class="titulos2">Tipo de Documento:</td>
                <td class="listado2">
<?php
                    $rs = $db->conn->Execute("select trad_descr, trad_codigo from tiporad where trad_inst_codi in (0,".$_SESSION["inst_codi"].") order by 2");
                    echo $rs->GetMenu2("txt_tipo_documento", "$txt_tipo_documento", "0:&lt;&lt Todos &gt;&gt;", false,"","class='select' id ='txt_tipo_documento'");
?>

                </td>
                <td width="30%" class="listado2">&nbsp;
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td class="titulos2">Categor&iacute;a:</td>
                <td class="listado2">
<?php
                $sql = "Select cat_descr, cat_codi from categoria order by cat_descr";
                $rs=$db->conn->query($sql);
                if($rs && !$rs->EOF)
                    print $rs->GetMenu2("txt_categoria", "$txt_categoria", ":&lt;&lt Seleccione Categor&iacute;a &gt;&gt;", false,"","class='select' id='txt_categoria'" );
?>
                </td>
                <td width="30%" class="listado2">&nbsp;
                </td>
            </tr>           
          
<?php
        //$queryCod = "select 'Sin Tipificación' as cod_descr, '0' as cod_codi union";
        $queryCod = "select cod_descripcion, cod_codi from codificacion where inst_codi in (0,".$_SESSION["inst_codi"].") order by 1";
        $rsCod=$db->conn->query($queryCod);
        if(!$rsCod->EOF)
        {
            echo "
                <tr>
                    <td class='titulos2'>Tipificaci&oacute;n:</td>
                    <td class='listado2'>";
                    print $rsCod->GetMenu2("txt_tipificacion", "$txt_tipificacion", ":&lt;&lt Seleccione Tipificaci&oacute;n &gt;&gt;", false,"",
                                            "class='select' style='width:330px' id='txt_tipificacion'" );
            echo "
                    </td><td width='30%' class='listado2'>&nbsp;
                    &nbsp;
                </td>
                </tr>";
        }
?>
             <tr>
            <td align="left" class="titulos2"><span class="titulos2">Firmado Electrónico: </span>
            </td>
            <td class="listado2">
                <?php $selected = "selected"; //para seleccionar si es el caso ?>
            <SELECT NAME="txt_sino_firma" id="txt_sino_firma" class="select">
                <?php 
                if ($_POST["txt_sino_firma"]=='')
                    $_POST["txt_sino_firma"]=2;
                ?>
            <OPTION VALUE="2" <?php if ($_POST["txt_sino_firma"]==2) echo $selected; ?>>&lt;&lt Todos &gt;&gt</OPTION>
            <OPTION VALUE="0" <?php if ($_POST["txt_sino_firma"]==0) echo $selected; ?>>No</OPTION>
            <OPTION VALUE="1" <?php if ($_POST["txt_sino_firma"]==1) echo $selected; ?>>Si</OPTION>
            </SELECT>
            </td>
            <td width="30%" class="listado2">&nbsp;
                    
                </td>
            </tr>
            <tr>
                <td class="titulos2">Buscar por Fecha de:</td>
                <td class="listado2">
                    <input type="radio" name="txt_tipo_fecha" id="txt_tipo_fecha" value="0" checked>Referencia
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="txt_tipo_fecha" id="txt_tipo_fecha" value="1" <?php if ($_POST["txt_tipo_fecha"]=="1") echo "checked";?>>Registro
                </td>
                <td width="30%" class="listado2">&nbsp;
                </td>
            </tr>
            <tr valign="top" height="23">
                <td class="titulos2">Desde Fecha (yyyy/mm/dd):</td>
                <td class="listado2">
                    <?php echo dibujar_calendario("txt_fecha_desde", $txt_fecha_desde, $ruta_raiz, ""); ?>
                </td>
                <td width="30%" class="listado2">&nbsp;
                </td>
            </tr>
            <tr valign="top" height="23">
                <td class="titulos2">Hasta Fecha (yyyy/mm/dd):</td>
                <td class="listado2"><?php echo dibujar_calendario("txt_fecha_hasta", $txt_fecha_hasta, $ruta_raiz, ""); ?></td>
                <td width="30%" class="listado2">&nbsp;</td>
            </tr>
          <tr style='display: <?= ($_SESSION["perm_buscar_doc_adscritas"]==1) ? "" : "none";?>;'>
      
                <td class="titulos2"><?=$descEmpresa?>:</td>
                <td class="listado2"> 
                  
                      <?php // $inst_codi = $_SESSION["inst_codi"];?>
                    <!--<input  type="text" id="txt_inst_codi"  name="txt_inst_codi" value='<?=$inst_codi;?>'>-->
                    
                   
                    <input type="hidden" readonly name="txt_inst_codi" id="txt_inst_codi" value="<?=$txt_inst_codi?>">
                    <span id="span_inst_nombre"><?=$span_inst_nombre?></span>
                    <span id="div_inst_combo" style="display: none;"></span>&nbsp;
                    <img src="<?=$ruta_raiz?>/imagenes/internas/pencil_add.png" onclick='fjs_buscar_cambiar_combo("inst");' id="img_inst_cambiar" align="middle" border="0" title="Cambiar de institucion" alt="cambiar">
                </td>
                <td width="30%" class="listado2">&nbsp;</td>
            </tr>
           <tr>
                <td class="titulos2"><?=$descDependencia?>:</td>
                <td class="listado2">
                    <input type="hidden" name="txt_depe_codi" id="txt_depe_codi" value="<?=$txt_depe_codi?>">
                    <span id="span_depe_nombre"><?=$span_depe_nombre?></span>
                    <span id="div_depe_combo" style="display: none;"></span>&nbsp;
                    <img src="<?=$ruta_raiz?>/imagenes/internas/pencil_add.png" onclick='fjs_buscar_cambiar_combo("depe");' id="img_depe_cambiar" align="middle" border="0" title="Cambiar de <?=$txt_depe_codi?>" alt="cambiar">
                </td>
                <td width="30%" class="listado2">&nbsp;</td>
            </tr>
        <tr>
                <td class="titulos2">Servidor P&uacute;blico:</td>
                <td class="listado2">
                    <input type="hidden" name="txt_usua_codi" id="txt_usua_codi" value="<?=$txt_usua_codi?>">
                    <span id="span_usua_nombre"><?=$span_usua_nombre?></span>
                    <div id="div_usua_combo" style="display: none;"></div>&nbsp;
                   
                    <img src="<?=$ruta_raiz?>/imagenes/internas/pencil_add.png" onclick='fjs_buscar_cambiar_combo("usua");' id="img_usua_cambiar" align="middle" border="0" title="Cambiar de <?=$txt_usua_codi?>" alt="cambiar">
                </td>
                <td width="30%" class="listado2">&nbsp;
                </td>
            </tr>

        </table>
        <br>
        <input type="button" name="btn_buscar" id="btn_buscar" class="botones_largo" value="Buscar" onclick="busqueda_buscar_documento(0);">
        <?php if (isset ($version_light) && $version_light==false) //Si hay problemas con la BDD
            echo '<input type="button" name="btn_buscar" class="botones_largo" value="Generar Reporte" onclick="busqueda_buscar_documento(1);" title="Soporta hasta 1000 Registros">';
        ?>
        <br><br>
        <div id='div_buscar_documentos' style="width: 99%"></div>
        <div id='div_reporte' style="width: 99%"></div>
      <div id='div_reporte_guardar_como' style="width: 99%"></div>
        <input type="hidden" name="txt_buscar" id="txt_buscar" value="<?=$txt_buscar?>">
        <input type="hidden" name="txt_reporte" id="txt_reporte" value="<?=$txt_reporte?>">
    
       
        </form>
    </center>
</body>
<script language="javascript" type="text/javascript">
    if (document.getElementById("txt_buscar").value == "1") {       
        realizar_busqueda();
    } else {
        flag_activar_boton_buscar = true; //Habilita el boton buscar si es la primera vez que ingresa a la pagina
    }
</script>
</html>