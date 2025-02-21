<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;
use App\User;
use App\Role;
use App\UserRole;
use App\Permission;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TranslationSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public $locales = ['en', 'kh'];
    public function run()
    {
        if (Translation::count() == 0) {
            foreach ($this->locales as $locale) {
                if (in_array($locale, $this->locales)) {
                    $localPath = __DIR__ . '/resources/lang/' . $locale . '.json';
                    if (File::exists($localPath)) {
                        $translations = json_decode(File::get($localPath), true);
                        $data = [];
                        $data_array = [];
                        foreach ($translations as $key => $value) {
                            $data[] = [
                                'item' => $key,
                                'text' => $value,
                                'locale' => $locale,
                                'created_at' => date("Y-m-d H:i:s"),
                                'updated_at' => date("Y-m-d H:i:s"),
                            ];
                            $data_array[$key] = $value;
                        }
                        Translation::insert($data);

                        $localPath = base_path('lang/' . $locale . '.json');
                        File::put($localPath, json_encode($data_array, JSON_UNESCAPED_UNICODE));
                    }
                }
            }
        } else {
            echo "Translations table is not empty. Please clear it first.";
        }
    }
}
