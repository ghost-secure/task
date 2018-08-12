<?php
namespace Api; 

class DataProvider implements DataProviderInterface
{
    private $requestClient;

    /**
     * Вне класса создаем $requestClient - new RequestClient($host, $login, $pass) и подставляем сюда при создании объекта
     *
     * @param RequestClient $requestClient
     */
    public function __construct(Client $requestClient)
    {
        $this->requestClient = $requestClient;
    }

    /**
     * @inheritdoc
     */
    public function getResponse(array $request)
    {
        $response = $this->requestClient->send($request);
        return $response;
    }
}