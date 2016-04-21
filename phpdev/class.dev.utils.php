<?php

class DevUtils
{
    public static function cleanArgv($key, $type = 'string')
    {
        if (isset($_SERVER['argv'])) {
            if ($type == 'string') {
                return (isset($_SERVER['argv'][$key]) && !empty($_SERVER['argv'][$key])) ? trim($_SERVER['argv'][$key]) : '';
            } elseif ($type == 'numeric') {
                return (isset($_SERVER['argv'][$key]) && is_numeric($_SERVER['argv'][$key])) ? intval($_SERVER['argv'][$key]) : '';
            } elseif ($type == 'boolean') {
                return (isset($_SERVER['argv'][$key])) ? true : false;
            } elseif ($type == 'array') {
                return (isset($_SERVER['argv'][$key]) && is_array($_SERVER['argv'][$key])) ? $_SERVER['argv'][$key] : array();
            }
        }
    
        return false;
    }
    
    public static function formatWindowsPath($path)
    {
        return str_replace('/', '\\', $path);
    }
    
    public static function formatUnixPath($path)
    {
        return str_replace('\\', '/', $path);
    }
    
    public static function startWith($string, $search)
    {
        $length = strlen($search);
        return (substr($string, 0, $length) === $search);
    }
    
    public static function endWith($string, $search)
    {
        $length = strlen($search);
        $start  = $length * -1;
        return (substr($string, $start) === $search);
    }
}
