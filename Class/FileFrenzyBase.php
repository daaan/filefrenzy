<?php

declare(strict_types=1);

class FileFrenzyBase
{

    protected string $absolutePath;

    public function __construct($attr)
    {
        $this->absolutePath = rtrim(ABSPATH, '/');
    }

    // From StackOverflow: https://stackoverflow.com/a/15188082 (but modified)
    public function humanReadableFileSize(int $size, ?int $decimals = null, $unit = "") {
        if ($decimals === null) {
            $decimals = 2;
        }
        if( (!$unit && $size >= 1<<30) || $unit === "GB") {
            return number_format($size / (1 << 30), $decimals) . "G";
        }
        if( (!$unit && $size >= 1<<20) || $unit === "MB") {
            return number_format($size / (1 << 20), $decimals) . "M";
        }
        if( (!$unit && $size >= 1<<10) || $unit === "KB") {
            return number_format($size / (1 << 10), $decimals) . "k";
        }
        return number_format($size) . " b";
    }

}
