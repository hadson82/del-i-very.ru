<?php
/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 * User: desar10
 * Date: 1/07/16
 * Time: 9:54
 */

function upgrade_module_1_0_14($module)
{
    return $module->desinstalarHook('actionProductAdd')
        && $module->desinstalarHook('actionProductUpdate')
        && $module->desinstalarHook('actionProductDelete');
}
