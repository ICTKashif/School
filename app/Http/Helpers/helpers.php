<?php
use App\ClassModel;
//use Storage;
if (! function_exists('FixData')) {
	function FixData(){
		
			$classes        = array();
			$section_array  = array(); 
			$section_array1 = array(); 
			$subject_array  = array(); 
			$result         = array();
			$result1        = array();

	    if(Auth::user()->group == 'Teacher'){

			   $teacher_id   = Auth::user()->group_id;
			    $sections    = DB::table('section')->where('teacher_id',$teacher_id)->get();
	            $timetable   = DB::table('timetable')->where('teacher_id',$teacher_id)->groupBy('section_id')->get();
	            $timetable   = DB::table('timetable')->where('teacher_id',$teacher_id)->groupBy('section_id')->get();
		      
		        $class_array  = array(); 
		        $class_array1 = array(); 
		        foreach($sections as $section){
		          $section_array[] = $section->id;
		          $class_array[]   = $section->class_code;
		        }
		        //$subject_array = array(); 
		        foreach($timetable as $tmtable){
		          $class_array1[]   = $tmtable->class_id;
		          $section_array1[] = $tmtable->section_id;
		          $subject_array[]  = $tmtable->subject_id;
		        }
		         $result1 = array_merge($class_array, $class_array1);
		      // $classes = DB::table('Class')->select('name','code')->whereIn('code',$result1)->get();
		       //$classes = ClassModel::whereIn('code',$result1)->pluck('name','code');
		       $result = array_merge($section_array, $section_array1);
		      
		}elseif(Auth::user()->group == 'Student'){
			
		}
	    return array('classes'=>$result1,'sections'=>$result,'subjects'=>$subject_array);
	}
	
}
if(! function_exists('accounting_check')) {
	function accounting_check()
	{
		if(Storage::disk('local')->exists('/public/accounting.txt')){
          $ac = Storage::get('/public/accounting.txt');
          $ac_data = explode('<br>',$ac );

			//echo "<pre>";print_r($data);
			$accounting = $ac_data[0]; 
		}else{
	      $accounting ='';
		}
		return $accounting;
	}
}
if(! function_exists('php_curl')) {
	function php_curl($method,$req, $arguments = array())
	{
		
		$accunting    =	DB::table('accounting_settings')->select('*')->first();
		$company_id   = $accunting->company_id; 
		$api_username = $accunting->username;    
		$api_password = $accunting->password; 
		$service_url  = $accunting->api_link; 
		$requestType  = $req; // This can be PUT or POST
		 $api_url      = "$service_url/$method?company_id=".$company_id;
		$urlaray      = explode('/',$method);
		$curl         = curl_init($api_url);

			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST,$requestType);
			curl_setopt($curl, CURLOPT_POST, true);
			$post_data   = $arguments;

		foreach($arguments as $key => $value) {
			if(is_array($value)){
				$post_data[$key] = json_encode($value);
			} else {
				$post_data[$key] = $value;
			}
		}
		$postData = json_encode($post_data); // Only USE this when request JSON data
		if($requestType =="PUT"  && in_array("media", $urlaray)){
			$fil = file_get_contents($post_data[0]->name);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $fil);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-HTTP-Method-Override: " . $requestType,'Content-Type: audio/x-wav'));
		}else{
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-HTTP-Method-Override: " . $requestType,'Content-Type: application/json'));
		}
		curl_setopt($curl, CURLOPT_USERPWD,  $api_username.":".$api_password);
		$curl_response = curl_exec($curl);
		curl_close($curl);
		//echo "<pre>";print_r($curl_response);exit;
		return json_decode($curl_response);  
	}
}