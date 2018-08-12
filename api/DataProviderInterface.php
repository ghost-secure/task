<?php
namespace Api; 

interface DataProviderInterface
{
    /**
     * @param array $request
     * @return array
     */
    public function getResponse(array $request);
}