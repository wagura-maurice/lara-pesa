<?php

namespace App\Helpers;

class Lara_Pesa {

	public static function phone_suffix($phone) {
        return "254" . substr($phone, -9);
    }
}