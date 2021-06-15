<?php

namespace App\ChainCommandBundle\DataProvider;

interface DataProviderInterface
{
    public function getData(): array;
    public function setData(array $data);
}
