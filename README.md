Задание 1: 
Задание: Спроектировать схему БД для хранения библиотеки. Интересуют авторы и книги.
Дополнительное задание: Написать SQL который вернет список книг, написанный 3-мя соавторами. Результат: книга - количество соавторов.
Решение должно быть представлено в виде ссылки на https://www.db-fiddle.com/.

Ответ: https://www.db-fiddle.com/f/5mxBSCqon8nhW8GB1BcEoj/1

-------------------------------------------------------------------------------------------------------

Задание 2:
Задание: Реализовать счетчик вызова скрипта. Было принято решение, хранить данные в файле.
<pre>
<?php 
file_put_contents("./counter.txt", file_get_contents("./counter.txt") + 1);
?>
</pre>
Вопрос: Какие проблемы имеет данные подход? Как вы их можете решить? 
(Нельзя использовать другие технологии)
Дополнительный вопрос: Через некоторое время нагрузка на сервер значительно выросла. Какие проблемы вы видите? Как вы их можете решить? 
Если бы вы могли выбрать другую технологию, то какую и почему?

Ответ: 
а) проблема одномоментного доступа к файлу нескольких процессов считывания и записи, необходимо включить блокировку файла через 
file_put_contents("./counter.txt", file_get_contents("./counter.txt") + 1, LOCK_EX);
б) при возрастании нагрузки процесс блокировки файла будет создавать очередь ожидания, что негативно скажется на производительности данного решения.
Лучше переходить к решениям с промужеточной агрегацией данных - например, в базе/таблице посещений, либо через memcached

-------------------------------------------------------------------------------------------------------

Задание: Проведите Code Review. Необходимо написать, с чем вы не согласны и почему.
Дополнительное задание: Напишите свой вариант. 
Решение должно быть представлено в виде ссылки на https://github.com/.
Требования были: Добавить возможность получения данных от стороннего сервиса. 

Ответ: замечания указал ниже
<?php
namespace src\Integration; 
/*-- namespace Vendor\Model согласно psr, а тут это скорее всего директория на сервере --*/

class DataProvider
{
    private $host; 
    private $user;
    private $password;

    /**
     * @param $host
     * @param $user
     * @param $password
     */
    /*-- не указан док и типы переменных, а также return --*/
    public function __construct($host, $user, $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }
    
    /**
     * @param array $request
     *
     * @return array
     */
    /*-- не указан док, название метода неинформативное --*/
    public function get(array $request)
    {
        // returns a response from external service
    }
}
?>
<?php

namespace src\Decorator;

use DateTime;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use src\Integration\DataProvider;

class DecoratorManager extends DataProvider
/*-- назвали декоратором, а нет общего интерфейса с декорируемым классом --*/
{
    public $cache;
    public $logger;
    /*-- свойства не public --*/

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     * @param CacheItemPoolInterface $cache
     */
    public function __construct($host, $user, $password, CacheItemPoolInterface $cache)
    {
        parent::__construct($host, $user, $password);
        $this->cache = $cache;
    }

    /*-- описания нет, переменные не описаны, заниматься логированием должен другой класс --*/
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(array $input)
    /*-- метод должен называться как в Dataprovider, get --*/
    {
        try {
            $cacheKey = $this->getCacheKey($input);
            $cacheItem = $this->cache->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $result = parent::get($input); 
            /*-- $this->get --*/
            /*-- заниматься кэшированием должен другой класс --*/
            $cacheItem
                ->set($result)
                ->expiresAt(
                    (new DateTime())->modify('+1 day')
                );

            return $result;
        } catch (Exception $e) {
            $this->logger->critical('Error');
        }

        return [];
    }
    /*-- док отсутствует, переменные не описаны, метод не public --*/
    public function getCacheKey(array $input)
    {
        return json_encode($input);
    }
}
-------------------------------------------------------------------------------------------------------

Задание 4:
У вас нет доступа к библиотекам для работы с большими числами. Дано два числа в виде строки. Числа могут быть очень большими, могут не поместиться в 64 битный integer.
Задание: Написать функцию которая вернет сумму этих чисел. 
Решение должно быть представлено в виде ссылки на https://github.com/.

Ответ: https://github.com/ghost-secure/task/blob/master/bigsum.php
-------------------------------------------------------------------------------------------------------

Задание 5:
Дано:
CREATE TABLE test (
  id INT NOT NULL PRIMARY KEY
);
INSERT INTO test (id) VALUES (1), (2), (3), (6), (8), (9), (12);
Задание Написать SQL запрос который выведет все пропуски.
Результат:
FROM | TO
3       | 6
6       | 8
9       | 12
Решение должно быть представлено в виде ссылки на https://www.db-fiddle.com/.

Ответ: https://www.db-fiddle.com/f/2P3xwx62YQ5VHCdMGWYynK/0

