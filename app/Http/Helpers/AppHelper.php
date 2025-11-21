<?php

namespace App\Http\Helpers;

class AppHelper
{
    // Existing constants and methods...

    const USER_SUPER_ADMIN = 1;
    const USER_ADMINISTRATOR = 2;
    const USER_ADMIN = 3;
    const USER_DIRECTOR = 4;
    const USER_MANAGER = 5;
    const USER_RSM = 6;
    const USER_ASM = 7;
    const USER_SUP = 8;
    const USER_EMPLOYEE = 9;

    const USER = [
        self::USER_SUPER_ADMIN => 'Super Administrator',
        self::USER_ADMINISTRATOR => 'Administrator',
        self::USER_ADMIN => 'Admin',
        self::USER_DIRECTOR => 'Director',
        self::USER_MANAGER => 'Manager',
        self::USER_RSM => 'RSM',
        self::USER_ASM => 'ASM',
        self::USER_SUP => 'Supervisor',
        self::USER_EMPLOYEE => 'Sale Support',
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

    // const AREAS = [
    //     'Ussa (R1-01)' => [
    //         1 => 'S-04',
    //         2 => 'S-111',
    //         3 => 'S-75',
    //         4 => 'S-90',
    //         5 => 'S-94',
    //     ],
    //     'S_Panha (R1-02)' => [
    //         6 => 'S-100',
    //         7 => 'S-112',
    //         8 => 'S-45',
    //         9 => 'S-77',
    //         10 => 'S-97',
    //     ],
    //     'S-VA (R2-01)' => [
    //         11 => 'S-113',
    //         12 => 'S-30',
    //         13 => 'S-86',
    //         14 => 'S-98',
    //         15 => 'S-99',
    //     ],
    //     'Doeun (R2-02)' => [
    //         16 => 'S-110',
    //         17 => 'S-76',
    //         18 => 'S-81',
    //         19 => 'S-91',
    //         20 => 'S-96',
    //     ],
    // ];

    const AREAS = [
        'Ussa (R1-01)' => [
            1 => 'S-04',
            2 => 'S-75',
            3 => 'S-90',
            4 => 'S-111',
        ],
        'S_Sovanpanha (R1-02)' => [
            5 => 'S-45',
            6 => 'S-97',
            7 => 'S-112',
        ],
        'L_Bundara (R1-03)' => [
            8 => 'S-77',
            9 => 'S-94',
            10 => 'S-100',
        ],
        'S_VA (R2-01)' => [
            11 => 'S-30',
            12 => 'S-86',
            13 => 'S-98',
            14 => 'S-99',
        ],
        'H_Doeun (R2-02)' => [
            15 => 'S-81',
            16 => 'S-91',
            17 => 'S-110',
        ],
        'Y_Sophat (R2-03)' => [
            18 => 'S-76',
            19 => 'S-96',
            20 => 'S-113',
        ],
    ];

    // / Customer type for sale promotion

        const អាហារដ្ឋាន = 1;
        const រមណីយដ្ឋាន = 2;
        const សាលារៀន = 3;
        const មន្ទីរពេទ្យ = 4;
        const តារាបាល់ = 5;
        const ក្លឺបហាត់ប្រាណ = 6;
        const កាស៊ីណូ = 7;
        const សណ្ឋាគារ = 8;
        const ការ៉ាសសាំង = 9;
        const កន្លែងកំសាន្ត = 10;
        const ម៉ាត់ឬផ្សារទំនើប = 11;
        const ហាងកាហ្វេ = 12;
        const ក្រុមហ៊ុនដៃគូសហការ = 13;
        const លក់តាមរទេះ = 14;


        const CUSTOMER_TYPE_PROVINCE = [
            1 => 'អាហារដ្ឋាន',
            2 => 'រមណីយដ្ឋាន',
            3 => 'សាលារៀន',
            4 => 'មន្ទីរពេទ្យ',
            5 => 'តារាងបាល់',
            6 => 'ក្លឺបហាត់ប្រាណ',
            7 => 'កាស៊ីណូ',
            8 => 'សណ្ឋាគារ',
            9 => 'ការ៉ាសសាំង',
            10 => 'កន្លែងកំសាន្ត',
            11 => 'ម៉ាត់ឬផ្សារទំនើប',
            12 => 'ហាងកាហ្វេ',
            13 => 'ក្រុមហ៊ុនដៃគូសហការ',
            14 => 'លក់តាមរទេះ',
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
public static function getAreaIdByText($areaText)
{
    foreach (self::AREAS as $areaGroup => $areas) {
        foreach ($areas as $id => $area) {
            if ($area === $areaText) {
                return $id;
            }
        }
    }
    return null;
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
    public static function normalizeIds($ids)
{
    if (empty($ids)) {
        return [];
    }
    
    // If it's a JSON string array
    if (is_string($ids) && strpos($ids, '[') === 0) {
        $decoded = json_decode($ids, true);
        return is_array($decoded) ? $decoded : [];
    }
    
    // If it's a single ID
    if (is_numeric($ids)) {
        return [$ids];
    }
    
    // If it's already an array
    if (is_array($ids)) {
        return $ids;
    }
    
    return [];
}
}
