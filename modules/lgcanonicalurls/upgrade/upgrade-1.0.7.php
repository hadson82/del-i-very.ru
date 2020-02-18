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

function upgrade_module_1_0_7()
{
    // Renaming old configuration variables to new ones
    $update1 = Configuration::updateValue(
        'LGCANONICALURLS_CANONICDOMAIN',
        Configuration::get('LG_CANONICALURLS_CANONICAL_DOMAIN')
    );
    $update2 = Configuration::updateValue(
        'LGCANONICALURLS_HTTP_HEADERS',
        Configuration::get('LG_CANONICALURLS_HTTP_HEADERS')
    );
    $update3 = Configuration::updateValue(
        'LGCANONICALURLS_FORCEHTTPHTTPS',
        Configuration::get('LG_CANONICALURLS_FORCE_HTTP_HTTPS')
    );
    $update4 = Configuration::updateValue(
        'LGCANONICALURLS_HTTPHTTPS_VAL',
        Configuration::get('LG_CANONICALURLS_FORCE_HTTP_HTTPS_VALUE')
    );
    $update5 = Configuration::updateValue(
        'LGCANONICALURLS_IGNORE_PARAMS',
        Configuration::get('LG_CANONICALURLS_IGNORE_PARAMS')
    );
    $update6 = Configuration::updateValue(
        'LGCANONICALURLS_PARAMS',
        Configuration::get('LG_CANONICALURLS_PARAMS')
    );

    // New configuration variables
    $update7 = Configuration::updateValue('LGCANONICALURLS_CANHOMESTATUS', 0);
    $update8 = Configuration::updateValue('LGCANONICALURLS_CANONICALHOME', '');
    $update9 = Configuration::updateValue('LGCANONICALURLS_CANHOME_TEXT', '');

    // Deleting old configuration variables
    $delete1 = Configuration::deleteByName('LG_CANONICALURLS_CANONICAL_DOMAIN');
    $delete2 = Configuration::deleteByName('LG_CANONICALURLS_HTTP_HEADERS');
    $delete3 = Configuration::deleteByName('LG_CANONICALURLS_FORCE_HTTP_HTTPS');
    $delete4 = Configuration::deleteByName('LG_CANONICALURLS_FORCE_HTTP_HTTPS_VALUE');
    $delete5 = Configuration::deleteByName('LG_CANONICALURLS_IGNORE_PARAMS');
    $delete6 = Configuration::deleteByName('LG_CANONICALURLS_PARAMS');

    return $update1 && $update2 && $update3 && $update4 && $update5 && $update6
    && $update7 && $update8 && $update9
    && $delete1 && $delete2 && $delete3 && $delete4 && $delete5 && $delete6;
}
