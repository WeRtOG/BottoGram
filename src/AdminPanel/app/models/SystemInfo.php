<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\Models;

class SystemInfo
{
    public static function GetBottoGramVersion(): string
    {
        $BottoGramVersion = 'неизвестно';

        $ComposerLockPath = BOTTOGRAM_FR_PROJECTROOT_PATH . '/composer.lock';
        if(file_exists($ComposerLockPath))
        {
            $ComposerLockRaw = @file_get_contents($ComposerLockPath);
            $ComposerLockJson = json_decode($ComposerLockRaw ?? '', true);
            
            if(!empty($ComposerLockJson))
            {
                $Packages = $ComposerLockJson['packages'] ?? null;
                
                if($Packages != null)
                {
                    foreach($Packages as $Package)
                    {
                        if($Package['name'] == BOTTOGRAM_PACKAGE_NAME)
                        {
                            $Version = $Package['version'] ?? 'неизвестная версия';
                            $Reference = isset($Package['source']['reference']) ? substr($Package['source']['reference'], 0, 7) : 'неизвестный хеш';

                            if($Reference != null)
                            {
                                $BottoGramVersion = $Version . ' [' . $Reference . ']';
                            }
                        } 
                    }
                }

            }

        }

        return $BottoGramVersion;
    }

}