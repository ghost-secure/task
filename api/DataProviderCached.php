<?php
namespace Api; 

use DateTime;
use Exception;

class DataProviderCached implements DataProviderInterface
{
    private $cacheItemPool;
    private $dataProviderCached;

    /**
     * @param DataProviderInterface  $dataProvider
     * @param CacheItemPoolInterface $cacheItemPool
     */
    public function __construct(DataProviderInterface $dataProvider, CacheItemPoolInterface $cacheItemPool)
    {
        $this->dataProviderCached = $dataProvider;
        $this->cacheItemPool     = $cacheItemPool;
    }

    /**
     * @inheritDoc
     */
    public function getResponse(array $request)
    {
        $cacheKey = $this->getCacheKey($request);
        $cacheItem = $this->cacheItemPool->getItem($cacheKey);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }
        try {
            $response = $this->dataProviderCached->getResponse($request);
            $cacheItem
                ->set($response)
                ->expiresAt((new DateTime())->modify('+1 day'));
            return $response;
        } catch (Exception $e) {
            $this->logger->error('Error '. $e->getMessage());
            return [];
        }
    }
    /**
     * Для красивых ключей преобразуем в md5 строку из массива 
     * @param array $input
     * @return string
     */
    private function getCacheKey(array $input)
    {
        return md5(serialize($input));
    }
}