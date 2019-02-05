<?php
namespace App\Http\Controllers;
use App\ClassModel;
use App\Subject;
use App\Student;
use App\Attendance;
use App\Accounting;
use App\Marks;
use App\AddBook;
use App\Teacher;
use Carbon\Carbon;
use DB;

class DashboardController extends BaseController {

	public function __construct() {
		/*$this->beforeFilter('csrf', array('on'=>'post'));
		$this->beforeFilter('auth', array('only'=>array('index')));*/
		 $this->middleware('auth', array('only'=>array('index')));
	}
	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
		
        $now             =  Carbon::now();
		$year            =  $now->year;
        $month           =  $now->month;
		$error           = \Session::get('error');
		$success         = \Session::get('success');
		$tclass          =  ClassModel::count();
		$tsubject        =  Subject::count();
		$tstudent        =  Student::where('isActive','Yes')->where('session',$year)->count();
		$teacher         =  Teacher::count();
 		$totalAttendance = Attendance::groupBy('date')->get();
 		//echo Carbon::now()->format('Y-m-d');
 		$totalabsent     = Attendance::where('date',Carbon::now()->format('Y-m-d'))->where('status','Absent')->count();
 		$totallate       = Attendance::where('date',Carbon::now()->format('Y-m-d'))->where('status','Late')->count();

 		//echo "<pre>";print_r($totalabsent );exit;
 		$totalExam = Marks::groupBy('exam')->groupBy('subject')->get();
		$book = AddBook::count();
 		$total = [
 			'class' =>$tclass,
 			'student' =>$tstudent,
 			'subject' =>$tsubject,
 			'attendance' =>count($totalAttendance),
 			'exam' =>count($totalExam),
			'book' => $book,
			'totalabsent' => $totalabsent,
			'teacher' => $teacher,
			'totallate'=>$totallate
 		];
 	     // 	//graph data
 	  //dd($total);
 		$monthlyIncome= Accounting::selectRaw('month(date) as month, sum(amount) as amount, year(date) as year')
 		->where('type','Income')
 		->groupBy('month')
 		->get();
              
 		$monthlyExpences= Accounting::selectRaw('month(date) as month, sum(amount) as amount, year(date) as year')
 		->where('type','Expence')
 		->groupBy('month')
 		->get();
 		$incomeTotal = Accounting::where('type','Income')
 		->sum('amount');
 		$expenceTotal = Accounting::where('type','Expence')
 		->sum('amount');
 		$incomes=$this->datahelper($monthlyIncome);
 		$expences=$this->datahelper($monthlyExpences);
 		$balance = $incomeTotal - $expenceTotal;
		//return View::Make('dashboard',compact('error','success','total','incomes','expences','balance'));
		
        //paid or unpaid fee list
		
        $attendances_b = array();
		$scetionarray = array();
	    $resultArray1 = array();
        $student_all12 =	DB::table('section')
             /*->where('session','=',$student->session)*/
             ->leftjoin('Student','section.id','=','Student.section')
             ->leftjoin('stdBill','Student.regiNo','=','stdBill.regiNo')
             ->leftJoin('billHistory', 'stdBill.billNo', '=', 'billHistory.billNo')
             ->select( 'stdBill.billNo','stdBill.payDate','stdBill.regiNo')
             ->where('Student.isActive','Yes')
             ->get();

            // echo "<pre>";print_r($student_all);exit;



		//$all_section =	DB::table('section')->select( '*')->get();
		$all_section =	DB::table('Class')->select( '*')->get();
		//$student_all =	DB::table('Student')->select( '*')->where('class','=',Input::get('class'))->where('section','=',Input::get('section'))->where('session','=',$student->session)->get();
        $ourallpaid =0;
        $ourallunpaid=0;
		if(count($all_section)>0){
			$i=0;
			
            
          
			foreach($all_section as $section){
				 $paid =0;
                 $unpaid=0;
                 $total_s=0;
             $student_all =	DB::table('Student')->select( '*')->where('class','=',$section->code)/*->where('section','=',$section->id)/**/->where('session','=',$year)
              //->where('Student.session','=',$year)
             ->where('Student.isActive','=','Yes')
             ->get();
			   $resultArray[$section->code.'_'.$section->name."_".'total']=0;
                $resultArray[$section->code.'_'.$section->name."_".'unpaid']=0;
		        $resultArray[$section->code.'_'.$section->name."_".'paid'] =  0;
			  if(count($student_all) >0){
			foreach($student_all as $stdfees){
				$student =	DB::table('billHistory')->leftJoin('stdBill', 'billHistory.billNo', '=', 'stdBill.billNo')
				->select( 'billHistory.billNo','billHistory.month','billHistory.fee','billHistory.lateFee','stdBill.class as class1','stdBill.payableAmount','stdBill.billNo','stdBill.payDate','stdBill.regiNo')
				// ->whereYear('stdBill.payDate', '=', 2017)
				->where('stdBill.regiNo','=',$stdfees->regiNo)->whereYear('stdBill.payDate', '=', $year)->where('billHistory.month','=',$month)->where('billHistory.month','<>','-1')
				//->orderby('stdBill.payDate')
				->get();
				if(count($student)>0 ){
					foreach($student as $rey){
						//$status[] = "paid".'_'.$stdfees->regiNo."_";
						//$resultArray[$i] = get_object_vars($stdfees);
						//array_push($resultArray[$i],'Paid',$rey->payDate,$rey->billNo,$rey->fee);
						$resultArray[$section->code.'_'.$section->name."_".'paid'] =  ++$paid;
					    //$yes ='yes';
					   $ourallpaid = ++$ourallpaid;
					}
				}else{
					//$status[$i] = "unpaid".'_'.$stdfees->regiNo."_";
					//$resultArray[] = get_object_vars($stdfees);
					//array_push($resultArray[$i],'unPaid');
					
					//$resultArray[$section->class_code.'_'.$section->name."_".'paid'] =  0;
					$resultArray[$section->code.'_'.$section->name."_".'unpaid']=++$unpaid;
				$ourallunpaid =++$ourallunpaid;
				}
				$resultArray[$section->code.'_'.$section->name."_".'total']=++$total_s;
			}
		}else{
          $resultArray[$section->code.'_'.$section->name."_".'total']=0;
          $resultArray[$section->code.'_'.$section->name."_".'unpaid']=0;
		  $resultArray[$section->code.'_'.$section->name."_".'paid'] =  0;

		}
			//$resultArray[] = get_object_vars($section);
			//array_push($resultArray[$i],$total,$paid,$unpaid);
            $scetionarray[] = array('section'=>$section->name,'class'=>$section->code);
            $resultArray1[] = array('total'=> $resultArray[$section->code.'_'.$section->name."_".'total'],'unpaid'=>$resultArray[$section->code.'_'.$section->name."_".'unpaid'],'paid'=>$resultArray[$section->code.'_'.$section->name."_".'paid']);

			}
			
		}
		else{
		$resultArray = array();
		}

          foreach($all_section as $teacher ){
          $sections[] = $teacher->id;
          $count_student1 = array();
          $count_student1 =  DB::table('Student')->select(DB::raw('COUNT(*) as total_student'))->where('class',$teacher->code)->first();
          // $count_student =  $count_student1->total_attendance;
          //$count_student[] =$count_student1->toArray();
          $attendances_a = DB::table('Attendance')
             ->join('Class', 'Attendance.class_id', '=', 'Class.id')
		     //->join('section', 'Attendance.section_id', '=', 'section.id')
             ->select(DB::raw('COUNT(*) as total_attendance,
                           SUM(Attendance.status="Absent") as absent,
                           SUM(Attendance.status="Present" ) as present ,
                           SUM(Attendance.coments="sick_leave" OR Attendance.coments="leave") as leaves'),'Class.id as class_id','Class.name as class')->where('Attendance.session',$year)->where('Attendance.class_id',$teacher->id)->where('date',Carbon::today()->toDateString())->first();
           //$tst[] = $attendances_a[$i]->total_attendance;
           //$attendances_a = $attendances_a + $count_student; 
         
           if($attendances_a->total_attendance==0){
           	 $attendances_b[] = array('total_attendance'=>0,'absent'=>0,'present'=>0,'leaves'=>0,'class_id'=>$teacher->id,'class'=>$teacher->name,'total_student'=>$count_student1->total_student);
           }else{
           	//$attendances_b[] = array(get_object_vars($attendances_a),'total_student'=>$count_student1->total_student);
              $attendances_b[] = array('total_attendance'=>$attendances_a->total_attendance,'absent'=>$attendances_a->absent,'present'=>$attendances_a->present,'leaves'=>$attendances_a->leaves,'class_id'=>$attendances_a->class_id,'class'=>$teacher->name,'total_student'=>$count_student1->total_student);


           //	$attendances_b[] =;
           }

          // $attendances_b['total_student'.'_'.$teacher->section] =$count_student1->total_student; 
          // $attendances_b['76']=65;
           //$merged = $attendances_b->merge($count_student);
//echo "<pre>";print_r($attendances_b);exit;
           //array_push($attendances_b,$count_student1);//($attendances_b,$count_student1);
          // $resultArray[$i] = $attendances_b;
              //$result[] = $attendances_b + $count_student1;
			// array_push($attendances_b,'rer');
    // $a = array_merge($attendances_b, $count_student1);

           $i++;
		}



        

		// $test = $resultArray1 + $scetionarray;
        // $result = array_merge_recursive($scetionarray , $resultArray1);
		//echo "<pre>".$ourallpaid;print_r($resultArray1);
		//exit;
		//echo "<pre>";print_r($scetionarray);
		//echo "<pre>";print_r($resultArray1);
		//echo "<pre>";print_r($total);
		
     $month_n = $now->format('F');
         $class = array();
         $present = array();
         $absent = array();
		 foreach($attendances_b as $attendance) :
          $class[] = $attendance['class'];
          $present[] = $attendance['present'];
          $absent[] = $attendance['absent'];
          
		 endforeach;
		/// echo "<pre>class";print_r($class);
		//// echo "<pre>p";print_r($present);
		// echo "<pre>a";print_r($absent);whereYear('created_at', '=', date('Y')
		// exit;
		 $holidays = DB::table('Holidays')/*->whereMonth('holiDate',$month)*/->get();
		 $class_off = DB::table('ClassOffDay')/*->whereMonth('offDate',$month)*/->get();
		
foreach($holidays as $holiday){
          $calender_event[] = array('title'=>$holiday->description,'start'=>$holiday->holiDate);
		}
		foreach($class_off as $class_of){
          $calender_event[] = array('title'=>$class_of->description,'start'=>$class_of->offDate);
		}
//$test = array_merge($class_off->toArray(),$holidays->toArray());
 //echo "<pre>a";print_r($class_off);
if(!empty($calender_event)){
$json_event_data = json_encode($calender_event);
}
else{
	$json_event_data = '';
}
//echo $json_event_data;
//echo "<pre>a";print_r($calender_event);

 //exit;
		return View('dashboard',compact('error','success','total','incomes','expences','balance','scetionarray','resultArray1','year','month_n','attendances_b','month','class','present','absent','ourallunpaid','ourallpaid','json_event_data'));
	}
	private function datahelper($data)
 	{
 		$DataKey = [];
 		$DataVlaue =[];
 		foreach ($data as $d) {
 			array_push($DataKey,date("F", mktime(0, 0, 0, $d->month, 10)).','.$d->year);
 			array_push($DataVlaue,$d->amount);

 		}
 		return ["key"=>$DataKey,"value"=>$DataVlaue];

 	}
}
