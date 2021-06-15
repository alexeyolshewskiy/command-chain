<?php

namespace App\ChainCommandBundle\DataProvider;

class JsonFileProvider implements DataProviderInterface
{
    private string $storageFileName;

    public function __construct(string $storageFileName)
    {
        $this->storageFileName = $storageFileName;
    }

    public function getData(): array
    {
        try {
            if (file_exists($this->storageFileName)) {
                $json = file_get_contents($this->storageFileName);
                $data = json_decode($json, true);
                return is_null($data) ? [] : $data;
            }
            return [];
        } catch (\Exception $exception) {
            return [];
        }
    }

    public function setData(array $data)
    {
        $json = json_encode($data);
        file_put_contents($this->storageFileName, $json);
    }
}
