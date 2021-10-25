<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\Optimization;

class MiniferMap
{

    public function __construct(
        private string $Path,
        private string $AssetsPath,
        private object $Data
    ) { }

    private function GetFilesToMinifyFromCategory(string $Category, string $FileExtension): array
    {
        $FilesToMinify = [];

        if(isset($this->Data->{$Category}))
        {
            foreach($this->Data->$Category as $MinifyItemRAW)
            {
                if(isset($MinifyItemRAW->source) && isset($MinifyItemRAW->result))
                {
                    if(is_string($MinifyItemRAW->source))
                        $FilesToMinify[] = new MinifyItem([$this->AssetsPath . '/' . $MinifyItemRAW->source], $this->AssetsPath . '/' . $MinifyItemRAW->result);

                    else if(is_array($MinifyItemRAW->source))
                    {
                        $CurrentSourceFiles = [];
                        foreach($MinifyItemRAW->source as $SourceFilePath)
                            $CurrentSourceFiles[] = $this->AssetsPath . '/' . $SourceFilePath;

                        $FilesToMinify[] = new MinifyItem($CurrentSourceFiles, $this->AssetsPath . '/' . $MinifyItemRAW->result);
                    }
                    

                }

                if(isset($MinifyItemRAW->sourceFolder) && isset($MinifyItemRAW->resultFolder))
                {
                    foreach(glob($this->AssetsPath . '/' . $MinifyItemRAW->sourceFolder . "/*.$FileExtension") as $SourceFilePath)
                    {
                        $ResultFolder = $this->AssetsPath . '/' . $MinifyItemRAW->resultFolder;
                        $ResultFilePath = $ResultFolder . '/' . basename($SourceFilePath);

                        if(!file_exists($ResultFolder))
                            mkdir($ResultFolder);

                        $FilesToMinify[] = new MinifyItem([$SourceFilePath], $ResultFilePath);
                    }
                }
            }
        }

        return $FilesToMinify;
    }

    public function GetFilesToMinify(): array
    {
        if(!file_exists($this->AssetsPath . '/dev/js'))
            mkdir($this->AssetsPath . '/dev/js', recursive: true);

        if(!file_exists($this->AssetsPath . '/production/js'))
            mkdir($this->AssetsPath . '/production/js', recursive: true);

        if(!file_exists($this->AssetsPath . '/dev/css'))
            mkdir($this->AssetsPath . '/dev/css', recursive: true);

        if(!file_exists($this->AssetsPath . '/production/css'))
            mkdir($this->AssetsPath . '/production/css', recursive: true);
        
        $FilesToMinify = [
            'css' => $this->GetFilesToMinifyFromCategory(Category: 'css', FileExtension: 'css'),
            'js' => $this->GetFilesToMinifyFromCategory(Category: 'js', FileExtension: 'js')
        ];
        
        return $FilesToMinify;
    }

    public static function FromJSONFile(string $Path, string $AssetsPath): ?self
    {
        if(file_exists($Path))
        {
            $FileData = @file_get_contents($Path);

            if(!empty($FileData))
            {
                $FileJSON = json_decode($FileData);

                if($FileJSON != null && is_object($FileJSON))
                    return new self($Path, $AssetsPath, $FileJSON);
            }
        }
        
        return null;
    }

}