<?php
namespace Netinteractive\Combiner\Interfaces;

interface CombinerInterface{
    public static function glueFiles($fromPaths, $toPath, $extension=null);
}