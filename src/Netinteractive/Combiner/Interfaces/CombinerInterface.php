<?php
namespace Netinteractive\Combiner\Interfaces;

interface CombinerInterface{
    public static function incFile($fromPaths, $toPath, $extension=null);
}