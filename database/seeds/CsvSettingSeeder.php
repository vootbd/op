<?php

use Illuminate\Database\Seeder;
use App\User;
use App\CsvSetting;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CsvSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = DB::table('users AS t1')
                    ->leftJoin('csv_setting_user AS t2','t2.user_id','=','t1.id')
                    ->leftJoin('model_has_roles AS t3', 't3.model_id', '=', 't1.id')
                    ->where('t3.model_id', '=', 2)
                    ->whereNull('t2.id')
                    ->select('t1.id',)
                    ->pluck('id')
                    ->toArray();

        $csvs = DB::table('csv_settings')
                    ->pluck('id')->toArray();
        
        $stack = array();
        foreach($users as $user){
            foreach($csvs as $csv){
                array_push($stack, array(
                    'user_id' => $user,
                    'csv_setting_id' => $csv,
                    'in_output' => 1,
                    'status' => 1,
                    'order' => 0,
                ));
            }
        }
        DB::connection('mysql')
                ->table('csv_setting_user')
                ->insert($stack);
    }
}
