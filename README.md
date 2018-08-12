<p>Задание 1:&nbsp;<br />
Задание: Спроектировать схему БД для хранения библиотеки. Интересуют авторы и книги.<br />
Дополнительное задание: Написать SQL который вернет список книг, написанный 3-мя соавторами. Результат: книга - количество соавторов.<br />
Решение должно быть представлено в виде ссылки на https://www.db-fiddle.com/.</p>

<p>Ответ: https://www.db-fiddle.com/f/5mxBSCqon8nhW8GB1BcEoj/1</p>

<p>-------------------------------------------------------------------------------------------------------</p>

<p>Задание 2:<br />
Задание: Реализовать счетчик вызова скрипта. Было принято решение, хранить данные в файле.<br />
&lt;?php&nbsp;<br />
file_put_contents(&quot;./counter.txt&quot;, file_get_contents(&quot;./counter.txt&quot;) + 1);<br />
?&gt;<br />
Вопрос: Какие проблемы имеет данные подход? Как вы их можете решить?&nbsp;<br />
(Нельзя использовать другие технологии)<br />
Дополнительный вопрос: Через некоторое время нагрузка на сервер значительно выросла. Какие проблемы вы видите? Как вы их можете решить?&nbsp;<br />
Если бы вы могли выбрать другую технологию, то какую и почему?</p>

<p>Ответ:&nbsp;<br />
а) проблема одномоментного доступа к файлу нескольких процессов считывания и записи, необходимо включить блокировку файла через&nbsp;<br />
file_put_contents(&quot;./counter.txt&quot;, file_get_contents(&quot;./counter.txt&quot;) + 1, LOCK_EX);<br />
б) при возрастании нагрузки процесс блокировки файла будет создавать очередь ожидания, что негативно скажется на производительности данного решения.<br />
Лучше переходить к решениям с промужеточной агрегацией данных - например, в базе/таблице посещений, либо через memcached</p>

<p>-------------------------------------------------------------------------------------------------------</p>

<p>Задание: Проведите Code Review. Необходимо написать, с чем вы не согласны и почему.<br />
Дополнительное задание: Напишите свой вариант.&nbsp;<br />
Решение должно быть представлено в виде ссылки на https://github.com/.<br />
Требования были: Добавить возможность получения данных от стороннего сервиса.&nbsp;</p>

<p>Ответ: замечания указал ниже<br />
&lt;?php<br />
namespace src\Integration;&nbsp;<br />
/*-- namespace Vendor\Model согласно psr, а тут это скорее всего директория на сервере --*/</p>

<p>class DataProvider<br />
{<br />
&nbsp; &nbsp; private $host;&nbsp;<br />
&nbsp; &nbsp; private $user;<br />
&nbsp; &nbsp; private $password;</p>

<p>&nbsp; &nbsp; /**<br />
&nbsp; &nbsp; &nbsp;* @param $host<br />
&nbsp; &nbsp; &nbsp;* @param $user<br />
&nbsp; &nbsp; &nbsp;* @param $password<br />
&nbsp; &nbsp; &nbsp;*/<br />
&nbsp; &nbsp; /*-- не указан док и типы переменных, а также return --*/<br />
&nbsp; &nbsp; public function __construct($host, $user, $password)<br />
&nbsp; &nbsp; {<br />
&nbsp; &nbsp; &nbsp; &nbsp; $this-&gt;host = $host;<br />
&nbsp; &nbsp; &nbsp; &nbsp; $this-&gt;user = $user;<br />
&nbsp; &nbsp; &nbsp; &nbsp; $this-&gt;password = $password;<br />
&nbsp; &nbsp; }<br />
&nbsp; &nbsp;&nbsp;<br />
&nbsp; &nbsp; /**<br />
&nbsp; &nbsp; &nbsp;* @param array $request<br />
&nbsp; &nbsp; &nbsp;*<br />
&nbsp; &nbsp; &nbsp;* @return array<br />
&nbsp; &nbsp; &nbsp;*/<br />
&nbsp; &nbsp; /*-- не указан док, название метода неинформативное --*/<br />
&nbsp; &nbsp; public function get(array $request)<br />
&nbsp; &nbsp; {<br />
&nbsp; &nbsp; &nbsp; &nbsp; // returns a response from external service<br />
&nbsp; &nbsp; }<br />
}<br />
?&gt;<br />
&lt;?php</p>

<p>namespace src\Decorator;</p>

<p>use DateTime;<br />
use Exception;<br />
use Psr\Cache\CacheItemPoolInterface;<br />
use Psr\Log\LoggerInterface;<br />
use src\Integration\DataProvider;</p>

<p>class DecoratorManager extends DataProvider<br />
/*-- назвали декоратором, а нет общего интерфейса с декорируемым классом --*/<br />
{<br />
&nbsp; &nbsp; public $cache;<br />
&nbsp; &nbsp; public $logger;<br />
&nbsp; &nbsp; /*-- свойства не public --*/</p>

<p>&nbsp; &nbsp; /**<br />
&nbsp; &nbsp; &nbsp;* @param string $host<br />
&nbsp; &nbsp; &nbsp;* @param string $user<br />
&nbsp; &nbsp; &nbsp;* @param string $password<br />
&nbsp; &nbsp; &nbsp;* @param CacheItemPoolInterface $cache<br />
&nbsp; &nbsp; &nbsp;*/<br />
&nbsp; &nbsp; public function __construct($host, $user, $password, CacheItemPoolInterface $cache)<br />
&nbsp; &nbsp; {<br />
&nbsp; &nbsp; &nbsp; &nbsp; parent::__construct($host, $user, $password);<br />
&nbsp; &nbsp; &nbsp; &nbsp; $this-&gt;cache = $cache;<br />
&nbsp; &nbsp; }</p>

<p>&nbsp; &nbsp; /*-- описания нет, переменные не описаны, заниматься логированием должен другой класс --*/<br />
&nbsp; &nbsp; public function setLogger(LoggerInterface $logger)<br />
&nbsp; &nbsp; {<br />
&nbsp; &nbsp; &nbsp; &nbsp; $this-&gt;logger = $logger;<br />
&nbsp; &nbsp; }</p>

<p>&nbsp; &nbsp; /**<br />
&nbsp; &nbsp; &nbsp;* {@inheritdoc}<br />
&nbsp; &nbsp; &nbsp;*/<br />
&nbsp; &nbsp; public function getResponse(array $input)<br />
&nbsp; &nbsp; /*-- метод должен называться как в Dataprovider, get --*/<br />
&nbsp; &nbsp; {<br />
&nbsp; &nbsp; &nbsp; &nbsp; try {<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; $cacheKey = $this-&gt;getCacheKey($input);<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; $cacheItem = $this-&gt;cache-&gt;getItem($cacheKey);<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; if ($cacheItem-&gt;isHit()) {<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; return $cacheItem-&gt;get();<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; }</p>

<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; $result = parent::get($input);&nbsp;<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; /*-- $this-&gt;get --*/<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; /*-- заниматься кэшированием должен другой класс --*/<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; $cacheItem<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; -&gt;set($result)<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; -&gt;expiresAt(<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; (new DateTime())-&gt;modify(&#39;+1 day&#39;)<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; );</p>

<p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; return $result;<br />
&nbsp; &nbsp; &nbsp; &nbsp; } catch (Exception $e) {<br />
&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; $this-&gt;logger-&gt;critical(&#39;Error&#39;);<br />
&nbsp; &nbsp; &nbsp; &nbsp; }</p>

<p>&nbsp; &nbsp; &nbsp; &nbsp; return [];<br />
&nbsp; &nbsp; }<br />
&nbsp; &nbsp; /*-- док отсутствует, переменные не описаны, метод не public --*/<br />
&nbsp; &nbsp; public function getCacheKey(array $input)<br />
&nbsp; &nbsp; {<br />
&nbsp; &nbsp; &nbsp; &nbsp; return json_encode($input);<br />
&nbsp; &nbsp; }<br />
}<br />
-------------------------------------------------------------------------------------------------------</p>

<p>Задание 4:<br />
У вас нет доступа к библиотекам для работы с большими числами. Дано два числа в виде строки. Числа могут быть очень большими, могут не поместиться в 64 битный integer.<br />
Задание: Написать функцию которая вернет сумму этих чисел.&nbsp;<br />
Решение должно быть представлено в виде ссылки на https://github.com/.</p>

<p>Ответ: https://github.com/ghost-secure/task/blob/master/bigsum.php<br />
-------------------------------------------------------------------------------------------------------</p>

<p>Задание 5:<br />
Дано:<br />
CREATE TABLE test (<br />
&nbsp; id INT NOT NULL PRIMARY KEY<br />
);<br />
INSERT INTO test (id) VALUES (1), (2), (3), (6), (8), (9), (12);<br />
Задание Написать SQL запрос который выведет все пропуски.<br />
Результат:<br />
FROM | TO<br />
3&nbsp; &nbsp; &nbsp; &nbsp;| 6<br />
6&nbsp; &nbsp; &nbsp; &nbsp;| 8<br />
9&nbsp; &nbsp; &nbsp; &nbsp;| 12<br />
Решение должно быть представлено в виде ссылки на https://www.db-fiddle.com/.</p>

<p>Ответ: https://www.db-fiddle.com/f/2P3xwx62YQ5VHCdMGWYynK/0</p>
