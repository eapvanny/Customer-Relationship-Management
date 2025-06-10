<?php

namespace App\Http\Helpers;

class AppHelper
{
    // Existing constants and methods...

    const USER_SUPER_ADMIN = 1;
    const USER_ADMIN = 2;
    const USER_DIRECTOR = 3;
    const USER_MANAGER = 4;
    const USER_RSM = 5;
    const USER_ASM = 6;
    const USER_SUP = 7;
    const USER_EMPLOYEE = 8;

    const USER = [
        self::USER_SUPER_ADMIN => 'Super Admin',
        self::USER_ADMIN => 'Admin',
        self::USER_DIRECTOR => 'Director',
        self::USER_MANAGER => 'Manager',
        self::USER_RSM => 'RSM',
        self::USER_ASM => 'ASM',
        self::USER_SUP => 'Supervisor',
        self::USER_EMPLOYEE => 'Employee',
    ];

    const ALL = 1;
    const SALE = 2;
    const SE = 3;
    const USER_TYPE = [
        self::ALL => 'All',
        self::SALE => 'SSP',
        self::SE => 'SE',
    ];

    const UMBRELLA = 1;
    const TUMBLER = 2;
    const PARASOL = 3;
    const JACKET = 4;
    const BOTTLE_HOLDER = 5;
    const ICE_BOX_200L = 6;
    const CAP_BLUE = 7;
    const HAT = 8;
    const GLASS_CUP = 9;
    const ICE_BOX_27L = 10;
    const ICE_BOX_45L = 11;
    const T_SHIRT_RUNNING = 12;
    const LUNCH_BOX = 13;
    const LSK_FAN_16_DSF_9163 = 14;
    const PAPER_CUP_250ML = 15;
    const TISSUE_BOX = 16;

    const MATERIAL = [
        self::UMBRELLA => 'Umbrella',
        self::TUMBLER => 'Tumbler',
        self::PARASOL => 'Parasol',
        self::JACKET => 'Jacket',
        self::BOTTLE_HOLDER => 'Bottle holder',
        self::ICE_BOX_200L => 'Ice box 200L',
        self::CAP_BLUE => 'Cap Blue',
        self::HAT => 'Hat',
        self::GLASS_CUP => 'Glass cup',
        self::ICE_BOX_27L => 'Ice Box 27L',
        self::ICE_BOX_45L => 'Ice Box 45L',
        self::T_SHIRT_RUNNING => 'T-Shirt (Running)',
        self::LUNCH_BOX => 'Lunch Box',
        self::LSK_FAN_16_DSF_9163 => 'LSK Fan 16" DSF-9163',
        self::PAPER_CUP_250ML => 'Paper Cup (250ml)',
        self::TISSUE_BOX => 'Tissue Box',
    ];

    const GENDER = [
        1 => 'Male',
        2 => 'Female'
    ];

    const រទះរុញ = 1;
    const រុឺម៉ក = 2;
    const លក់រាយ = 3;
    const ធុងទឹកកក = 4;
    const អ្នកប្រើប្រាស់ចុងក្រោយ​ = 5;
    const CUSTOMER_TYPE = [
        1 => 'រទះរុញ',
        2 => 'រុឺម៉ក',
        3 => 'លក់រាយ',
        4 => 'ធុងទឹកកក',
        5 => 'អ្នកប្រើប្រាស់ចុងក្រោយ',
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


    public static function getAreaNameById($areaId)
    {
    foreach (self::AREAS as $area => $rooms) {
            if (isset($rooms[$areaId])) {
                return $rooms[$areaId];
            }
        }
        return 'Unknown Area'; // fallback if not found
    }

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
