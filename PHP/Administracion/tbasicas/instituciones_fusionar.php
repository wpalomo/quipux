x�Wko�6�W�W�i�g������<�N�s�`uw�0�D�ZeQ/I9���=����nRI��>�9tZ��?~��GqVimY�H�"�dV��6�Uu�K�[������tL������Vi��r-��_-u+V�j+u--���6O����.,�w^&����f�����d~L�NOO����2����E��R�^���)>�$�����5-��ׯ/���,�L�F���PA�9�7�TA����
UU�í�C��N���gk�է�J������Ԣ��6���wR�R��w%7M�B��v�6�$aU��ҫ����*\�M���BU�d4<;4���v���R��U%�*ĳ����p���
�S�Ɖ^\��_��b�T���*i`��/[��W?��)ʘ�H�V�c�q6����������z�jA�o^ !L�����(:_������3ZjQ�Ңz����kF8W�R��(����lw�_��l���S����&�\���.w�(�_�!�H��_B�ݒ��'��)��F&�V��fp�V%���	���֝�����,� #�f�����]%�ZJ��#�}'��(6�.;��El�_��q��������s�ձr�u�Um��3I���ǣ�Ф �*J(WY�A������|BG�&]�zբ�戎��xĕ� W���>�����(+f�~����G���ٽi��I�������>�#���|���щw~�1� í�Z�0=��y���m���s����A^|�8o'�\K����-��ׇ8Lp�%x���$��]^�\��x䴆^�	E�O�W෻���An�e��\�^���d�K8��-m�k�D�Q�}"&�H_z�x8Wwa�2��հr����Aq��I���8��v�Q�>�|#ǩ�w��gzy�-@O�j`���hJ=����x�.�.#�Vwƍ�n�������"=D���v5�2���(�c/Km���[��lOva�U�f�/ǴX�L{-q�g�ܐk�$�Y�����g�l����0�0ȑR�&q��޶��5���'��L��h	��v�oEm�i�EB�33sGr3�n����q��{�Ǳ�&�LVUHb'�)ߦY�����9Dm-��h�Z�bK_����S� �(���Ec�e�IPO����Np�C�ZT�w�1����)������+���D�!�$�X�Q��<V�Y�	c������A&�������4�X����CY˷J���nH�THW��0�v���+�����dk�e	-df�2���r���%�Z�!��O2>�O��9��w��	yf�IA�&�vM�P�Io����0���^x�ϗk�3J(��i ��	eJ�(n<��_���ZիY׼<�QL�ҏ�%�%`Η��܍���m��Z���@�H̀�fvt�L�ӿ;���Js.�������onbp��dn���ݡ(W?�q?���,����gV^�
Mʙ��!Xm���0�s
/�S���t�g��P:�œ���������3(��}���6����x�5`�*��b@+#ׂ����å�ؘ6ݔh3�$x�?��qĭ����w7{�~��w��\�ѱ"zL�                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 �n ' + document.getElementById("txt_inst_destino").options[document.getElementById("txt_inst_destino").selectedIndex].text
//                        + '\n hacia la institución ' + document.getElementById("txt_inst_origen").options[document.getElementById("txt_inst_origen").selectedIndex].text + '?')) {

                depe_origen = document.getElementById("txt_depe_origen").value;
                if(document.getElementById("chk_inst_adscrita").checked) inst_adscrita=1; else inst_adscrita=0;
                if(document.getElementById("chk_encerar_secuencias").checked) chk_encerar_secuencias=1; else chk_encerar_secuencias=0;
                txt_observacion = document.getElementById("txt_observacion").value;

                parametros = 'txt_inst_origen=' + inst_origen + '&txt_inst_destino=' + inst_destino +
                             '&txt_depe_origen=' + depe_origen + '&chk_inst_adscrita=' + inst_adscrita +
                             '&chk_encerar_secuencias=' + chk_encerar_secuencias + '&txt_observacion=' + txt_observacion;

                fjs_inicializar_proceso();
                fjs_ejecutar_proceso();
            } else {
                return;
            }
        }

        function fjs_inicializar_proceso() {
            document.getElementById("txt_inst_origen").disabled=true;
            document.getElementById("txt_inst_destino").disabled=true;
            document.getElementById("txt_depe_origen").disabled=true;
            document.getElementById("chk_inst_adscrita").disabled=true;
            document.getElementById("chk_encerar_secuencias").disabled=true;
            document.getElementById("txt_observacion").disabled=true;

            document.getElementById("btn_aceptar").disabled=true;
            document.getElementById("btn_cancelar").disabled=true;
            document.getElementById("tr_botones").style.display = 'none';
            tabla = '<h3><i>Estado de la migraci&oacute;n</i></h3><br>\n';
            tabla += '<table width="100%" border="0" cellspacing="5" cellpadding="0" class="borde_tab">\n';
            tabla += '<tr><th width="15%">Hora Inicio</th><th width="35%">Acci&oacute;n</th><th width="35%">Mensaje</th><th width="15%">Estado</th></tr>\n';
            for (i=0; i<proceso.length; ++i) {
                tabla += '<tr><td id="td_fecha_'+i+'"></td><td>'+proceso[i]+'</td><td id="td_mensaje_'+i+'"><td id="td_estado_'+i+'"></tr>\n';
            }
            tabla += '<table>';
            document.getElementById("div_proceso").innerHTML = tabla;

            return;
        }

        function fjs_ejecutar_proceso() {
            if (num_proceso>=proceso.length) {
                alert ('La ejecución del proceso de migración ha finalizado correctamente.');
                return;
            }
            if (trim(document.getElementById("td_fecha_"+num_proceso).innerHTML) == '') {
                fecha = new Date();
                h=("0" + fecha.getHours()).slice (-2);
                m=("0" + fecha.getMinutes()).slice (-2);
                s=("0" + fecha.getSeconds()).slice (-2);
                document.getElementById("td_fecha_"+num_proceso).innerHTML = h+":"+m+":"+s;
            }
            document.getElementById("td_estado_"+num_proceso).innerHTML = '<b>Ejecutando</b>';
            nuevoAjax('div_ejecutar_proceso', 'POST', pagina[num_proceso], parametros, 'fjs_finalizar_proceso()');
        }

        function fjs_finalizar_proceso() {
            var respuesta = document.getElementById('div_ejecutar_proceso').innerHTML;
            if (respuesta.indexOf('-') >= 0) {
                datos = respuesta.split('-', 2);
                document.getElementById("td_mensaje_"+num_proceso).innerHTML = datos[1];
                if (trim(datos[0])=='Error') {
                    document.getElementById("td_estado_"+num_proceso).innerHTML = '<font color="red"><b>Error</b></font>';
                    alert ('Existieron errores en la migración de datos.\nError: '+datos[1]);
                    return;
                }
                document.getElementById("td_estado_"+num_proceso).innerHTML = 'Finalizado';
                ++num_proceso;
                total_registros = 0;
                fjs_ejecutar_proceso();
                return;
            }
            if (trim(respuesta) == 'Finalizado') {
                document.getElementById("td_estado_"+num_proceso).innerHTML = 'Finalizado';
                ++num_proceso;
                total_registros = 0;
                fjs_ejecutar_proceso();
            } else {
                registros = parseInt(respuesta, 10);
                if (isNaN(registros)) registros = 0;
                total_registros += registros;
                document.getElementById("td_mensaje_"+num_proceso).innerHTML = 'Se han actualizado '+total_registros+' registros.';
                fjs_ejecutar_proceso();
            }
            return;
        }

        function fjs_cargar_combo_areas(inst_codi) {
        document.getElementById('tr_area_origen').style.display='';
            nuevoAjax('div_area_origen', 'POST', 'instituciones_fusionar_combos.php', 'txt_inst_codi='+inst_codi);
        }
    </script>
<body>
  <center>
    <table width="98%" border="0" cellspacing="5" cellpadding="0" class="borde_tab">
        <tr>
            <th width="100%" colspan="2"><center>Fusionar dos instituciones</center></th>
        </tr>
        <tr>
            <td class="listado1" width="30%">
                Instituci&oacute;n Origen
            </td>
            <td class="listado1" width="70%">
                <?=$menu_inst_origen?>
            </td>
        </tr>
        <tr id="tr_area_origen" style="display: none;">
            <td class="listado1">
                &Aacute;rea Origen
            </td>
            <td class="listado1">
                <div id="div_area_origen"></div>
            </td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
            <td class="listado1">
                Instituci&oacute;n Destino
            </td>
            <td class="listado1">
                <?=$menu_inst_destino?>
            </td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
            <td class="listado1" colspan="2">
                <input type="checkbox" id="chk_inst_adscrita" value="1">
                &nbsp;Migrar como instituci&oacute;n adscrita
            </td>
        </tr>
        <tr>
            <td class="listado1" colspan="2">
                <input type="checkbox" id="chk_encerar_secuencias" value="1">
                &nbsp;Encerar secuencias de los documentos
            </td>
        </tr>
        <tr>
            <td class="listado1" valign="middle">
                Observaci&oacute;n
            </td>
            <td class="listado1">
                <textarea id="txt_observacion" cols="100" class="tex_area" rows="3"></textarea>
            </td>
        </tr>
        <tr id="tr_botones">
            <td class="listado1" colspan="2">
                <center>
                    <br>
                    <input  name="btn_aceptar"  id="btn_aceptar"  type="button" class="botones" value="Aceptar" title="Inicia el proceso para fusionar las dos instituciones" onClick="fjs_fusionar_instituciones()"/>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input  name="btn_cancelar" id="btn_cancelar" type="button" class="botones" value="Regresar" title="Regresa al men&uacute; de administraci&oacute;n" onClick="location='../formAdministracion.php'"/>
                    <br>&nbsp;
                </center>
            </td>
        </tr>
    </table>
    <br>
    <div id="div_proceso" style="width: 98%"></div>
    <br>
    <div id="div_ejecutar_proceso" style="display: none"></div>
  </center>
</body>
</html>

