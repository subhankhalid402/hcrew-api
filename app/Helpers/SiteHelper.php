<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SiteHelper
{
    public static function reformatDbDateTime($datetimeStr)
    {

        if (!empty($datetimeStr)) {
            return Carbon::createFromFormat('d/m/Y H:i', $datetimeStr)->format('Y-m-d H:i:s');
        } else {
            return NULL;
        }
    }

    public static function reformatDbDate($dateStr)
    {
        if (!empty($dateStr)) {
            return Carbon::createFromFormat('d/m/Y H:i:s', "{$dateStr} 00:00:00")->format('Y-m-d');
        } else {
            return NULL;
        }
    }

    public static function reformatDate($date)
    {
        if (!empty($date)) {
            return Carbon::createFromFormat('Y-m-d H:i:s', "{$date} 00:00:00");
        } else {
            return NULL;
        }
    }

    public static function reformatReadableMonthNice($date)
    {
        if (!empty($date)) {
            return Carbon::parse($date)->format('M/Y');
        } else {
            return false;
        }
    }

    public static function reformatReadableDateTime($datetimeStr)
    {

        if (!empty($datetimeStr)) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $datetimeStr)->format('d/m/Y H:i');
        } else {
            return NULL;
        }
    }

    public static function reformatReadableDate($dateStr)
    {
        if (!empty($dateStr)) {
            return Carbon::createFromFormat('Y-m-d H:i:s', "{$dateStr} 00:00:00")->format('d/m/Y');
        } else {
            return NULL;
        }
    }

    public static function checkPermission($menu_key)
    {
        if (Auth::user()->menus->where('menu_key', $menu_key)->isNotEmpty()) {
            return true;
        } else {
            return false;
        }
    }

    public static function reformatReadableDateNice($dateStr)
    {
        if (!empty($dateStr)) {
            return Carbon::parse($dateStr)->format('d M, Y');
        } else {
            return false;
        }
    }

    public static function amountFormatter($amount, $decimalPlaces = 2)
    {
        return number_format($amount, $decimalPlaces, '.', ',');
    }

}
