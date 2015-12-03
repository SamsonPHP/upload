<?php
/**
 * Created by PhpStorm.
 * User: onysko
 * Date: 13.02.2015
 * Time: 15:58
 */

namespace samsonphp\upload;


interface AdapterInterface
{
    /**
     * @return File[] Array of uploaded files
     */
    public function upload();
}
