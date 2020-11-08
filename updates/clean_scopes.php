<?php namespace Waka\Pdfer\Updates;

//use Excel;
use Seeder;
use Waka\Pdfer\Models\WakaPdf;

class CleanScopes extends Seeder
{
    public function run()
    {
        WakaPdf::where('scopes', '<>', null)->update(['scopes' => null]);

    }
}
