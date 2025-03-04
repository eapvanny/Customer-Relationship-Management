<?php
namespace App\Http\Helpers;

class AppHelper {

    const USER_SUPER_ADMIN = 1;
    const USER_ADMIN = 2;
    const USER_EMPLOYEE = 3;

    const USER = [
        self::USER_SUPER_ADMIN => 'Super Admin',
        self::USER_ADMIN => 'Admin',
        self::USER_EMPLOYEE => 'Employee',
    ];

    const GENDER = [
        1 => 'Male',
        2 => 'Female'
    ];
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const LANGUAGES = ['en', 'kh'];

    
   
}



