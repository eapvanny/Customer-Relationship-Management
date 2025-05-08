<?php
namespace App\Http\Helpers;

class AppHelper
{
    // Existing constants and methods...
    
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

    const រទះរុញ = 1;
    const រុឺម៉ក = 2;
    const លក់រាយ = 3;
    const ធុងទឹកកក = 4;
    const CUSTOMER_TYPE = [
        1 => 'រទះរុញ',
        2 => 'រុឺម៉ក',
        3 => 'លក់រាយ',
        4 => 'ធុងទឹកកក',
    ];

    const ALL = 1;
    const SALE = 2;
    const SE = 3;
    const USER_TYPE = [
        self::ALL => 'All',
        self::SALE => 'Sale',
        self::SE => 'SE',
    ];

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const LANGUAGES = ['en', 'kh'];

    const AREAS = [
        'Ussa (R1-01)' => [
            1 => 'S-04',
            2 => 'S-111',
            3 => 'S-75',
            4 => 'S-90',
            5 => 'S-94',
        ],
        'S_Panha (R1-02)' => [
            6 => 'S-100',
            7 => 'S-112',
            8 => 'S-45',
            9 => 'S-77',
            10 => 'S-97',
        ],
        'S-VA (R2-1)' => [
            11 => 'S-113',
            12 => 'S-30',
            13 => 'S-86',
            14 => 'S-98',
            15 => 'S-99',
        ],
        'Doeun (R2-2)' => [
            16 => 'S-110',
            17 => 'S-76',
            18 => 'S-81',
            19 => 'S-91',
            20 => 'S-96',
        ],
    ];
    

    public static function getAreas()
    {
        return self::AREAS;
    }

    /**
     * Resolve area_id to a display name (e.g., 'S-04' to 'Ussa (R1-01): S-04').
     *
     * @param string $area_id
     * @return string
     */
    public static function getAreaName($area_id)
{
    foreach (self::AREAS as $group => $areas) {
        if (isset($areas[$area_id])) {
            return "$group: {$areas[$area_id]}";
        }
    }
    return $area_id ?: '-';
}

}