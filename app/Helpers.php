<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class Helpers
{
    /**
     * Возвращает корректную форму множественного числа
     * Ограничения: только для целых чисел
     *
     * @param int $number Число, по которому вычисляем форму множественного числа
     * @param string $one Форма единственного числа: яблоко, час, минута
     * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
     * @param string $many Форма множественного числа для остальных чисел
     *
     * @return string Рассчитанная форма множественнго числа
     */
    public static function getNounPluralForm(int $number, string $one, string $two, string $many): string
    {
        $mod10 = $number % 10;
        $mod100 = $number % 100;

        switch (true) {
            case ($mod100 >= 11 && $mod100 <= 20):
                return $many;

            case ($mod10 > 5):
                return $many;

            case ($mod10 === 1):
                return $one;

            case ($mod10 >= 2 && $mod10 <= 4):
                return $two;

            default:
                return $many;
        }
    }

    public static function getNounPluralDateForm($createdAt): array
    {
        $locale = App::getLocale();
        $localeRu = 'ru';

        $oneYear = $locale == $localeRu ? 'год' : 'year';
        $twoYear = $locale == $localeRu ? 'года' : 'years';
        $manyYears = $locale == $localeRu ? 'лет' : 'years';

        $diff = (int)Carbon::now()->diffInYears($createdAt, true);
        if ($diff >= 1) return [$diff, static::getNounPluralForm($diff, $oneYear, $twoYear, $manyYears)];

        $oneMonth = $locale == $localeRu ? 'месяц' : 'month';
        $twoMonths = $locale == $localeRu ? 'месяца' : 'months';
        $manyMonths = $locale == $localeRu ? 'месяцев' : 'months';

        $diff = (int)Carbon::now()->diffInMonths($createdAt, true);
        if ($diff >= 1) return [$diff, static::getNounPluralForm($diff, $oneMonth, $twoMonths, $manyMonths)];

        $oneDay = $locale == $localeRu ? 'день' : 'day';
        $twoDays = $locale == $localeRu ? 'дня' : 'days';
        $manyDays = $locale == $localeRu ? 'дней' : 'days';

        $diff = (int)Carbon::now()->diffInDays($createdAt, true);
        if ($diff >= 1) return [$diff, static::getNounPluralForm($diff, $oneDay, $twoDays, $manyDays)];

        $oneHour = $locale == $localeRu ? 'час' : 'hour';
        $twoHours = $locale == $localeRu ? 'часа' : 'hours';
        $manyHours = $locale == $localeRu ? 'часов' : 'hours';

        $diff = (int)Carbon::now()->diffInHours($createdAt, true);
        if ($diff >= 1) return [$diff, static::getNounPluralForm($diff, $oneHour, $twoHours, $manyHours)];

        $oneMinute= $locale == $localeRu ? 'минуту' : 'minute';
        $twoMinute = $locale == $localeRu ? 'минуты' : 'minutes';
        $manyMinute = $locale == $localeRu ? 'минут' : 'minutes';

        $diff = (int)Carbon::now()->diffInMinutes($createdAt, true);
        return [$diff, static::getNounPluralForm($diff, $oneMinute, $twoMinute, $manyMinute)];
    }

    public static function removeUTF8Bom(string $text): string
    {
        $bom = pack('H*','EFBBBF');
        return preg_replace("/^$bom/", '', $text);
    }
}
