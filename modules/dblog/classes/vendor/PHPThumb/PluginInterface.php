<?php
interface PluginInterface
{
    /**
     * @param  PHPThumb $phpthumb
     * @return PHPThumb
     */
    public function execute($phpthumb);
}
