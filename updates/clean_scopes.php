<?php namespace Waka\Pdfer\Updates;

//use Excel;
use Seeder;
use Waka\Pdfer\Models\WakaPdf;

//use System\Models\File;
//use Waka\Worder\Models\BlocType;

// use Waka\Crsm\Classes\CountryImport;

class CleanScopes extends Seeder
{
    public function run()
    {
        //$this->call('Waka\Crsm\Updates\Seeders\SeedWorder');
        WakaPdf::where('scopes', '<>', null)->update(['scopes' => null]);

    }
}
