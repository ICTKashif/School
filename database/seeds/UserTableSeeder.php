<?php

// Composer: "fzaninotto/faker": "v1.3.0"

use Illuminate\Database\Seeder;
use \App\User;
use App\Institute;
use Faker\Factory as Faker;

class UserTableSeeder extends Seeder {

	public function run()
	{
		$users = DB::table('users');
		if($users->count()==0){		
			User::create(array('firstname'=>'Mr.','lastname'=>'Admin','login'=>'admin','email' => 'admin@school.dev','group'=>'Admin','desc'=>'Admin Details Here',"password"=> Hash::make("123456")));
			User::create(array('firstname'=>'Mr.','lastname'=>'Other','login'=>'other','email' => 'other@school.dev','group'=>'Other','desc'=>'other Deatils Here',"password"=> Hash::make("123456")));
			User::create(array('firstname'=>'Mr.','lastname'=>'kashif','login'=>'ictkashif','email' => 'kashif@ictinnovations.com','group'=>'Other','desc'=>'other Deatils Here',"password"=> Hash::make("123456")));
		}
		$institute  = Institute::select('*');
		if($institute->count()==0){
			Institute::create(array('name'=>'Ict Vision','establish'=>'2017','email'=>'info@ictinnovations.com','web' => 'http://ictvision.net/','phoneNo'=>'923125555555','address'=>'Khawar center Multan'));
	    }
	}
}
