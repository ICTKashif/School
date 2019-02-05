<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use App\Student;
use Illuminate\Support\Facades\File;
use App\Ictcore_integration;
use App\Ictcore_attendance;
use App\Ictcore_fees;
use App\SectionModel;
use App\Schedule;
use App\ClassModel;
use App\Notification;
use DB;
use Carbon\Carbon;
use Storage;
class ictcoreController {

	public function __construct() {

	}
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{

		$ictcore_integration= Ictcore_integration::select("*")->where('type',Input::get('type'))->first();
        $type = Input::get('type');
		if(is_null($ictcore_integration)){
			$ictcore_integration = new Ictcore_integration;
			$ictcore_integration->ictcore_url = "";
			$ictcore_integration->ictcore_user = "";
			$ictcore_integration->ictcore_password = "";
		}
		return View('app.ictcore',compact('ictcore_integration','type'));
	}

	public function create()
	{
		if(Input::get('method')==''){
			$rules=[
			//'ictcore_url' => 'required',
			'ictcore_user' => 'required',
			'ictcore_password' => 'required',
			];
	    }else{
			$rules=[
			'ictcore_url' => 'required',
			'ictcore_user' => 'required',
			'ictcore_password' => 'required',
			];

	    }
		$validator = \Validator::make(Input::all(), $rules);
		if ($validator->fails()){
			return Redirect::to('ictcore?type='.Input::get('type'))->withinput(Input::all())->withErrors($validator);
		}else {
			if(Input::get('method')==''){
				$method = 'telenor';
			}else{
				$method = 'ictcore';
			}

			if(Input::get('ictcore_url')==''){
				$url = '';
			}else{
				$url =Input::get('ictcore_url');
			}
			
			DB::table("ictcore_integration")->where('type',Input::get('type'))->delete();
			$ictcore_integration=new Ictcore_integration;
			$ictcore_integration->ictcore_url =$url;
			$ictcore_integration->ictcore_user = Input::get('ictcore_user');
			$ictcore_integration->ictcore_password = Input::get('ictcore_password');
			$ictcore_integration->method = $method;
			$ictcore_integration->type = Input::get('type');
			$ictcore_integration->save();
			return Redirect::to('ictcore?type='.Input::get('type'))->with('success', 'Integration  Information saved.');
		}
	}

	public function attendance_index()
	{
		$ictcore_attendance= Ictcore_attendance::select("*")->first();
		if(is_null($ictcore_attendance)){
			$ictcore_attendance=new Ictcore_attendance;
			$ictcore_attendance->name = "";
			$ictcore_attendance->description = "";
			$ictcore_attendance->recording = "";
		}


		$ictcore_fees= Ictcore_fees::select("*")->first();
		if(is_null($ictcore_fees)){
			$ictcore_fees=new Ictcore_fees;
			$ictcore_fees->name = "";
			$ictcore_fees->description = "";
			$ictcore_fees->recording = "";
		}
		return View('app.ictcoreAttendance',compact('ictcore_attendance','ictcore_fees'));
	}

	public function post_attendance()
	{
		//echo  "<pre>";print_r(Input::all());
		//exit;
     $ictcore_late =	DB::table('ictcore_attendance')->select('*')->where('late_file',Input::get('message_late'))->first();
     $ictcore_absent =	DB::table('ictcore_attendance')->select('*')->where('recording',Input::get('message_absent'))->first();
				   //echo "<pre>";print_r($ictcore_attendance );exit;
	 $ictcore_fess =	DB::table('ictcore_fees')->select('*')->where('recording',Input::get('fee_message'))->first();
		if(!empty($ictcore_late) ){
			$late = 'message_late' .'=>'. 'required';
		}else{
		$late ='message_late'.'=>'.'required|mimes:wav';
     	}

     	if(!empty($ictcore_late) ){
			$absent = 'message_absent' .'=>'. 'required';
		}else{
		$absent ='message_absent'.'=>'.'required|mimes:wav';
     	}

     	if(!empty($ictcore_fess) ){
			$fe = 'fee_message' .'=>'. 'required';
		}else{
		$fe ='fee_message'.'=>'.'required|mimes:wav';
     	}
        
		$rules=[
		//'title.*' => 'required',
		$late,
		$absent,
		$fe,
		];
		$validator = \Validator::make(Input::all(), $rules);
		
		if ($validator->fails()){
			return Redirect::to('/ictcore/attendance')->withErrors($validator);
		}
		else{
			$drctry = storage_path('app/public/messages/');

		    $attendance_noti = DB::table('notification_type')->where('notification','attendance')->first();

			$ictcore_integration =	DB::table('ictcore_integration')->select('*')->where('type',$attendance_noti->type)->first();
              // echo "<pre>";print_r($ictcore_integration);
               //exit;
                $ictcore_attendance =	DB::table('ictcore_attendance')->select('*')->first();
				   //echo "<pre>";print_r($ictcore_attendance );exit;
				$ictcore_fess =	DB::table('ictcore_fees')->select('*')->first();
				 $name_absent = Input::get('title_abent');
				 $name_late = Input::get('title_late');
				 $name_fee = Input::get('fee_title');
                 

                 $recording_id_absent = '';
                 $program_id_absent = '';
	             $recording_id_late = '';
	             $program_id_late   = '';
	             $upload_audio_ab   = '';
	             $upload_audio_lt   = '';
	             $upload_audio_fe   = '';
	             $recording_id_fee   = '';
	             $program_id_fee   = '';

               //if($ictcore_integration->method=="telenor" && $attendance_noti->type=='voice'){
                  
                    if(Input::file('message_absent')==''){
                   //echo   $ictcore_attendance->late_file ."=======".Input::get('message_late1');
                   $inpet_attendance_file_ab = Input::get('message_absent1');
                   }else{
                   	                  // echo   $ictcore_attendance->late_file ."=======ee".Input::get('message_late');
                     $inpet_attendance_file_ab = Input::file('message_absent');

                   }
                    if($inpet_attendance_file_ab!=''){
                    if(empty($ictcore_attendance) || $ictcore_attendance->recording != $inpet_attendance_file_ab){
                        $fileName_absnet = $name_absent.'_'.time().'.'.Input::file('message_absent')->getClientOriginalExtension();
                 
                        Input::file('message_absent')->move($drctry ,$fileName_absnet);
						sleep(2);
                        echo exec('sox '.$drctry.'/'.$fileName_absnet .' -b 16 -r 8000 -c 1 -e signed-integer '.$drctry.'/'.'absent.wav');
						$name_ab          =  $drctry .'absent.wav';
						$finfo_ab         =  new \finfo(FILEINFO_MIME_TYPE);
						$mimetype_ab      =  $finfo_ab->file($name_ab);
						$cfile_ab            =  curl_file_create($name_ab, $mimetype_ab, basename($name_ab));
						$data             = array('name'=>time(),'audio_file'=> $cfile_ab);
                        
                        if($ictcore_integration->method=="telenor" && $attendance_noti->type=='voice'){
                        	$upload_audio_ab     = $this->verification_number_telenor_voice($data,$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password);
                        }else{
                        /////////// ictcore ///////////

                        	//Input::file('message')->move($drctry,$fileName);
							//sleep(2);
							$data_abs = array(
							'name' => Input::get('title_abent'),
							'description' => Input::get('description_absent'),
							);
				          $recording_id_absent  =  $this->ictcore_api('messages/recordings','POST',$data_abs );
						//  echo "<pre>";print_r( $recording_id_absent);
						  //exit;

						if(!empty($recording_id_absent->error)){
							return Redirect::to('/ictcore/attendance')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
						}
                          $result        =  $this->ictcore_api('messages/recordings/'.$recording_id_absent.'/media','PUT',array( $cfile_ab));
				          $recording_id_absent  =  $result ;
				          //
							if(!empty($recording_id_absent->error)){
								return Redirect::to('/ictcore/attendance')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
							}
							if(!is_array($recording_id_absent )){
								$data = array(
								'name' => Input::get('title'),
								'recording_id' => $recording_id_absent,
								);
								$program_id_absent = $this->ictcore_api('programs/voicemessage','POST',$data );
								if(!empty($program_id_absent->error)){
									return Redirect::to('/ictcore/attendance')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
								}
								if(!is_array( $program_id_absent )){
									$program_id_absent = $program_id_absent;
								}else{
									return Redirect::to('/ictcore/attendance')->withErrors("ERROR: Program not Created" );
								}
							}else{
							return Redirect::to('/ictcore/attendance')->withErrors("ERROR: Recording not Created" );               
							}

							  echo "<pre>";print_r( $recording_id_absent);
						  //exit;
				          //
                        /////////////END//////////////
                        	
                        }
                    }else{
                    	 $upload_audio_ab = $ictcore_attendance->telenor_file_id;
                    	 $fileName_absnet = $ictcore_attendance->recording;
	                    $recording_id_absent = $ictcore_attendance->ictcore_recording_id;
                        $program_id_absent = $ictcore_attendance->ictcore_program_id;
                    }
                }else{

                	     $upload_audio_ab = '';
                    	 $fileName_absnet = '';
	                    $recording_id_absent = '';
                        $program_id_absent = '';

                }
                    if(Input::file('message_late')==''){
                   //echo   $ictcore_attendance->late_file ."=======".Input::get('message_late1');
                   $inpet_attendance_file = Input::get('message_late1');
                   }else{
                   	                  // echo   $ictcore_attendance->late_file ."=======ee".Input::get('message_late');
                     $inpet_attendance_file = Input::file('message_late');

                   }
                   // echo   $ictcore_attendance->late_file ."=======ee".$inpet_attendance_file;
                  // exit;
                    if($inpet_attendance_file!=''){
                    if(empty($ictcore_attendance) || $ictcore_attendance->late_file !=  $inpet_attendance_file){
                        $fileName_late   =   $name_late.'_'.time().'.'.Input::file('message_late')->getClientOriginalExtension();
                 
                        Input::file('message_late')->move($drctry ,$fileName_late);
						sleep(2);
                        echo exec('sox '.$drctry.'/'.$fileName_late .' -b 16 -r 8000 -c 1 -e signed-integer '.$drctry.'/'.'late.wav');

						$name_lt               =  $drctry .'late.wav';
						$finfo_lt              =  new \finfo(FILEINFO_MIME_TYPE);
						$mimetype_lt           =  $finfo_lt->file($name_lt);
						$cfile_lt              =  curl_file_create($name_lt, $mimetype_lt, basename($name_lt));
						$data_lt               = array('name'=>time(),'audio_file'=> $cfile_lt);
                        if($ictcore_integration->method=="telenor" && $attendance_noti->type=='voice'){
                       	 	$upload_audio_lt       = $this->verification_number_telenor_voice($data_lt,$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password);
                        }else{
                        	//Input::file('message')->move($drctry,$fileName);
							//sleep(2);
							$data = array(
							'name' => Input::get('title_late'),
							'description' => Input::get('description_late'),
							);
				        $recording_id_late  =  $this->ictcore_api('messages/recordings','POST',$data );
						if(!empty($recording_id_late->error)){
							return Redirect::to('/ictcore/attendance')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
						}
                          $result        =  $this->ictcore_api('messages/recordings/'.$recording_id_late.'/media','PUT',array($cfile_lt) );
				          $recording_id_late  =  $result ;
				          //
							if(!empty($recording_id_late->error)){
								return Redirect::to('/ictcore/attendance')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
							}
							if(!is_array($recording_id_late )){
								$data = array(
								'name' => Input::get('title'),
								'recording_id' => $recording_id_late,
								);
								$program_id_late = $this->ictcore_api('programs/voicemessage','POST',$data );
								if(!empty($recording_id_late->error)){
									return Redirect::to('/ictcore/attendance')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
								}
								if(!is_array( $program_id_late )){
									$program_id_late = $program_id_late;
								}else{
									return Redirect::to('/ictcore/attendance')->withErrors("ERROR: Program not Created" );
								}
							}else{
							return Redirect::to('/ictcore/attendance')->withErrors("ERROR: Recording not Created" );               
							}
				          //
                        }
                    }else{

                    	 $upload_audio_lt = $ictcore_attendance->telenor_file_id_late;
                    	 $fileName_late   = $ictcore_attendance->late_file;
	                     $recording_id_late = $ictcore_attendance->ictcore_recording_id_late;
	                     $program_id_late   = $ictcore_attendance->ictcore_program_id_late;
                    }
                }else{
                       $upload_audio_lt = '';
                    	 $fileName_late   = '';
	                     $recording_id_late = '';
	                     $program_id_late   = '';
                }

                     if(Input::file('fee_message')==''){
                   //echo   $ictcore_attendance->late_file ."=======".Input::get('message_late1');
                   $inpet_attendance_file_fee = Input::get('fee_message1');
                   }else{
                   	                  // echo   $ictcore_attendance->late_file ."=======ee".Input::get('message_late');
                     $inpet_attendance_file_fee = Input::file('fee_message');

                   }
                  //echo "<pre>";print_r($ictcore_fess);
                   //echo $ictcore_fess->recording ." != ". $inpet_attendance_file_fee;
                  //exit;
                    if( $inpet_attendance_file_fee !=''){
                    if(empty($ictcore_fess) || $ictcore_fess->recording != $inpet_attendance_file_fee){
                      $fileName_fee    =    $name_fee.'_'.time().'.'.Input::file('fee_message')->getClientOriginalExtension();
                        Input::file('fee_message')->move($drctry ,$fileName_fee);
						
						sleep(2);
						echo 'sox '.$drctry.'/'.$fileName_fee .' -b 16 -r 8000 -c 1 -e signed-integer '.$drctry.'/'.'fee.wav';
                        echo exec('sox '.$drctry.'/'.$fileName_fee .' -b 16 -r 8000 -c 1 -e signed-integer '.$drctry.'/'.'fee.wav');
						//exit;
						$name_fe               =  $drctry .'fee.wav';
						$finfo_fe              =  new \finfo(FILEINFO_MIME_TYPE);
						$mimetype_fe          =  $finfo_fe->file($name_fe);
						$cfile_fe              =  curl_file_create($name_fe, $mimetype_fe, basename($name_fe));
						$data_fe               = array('name'=>time(),'audio_file'=> $cfile_fe);
                        if($ictcore_integration->method=="telenor" && $attendance_noti->type=='voice'){
                        	$upload_audio_fe       = $this->verification_number_telenor_voice($data_fe,$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password);
                        }else{
                          /////////// ictcore ///////////

                        	//Input::file('message')->move($drctry,$fileName);
							//sleep(2);
							$data_abs = array(
							'name' => Input::get('title_abent'),
							'description' => Input::get('description_absent'),
							);
				          $recording_id_fee  =  $this->ictcore_api('messages/recordings','POST',$data_abs );
						//  echo "<pre>";print_r( $recording_id_absent);
						  //exit;

						if(!empty($recording_id_fee->error)){
							return Redirect::to('/ictcore/attendance')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
						}
                          $result        =  $this->ictcore_api('messages/recordings/'.$recording_id_fee.'/media','PUT',array( $cfile_fe));
				          $recording_id_fee  =  $result ;
				          //
							if(!empty($recording_id_fee->error)){
								return Redirect::to('/ictcore/attendance')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
							}
							if(!is_array($recording_id_fee )){
								$data = array(
								'name' => Input::get('title'),
								'recording_id' => $recording_id_fee,
								);
								$program_id_fee = $this->ictcore_api('programs/voicemessage','POST',$data );
								if(!empty($program_id_fee->error)){
									return Redirect::to('/ictcore/attendance')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
								}
								if(!is_array( $program_id_fee )){
									$program_id_fee = $program_id_fee;
								}else{
									return Redirect::to('/ictcore/attendance')->withErrors("ERROR: Program not Created" );
								}
							}else{
							return Redirect::to('/ictcore/attendance')->withErrors("ERROR: Recording not Created" );               
							}

							  echo "<pre>";print_r( $recording_id_absent);
						  //exit;
				          //
                        /////////////END//////////////
                        }
                   }else{
                    	 $upload_audio_fe = $ictcore_fess->telenor_file_id;
                    	 $fileName_fee   = $ictcore_fess->recording;
                          $recording_id_fee   = $ictcore_fess->ictcore_recording_id;
	                       $program_id_fee   = $ictcore_fess->ictcore_program_id;

                    }
                }else{
                	$upload_audio_fe = '';
                    	 $fileName_fee   = '';
                          $recording_id_fee   = '';
	                       $program_id_fee   = '';
                }
					/*if(!empty($ictcore_attendance) && File::exists($drctry.$ictcore_attendance->recording)){
						unlink($drctry .$ictcore_attendance->recording);
					}
						$sname = Input::get('title');
						$remove_spaces =  str_replace(" ","_",Input::get('title'));
						$fileName= $remove_spaces.'_'.time().'.'.Input::file('message')->getClientOriginalExtension();
						
						Input::file('message')->move($drctry ,$fileName);
						sleep(2);
					    echo exec('sox '.$drctry.'/'.$fileName .' -b 16 -r 8000 -c 1 -e signed-integer'.$drctry.'/'.$fileName);

						$name          =  $drctry .$fileName;
						$finfo         =  new \finfo(FILEINFO_MIME_TYPE);
						$mimetype      =  $finfo->file($name);
						$cfile         =  curl_file_create($name, $mimetype, basename($name));
						$data          = array('name'=>time(),'audio_file'=> $cfile);
                        $upload_audio  = $this->verification_number_telenor_voice($data,$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password);
                         echo $upload_audio;
                        // echo "<pre>";print_r($upload_audio[0] );
                        */
                         DB::table("ictcore_attendance")->delete();
							$ictcore_attendance = new Ictcore_attendance;
							$ictcore_attendance->name = Input::get('title_abent');
							$ictcore_attendance->description = Input::get('description_absent');
							$ictcore_attendance->late_description = Input::get('description_late');
							$ictcore_attendance->recording =$fileName_absnet;
							$ictcore_attendance->late_file =$fileName_late;
							$ictcore_attendance->ictcore_recording_id = $recording_id_absent ;
							$ictcore_attendance->ictcore_program_id  =$program_id_absent;
							$ictcore_attendance->ictcore_recording_id_late = $recording_id_late;
							$ictcore_attendance->ictcore_program_id_late  =$program_id_late;
							$ictcore_attendance->telenor_file_id  =$upload_audio_ab;
							$ictcore_attendance->telenor_file_id_late  =$upload_audio_lt;
							$ictcore_attendance->save();
                         	
                         	DB::table("ictcore_fees")->delete();
							$ictcore_fees = new Ictcore_fees;
							$ictcore_fees->name = Input::get('fee_title');
							$ictcore_fees->description = Input::get('fee_description');
							if(Input::get('fee_description')==''){
			                	$ictcore_fees->description ='';
							}
							$ictcore_fees->recording =$fileName_fee;
							$ictcore_fees->ictcore_recording_id = $recording_id_fee;
	             			$ictcore_fees->ictcore_program_id  =$program_id_fee;
							$ictcore_fees->telenor_file_id  =$upload_audio_fe;
							$ictcore_fees->save();

                         	return Redirect::to('/ictcore/attendance')->with("success", "Attendance Message Created Succesfully.");
            /*}else{

			if(!empty($ictcore_integration) && $ictcore_integration->ictcore_url && $ictcore_integration->ictcore_user && $ictcore_integration->ictcore_password){
				


				/*$ictcore_attendance =	DB::table('ictcore_attendance')->select('*')->first();
				
				if(!empty($ictcore_attendance) && File::exists($drctry.$ictcore_attendance->recording)){
					unlink($drctry .$ictcore_attendance->recording);
				}
				$sname = Input::get('title');
				$remove_spaces =  str_replace(" ","_",Input::get('title'));
				$fileName= $remove_spaces.'.'.Input::file('message')->getClientOriginalExtension();
				Input::file('message')->move($drctry ,$fileName);
				sleep(2);
				$name          =  $drctry .$fileName;
				$finfo         =  new \finfo(FILEINFO_MIME_TYPE);
				$mimetype      =  $finfo->file($name);
				$cfile         =  curl_file_create($name, $mimetype, basename($name));
				$data          =  array( $cfile);
				$result        =  $this->ictcore_api('messages/recordings/'.$recording_id.'/media','PUT',$data );
				$recording_id  =  $result ;
				if(!empty($recording_id->error)){
					return Redirect::to('/ictcore/attendance')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
				}
				if(!is_array($recording_id )){
					$data = array(
					'name' => Input::get('title'),
					'recording_id' => $recording_id,
					);
					$program_id = $this->ictcore_api('programs/voicemessage','POST',$data );
					if(!empty($recording_id->error)){
						return Redirect::to('/ictcore/attendance')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
					}
					if(!is_array( $program_id )){
						$program_id = $program_id;
					}else{
						return Redirect::to('/ictcore/attendance')->withErrors("ERROR: Program not Created" );
					}
				}else{
					return Redirect::to('/ictcore/attendance')->withErrors("ERROR: Recording not Created" );               
				}*/

				/*DB::table("ictcore_attendance")->delete();
				$ictcore_attendance = new Ictcore_attendance;
				$ictcore_attendance->name = Input::get('title');
				$ictcore_attendance->description = Input::get('description');
				$ictcore_attendance->recording =$fileName;
				$ictcore_attendance->ictcore_recording_id =$recording_id;
				$ictcore_attendance->ictcore_program_id  =$program_id;
				$ictcore_attendance->telenor_file_id  =$file_id;
				$ictcore_attendance->save();
				return Redirect::to('/ictcore/attendance')->with("success", "Attendance Message Created Succesfully.");
			}else{
			return Redirect::to('/ictcore/attendance')->withErrors("ERROR: Please Add Ictcore integration in Setting tab" );  
			}
		}*/
	}
	}
	public function fee_message_index()
	{
		$ictcore_fees= Ictcore_fees::select("*")->first();
		if(is_null($ictcore_fees)){
			$ictcore_fees=new Ictcore_fees;
			$ictcore_fees->name = "";
			$ictcore_fees->description = "";
			$ictcore_fees->recording = "";
		}
		return View('app.ictcoreFees',compact('ictcore_fees'));
	}

	public function post_fees()
	{
		$rules=[
		'title' => 'required',
		'message' => 'required|mimes:wav',
		];
		$validator = \Validator::make(Input::all(), $rules);
		if ($validator->fails()){
			return Redirect::to('/ictcore/fees')->withErrors($validator);
		}
		else {
			//$ictcore_integration =	DB::table('ictcore_integration')->select('*')->first();
			 $attendance_noti = DB::table('notification_type')->where('notification','fess')->first();

			$ictcore_integration =	DB::table('ictcore_integration')->select('*')->where('type',$attendance_noti->type)->first();
            if($ictcore_integration->method=="telenor" && $attendance_noti->type=='voice'){
              // $ictcore_fees =	DB::table('ictcore_fees')->select('*')->first();
			  $drctry       = storage_path('app/public/messages/');
              $ictcore_fess =	DB::table('ictcore_fees')->select('*')->first();
					if(!empty($ictcore_fess) && File::exists($drctry.$ictcore_fess->recording)){
						unlink($drctry .$ictcore_fess->recording);
					}
						$sname = Input::get('title');
						$remove_spaces =  str_replace(" ","_",Input::get('title'));
						$fileName= $remove_spaces.'.'.Input::file('message')->getClientOriginalExtension();
						Input::file('message')->move($drctry ,$fileName);
						sleep(2);
						$php_dir =  exec('which php');
						//echo 'sox '."$drctry"."$fileName" .' -b 16 -r 8000 -c 1 -e signed-integer '."$drctry"."$fileName";
					     $data  = exec('sox '."$drctry"."$fileName" .' -b 16 -r 8000 -c 1 -e signed-integer '."$drctry"."fesstelenor.wav");
                        echo $data;
						//exit;
						$name          =  $drctry .$fileName;
						$nname         = $drctry .'fesstelenor.wav';
						$finfo         =  new \finfo(FILEINFO_MIME_TYPE);
						$mimetype      =  $finfo->file($nname);
						$cfile         =  curl_file_create($nname, $mimetype, basename($nname));
						$data          = array('name'=>time(),'audio_file'=> $cfile);
                        $upload_audio  = $this->verification_number_telenor_voice($data,$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password);
                         //echo $upload_audio;
                           DB::table("ictcore_fees")->delete();
						$ictcore_fees = new Ictcore_fees;
						$ictcore_fees->name = Input::get('title');
						$ictcore_fees->description = Input::get('description');
						if(Input::get('description')==''){
		                	$ictcore_fees->description ='';
						}
						$ictcore_fees->recording =$fileName;
						$ictcore_fees->ictcore_recording_id ='';
						$ictcore_fees->ictcore_program_id  ='';
						$ictcore_fees->telenor_file_id  =$upload_audio;
						$ictcore_fees->save();
						unlink($drctry .'fesstelenor.wav');
						return Redirect::to('/ictcore/fees')->with("success", "Fees Message Created Succesfully.");



            }else{

			if(!empty($ictcore_integration) && $ictcore_integration->ictcore_url && $ictcore_integration->ictcore_user && $ictcore_integration->ictcore_password){
				$ictcore_fees =	DB::table('ictcore_fees')->select('*')->first();
				$drctry = storage_path('app/public/messages/');
				if(count($ictcore_fees) > 0 && File::exists($drctry.$ictcore_fees->recording)){
					unlink($drctry.$ictcore_fees->recording);
				}
				$sname = Input::get('title');
				$remove_spaces =  str_replace(" ","_",Input::get('title'));
				$fileName= $remove_spaces.'.'.Input::file('message')->getClientOriginalExtension();
				Input::file('message')->move($drctry,$fileName);
				sleep(2);
				$data = array(
				'name' => Input::get('title'),
				'description' => Input::get('description'),
				);
				$recording_id  =  $this->ictcore_api('messages/recordings','POST',$data );
				if(!empty($recording_id->error)){
					return Redirect::to('/ictcore/fees')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
				}
				$name          =   $drctry.$fileName;
				$finfo         =  new \finfo(FILEINFO_MIME_TYPE);
				$mimetype      =  $finfo->file($name);
				$cfile         =  curl_file_create($name, $mimetype, basename($name));
				$data          =  array( $cfile);
				$result        =  $this->ictcore_api('messages/recordings/'.$recording_id.'/media','PUT',$data );
				$recording_id  =  $result ;
				if(!empty($recording_id->error)){
					return Redirect::to('/ictcore/fees')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
				}
				if(!is_array($recording_id )){
					$data = array(
					'name' => Input::get('title'),
					'recording_id' => $recording_id,
					);
					$program_id = $this->ictcore_api('programs/voicemessage','POST',$data );
					if(!empty($program_id->error)){
						return Redirect::to('/ictcore/fees')->withErrors("ERROR: some thing wrong in ictcore check password or user name " );
					}
					if(!is_array( $program_id )){
						$program_id = $program_id;
					}else{
						return Redirect::to('/ictcore/fees')->withErrors("ERROR: Program not Created" );
					}
				}else{
					return Redirect::to('/ictcore/fees')->withErrors("ERROR: Recording not Created" );               
				}
				DB::table("ictcore_fees")->delete();
				$ictcore_fees = new Ictcore_fees;
				$ictcore_fees->name = Input::get('title');
				$ictcore_fees->description = Input::get('description');
				if(Input::get('description')==''){
                	$ictcore_fees->description ='';
				}
				$ictcore_fees->recording =$fileName;
				$ictcore_fees->ictcore_recording_id =$recording_id;
				$ictcore_fees->ictcore_program_id  =$program_id;
				$ictcore_fees->save();
				return Redirect::to('/ictcore/fees')->with("success", "Fees Message Created Succesfully.");

			}else{
			return Redirect::to('/ictcore/attendance')->withErrors("ERROR: Please Add Ictcore integration in Setting tab" );  
			}
		}
		}
	}
	function ictcore_api($method,$req, $arguments = array()) {

		$ictcore_integration =	DB::table('ictcore_integration')->select('*')->where('method','ictcore')->first();
		$api_username = $ictcore_integration->ictcore_user;    // <=== Username at ICTCore
		$api_password = $ictcore_integration->ictcore_password;  // <=== Password at ICTCore
		$service_url  =  $ictcore_integration->ictcore_url;  //'http://172.17.0.2/ictcore/api'; // <=== URL for ICTCore REST APIs
		$requestType  = $req; // This can be PUT or POST
		$api_url      = "$service_url/$method";
		$urlaray      = explode('/',$method);
		$curl         = curl_init($api_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST,$requestType);
		curl_setopt($curl, CURLOPT_POST, true);
		$post_data = $arguments;
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
		return json_decode($curl_response);  
	}

	public function noti_index(){

		$notification_type = DB::table('notification_type');
          if($notification_type->count()>0){
           $notification_types = $notification_type->get(); 
          }else{
          	$notification_types =array();
          }
         // $result = File::exists(Storage::get('/public/cronsettings.txt'));
         //print_r($result);
        // Storage::disk('public')->exists('.cronsettings.txt');
         if(Storage::disk('local')->exists('/public/cronsettings.txt')){
          $contant = Storage::get('/public/cronsettings.txt');
          $data = explode('<br>',$contant );

			//echo "<pre>";print_r($data);
			$attendance_time = $data[0]; 
		}else{
	      $attendance_time ='';
		}
		 $schedule = Schedule::select('date','time')->first();
		if(is_null($schedule)){
			$schedule=new Schedule;
			$schedule->date = "";
			$schedule->time = "";
		
		}
		$datee=date('F');
		$year= date('Y');
	//	echo $attendance_time;
	   //exit;
	    return View('app.notifications',compact('notification_types','attendance_time','schedule','datee','year'));
	}

	public function noti_create()
	{
       $rules=[
		'fess' => 'required',
		'attendance' => 'required',
		'time_set' => 'required',
		'time' => 'required',
		'date' => 'required',
		];
		$validator = \Validator::make(Input::all(), $rules);
		if ($validator->fails()){
			return Redirect::to('/notification_type')->withErrors($validator);
		}
		else {
		
			$data = array(Input::all());
			//echo "<pre>";print_r($data[0]);
			//exit;
			//$contant = Storage::get('/public/cronsettings.txt');
			//echo "<pre>";print_r($contant);
			//exit;
			unset($data[0]['_token']);
			Notification::truncate();
			foreach($data[0] as $key=>$value){
			//	echo $value;
				//exit;
				$add_noti = new Notification;
			     if($value=='sms' || $value=='voice'){
			     $add_noti->type = $value;
			     $add_noti->notification = $key;
			     $add_noti->save();
			    }else{

			    }
			     //echo $key;echo $value;
			     //echo "<br>";

			}  
			 DB::table("cronschedule")->delete();
				$schedule=new Schedule;
				$schedule->date = $data[0]['date'];
				$schedule->time = $data[0]['time_set'];
				$schedule->save();
			//echo DATE("g:i a", STRTOTIME("13:30"));
       // echo   $setting = $data[0]['time']."<br>".'';
        //exit;
        $time = DATE("H:i", STRTOTIME($data[0]['time']));
            $setting = $time."<br>".'';
           
           Storage::put('/public/cronsettings.txt', $setting);
  
			return Redirect::to('/notification_type')->with("success", "Notifications setting Created Succesfully.");
	    }
	}

	public function verification_number_telenor_sms($to,$msg,$mask,$user,$pass,$type)
	{

	    
	    $planetbeyondApiUrl="https://telenorcsms.com.pk:27677/corporate_sms2/api/auth.jsp?msisdn=#username#&password=#password#";
		if($type == 'sms'){
		$planetbeyondApiSendSmsUrl="https://telenorcsms.com.pk:27677/corporate_sms2/api/sendsms.jsp?session_id=#session_id#&to=#to_number_csv#&text=#message_text#"; 
	    }elseif($type == 'voice'){
	    	$attandace_message = DB::table("ictcore_attendance")->first();
	    	$planetbeyondApiSendSmsUrl="https://telenorcsms.com.pk:27677/corporate_sms2/api/makecall.jsp?session_id=#session_id#&to=#to_number_csv#&file_id=#message_text#&max_retries=1";
	    }
	    $userName = $user;
	    $password = $pass;
	    $url      =  str_replace("#username#",$userName,$planetbeyondApiUrl);
		$url      =  str_replace("#password#",$password,$url);

        if (preg_match("~^0\d+$~", $to)) {
            $to = preg_replace('/0/', '92', $to, 1);
	    }else {
	        $to =$to;  
	    }
	   $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$url);
	    curl_setopt($ch, CURLOPT_FAILONERROR,1);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	    $retValue = curl_exec($ch);          
	    curl_close($ch);
			
		$xml = new \SimpleXMLElement($retValue);
	    $session_id = $xml->data;

         $url_sms = str_replace("#message_text#",urlencode($msg),$planetbeyondApiSendSmsUrl);

	     $url_sms = str_replace("#to_number_csv#",$to,$url_sms);
	   //$url=str_replace("#from_number#",$fromNumber,$url);

	     

	    $urlWithSessionKey = str_replace("#session_id#",$session_id,$url_sms);
        if($mask!=null)
	    {
	    	if($type=='sms'){

				$urlWithSessionKey = $urlWithSessionKey . "&mask=" . urlencode($mask);
	        }
	    }
	    //return $urlWithSessionKey;
            $snd_sms = curl_init();
		    curl_setopt($snd_sms, CURLOPT_URL,$urlWithSessionKey);
		    curl_setopt($snd_sms, CURLOPT_FAILONERROR,1);
		    curl_setopt($snd_sms, CURLOPT_FOLLOWLOCATION,1);
		    curl_setopt($snd_sms, CURLOPT_RETURNTRANSFER,1);
		    curl_setopt($snd_sms, CURLOPT_TIMEOUT, 15);
		    $sms_data = curl_exec($snd_sms);          
		    curl_close($snd_sms);
		   // echo $urlWithSessionKey;
		    //echo "<pre>";print_r($sms_data);
				
			$xml_sms = new \SimpleXMLElement($sms_data);
		    //$data    = $xml_sms->data;
		    return $xml_sms ;
	}

	public function verification_number_telenor_voice($post,$user,$pass)
	{

	    $planetbeyondApiUrl        = "https://telenorcsms.com.pk:27677/corporate_sms2/api/auth.jsp?msisdn=#username#&password=#password#";
		$planetbeyondApiSendSmsUrl = "https://telenorcsms.com.pk:27677/corporate_sms2/api/audio_upload.jsp?session_id=#session_id#"; 
	    $userName = $user;
	    $password = $pass;
	     $url      =  str_replace("#username#",$userName,$planetbeyondApiUrl);
		 $url      =  str_replace("#password#",$password,$url);

      //echo $url;
      //exit;
	   $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$url);
	    curl_setopt($ch, CURLOPT_FAILONERROR,1);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	    $retValue = curl_exec($ch);          
	    curl_close($ch);
			
		$xml = new \SimpleXMLElement($retValue);
	    $session_id = $xml->data;

        // $url_sms = str_replace("#message_text#",urlencode($msg),$planetbeyondApiSendSmsUrl);

	    // $url_sms = str_replace("#to_number_csv#",$to,$url_sms);
	   //$url=str_replace("#from_number#",$fromNumber,$url);

	    $urlWithSessionKey = str_replace("#session_id#",$session_id,$planetbeyondApiSendSmsUrl);
       
	    //return $urlWithSessionKey;
          /*$snd_sms = curl_init();
		    curl_setopt($snd_sms, CURLOPT_URL,$urlWithSessionKey);
		    curl_setopt($snd_sms, CURLOPT_FAILONERROR,1);
		    curl_setopt($snd_sms, CURLOPT_FOLLOWLOCATION,1);
		    curl_setopt($snd_sms, CURLOPT_RETURNTRANSFER,1);
		    curl_setopt($snd_sms, CURLOPT_TIMEOUT, 15);
		    $sms_data = curl_exec($snd_sms);          
		    curl_close($snd_sms);
				
			$xml_sms = new \SimpleXMLElement($sms_data);
		    $data = $xml_sms->data;
		    return $xml_sms ;*/
            //echo "<pre>";print_r($urlWithSessionKey);exit;
		    $headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
			$cha = curl_init();
			curl_setopt($cha, CURLOPT_URL,$urlWithSessionKey);
			//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($cha, CURLOPT_FAILONERROR,1);
	        curl_setopt($cha, CURLOPT_FOLLOWLOCATION,1);
	        curl_setopt($cha, CURLOPT_RETURNTRANSFER,1);
	        curl_setopt($cha, CURLOPT_TIMEOUT, 15);
			curl_setopt($cha, CURLOPT_POST,1);
			curl_setopt($cha, CURLOPT_POSTFIELDS, $post);
			$result=curl_exec ($cha);
			curl_close ($cha);
			//echo "<pre>";print_r($result);
            //exit;
			$xml =  new \SimpleXMLElement($result);
			    $data = $xml->data;
			   
			return $data;
	}

	public function telenor_apis($method,$group_id,$to,$sms_msg,$file_id,$type)
	{  
      $ictcore_integration = Ictcore_integration::select("*")->where('method','telenor')->first();
       if(!empty($ictcore_integration)){
        $planetbeyondApiUrl        = "https://telenorcsms.com.pk:27677/corporate_sms2/api/auth.jsp?msisdn=#username#&password=#password#";
		if($method == 'group'){
		$planetbeyondApi = "https://telenorcsms.com.pk:27677/corporate_sms2/api/list.jsp?session_id=#session_id#&list_name=fee_defulter_".time(); 
	    }
	    if($method == 'add_contact'){

	    	
           if (preg_match("~^0\d+$~", $to)) {
           	$tos = preg_replace('/0/', '92', $to, 1);
			   
			}
			else {
			     $tos =$to;  
			}
          $planetbeyondApi = "https://telenorcsms.com.pk:27677/corporate_sms2/api/addcontacts.jsp?session_id=#session_id#&list_id=".$group_id."&to=".$tos;
	    }
	    if($method == 'campaign_create' && $type =='sms'){
	    	//echo "<=sms_msg=>".$sms_msg;
	    	 date_default_timezone_set('Asia/Karachi');
	    	$planetbeyondApi="https://telenorcsms.com.pk:27677/corporate_sms2/api/campaign.jsp?session_id=#session_id#&name=fee_defulter_".time()."&group_ids=".$group_id."&text=".urlencode($sms_msg)."&time=".urlencode(date("Y-m-d H:i:s", strtotime("+1 hours")))."&mask=".urlencode("SidraSchool");

	    }
	    if($method == 'campaign_create' && $type =='voice'){
	    	//$planetbeyondApi="https://telenorcsms.com.pk:27677/corporate_sms2/api/voice_dynamic_campaign.jsp?session_id=#session_id#&name=fee_defulter_voice_".time()."&file_id=".$file_id."&group_ids=".$group_id."&language=English&digits=123&voice=Male&text=".urlencode($sms_msg)."&max_retries=1";
	    	$planetbeyondApi="https://telenorcsms.com.pk:27677/corporate_sms2/api/voice_broadcast_campaign.jsp?session_id=#session_id#&name=notification_voice_".time()."&file_id=".$file_id."&group_ids=".$group_id."&text=".urlencode($sms_msg)."&max_retries=1";

	    }
	    if($method == 'send_msg'){

	    	$planetbeyondApi="https://telenorcsms.com.pk:27677/corporate_sms2/api/campstatus.jsp?session_id=#session_id#&campid=".$type;

	    }
	    $userName  = $ictcore_integration->ictcore_user;
	    $password  = $ictcore_integration->ictcore_password;
	     $url      =  str_replace("#username#",$userName,$planetbeyondApiUrl);
		 $url      =  str_replace("#password#",$password,$url);


	   $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,$url);
	    curl_setopt($ch, CURLOPT_FAILONERROR,1);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
	    $retValue = curl_exec($ch);          
	    curl_close($ch);
			
		$xml = new \SimpleXMLElement($retValue);
	    $session_id = $xml->data;
	    $urlWithSessionKey = str_replace("#session_id#",$session_id,$planetbeyondApi);
         //return $urlWithSessionKey ;
         echo $urlWithSessionKey;
            $api = curl_init();
		    curl_setopt($api, CURLOPT_URL,$urlWithSessionKey);
		    curl_setopt($api, CURLOPT_FAILONERROR,1);
		    curl_setopt($api, CURLOPT_FOLLOWLOCATION,1);
		    curl_setopt($api, CURLOPT_RETURNTRANSFER,1);
		    curl_setopt($api, CURLOPT_TIMEOUT, 15);
		    $api_data = curl_exec($api); 
		    curl_close($api);
		    //echo "hgggggggg<pre>hgggggggggggggggggg";print_r($api_data);
		if($method != 'add_contact'){
			$xml     = new \SimpleXMLElement($api_data);
		    //echo "<pre>";print_r($xml);
		    $data    = $xml->data;
		    return $data ;
		}

       }else{

       }
	}
}
