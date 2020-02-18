/*
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.fontweight0
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf https://www.lineagrafica.es/licenses/license_es.pdf https://www.lineagrafica.es/licenses/license_fr.pdf
 */

$(document).ready( function() {

    if($('#lgcanonicalurls_ignoreparams_off').prop('checked') == true) {
        $('#lgcanonicalurls_params').prop('disabled',true);
        $('#lgcanonicalurls_params').closest('div.form-group').slideUp();
    }

    if($('#lgcanonicalurls_ignoreparams_on').prop('checked') == true) {
        $('#lgcanonicalurls_params').prop('disabled',false);
        $('#lgcanonicalurls_params').closest('div.form-group').slideDown();
    }

    if($('#lgcanonicalurls_force_http_https_off').prop('checked') == true) {
        $('#lgcanonicalurls_force_http_https_value_http').prop('disabled',true);
        $('#lgcanonicalurls_force_http_https_value_https').prop('disabled',true);
        $('input[name^="lgcanonicalurls_force_http_https_value"]:first').closest('div.form-group').slideUp();
    }

    if($('#lgcanonicalurls_force_http_https_on').prop('checked') == true) {
        $('#lgcanonicalurls_force_http_https_value_http').prop('disabled',false);
        $('#lgcanonicalurls_force_http_https_value_https').prop('disabled',false);
        $('input[name^="lgcanonicalurls_force_http_https_value"]:first').closest('div.form-group').slideDown();
    }

    if($('#lgcanonicalurls_canonicalhome_off').prop('checked') == true) {
        $('input[name^="lg_radiotext"]').prop('disabled', true);
        $('#lgcanonicalurls_canonicalhome_type_default').closest('div.form-group').slideUp();
        $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]:first')
            .closest('div.form-group')
            .parent()
            .closest('div.form-group').slideUp();
    }

    if($('#lgcanonicalurls_canonicalhome_on').prop('checked') == true) {
        $('input[name^="lg_radiotext"]').prop('disabled', false);
        $('#lgcanonicalurls_canonicalhome_type_default').closest('div.form-group').slideDown();
        $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]:first')
            .closest('div.form-group')
            .parent()
            .closest('div.form-group').slideDown();
    }

    if($('#lgcanonicalurls_ignoreparams_on').prop('checked') == true) {
        $('#lgcanonicalurls_params').prop('disabled', false);
    }

    $(document).on('change', '#lgcanonicalurls_ignoreparams_off', function() {
        $('#lgcanonicalurls_params').prop('disabled',true);
        $('#lgcanonicalurls_params').closest('div.form-group').slideUp();
    });

    $(document).on('change', '#lgcanonicalurls_ignoreparams_on', function() {
        $('#lgcanonicalurls_params').prop('disabled', false);
        $('#lgcanonicalurls_params').closest('div.form-group').slideDown();
    });

    $(document).on('change', '#lgcanonicalurls_force_http_https_off', function() {
        $('#lgcanonicalurls_force_http_https_value_http').prop('disabled',true);
        $('#lgcanonicalurls_force_http_https_value_https').prop('disabled',true);
        $('input[name^="lgcanonicalurls_force_http_https_value"]:first').closest('div.form-group').slideUp();
    });

    $(document).on('change', '#lgcanonicalurls_force_http_https_on', function() {
        $('#lgcanonicalurls_force_http_https_value_http').prop('disabled',false);
        $('#lgcanonicalurls_force_http_https_value_https').prop('disabled',false);
        $('input[name^="lgcanonicalurls_force_http_https_value"]:first').closest('div.form-group').slideDown();
    });

    $(document).on('change', 'input[name="lgcanonicalurls_type"]', function() {
        $('input[name="lgcanonicalurls_type_selected"]').val($(this).val());

        if($(this).val()>1) {
            $('input[name^="lgcanonicalurls_canonical_url"]').each(function() {
                $(this).prop('disabled', false);
            });
        } else {
            $('input[name^="lgcanonicalurls_canonical_url"]').each(function() {
                $(this).prop('disabled', true);
            });
        }
    });

    $(document).on('change', 'input[name="lgcanonicalurls_canonicalhome_type"]', function() {
        $('input[name="lgcanonicalurls_canonicalhome_type_custom"]').val($(this).val());

        if($(this).val() == 'custom') {
            $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]').each(function() {
                $(this).prop('disabled', false);
            });
        } else {
            $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]').each(function() {
                $(this).prop('disabled', true);
            });
        }
    });

    window.lgcanonicalrurls_guardar = function () {
        var tipo = '';
        $('input[name^="lgcanonicalurls_type"]').each(function() {
            if($(this).prop('checked')) {
                tipo = $(this).val();
            }
        });
        var datos = {
            controller: 'AdminModules',
            action: 'ajaxSaveProductForm',
            module_name: 'lgcanonicalurls',
            configure: 'lgcanonicalurls',
            id_product: id_product,
            token: $('#product-tab-content-ModuleLgcanonicalurls').find('#form_token').val(),
            lgcanonicalurls_type: tipo,
            data: JSON.stringify($(this).serializeArray()),
        };
        $('input[name^="lgcanonicalurls_canonical_url"]').each(function() {
            if(!$(this).prop('disabled')) {
                datos[$(this).attr('name')] = $(this).val();
            }
        });
        doAdminAjax(
            datos,
            function (ret) {
                ret = $.parseJSON(ret);
                if (ret.status == 'ok')
                    showSuccessMessage(ret.confirmation);
                else
                    showErrorMessage(ret.error);
            }
        );
    }

    $(document).on('click','#lgcanonicalurls_canonicalhome_off', function(){
        $('input[name^="lg_radiotext"]').prop('disabled', true);
        $('#lgcanonicalurls_canonicalhome_type_default').closest('div.form-group').slideUp();
        $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]:first')
            .closest('div.form-group')
            .parent()
            .closest('div.form-group').slideUp();
    });
    $(document).on('click','#lgcanonicalurls_canonicalhome_on', function(){
        $('input[name^="lg_radiotext"]').prop('disabled', false);
        $('#lgcanonicalurls_canonicalhome_type_default').closest('div.form-group').slideDown();
        $('input[name^="LGCANONICALURLS_CANHOME_TEXT"]:first')
            .closest('div.form-group')
            .parent()
            .closest('div.form-group').slideDown();
    });
    function getLgcanonicalurlsType() {

    }
});
