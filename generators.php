<?php

/** Создаёт случайное имя, включающее в себя ключ $key.
 *
 * @param $key
 *
 * @return string
 */
function randomName($key)
{
    return 'Ivan ' . $key;
}

/** Создаёт случайный номер телефона (сплошные цифры без знаков препинания) заданной длины.
 * @param $length
 *
 * @return string
 */
function randomPhoneNumber($length)
{
    $phone = '';
    for ($number = 1; $number <= $length; $number++) {
        $phone .= (string)mt_rand(0, 9);
    }
    return $phone;
}