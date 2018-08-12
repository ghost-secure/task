<?php
$str1 = '30345783475983759374592757353405830495839054';
$str2 = '74592757353405830495839054';

/**
 * @param string $a
 * @return array
 */
function digitals($str) {
	return array_reverse(str_split($str));
}

/**
 * @param array $str_array1
 * @param array $str_array2
 * @return string
 */
function big_sum($str_array1, $str_array2) {
	$sum = array();
	foreach($str_array1 as $index => $value1) {

		// сумма цифр + то что в уме
		$sum[$index] = $str_array1[$index] + $str_array2[$index] + (isset($sum[$index]) ? $sum[$index] : 0);

		// выносим в ум то что больше 10
		$next = (int) ($sum[$index]/10);
		if($next) {
			$sum[$index + 1] = $next;
		}

		// в текущем разрядке только остаток от деления на 10
		$sum[$index] = fmod($sum[$index],10);
	}
	// обратно инвертируем все разряды и собираем в строку
	return implode("", array_reverse($sum));
}

// use
echo big_sum(digitals($str1), digitals($str2));