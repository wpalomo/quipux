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
$ruta_raiz = isset($ruta_raiz) ? $ruta_raiz : "..";
session_start();
include_once "$ruta_raiz/rec_session.php";
if (isset ($replicacion) && $replicacion && $config_db_replica_trd_lista_expediente!="") $db = new ConnectionHandler($ruta_raiz,$config_db_replica_trd_lista_expediente);

include_once "$ruta_raiz/tipo_documental/obtener_datos_trd.php";
include_once "$ruta_raiz/funciones_interfaz.php";
echo "<html>".html_head();
?>
<script type="text/javascript">
    var tot_exp=0;
    function regresar(){
        window.location.reload();
    }
    function incluir_documento()
    {
        window.open("<?=$ruta_raiz?>/expediente/IncluirEnExpediente.php?numrad=<?=$verrad?>","MflujoExp<?=$verrad?>","height=450,width=750,scrollbars=yes");
    }
   
</script>

<body bgcolor="#FFFFFF" topmargin="0">
    <center>

<?
    $boton = "";
        if ($nivel_seguridad_documento > 3)
            $boton = "<input type='button' onClick='DefinirMetadato();' name='Submit1' value='Definir Metadatos' ".
                     "title='Se define el metadato para el documento' class='botones_largo'>";
        
        $rs = ConsultarMetadatosRadiDoc($db, $verrad);
?>
        <table border="0" width="100%" class="borde_tab" align="center" class="titulos2">
            <tr>
                <th><?=$descDependencia?></th><th>Metadato</th>
            </tr>
<?
        $met_codi = 0;
        $nombre_completo = "Este documento no tiene metadato definido.";
        while ($rs && !$rs->EOF) {
            $nombre_trd = $rs->fields["METADATO_TEXTO"];
            echo '<tr><td class="listado1">'.$rs->fields["DEPE_NOMB"].'</td><td class="listado1">'.$nombre_trd.'</td></tr>';
            if ($rs->fields["DEPE_CODI"] == $_SESSION["depe_codi"]) {
                $met_codi = $rs->fields["MET_CODI"];
                $nombre_completo = $nombre_trd;
            }
            $rs->MoveNext();
        }
        if ($met_codi == 0) 
            echo '<tr><td class="listado1">'.$_SESSION["depe_nomb"].'</td><td class="listado1">'.$nombre_completo.'</td></tr>';

?>
        </table>

<?
    echo "<br><center>$boton</center><br>";
?>
</body>
</html>