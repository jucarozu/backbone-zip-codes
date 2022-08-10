<?php

if (!function_exists('replace_accents')) {
    function replace_accents(string $str)
    {
        $str = htmlentities($str, ENT_COMPAT, "UTF-8");
        $str = preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|ring);/', '$1', $str);

        return html_entity_decode($str);
    }
}
