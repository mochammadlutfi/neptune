<?php

namespace Database\Seeders\Base;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Locale;
use App\Models\LocaleTranslation;
class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        if(Locale::latest()->get()->count() == 0){
            $en = new Locale();
            $en->name = 'English';
            $en->key = 'en';
            $en->images = '/uploads/locales/en.svg';
            $en->save();
            
            $id = new Locale();
            $id->name = 'Bahasa Indonesia';
            $id->key = 'id';
            $id->images = '/uploads/locales/id.svg';
            $id->save();
        }
    }
}
