<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use App\Student;
use App\User;
use App\SectionModel;
use App\ClassModel;
use App\FeeSetup;
use Hash;
use DB;
use App\Ictcore_integration;
use App\Http\Controllers\ictcoreController;
use Carbon\Carbon;
class foobar{

}
Class formfoo{

}
class studentController extends BaseController {

	public function __construct() {
		/*$this->beforeFilter('csrf', array('on'=>'post'));
		$this->beforeFilter('auth');
		$this->beforeFilter('userAccess',array('only'=> array('delete')));*/
	       $this->middleware('auth');
               $this->middleware('auth',array('only'=> array('delete')));
	}
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
		$classes = ClassModel::select('name','code')->get();
		
		$section= SectionModel::select('id','name')->where('class_code','=','cl1')->get();
		//$sections = SectionModel::select('name')->get();


		//return View::Make('app.studentCreate',compact('classes'));
		return View('app.studentCreate',compact('classes','section'));
	}
//

	public function search(Request $request)
	{

		
			$query = Input::get('query');
			$output="";
			$data=DB::table('Student')->where('isActive', '=', 'Yes')->where('firstName','LIKE','%'.$query."%")->orwhere('lastName','LIKE','%'.$query."%")->orwhere('fatherName','LIKE','%'.$query."%")->orwhere('b_form','LIKE','%'.$query."%")
			->orwhere('session','LIKE','%'.$query."%")->orwhere('class','LIKE','%'.$query."%")->orwhere('dob','LIKE','%'.$query."%")
			->orwhere('parmanentAddress','LIKE','%'.$query."%")->orwhere('discount_id','LIKE','%'.$query."%")
			->orwhere('regiNo','LIKE','%'.$query."%")
			->orderBy('session', 'desc')
			->orderBy('class', 'asc')
			->limit(20)
			->get();
			//return Response($output);
			 $output = '<ul class="dropdown-menu" id="searchlist" style="display:block; position:relative">';
		      foreach($data as $row)
		      {
		      	$section = DB::table('section')->where('id',$row->section)->first();
		       $output .= '
		       <li><a href="#">'.$row->firstName.''.$row->lastName.' (class = '.$row->class.')'.'(sectoion = '.$section->name.')'.'(regiNo = '.$row->regiNo.')'.'(session = '.$row->session.')'.'</a></li>
		       ';
		      }
		      $output .= '</ul>';
		      echo $output;
		
	}
	public  function getRegi($class,$session,$section)
	{
		$ses =trim($session);
		$stdcount = Student::select(DB::raw('count(*) as total'))->where('class','=',$class)->where('session','=',$ses)->first();

		$stdseccount = Student::select(DB::raw('count(*) as total'))->where('class','=',$class)->where('session','=',$ses)->where('section','=',$section)->first();
		$regiNolast  = Student::where('class','=',$class)->where('session','=',$ses)->where('section','=',$section)->orderBy('id', 'desc')->first();
		$r = intval($stdcount->total)+1;
		//echo substr($regiNolast->regiNo,4);
		
		if(strlen($r)<2)
		{
			$r='0'.$r;
		}
		$c = intval($stdseccount->total)+1;




		$cl=substr($class,2);
       
        if(!empty($regiNolast) && $r == substr($regiNolast->regiNo,4)){
         	$r   = intval($stdcount->total)+2;
         	$c = intval($stdseccount->total)+2;
		}
		$foo = array();
		if(strlen($cl)<2) {
			$foo[0]= substr($ses, 2) .'0'.$cl.$r;
		}
		else
		{
			$foo[0]=  substr($ses, 2) .$cl.$r;
		}
		if(strlen($c)<2) {
			$foo[1] ='0'.$c;
		}
		else
		{
			$foo[1] =$c;
		}

		return $foo;

	}

	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create(Request $request)
	{

		$rules=['regiNo' => 'required',
		'fname' => 'required',
		'lname' => 'required',
		'gender' => 'required',
		//'religion' => 'required',
		//'bloodgroup' => 'required',
		//'nationality' => 'required',
		'dob' => 'required',
		'session' => 'required',
		'class' => 'required',
		'section' => 'required',
		'rollNo' => 'required',
		'shift' => 'required',
		'photo' => 'mimes:jpeg,jpg,png',
		'b_form' => 'required',
		'fatherName' => 'required',
		'fatherCellNo' => 'required',
		//'motherName' => 'required',
		//'motherCellNo' => 'required',
		'presentAddress' => 'required',
		//'parmanentAddress' => 'required'
	];
	$validator = \Validator::make(Input::all(), $rules);
	if ($validator->fails())
	{
		return Redirect::to('/student/create')->withErrors($validator);
	}
	else {

		if(Input::file('photo')!=''){

		$fileName=Input::get('regiNo').'.'.Input::file('photo')->getClientOriginalExtension();
		
		}else{
			$fileName='';
		}
        
		$student = new Student;
		$student->regiNo = Input::get('regiNo');
		$student->discount_id = Input::get('discount_id');
		if(Input::get('discount_id') ==''){
			$student->discount_id = NULL;
		}
		$student->firstName = Input::get('fname');

		$student->middleName = Input::get('mname');
		if(Input::get('mname') ==''){
			$student->middleName = "";
		}
		$student->lastName = Input::get('lname');
		$student->gender= Input::get('gender');
		
		$student->religion= Input::get('religion');

		if(Input::get('religion') ==''){
			$student->religion = "";
		}
		$student->bloodgroup= Input::get('bloodgroup');

		if(Input::get('bloodgroup')==''){
		$student->bloodgroup="";

		}
		$student->dob= Input::get('dob');
		$student->session= trim(Input::get('session'));
		$student->class= Input::get('class');
		$student->section= Input::get('section');
		$student->group= Input::get('group');
		$student->rollNo= Input::get('rollNo');
		$student->shift= Input::get('shift');

		$student->photo= $fileName;
		$student->nationality= Input::get('nationality');
		if(Input::get('nationality') ==''){
			$student->nationality="";
		}
		$student->extraActivity= Input::get('extraActivity');
		if(Input::get('extraActivity') ==''){
			$student->extraActivity = "";
		}
		$student->remarks= Input::get('remarks');
       if(Input::get('remarks') ==''){
			$student->remarks = "";
		}
		$student->b_form= Input::get('b_form');
		$student->fatherName= Input::get('fatherName');
		$student->fatherCellNo= Input::get('fatherCellNo');
		
		$student->motherName= Input::get('motherName' );
		if(Input::get('motherName')==''){
			$student->motherName= "";
			
		}
		$student->motherCellNo= Input::get('motherCellNo');
		if(Input::get('motherCellNo')==''){
			$student->motherCellNo="";
		}
		$student->localGuardian= Input::get('localGuardian');
		if(Input::get('localGuardian')==''){
			$student->localGuardian="";
		}
		$student->localGuardianCell= Input::get('localGuardianCell');
		if(Input::get('localGuardianCell') ==''){
			$student->localGuardianCell="";
		}

		$student->presentAddress= Input::get('presentAddress');
		$student->parmanentAddress= Input::get('parmanentAddress');
		if(Input::get('parmanentAddress')==''){
			$student->parmanentAddress='';
		}
		$student->isActive= "Yes";

		$hasStudent = Student::where('regiNo','=',Input::get('regiNo'))->where('class','=',Input::get('class'))->first();
		if ($hasStudent)
		{
			$messages = $validator->errors();
			$messages->add('Duplicate!', 'Student already exits with this registration no.');
			return Redirect::to('/student/create')->withErrors($messages)->withInput();
		}
		else {
			$student->save();
			if( Input::file('photo')!=''){
             Input::file('photo')->move(base_path() .'/public/images',$fileName);
         	}
               /*  $user = new User;

                $user->firstname = Input::get('fname');
                $user->lastname  = Input::get('lname');
                $user->email =     Input::get('regiNo').'@gmail.com';
              	$user->login     = Input::get('regiNo');
              	$user->group     =  'Student';
                $user->password  =	Hash::make(Input::get('regiNo'));
                $user->save();

                 $ictcore_integration = Ictcore_integration::select("*")->first();
                 
			if(!empty($ictcore_integration) && $ictcore_integration->ictcore_url !='' && $ictcore_integration->ictcore_user !='' && $ictcore_integration->ictcore_password !=''){ 

							 $ict  = new ictcoreController();
							 	$data = array(
								'first_name' => $student->firstName,
								'last_name' => $student->lastName,
								'phone'     => $student->fatherCellNo,
								'email'     => '',
								);
								$contact_id = $ict->ictcore_api('contacts','POST',$data );

                               $message = 'School name'.'<br>'.'Login Name: '. Input::get('regiNo').'Password: '.Input::get('regiNo');
                                $data = array(
								'name' => 'School Name',
								'data' => $message,
								'type'     => 'plain',
								'description'     => 'testing message',
								);

	                          $text_id = $ict->ictcore_api('messages/texts','POST',$data );

	                          $data = array(
								'name' => 'School Name',
								'text_id' => $text_id
								);

                                $program_id = $ict->ictcore_api('programs/sendsms','POST',$data );

								$data = array(
								'title' => 'User Detail',
								'program_id' => $program_id,
								'account_id'     => 1,
								'contact_id'     => $contact_id,
								'origin'     => 1,
								'direction'     => 'outbound',
								);
								$transmission_id = $ict->ictcore_api('transmissions','POST',$data );
								//echo "================================================================transmission==========================================";
								// print_r($transmission_id);
								//GET transmissions/{transmission_id}/send
								//$transmission_send = $ict->ictcore_api('transmissions/'.$transmission_id.'/send','POST',$data=array() );

             
            }*/





			return Redirect::to('/student/create')->with("success","Student Admited Succesfully.");
		}


	}
}


/**
* Display the specified resource.
*
* @param  int  $id
* @return Response
*/
public function show()
{
	$students=array();
	$classes = ClassModel::pluck('name','code');
	$formdata = new formfoo;
	$formdata->class="";
	$formdata->section="";
	$formdata->shift="";
	$formdata->session="";
	//return View::Make("app.studentList",compact('students','classes','formdata'));
	return View("app.studentList",compact('students','classes','formdata'));
}
public function getList()
{
	
if(Input::get('search')==''){
	$rules = [
		'class' => 'required',
		'section' => 'required',
		'shift' => 'required',
		'session' => 'required'


	];
}else{
	$rules = [
	
	];
}
	$validator = \Validator::make(Input::all(), $rules);
	if ($validator->fails()) {
		return Redirect::to('/student/list')->withInput(Input::all())->withErrors($validator);
	} else {

		if(Input::get('search')=='yes'){
         //echo Input::get('student_name');
            $exp       = explode('(',Input::get('student_name'));
         	$class     = explode(')',$exp[1]);
         	$section   = explode(')',$exp[2]);
         	$regiNo    = explode(')',$exp[3]);
         	$session   = explode(')',$exp[4]);
         	$class1    = explode('=',$class[0]);
         	$section1  = explode('=',$section[0]);
         	$regiNo1   = explode('=',$regiNo[0]);
         	$session1  = explode('=',$session[0]);
	         $students = DB::table('Student')
			->join('Class', 'Student.class', '=', 'Class.code')
			->join('section', 'Student.section', '=', 'section.id')
			->select('Student.id', 'Student.regiNo', 'Student.rollNo', 'Student.firstName', 'Student.middleName', 'Student.lastName', 'Student.fatherName', 'Student.motherName', 'Student.fatherCellNo', 'Student.motherCellNo', 'Student.localGuardianCell',
			'Class.Name as class','Class.code as class_code', 'Student.presentAddress', 'Student.gender', 'Student.session','section.name','section.id as section_id')
			->where('Student.isActive', '=', 'Yes')
			->where('session',trim($session1[1]))
			->where('regiNo',trim($regiNo1[1]))
			->first();
         	return Redirect::to('student/view/'.$students->id);
		}else{
		$class_code	=Input::get('class');
		$section_id=Input::get('section');
		$shift=Input::get('shift');
		$session_year =trim(Input::get('session'));
		$students = DB::table('Student')
		->join('Class', 'Student.class', '=', 'Class.code')
		->join('section', 'Student.section', '=', 'section.id')
		->select('Student.id', 'Student.regiNo', 'Student.rollNo', 'Student.firstName', 'Student.middleName', 'Student.lastName', 'Student.fatherName', 'Student.motherName', 'Student.fatherCellNo', 'Student.motherCellNo', 'Student.localGuardianCell',
		'Class.Name as class', 'Student.presentAddress', 'Student.gender', 'Student.religion','section.name')
		->where('Student.isActive', '=', 'Yes')
		->where('Student.class',$class_code)
		->where('Student.section',$section_id)
		->where('Student.shift',$shift)
		->where('Student.session',$session_year)
		->get();
		}
		if(count($students)<1)
		{
			return Redirect::to('/student/list')->withInput(Input::all())->with('error','No Students Found!');

		}
		else {
			$classes = ClassModel::pluck('name','code');
			$formdata = new formfoo;
			$formdata->class=Input::get('class');
			$formdata->section=Input::get('section');
			$formdata->shift=Input::get('shift');
			$formdata->session=trim(Input::get('session'));
			$month=8;
			$fee_name=2;
			//return View::Make("app.studentList", compact('students','classes','formdata'));
			return View("app.studentList", compact('students','classes','formdata','month','fee_name'));
		}
	}

}

public function view($id)
{
	$student=	DB::table('Student')
	->join('Class', 'Student.class', '=', 'Class.code')
	->join('section', 'Student.section', '=', 'section.id')
	//->leftjoin('Attendance', 'Student.regiNo', '=', 'Attendance.regiNo')
	->select('Student.id', 'Student.regiNo','Student.rollNo','Student.firstName','Student.middleName','Student.lastName',
	'Student.fatherName','Student.motherName', 'Student.fatherCellNo','Student.motherCellNo','Student.localGuardianCell',
	'Class.Name as class','Class.code as class_code','Student.presentAddress','Student.gender','Student.religion','Student.section','Student.shift','Student.session',
	'Student.group','Student.dob','Student.bloodgroup','Student.nationality','Student.photo','Student.extraActivity','Student.remarks',
	'Student.localGuardian','Student.parmanentAddress','section.name as section_name')
	->where('Student.id','=',$id)
	
	->first();
	$attendances = DB::table('Attendance')->where('Attendance.date',Carbon::today()->toDateString())->where('regiNo',$student->regiNo)->first();
	   //return View::Make("app.studentView",compact('student'));
	$fees= FeeSetup::select('id','title')->where('class','=',$student->class_code)->where('type','=','Monthly')->first();

	$now   = Carbon::now();
             $year  =  $now->year;
            $month =  $now->month;
            $fee_name =  $fees->id;
	return View("app.studentView",compact('student','attendances','year','month','fee_name'));
}
/**
* Show the form for editing the specified resource.
*
* @param  int  $id
* @return Response
*/
public function edit($id)
{
	$classes = ClassModel::pluck('name','code');
	$student= Student::find($id);
	
	$sections = SectionModel::select('id','name')->where('class_code','=',$student->class)->get();
	//$sections = $sections->toArray();
      // $sections = SectionModel::pluck('id', 'name')->where('class_code','=',$student->class);
	//echo "<pre>";print_r($sections);
	//dd($student);
	//$sections = SectionModel::select('name')->get();
	//return View::Make("app.studentEdit",compact('student','classes'));
	return View("app.studentEdit",compact('student','classes','sections'));
}


/**
* Update the specified resource in storage.
*
* @param  int  $id
* @return Response
*/
public function update()
{

	$rules=[
		'fname' => 'required',
		'lname' => 'required',
		'gender' => 'required',
		//'religion' => 'required',
		//'bloodgroup' => 'required',
		//'nationality' => 'required',
		'dob' => 'required',
		'session' => 'required',
		'class' => 'required',
		'section' => 'required',
		'rollNo' => 'required',
		'shift' => 'required',
		'b_form' => 'required',
		'fatherName' => 'required',
		'fatherCellNo' => 'required',
		//'motherName' => 'required',
		//'motherCellNo' => 'required',
		'presentAddress' => 'required',
		//'parmanentAddress' => 'required'
	];
	$validator = \Validator::make(Input::all(), $rules);
	if ($validator->fails())
	{
		return Redirect::to('/student/edit/'.Input::get('id'))->withErrors($validator);
	}
	else {

		$student = Student::find(Input::get('id'));

		if(Input::hasFile('photo'))
		{

			if(substr(Input::file('photo')->getMimeType(), 0, 5) != 'image')
			{
				$messages = $validator->errors();
				$messages->add('Notvalid!', 'Photo must be a image,jpeg,jpg,png!');
				return Redirect::to('/student/edit/'.Input::get('id'))->withErrors($messages);
			}
			else {

				$fileName=Input::get('regiNo').'.'.Input::file('photo')->getClientOriginalExtension();
				$student->photo = $fileName;
				Input::file('photo')->move(base_path() .'/public/images',$fileName);
			}

		}
		else {
			$student->photo= Input::get('oldphoto');

		}
		//$student->regiNo=Input::get('regiNo');
		//$student->rollNo=Input::get('rollNo');
		/*$student->firstName= Input::get('fname');
		$student->middleName= Input::get('mname');
		$student->lastName= Input::get('lname');
		$student->gender= Input::get('gender');
		$student->religion= Input::get('religion');
		$student->bloodgroup= Input::get('bloodgroup');
		$student->nationality= Input::get('nationality');
		$student->dob= Input::get('dob');
		$student->session= trim(Input::get('session'));
		$student->class= Input::get('class');
		$student->section= Input::get('section');
		$student->group= Input::get('group');
		$student->nationality= Input::get('nationality');
		$student->extraActivity= Input::get('extraActivity');
		$student->remarks= Input::get('remarks');

		$student->fatherName= Input::get('fatherName');
		$student->fatherCellNo= Input::get('fatherCellNo');
		$student->motherName= Input::get('motherName');
		$student->motherCellNo= Input::get('motherCellNo');
		$student->localGuardian= Input::get('localGuardian');
		$student->localGuardianCell= Input::get('localGuardianCell');
		$student->shift= Input::get('shift');

		$student->presentAddress= Input::get('presentAddress');
		$student->parmanentAddress= Input::get('parmanentAddress');*/

		$student->firstName = Input::get('fname');

		$student->middleName = Input::get('mname');
		if(Input::get('mname') ==''){
			$student->middleName = "";
		}
		$student->lastName = Input::get('lname');
		$student->gender= Input::get('gender');
		
		$student->religion= Input::get('religion');

		if(Input::get('religion') ==''){
			$student->religion = "";
		}
		$student->bloodgroup= Input::get('bloodgroup');

		if(Input::get('bloodgroup')==''){
		$student->bloodgroup="";

		}
		$student->dob= Input::get('dob');
		$student->session= trim(Input::get('session'));
		$student->class= Input::get('class');
		$student->section= Input::get('section');
		$student->group= Input::get('group');
		//$student->rollNo= Input::get('rollNo');
		$student->shift= Input::get('shift');

		//$student->photo= $fileName;
		$student->nationality= Input::get('nationality');
		if(Input::get('nationality') ==''){
			$student->nationality="";
		}
		$student->extraActivity= Input::get('extraActivity');
		if(Input::get('extraActivity') ==''){
			$student->extraActivity = "";
		}
		$student->remarks= Input::get('remarks');
       if(Input::get('remarks') ==''){
			$student->remarks = "";
		}
		$student->b_form= Input::get('b_form');
		$student->fatherName= Input::get('fatherName');
		$student->fatherCellNo= Input::get('fatherCellNo');
		
		$student->motherName= Input::get('motherName' );
		if(Input::get('motherName')==''){
			$student->motherName= "";
			
		}
		$student->motherCellNo= Input::get('motherCellNo');
		if(Input::get('motherCellNo')==''){
			$student->motherCellNo="";
		}
		$student->localGuardian= Input::get('localGuardian');
		if(Input::get('localGuardian')==''){
			$student->localGuardian="";
		}
		$student->localGuardianCell= Input::get('localGuardianCell');
		if(Input::get('localGuardianCell') ==''){
			$student->localGuardianCell="";
		}

		$student->presentAddress= Input::get('presentAddress');
		$student->parmanentAddress= Input::get('parmanentAddress');
		if(Input::get('parmanentAddress')==''){
			$student->parmanentAddress='';
		}
        $student->discount_id = Input::get('discount_id');
		if(Input::get('discount_id') ==''){
			$student->discount_id = NULL;
		}

		$student->save();

		return Redirect::to('/student/list')->with("success","Student Updated Succesfully.");
	}


}


/**
* Remove the specified resource from storage.
*
* @param  int  $id
* @return Response
*/
public function delete($id)
{
	$student = Student::find($id);
	$student->isActive= "No";
	$student->save();

	return Redirect::to('/student/list')->with("success","Student Deleted Succesfully.");
}

/**
* Display the specified resource.
*
* @param  int  $id
* @return Response
*/
public function getForMarks($class,$section,$shift,$session)
{
	$students= Student::select('regiNo','rollNo','firstName','middleName','lastName','discount_id')->where('isActive','=','Yes')->where('class','=',$class)->where('section','=',$section)->where('shift','=',$shift)->where('session','=',$session)->get();
	return $students;
}

public function getForMarksjoin($class,$section,$shift,$session)
{
	$students= Student::select('regiNo','rollNo','firstName','middleName','lastName','discount_id')->where('isActive','=','Yes')->where('class','=',$class)->where('section','=',$section)->where('shift','=',$shift)->where('session','=',$session)->get();
	/* $students=	DB::table('Student')
	->leftjoin('Marks', 'Student.regiNo', '=', 'Marks.regiNo')
	->select('Student.id', 'Student.regiNo','Student.rollNo','Student.firstName','Student.middleName','Student.lastName',
	'Student.discount_id','Marks.written','Marks.written','Marks.mcq','Marks.practical','Marks.ca','Marks.Absent')
	->where('Student.section','=',$section)->where('Student.shift','=',$shift)->where('Student.session','=',$session)->get();

*/
	return $students;
}
public function getdiscount($reg)
{
	$discount= Student::select('discount_id')->where('isActive','=','Yes')->where('regiNo','=',$reg)->first();
	return $discount;
}

public function index_file(){
	return View('app.studentCreateFile');


}
public function create_file(){

$file = Input::file('fileUpload');
			$ext = strtolower($file->getClientOriginalExtension());
			$validator = \Validator::make(array('ext' => $ext),array('ext' => 'in:xls,xlsx,csv'));

			if ($validator->fails()) {
				return Redirect::to('student/create-file')->withErrors($validator);
			}else {
				    try{
						$toInsert = 0;
			            $data = \Excel::load(Input::file('fileUpload'), function ($reader) { })->get();

			             

			                if(!empty($data) && $data->count()){
								DB::beginTransaction();
								try {
			                        foreach ($data->toArray() as $raw) {
                                     // echo "<pre>";print_r($raw);
									$studentData= [
											'class' => $raw['class'],
											'section' => $raw['section'],
											'session' =>    $raw['session'],
											 'regiNo' => $raw['registration'],
											  'rollNo' => $raw['nocardroll_no'],
                                               'shift' => 'Morning',
                                               'isActive'=>'Yes',
											  'group' => $raw['group'],
											'firstName' => $raw['first_name'],
											'lastName' =>    $raw['last_name'],
											 'Gender' => $raw['gender'],
											  'fatherName' => $raw['father_name'],
											  'fatherCellNo' => $raw['fathers_mobile_no']

										];
										$hasStudent = Student::where('rollNo','=',$raw['nocardroll_no'])->first();
											if ($hasStudent)
											{
												$errorMessages = new \Illuminate\Support\MessageBag;
									             $errorMessages->add('Error', 'Doublication rollNo ');
									            return Redirect::to('/student/create-file')->withErrors($errorMessages);
											}else{
												Student::insert($studentData);
												$toInsert++;
											}
			                        }
			                         
										 DB::commit();
								} catch (Exception $e) {
									DB::rollback();
									$errorMessages = new \Illuminate\Support\MessageBag;
									 $errorMessages->add('Error', 'Something went wrong!');
									return Redirect::to('/student/create-file')->withErrors($errorMessages);

									// something went wrong
								}
			            }
					   if($toInsert){
			                return Redirect::to('/student/create-file')->with("success", $toInsert.' Student data upload successfully.');
			            }
						$errorMessages = new \Illuminate\Support\MessageBag;
						 $errorMessages->add('Validation', 'File is empty!!!');
						return Redirect::to('/student/create-file')->withErrors($errorMessages);
	                }catch (Exception $e) {
						  $errorMessages = new \Illuminate\Support\MessageBag;
						  $errorMessages->add('Error', 'Something went wrong!');
						   return Redirect::to('/student/create-file')->withErrors($errorMessages);
	                }
		}

	
}

public function csvexample(){

 return response()->download(storage_path('app/public/' . 'student.csv'));

}

public function access($id)
{
   $student= Student::find($id);
   if(!empty($student) && count($student)>0){
   	$chk_studnet  = User::where('login',$student->firstName.$student->lastName)->where('group_id',$student->id)->first();
      if(count($chk_studnet)>0){
      	   return Redirect::to('/student/list')->with("error","Already have Accessed .");

      }
        $user = new User;
        $user->firstname = $student->firstName;
        $user->lastname  = $student->lastName;
        $user->email     =     $student->regiNo;
      	$user->login     = $student->firstName.$student->lastName;
      	$user->group     =  'Student';
      	$user->group_id  = $student->id ;
      	$user->access    = 1 ;
        $user->password  =	Hash::make($student->regiNo);
        $user->save();

            $ictcore_integration = Ictcore_integration::select("*")->first();
                 
			if(!empty($ictcore_integration) && $ictcore_integration->ictcore_url !='' && $ictcore_integration->ictcore_user !='' && $ictcore_integration->ictcore_password !=''){ 

				 $ict  = new ictcoreController();
				 	$data = array(
					'first_name' => $student->firstName,
					'last_name' => $student->lastName,
					'phone'     => $student->fatherCellNo,
					'email'     => '',
					);
					$contact_id = $ict->ictcore_api('contacts','POST',$data );

                   $message = 'School name'.'<br>'.'Login Name: '.  $student->firstName.$student->lastName.' Password: '.$student->regiNo;
                    $data = array(
					'name' => 'School Name',
					'data' => $message,
					'type'     => 'plain',
					'description'     => 'testing message',
					);

                  $text_id = $ict->ictcore_api('messages/texts','POST',$data );

                  $data = array(
					'name' => 'School Name',
					'text_id' => $text_id
					);

                    $program_id = $ict->ictcore_api('programs/sendsms','POST',$data );

					$data = array(
					'title' => 'User Detail',
					'program_id' => $program_id,
					'account_id'     => 1,
					'contact_id'     => $contact_id,
					'origin'     => 1,
					'direction'     => 'outbound',
					);
					$transmission_id = $ict->ictcore_api('transmissions','POST',$data );
					
             
            }

   	   return Redirect::to('/student/list')->with("success","Teacher Moblie Access Created.");

   }
   return Redirect::to('/student/list')->with("error","Teacher not found.");
}

public function send_sms()
{
    $ictcore_integration  = Ictcore_integration::select("*")->where('type','sms')->first();
    $ict                  = new ictcoreController();
    $snd_msg  = $ict->verification_number_telenor_sms(Input::get('phone'),Input::get('message'),'SidraSchool',$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password,'sms');
    //echo "<pre>";print_r( $snd_msg);
    //exit;
    if($snd_msg->response!='OK'){
    $error = "Whoops!";
	return Redirect::to('/student/view/'.Input::get('id'))->with("error"," $error Message Not send");
    }else{
     return Redirect::to('/student/view/'.Input::get('id'))->with("success","  Message sended");

    }
}

}
