<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Student;
use App\ClassModel;
use App\Subject;
use App\GPA;
use App\Marks;
use DB;
Class formfoo{

}
class markController extends BaseController {


	public function __construct() {
		/*$this->beforeFilter('csrf', array('on'=>'post'));
		$this->beforeFilter('auth');*/
		$this->middleware('auth');
	}
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
		$classes = ClassModel::select('code','name')->orderby('code','asc')->get();
		$subjects = Subject::all();
		$class_code =Input::get('class_id');
		if($class_code !=''){
           $sections = DB::table('section')->where('class_code',$class_code)->get();
		}else{
			$eections = array();
		}
		$section =Input::get('section');
		$session =Input::get('session');
		$exam =Input::get('exam');
		if($exam !='' && $class_code!=''){
			$exams = DB::table('exam')->where('id',$exam)->get();
		}else{
			$exams = array();
		}
		//return View::Make('app.markCreate',compact('classes','subjects'));
		return View('app.markCreate',compact('classes','subjects','class_code','section','session','exam','sections','exams'));
	}
	public function m_index()
	{
		$classes = ClassModel::select('code','name')->orderby('code','asc')->get();
		$subjects = Subject::all();
		$class_code =Input::get('class_id');
		if($class_code !=''){
           $sections = DB::table('section')->where('class_code',$class_code)->get();
		}else{
			$eections = array();
		}
		$section =Input::get('section');
		$session =Input::get('session');
		$exam =Input::get('exam');
		if($exam !='' && $class_code!=''){
			$exams = DB::table('exam')->where('id',$exam)->get();
		}else{
			$exams = array();
		}
		//return View::Make('app.markCreate',compact('classes','subjects'));
		return View('app.mmarkCreate',compact('classes','subjects','class_code','section','session','exam','sections','exams'));
	}


	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create()
	{
		$rules=[
			'class' => 'required',
			'section' => 'required',
			'shift' => 'required',
			'session' => 'required',
			'regiNo' => 'required',
			'exam' => 'required',
			'subject' => 'required',
			'written' => 'required',
			'mcq' => 'required',
			'practical' =>'required',
			'ca' =>'required'
		];
		$validator = \Validator::make(Input::all(), $rules);
		if ($validator->fails())
		{
			return Redirect::to('/mark/create?class_id='.Input::get('class').'&section='.Input::get('section').'&session='.Input::get('session').'&exam='.Input::get('exam'))->withErrors($validator);
		}
		else {
			$subGradeing = Subject::select('gradeSystem')->where('code',Input::get('subject'))->where('class',Input::get('class'))->first();
			if($subGradeing->gradeSystem=="1")
			{
				$gparules = GPA::select('gpa','grade','markfrom')->where('for',"1")->get();

			}
			else if($subGradeing->gradeSystem=="2") {
				$gparules = GPA::select('gpa','grade','markfrom')->where('for',"2")->get();
			}else{
				$gparules = GPA::select('gpa','grade','markfrom')->where('for',$subGradeing->gradeSystem)->get();

			}

			//	 $totalMark = Input
			$len = count(Input::get('regiNo'));

			$regiNos = Input::get('regiNo');
			$writtens=Input::get('written');
			$mcqs =Input::get('mcq');
			$practicals=Input::get('practical');
			$cas=Input::get('ca');
			$isabsent = Input::get('absent');
			$counter=0;

			for ( $i=0; $i< $len;$i++) {
				$isAddbefore = Marks::where('regiNo','=',$regiNos[$i])->where('exam','=',Input::get('exam'))->where('subject','=',Input::get('subject'))->first();
				if($isAddbefore)
				{

				}
				else {
					$marks = new Marks;
					$marks->class = Input::get('class');
					$marks->section = Input::get('section');
					$marks->shift = Input::get('shift');
					$marks->session = trim(Input::get('session'));
					$marks->regiNo = $regiNos[$i];
					$marks->exam = Input::get('exam');
					$marks->subject = Input::get('subject');
					$marks->written = $writtens[$i];
					$marks->mcq = $mcqs[$i];
					$marks->practical = $practicals[$i];
					$marks->ca = $cas[$i];
					$isExcludeClass = Input::get('class');
					if($isExcludeClass=="cl3" ||  $isExcludeClass=="cl4" || $isExcludeClass=="cl5")
					{
						$totalmark = $writtens[$i]+$mcqs[$i]+$practicals[$i]+$cas[$i];
					}
					else
					{
						//$totalmark = ((($writtens[$i]+$mcqs[$i])*80)/100)+$practicals[$i]+$cas[$i];
						$totalmark = $writtens[$i]+$mcqs[$i]+$practicals[$i]+$cas[$i];
					}
					$marks->total=$totalmark;
					//echo "<pre>d";print_r($gparules->toArray());
					foreach ($gparules as $gpa) {

						if ($totalmark >= $gpa->markfrom){
							$marks->grade = $gpa->gpa;
							$marks->point = $gpa->grade;
							break;
						}
					}

					if($isabsent[$i]!== "")
					{
						$marks->Absent = $isabsent[$i];
					}
                    //   echo "<pre>";print_r($marks);exit;
					$marks->save();
					$counter++;
				}
			}
			if($counter==$len)
			{
				return Redirect::to('/mark/create?class_id='.Input::get('class').'&section='.Input::get('section').'&session='.Input::get('session').'&exam='.Input::get('exam'))->with("success",$counter."'s student mark save Succesfully.");
			}
			else {
				$already=$len-$counter;
				return Redirect::to('/mark/create?class_id='.Input::get('class').'&section='.Input::get('section').'&session='.Input::get('session').'&exam='.Input::get('exam'))->with("success",$counter." students mark save Succesfully and ".$already." Students marks already saved.</strong>");
			}
		}
	}

    public function m_create()
	{
		$rules=[
			'class'       => 'required',
			'section'     => 'required',
			'shift'       => 'required',
			'session'     => 'required',
			'regiNo'      => 'required',
			'exam'        => 'required',
			'subject'     => 'required',
			'written'     => 'required',
			'total_marks' => 'required',
			//'mcq' => 'required',
			//'practical' =>'required',
			//'ca' =>'required'
		];
		$validator = \Validator::make(Input::all(), $rules);
		if ($validator->fails())
		{
			return Redirect::to('/mark/m_create?class_id='.Input::get('class').'&section='.Input::get('section').'&session='.Input::get('session').'&exam='.Input::get('exam'))->withErrors($validator);
		}
		else {
			//echo "<pre>";
			//////print_r(Input::all());
			//exit;
			$total_marks = Input::get('total_marks');
			if($total_marks==100){
				$grade = 1;
			}
			if($total_marks==50){
				$grade = 2;
			}
			if($total_marks==75){
				$grade = 3;
			}
			if($total_marks==30){
				$grade = 4;
			}
			if($total_marks==25){
				$grade = 5;
			}
			if($total_marks==20){
				$grade = 6;
			}
			if($total_marks==15){
				$grade = 7;
			}
			if($total_marks==10){
				$grade = 8;
			}
			if($total_marks==5){
				$grade = 9;
			}
			//$subGradeing = Subject::select('gradeSystem')->where('code',Input::get('subject'))->where('class',Input::get('class'))->first();
			$gparules = GPA::select('gpa','grade','markfrom')->where('for',$grade )->orderBy('markfrom','desc')->get();
           //echo "<pre>";print_r($gparules->toArray());
			/*if($subGradeing->gradeSystem=="1")
			{
				$gparules = GPA::select('gpa','grade','markfrom')->where('for',"1")->get();

			}
			else if($subGradeing->gradeSystem=="2") {
				$gparules = GPA::select('gpa','grade','markfrom')->where('for',"2")->get();
			}else{
				$gparules = GPA::select('gpa','grade','markfrom')->where('for',$subGradeing->gradeSystem)->get();

			}*/

			//	 $totalMark = Input
			$len = count(Input::get('regiNo'));

			$regiNos = Input::get('regiNo');
			$writtens=Input::get('written');
			$mcqs =Input::get('mcq');
			$practicals=Input::get('practical');
			$cas=Input::get('ca');
			$isabsent = Input::get('absent');
			$counter=0;

			for ( $i=0; $i< $len;$i++) {
				$isAddbefore = Marks::where('regiNo','=',$regiNos[$i])->where('exam','=',Input::get('exam'))->where('subject','=',Input::get('subject'))->first();
				if($isAddbefore)
				{

				}
				else {
					$marks = new Marks;
					$marks->class = Input::get('class');
					$marks->section = Input::get('section');
					$marks->shift = Input::get('shift');
					$marks->session = trim(Input::get('session'));
					$marks->regiNo = $regiNos[$i];
					$marks->exam = Input::get('exam');
					$marks->subject = Input::get('subject');
					$marks->written = '';
					$marks->mcq = '';
					$marks->practical = '';
					$marks->ca = '';
					$marks->obtain_marks = $writtens[$i];
					$marks->total_marks = $total_marks;
					$marks->ca = '';
					$isExcludeClass = Input::get('class');
					
					$marks->total=$writtens[$i];
					//echo "<pre>d";print_r($gparules->toArray());
					foreach ($gparules as $gpa) {

						if ($writtens[$i] >= $gpa->markfrom){
							$marks->grade = $gpa->gpa;
							$marks->point = $gpa->grade;
							break;
						}
					}
					if($isabsent[$i]!== "")
					{
						$marks->Absent = $isabsent[$i];
					}
                    //echo "<pre>";print_r($marks);exit;
					//$test[] = $marks;
					$marks->save();
					$counter++;
				}
				
			}
			//echo "<pre>";print_r($test);
				//exit;
			if($counter==$len)
			{
				return Redirect::to('/mark/m_create?class_id='.Input::get('class').'&section='.Input::get('section').'&session='.Input::get('session').'&exam='.Input::get('exam'))->with("success",$counter."'s student mark save Succesfully.");
			}
			else {
				$already=$len-$counter;
				return Redirect::to('/mark/m_create?class_id='.Input::get('class').'&section='.Input::get('section').'&session='.Input::get('session').'&exam='.Input::get('exam'))->with("success",$counter." students mark save Succesfully and ".$already." Students marks already saved.</strong>");
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

		$formdata = new formfoo;
		$formdata->class="";
		$formdata->section="";
		$formdata->shift="";
		$formdata->session="";
		$formdata->subject="";
		$formdata->exam="";
		$classes = ClassModel::select('code','name')->orderby('code','asc')->get();
		//$subjects = Subject::lists('name','code');
		$marks=array();


		//$formdata["class"]="";
		//return View::Make('app.markList',compact('classes','marks','formdata'));
		return View('app.markList',compact('classes','marks','formdata'));
	}
	public function m_show()
	{

		$formdata = new formfoo;
		$formdata->class="";
		$formdata->section="";
		$formdata->shift="";
		$formdata->session="";
		$formdata->subject="";
		$formdata->exam="";
		$classes = ClassModel::select('code','name')->orderby('code','asc')->get();
		//$subjects = Subject::lists('name','code');
		$marks=array();


		//$formdata["class"]="";
		//return View::Make('app.markList',compact('classes','marks','formdata'));
		return View('app.mmarkList',compact('classes','marks','formdata'));
	}

	public function getlist()
	{
		$rules=[
			'class' => 'required',
			'section' => 'required',
			'shift' => 'required',
			'session' => 'required',
			'exam' => 'required',
			'subject' => 'required',

		];
		$validator = \Validator::make(Input::all(), $rules);
		if ($validator->fails())
		{
			return Redirect::to('/mark/list/')->withErrors($validator);
		}
		else {
			$classes2 = ClassModel::orderby('code','asc')->pluck('name','code');
			$subjects = Subject::where('class',Input::get('class'))->pluck('name','code');
			$marks=	DB::table('Marks')
			->join('Student', 'Marks.regiNo', '=', 'Student.regiNo')
			->select('Marks.id','Marks.regiNo','Student.rollNo', 'Student.firstName','Student.middleName','Student.lastName', 'Marks.written','Marks.mcq','Marks.practical','Marks.ca','Marks.total','Marks.grade','Marks.point','Marks.Absent')
			->where('Student.isActive', '=', 'Yes')
			->where('Student.class','=',Input::get('class'))
			->where('Marks.class','=',Input::get('class'))
			->where('Marks.section','=',Input::get('section'))
		         //->Where('Marks.shift','=',Input::get('shift'))
			->where('Marks.session','=',trim(Input::get('session')))
			->where('Marks.subject','=',Input::get('subject'))
			->where('Marks.exam','=',Input::get('exam'))
			->get();

			$formdata = new formfoo;
			$formdata->class=Input::get('class');
			$formdata->section=Input::get('section');
			$formdata->shift=Input::get('shift');
			$formdata->session=Input::get('session');
			$formdata->subject=Input::get('subject');
			$formdata->exam=Input::get('exam');

			if(count($marks)==0)
			{
				$noResult = array("noresult"=>"No Results Found!!");
				//return Redirect::to('/mark/list')->with("noresult","No Results Found!!");
				//return View::Make('app.markList',compact('classes2','subjects','marks','noResult','formdata'));
				return View('app.markList',compact('classes2','subjects','marks','noResult','formdata'));
			}

			//return View::Make('app.markList',compact('classes2','subjects','marks','formdata'));
			return View('app.markList',compact('classes2','subjects','marks','formdata'));
		}
	}


	public function m_getlist()
	{
		$rules=[
			'class' => 'required',
			'section' => 'required',
			'shift' => 'required',
			'session' => 'required',
			'exam' => 'required',
			'subject' => 'required',

		];
		$validator = \Validator::make(Input::all(), $rules);
		if ($validator->fails())
		{
			return Redirect::to('/mark/m_list/')->withErrors($validator);
		}
		else {
			$classes2 = ClassModel::orderby('code','asc')->pluck('name','code');
			$subjects = Subject::where('class',Input::get('class'))->pluck('name','code');
			$marks=	DB::table('Marks')
			->join('Student', 'Marks.regiNo', '=', 'Student.regiNo')
			->select('Marks.id','Marks.regiNo','Student.rollNo', 'Student.firstName','Student.middleName','Student.lastName', 'Marks.written','Marks.mcq','Marks.practical','Marks.ca','Marks.total','Marks.obtain_marks','Marks.total_marks','Marks.grade','Marks.point','Marks.Absent')
			->where('Student.isActive', '=', 'Yes')
			->where('Student.class','=',Input::get('class'))
			->where('Marks.class','=',Input::get('class'))
			->where('Marks.section','=',Input::get('section'))
		         //->Where('Marks.shift','=',Input::get('shift'))
			->where('Marks.session','=',trim(Input::get('session')))
			->where('Marks.subject','=',Input::get('subject'))
			->where('Marks.exam','=',Input::get('exam'))
			->get();

			$formdata = new formfoo;
			$formdata->class=Input::get('class');
			$formdata->section=Input::get('section');
			$formdata->shift=Input::get('shift');
			$formdata->session=Input::get('session');
			$formdata->subject=Input::get('subject');
			$formdata->exam=Input::get('exam');

			if(count($marks)==0)
			{
				$noResult = array("noresult"=>"No Results Found!!");
				//return Redirect::to('/mark/list')->with("noresult","No Results Found!!");
				//return View::Make('app.markList',compact('classes2','subjects','marks','noResult','formdata'));
				return View('app.mmarkList',compact('classes2','subjects','marks','noResult','formdata'));
			}

			//return View::Make('app.markList',compact('classes2','subjects','marks','formdata'));
			return View('app.mmarkList',compact('classes2','subjects','marks','formdata'));
		}
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function edit($id)
	{
		$marks=	DB::table('Marks')
		->join('Student', 'Marks.regiNo', '=', 'Student.regiNo')
		->select('Marks.id','Marks.regiNo','Student.rollNo', 'Student.firstName','Student.middleName','Student.lastName','Marks.subject','Marks.class', 'Marks.written','Marks.mcq','Marks.practical','Marks.ca','Marks.total','Marks.grade','Marks.point','Marks.Absent')
		->where('Marks.id','=',$id)
		->first();

		//return View::Make('app.markEdit',compact('marks'));
		return View('app.markEdit',compact('marks'));


	}
	public function m_edit($id)
	{
		$marks=	DB::table('Marks')
		->join('Student', 'Marks.regiNo', '=', 'Student.regiNo')
		->select('Marks.id','Marks.regiNo','Marks.obtain_marks','Marks.total_marks','Student.rollNo', 'Student.firstName','Student.middleName','Student.lastName','Marks.subject','Marks.class', 'Marks.written','Marks.mcq','Marks.practical','Marks.ca','Marks.total','Marks.grade','Marks.point','Marks.Absent')
		->where('Marks.id','=',$id)
		->first();

		//return View::Make('app.markEdit',compact('marks'));
		return View('app.mmarkEdit',compact('marks'));


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
			'written' => 'required',
			'mcq' => 'required',
			'practical' =>'required',
			'ca' =>'required',
			'subject' => 'required',
			'class' => 'required'
		];
		$validator = \Validator::make(Input::all(), $rules);
		if ($validator->fails())
		{
			return Redirect::to('/mark/edit/'.Input::get('id'))->withErrors($validator);
		}
		else {

			$marks = Marks::find(Input::get('id'));
			//get subject grading system
			$subGradeing = Subject::select('gradeSystem','class')->where('code',Input::get('subject'))->where('class',Input::get('class'))->first();
			if($subGradeing->gradeSystem=="1")
			{
				$gparules = GPA::select('gpa','grade','markfrom')->where('for',"1")->get();

			}
			else {
				$gparules = GPA::select('gpa','grade','markfrom')->where('for',"2")->get();
			}
			//end
			$marks->written=Input::get('written');
			$marks->mcq = Input::get('mcq');
			$marks->practical=Input::get('practical');
			$marks->ca=Input::get('ca');

			$isExcludeClass=$subGradeing->class;
			if($isExcludeClass=="cl3" ||  $isExcludeClass=="cl4" || $isExcludeClass=="cl5")
			{
				$totalmark =Input::get('written')+Input::get('mcq')+Input::get('practical')+Input::get('ca');
			}
			else
			{
				//$totalmark = (((Input::get('written')+Input::get('mcq'))*80)/100)+Input::get('practical')+Input::get('ca');
				 $totalmark =Input::get('written')+Input::get('mcq')+Input::get('practical')+Input::get('ca');

			}
			$marks->total=$totalmark;
			foreach ($gparules as $gpa) {
				if ($totalmark >= $gpa->markfrom){
					$marks->grade=$gpa->gpa;
					$marks->point=$gpa->grade;
					break;
				}
			}
			$marks->Absent=Input::get('Absent');

			$marks->save();
			return Redirect::to('/mark/list')->with("success","Marks Update Sucessfully.");

		}
	}
	public function m_update()
	{
		$rules=[
			'written' => 'required',
			//'mcq' => 'required',
			//'practical' =>'required',
			///'ca' =>'required',
			'subject' => 'required',
			'class' => 'required',
			'total_marks' => 'required',
		];
		$validator = \Validator::make(Input::all(), $rules);
		if ($validator->fails())
		{
			return Redirect::to('/mark/m_edit/'.Input::get('id'))->withErrors($validator);
		}
		else {
			$marks = Marks::find(Input::get('id'));
			//get subject grading system
			//$subGradeing = Subject::select('gradeSystem','class')->where('code',Input::get('subject'))->where('class',Input::get('class'))->first();
			$total_marks = Input::get('total_marks');
			if($total_marks==100){
				$grade = 1;
			}
			if($total_marks==50){
				$grade = 2;
			}
			if($total_marks==75){
				$grade = 3;
			}
			if($total_marks==30){
				$grade = 4;
			}
			if($total_marks==25){
				$grade = 5;
			}
			if($total_marks==20){
				$grade = 6;
			}
			if($total_marks==15){
				$grade = 7;
			}
			if($total_marks==10){
				$grade = 8;
			}
			if($total_marks==5){
				$grade = 9;
			}
			//$subGradeing = Subject::select('gradeSystem')->where('code',Input::get('subject'))->where('class',Input::get('class'))->first();
			$gparules = GPA::select('gpa','grade','markfrom')->where('for',$grade )->orderBy('markfrom','desc')->get();
           //echo "<pre>";print_r($gparules->toArray());

			//end
			$marks->obtain_marks=Input::get('written');
			//$marks->total = Input::get('written');
			$marks->total_marks=Input::get('total_marks');
			//$marks->ca=Input::get('ca');

			
			$marks->total=Input::get('written');
			foreach ($gparules as $gpa) {
				if (Input::get('written') >= $gpa->markfrom){
					$marks->grade=$gpa->gpa;
					$marks->point=$gpa->grade;
					break;
				}
			}
			$marks->Absent=Input::get('Absent');

			$marks->save();
			return Redirect::to('/mark/m_list')->with("success","Marks Update Sucessfully.");

		}
	}
}
