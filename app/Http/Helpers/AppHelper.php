<?php
namespace App\Http\Helpers;

class AppHelper {

    const USER_SUPER_ADMIN = 1;
    const USER_ADMIN = 2;
    const USER_EMPLOYEE = 3;
    const USER_MANAGER = 4;

    const USER = [
        self::USER_SUPER_ADMIN => 'Super Admin',
        self::USER_ADMIN => 'Admin',
        self::USER_EMPLOYEE => 'Employee',
        self::USER_MANAGER => 'Manager',
    ];

    const UMBRELLA = 1;
    const SHIRT = 2;
    const FAN = 3;
    const CALENDAR = 4;
    
    const MATERIAL = [
        self::UMBRELLA => 'Umbrella',
        self::SHIRT => 'T-Shirt',
        self::FAN => 'Fan',
        self::CALENDAR => 'Calendar',
    ];

    const GENDER = [
        1 => 'Male',
        2 => 'Female'
    ];
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const LANGUAGES = ['en', 'kh'];

    
   
}



