<?php

/**
 * metodo die n' dump (debug)
 *
 * @param  mixed $value
 * @return void
 */
function dd($value)
{
    echo "<pre>";
    print_r($value);
    echo "</pre>";
    // exit;
}