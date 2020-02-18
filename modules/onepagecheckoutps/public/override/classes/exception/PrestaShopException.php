<?php
/**
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * We are experts and professionals in PrestaShop
 *
 * @category  PrestaShop
 * @category  Module
 * @author    PresTeamShop.com <support@presteamshop.com>
 * @copyright 2011-2016 PresTeamShop
 * @license   see file: LICENSE.txt
 */

class PrestaShopException extends PrestaShopExceptionCore
{
    /**
     * This method acts like an error handler, if dev mode is on, display the error else use a better silent way
     */
    public function displayMessage()
    {/* KEY_OPC_2.1.6 */
        header('HTTP/1.1 500 Internal Server Error');
        if (_PS_MODE_DEV_ || defined('_PTS_SHOW_ERRORS_') || defined('_PS_ADMIN_DIR_')) {
            // Display error message
            echo '<style>
				#psException{font-family: Verdana; font-size: 14px}
				#psException h2{color: #F20000}
				#psException p{padding-left: 20px}
				#psException ul li{margin-bottom: 10px}
				#psException a{font-size: 12px; color: #000000}
				#psException .psTrace, #psException .psArgs{display: none}
				#psException pre{border:1px solid #236B04; background-color:#EAFEE1; padding:5px; font-family:Courier;
				width: 99%; overflow-x: auto; margin-bottom: 30px;}
				#psException .psArgs pre{background-color: #F1FDFE;}
				#psException pre .selected{color: #F20000; font-weight: bold;}
			</style>';
            echo '<div id="psException">';
            echo '<h2>['.get_class($this).']</h2>';

            if (!method_exists($this, 'getExtendedMessage')) {
                printf(
                    '<p><b>%s</b><br /><i>at line </i><b>%d</b><i> in file </i><b>%s</b></p>',
                    Tools::safeOutput($this->getMessage()),
                    $this->getLine(),
                    ltrim(str_replace(array(_PS_ROOT_DIR_, '\\'), array('', '/'), $this->getFile()), '/')
                );
            } else {
                echo $this->getExtendedMessage();
            }
            
            $this->displayFileDebug($this->getFile(), $this->getLine());

            // Display debug backtrace
            echo '<ul>';
            foreach ($this->getTrace() as $id => $trace) {
                $relative_file = '';

                if (isset($trace['file'])) {
                    $relative_file = ltrim(
                        str_replace(
                            array(_PS_ROOT_DIR_, '\\'),
                            array('', '/'),
                            $trace['file']
                        ),
                        '/'
                    );
                }
                $current_line = (isset($trace['line'])) ? $trace['line'] : '';

                if (defined('_PS_ADMIN_DIR_')) {
                    $relative_file = str_replace(
                        basename(_PS_ADMIN_DIR_).DIRECTORY_SEPARATOR,
                        'admin'.DIRECTORY_SEPARATOR,
                        $relative_file
                    );
                }
                echo '<li>';
                echo '<b>'.((isset($trace['class'])) ? $trace['class'] : '');
                echo((isset($trace['type'])) ? $trace['type'] : '').$trace['function'].'</b>';
                echo ' - <a style="font-size: 12px; color: #000000; cursor:pointer; color: blue;"
					onclick="document.getElementById(\'psTrace_'.$id.'\').style.display =
					(document.getElementById(\'psTrace_'.$id.'\').style.display != \'block\') ? \'block\' : \'none\';
					return false">[line '.$current_line.' - '.$relative_file.']</a>';

                if (isset($trace['args']) && count($trace['args'])) {
                    echo ' - <a style="font-size: 12px; color: #000000; cursor:pointer; color: blue;"
						onclick="document.getElementById(\'psArgs_'.$id.'\').style.display =
						(document.getElementById(\'psArgs_'.$id.'\').style.display != \'block\') ? \'block\' : \'none\';
						return false">['.count($trace['args']).' Arguments]</a>';
                }

                if ($relative_file) {
                    $this->displayFileDebug($trace['file'], $trace['line'], $id);
                }
                if (isset($trace['args']) && count($trace['args'])) {
                    $this->displayArgsDebug($trace['args'], $id);
                }
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        } else {
            // If not in mode dev, display an error page
            if (file_exists(_PS_ROOT_DIR_.'/error500.html')) {
                echo Tools::file_get_contents(_PS_ROOT_DIR_.'/error500.html');
            }
        }
        // Log the error in the disk
        if (method_exists($this, 'logError')) {
            $this->logError();
        }
        exit;
    }
}
