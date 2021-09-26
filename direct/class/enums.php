<?php

abstract class Sources
{
    const FFNET = "ffnet";
    const FF = "ff"; // old style source, need to convert to new style.
    const HPFF = "hpff";
    const FPCOM = "fpcom";
    const FP = "fp"; // old style source, need to convert to new style.
    const HPFFA = "hpffa";
    const FHCOM = "fhcom";
    const SOURCES = [Sources::FFNET, Sources::HPFF, Sources::FPCOM, Sources::HPFFA, Sources::FHCOM];

    /**
     * @param string $source
     * @return bool
     */
    
    static function isValid($source) {
        return in_array(strtolower($source), Sources::SOURCES);
    }

    static function update($source) {
        switch ($source) {
            case Sources::FF:
                return Sources::FFNET;
            
            case Sources::FP:
                return Sources::FPCOM;
        }

        return $source;
    }
}

abstract class FileType
{
    const EPUB = "epub";
    const MOBI = "mobi";
    const TYPES = [FileType::EPUB, FileType::MOBI];
    const DEFAULT = FileType::EPUB;

    /**
     * @param string $type
     * @return bool
     */
    static function isValid($type) {
        return in_array(strtolower($type), FileType::TYPES);
    }
}