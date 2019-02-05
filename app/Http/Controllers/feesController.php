<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Subject;
use App\ClassModel;
use App\Student;
use App\Attendance;
use App\Accounting;
use App\Marks;
use App\AddBook;
use App\FeeCol;
use App\FeeSetup;
use App\Institute;
use App\FeeHistory;
use DB;
use App\Ictcore_fees;
use App\Ictcore_integration;
use App\Http\Controllers\ictcoreController;
use Carbon\Carbon;
class studentfdata{


}
class feesController extends BaseController {

	public function __construct()
	{
		/*$this->beforeFilter('csrf', array('on'=>'post'));
		$this->beforeFilter('auth');
		$this->beforeFilter('userAccess',array('only'=> array('getDelete','stdfeesdelete')));*/
		$this->middleware('auth');
		$this->middleware('auth', array('only'=>array('index')));
	}
	public function getsetup()
	{

		$classes = ClassModel::select('code','name')->orderby('code','asc')->get();
		//return View::Make('app.feesSetup',compact('classes'));
		return View('app.feesSetup',compact('classes'));
	}

	/**
	* Store a newly created resource in storage.
	*
	* @return Response
	*/
	public function postSetup()
	{
		$rules=[

		'class' => 'required',
		'type' => 'required',
		'fee' => 'required|numeric',
		'Latefee' => 'required|numeric',
		'title' => 'required'

		];
		$validator = \Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::to('/fees/setup')->withErrors($validator);
		}
		else {

			/**
			* Save feestep in akunting as a item if accounting enable
			*
			**/
             
				$data = array(
					'name'=>Input::get('title'),
					'sku'=>Input::get('class').'_'.Input::get('type'),
					'description'=>Input::get('description'),
					'sale_price'=>Input::get('fee'),
					'purchase_price'=>Input::get('Latefee'),
					'quantity'=>'20000000',
					'category_id'=>'5',
					'tax_id'=>'1',
					'enabled'=>'1',
					//'name'=>'api2',
				);
			if(accounting_check()!='' && accounting_check()=='yes' ){
				$item = php_curl('items','POST',$data);
				//echo "<pre>vv";print_r($item);exit;
				if (is_int($item)){

					$fee              = new FeeSetup();
					$fee->class       = Input::get('class');
					$fee->type        = Input::get('type');
					$fee->title       = Input::get('title');
					$fee->fee         = Input::get('fee');
					$fee->Latefee     = Input::get('Latefee');
					$fee->description = Input::get('description');
					$fee->item_id     = $item;
					if(Input::get('description')==''){
						$fee->description ='';
					}
					$fee->save();
				}else{
					return Redirect::to('/fees/setup')->withErrors('somthing wrong please try again to creat new feesetup');
				}
			}else{
				    $fee              = new FeeSetup();
					$fee->class       = Input::get('class');
					$fee->type        = Input::get('type');
					$fee->title       = Input::get('title');
					$fee->fee         = Input::get('fee');
					$fee->Latefee     = Input::get('Latefee');
					$fee->description = Input::get('description');
					//$fee->item_id     = $item;
					if(Input::get('description')==''){
						$fee->description ='';
					}
					$fee->save();
			    }
			return Redirect::to('/fees/setup')->with("success","Fee Save Succesfully.");
		}
	}

	public function getList()
	{
		$fees=array();
		$classes = ClassModel::pluck('name','code');

		$formdata = new formfoo;
		$formdata->class="";
		//return View::Make('app.feeList',compact('classes','formdata','fees'));
		return View('app.feeList',compact('classes','formdata','fees'));
	}
	/**
	* Display the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function postList()
	{
		$rules=[

		'class' => 'required'
		];
		$validator = \Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::to('/fees/list')->withErrors($validator);
		}
		else {

			$fees = FeeSetup::select("*")->where('class',Input::get('class'))->get();
			$classes = ClassModel::pluck('name','code');
			$formdata = new formfoo;
			$formdata->class=Input::get('class');
			//return View::Make('app.feeList',compact('classes','formdata','fees'));
			return View('app.feeList',compact('classes','formdata','fees'));



		}
	}


	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function getEdit($id)
	{
		$classes = ClassModel::pluck('name','code');
		$fee = FeeSetup::find($id);
		//return View::Make('app.feeEdit',compact('fee','classes'));
		return View('app.feeEdit',compact('fee','classes'));

	}


	/**
	* Update the specified resource in storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function postEdit()
	{
		$rules=[

		'class' => 'required',
		'type' => 'required',
		'fee' => 'required|numeric',
		'title' => 'required'
		];
		$validator = \Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::to('/fee/edit/'.Input::get('id'))->withErrors($validator);
		}
		else {

			$fee              = FeeSetup::find(Input::get('id'));
			$fee->class       = Input::get('class');
			$fee->type        = Input::get('type');
			$fee->title       = Input::get('title');
			$fee->fee         = Input::get('fee');
			$fee->Latefee     = Input::get('Latefee');
			$fee->description = Input::get('description');
			$fee->save();
			return Redirect::to('/fees/list')->with("success","Fee Updated Succesfully.");


		}
	}


	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function getDelete($id)
	{
		$fee = FeeSetup::find($id);
		$fee->delete();
		return Redirect::to('/fees/list')->with("success","Fee Deleted Succesfully.");
	}
	public function getvouchar()
	{
		$classes = ClassModel::select('code','name')->orderby('code','asc')->get();
		//return View::Make('app.feeCollection',compact('classes'));
		return View('app.feeVouchar',compact('classes'));
	}
	public function postvouchar()
	{
		$classes = ClassModel::select('code','name')->orderby('code','asc')->get();
		//return View::Make('app.feeCollection',compact('classes'));
		return View('app.feeCollection',compact('classes'));
	}

	public function detail()
	{

		$month = array('1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December');
		//echo "<pre>";print_r($fee_list);
		foreach($month as $key=>$mnth) :
			$fee_list =DB::table('stdBill')
		->join('billHistory','stdBill.billNo','=','billHistory.billNo')
		->select('stdBill.billNo','billHistory.month','billHistory.fee','billHistory.lateFee','billHistory.total')
		->where('stdBill.regiNo',Input::get('regiNo'))
		->whereYear('stdBill.created_at', date('Y'))
		->where('billHistory.month',$key);
			// ->orderBy('billHistory.month','ASC');
		if($fee_list->count()>0) :
			$fee_list =$fee_list->first();
		$fee_data[] = array('month'=>$mnth,'fee'=>$fee_list->fee,'lateFee'=>$fee_list->lateFee,'total'=>$fee_list->total,'status'=>'paid');
		else :
			$fee_data[] = array('month'=>$mnth,'fee'=>'','lateFee'=>'','total'=>'','status'=>'unpaid');
		endif ;
		endforeach ;
		return View('app.feedetail',compact('fee_data'));
	}

	public function getCollection()
	{
		$classes = ClassModel::select('code','name')->orderby('code','asc')->get();
		if(Input::get('section')!=''){
			$sections = DB::table('section')->select('*')->where('class_code',Input::get('class_id'))->get();
		}else{
			$sections = '';
		}
		if(Input::get('fee_name')!=''){
			$fees= FeeSetup::select('id','title')->where('id','=',Input::get('fee_name'))->get();
		}else{
			$fees=array();
		}
		if(Input::get('regiNo')!=''){
			$student= Student::select('regiNo','rollNo','firstName','middleName','lastName','discount_id')->where('isActive','=','Yes')->where('regiNo','=',Input::get('regiNo'))->first();
	      //return $students;
		}
		else{
			$student=array();
		}
		    $now             =  Carbon::now();
			$year            =  $now->year;
			$month      =  $now->month;

		//echo "<pre>";print_r($fees->toArray());exit;
		//return View::Make('app.feeCollection',compact('classes'));
		return View('app.feeCollection',compact('classes','sections','fees','student','month'));
	}
	public function postCollection()
	{

		$rules=[

		'class'      => 'required',
		'student'    => 'required',
			//'date'     => 'required',
		'paidamount' => 'required',
		'dueamount'  => 'required',
		'ctotal'     => 'required'

		];
		//echo "<pre>";print_r(Input::all());
		//exit;
		$validator = \Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			return Redirect::to('/fee/collection?class_id='.Input::get('class').'&section='.Input::get('section').'&session='.Input::get('session').'&type='.Input::get('type').'&month='.Input::get('gridMonth')[0].'&fee_name='.Input::get('fee'))->withInput(Input::all())->withErrors($validator);
		}
		else {

			try {

				/*$chk = DB::table('stdBill')
				->join('billHistory','stdBill.billNo','=','billHistory.billNo')
				->where('stdBill.regiNo',Input::get('student'))
				->where('billHistory.month',);
				*/
				$feeTitles       = Input::get('gridFeeTitle');
				$feeAmounts      = Input::get('gridFeeAmount');
				$feeLateAmounts  = Input::get('gridLateFeeAmount');
				$feeTotalAmounts = Input::get('gridTotal');
				$feeMonths       = Input::get('gridMonth');
				$month = $feeMonths[0]; 
				$counter         = count($feeTitles);

				if($counter>0)
				{
					$rows = FeeCol::count();
					if($rows < 9)
					{
						$billId = 'B00'.($rows+1);
					}
					else if($rows < 100)
					{
						$billId = 'B0'.($rows+1);
					}
					else {
						
						$billId = 'B'.($rows+1);
					}

					DB::transaction(function() use ($billId,$counter,$feeTitles,$feeAmounts,$feeLateAmounts,$feeTotalAmounts,$feeMonths)
					{
						$j=0;
						for ($i=0;$i<$counter;$i++) {

							$chk = DB::table('stdBill')
							->join('billHistory','stdBill.billNo','=','billHistory.billNo')
							->where('stdBill.regiNo',Input::get('student'))
							->where('billHistory.month',$feeMonths[$i]);


							if(  $chk->count()==0){
								$feehistory          = new FeeHistory();
								$feehistory->billNo  = $billId;
								$feehistory->title   = $feeTitles[$i];
								$feehistory->fee     = $feeAmounts[$i];
								$feehistory->lateFee = $feeLateAmounts[$i];
								$feehistory->total   = $feeTotalAmounts[$i];
								$feehistory->month   = $feeMonths[$i];
								//$feehistory->save();
								$j++;
							}

						}
						if($j>0){
							$feeCol                = new FeeCol();
							$feeCol->billNo        = $billId;
							$feeCol->class         = Input::get('class');
							$feeCol->regiNo        = Input::get('student');
							$feeCol->payableAmount = Input::get('ctotal');
							$feeCol->paidAmount    = Input::get('paidamount');
							$feeCol->dueAmount     = Input::get('dueamount');
							$feeCol->payDate       = Carbon::now()->format('Y-m-d');
						//echo "<pre>";print_r(Carbon::now()->format('Y-m-d'));exit;
							//$feeCol->save();
							\Session::put('not_save', $j);
						}else{
							\Session::put('not_save', 0);
						}

						/*for ($i=0;$i<$counter;$i++) {

							$feehistory          = new FeeHistory();
							$feehistory->billNo  = $billId;
							$feehistory->title   = $feeTitles[$i];
							$feehistory->fee     = $feeAmounts[$i];
							$feehistory->lateFee = $feeLateAmounts[$i];
							$feehistory->total   = $feeTotalAmounts[$i];
							$feehistory->month   = $feeMonths[$i];
							$feehistory->save();

						}*/
					});
	if(\Session::get('not_save')!=0){
		\Session::forget('not_save');
		$mesg = '';
		if(Input::get('save_sms')=='save_sms'){

			$send_sms = $this->send_sms(Input::get('student'),Input::get('class'),Input::get('paidamount'));
           if($send_sms == 200){
           	 $mesg = "and Send sms";
           }else{
             	 $mesg = "and sms not send some thing wrong";
           }
		}
		return Redirect::to('/fee/collection?class_id='.Input::get('class').'&section='.Input::get('section').'&session='.Input::get('session').'&type='.Input::get('type').'&month='.$month.'&fee_name='.Input::get('fee'))->with("success","Fee collection succesfull ".$mesg);
	}else{
		\Session::forget('not_save');
		$messages = "Student already add fee for this month"; 

		return Redirect::to('/fee/collection?class_id='.Input::get('class').'&section='.Input::get('section').'&session='.Input::get('session').'&type='.Input::get('type').'&month='.$month.'&fee_name='.Input::get('fee'))->withErrors($messages);
	}
	}
	else {
		$messages = $validator->errors();
		$messages->add('Validator!', 'Please add atlest one fee!!!');

		return Redirect::to('/fee/collection?class_id='.Input::get('class').'&section='.Input::get('section').'&session='.Input::get('session').'&type='.Input::get('type').'&month='.$month.'&fee_name='.Input::get('fee'))->withInput(Input::all())->withErrors($messages);

	}
	}
	catch(\Exception $e)
	{
	               //echo $e->getMessage();
		return Redirect::to('/fee/collection?class_id='.Input::get('class').'&section='.Input::get('section').'&session='.Input::get('session').'&type='.Input::get('type').'&month='.Input::get('gridMonth')[0].'&fee_name='.Input::get('fee'))->withErrors( $e->getMessage())->withInput();
	}

	}
	}

	public function send_sms($regiNo,$class,$amount)
	{
		$student_all = DB::table('Student')->where('regiNo',$regiNo)->where('class',$class)->where('isActive','Yes')->first();
		if(!empty($student_all)){
			$ict     = new ictcoreController();
			$i       =0;
			$attendance_noti     = DB::table('notification_type')->where('notification','fess')->first();
			$ictcore_fees        = Ictcore_fees::select("*")->first();
			$ictcore_integration = Ictcore_integration::select("*")->where('type','sms');
			//echo $ictcore_integration->method;
			//exit;
			if($ictcore_integration->count()>0){
				$ictcore_integration = $ictcore_integration->first();
			}else{
				//return Redirect::to('fee_detail?action=unpaid')->withErrors("Sms credential not found");
				return 404;
			}
				//$group_id = $ict->telenor_apis('group','','','','','');
				$contacts = array();
				$contacts1 = array();
				$i=0;
			

			if (preg_match("~^0\d+$~", $student_all->fatherCellNo)) {
					$to = preg_replace('/0/', '92', $student_all->fatherCellNo, 1);
				}else {
					$to =$student_all->fatherCellNo;  
				}
				//$contacts1[] = $to;
				if(strlen(trim($to))==12){
					$contacts = $to;
					//$i++;
				}
				//$comseprated= implode(',',$contacts);
				//$group_contact_id = $ict->telenor_apis('add_contact',$group_id,$contacts,'','','');
		}else{
			return 403;
		}
			$col_msg = DB::table('message')->first();
			if(empty($col_msg)){
				$msg = 'Fee Paid paid amount is '.$amount ;
	      	}else{
	      		$msg =$col_msg->description;
	      		$msg1 = str_replace("[name]",$student_all->firstName.''.$student_all->lastName,$msg);
	      		//$msg2 = str_replace("[month]",$month,$msg1);
	      		$msg = str_replace("[amount]",$amount,$msg1);
	      	}
			/*if($fee_msg->count()>0 && $fee_msg->first()->description!=''){
				$msg = $fee_msg->first()->description;
			}else{
				$msg = "please submit your child  fee for this month";
			}*/
			$snd_msg  = $ict->verification_number_telenor_sms($to,$msg,'SidraSchool',$ictcore_integration->ictcore_user,$ictcore_integration->ictcore_password,'sms');
			//$campaign      = $ict->telenor_apis('campaign_create',$group_id,'',$msg,'','sms');
			//$send_campaign = $ict->telenor_apis('send_msg','','','','',$campaign);
			//session()->forget('upaid');
			return 200;
	}

	public function getListjson($class,$type)
	{
		$fees= FeeSetup::select('id','title')->where('class','=',$class)->where('type','=',$type)->get();
		return $fees;
	}
	public function getFeeInfo($id)
	{
		$fee= FeeSetup::select('fee','Latefee')->where('id','=',$id)->get();
		return $fee;
	}

	public function getDue($class,$stdId)
	{
		$due = FeeCol::select(DB::RAW('IFNULL(sum(payableAmount),0)- IFNULL(sum(paidAmount),0) as dueamount'))
		->where('class',$class)
		->where('regiNo',$stdId)
		->first();
		return $due->dueamount;

	}
	public function stdfeeview()
	{
		$classes = ClassModel::pluck('name','code');
		$student = new studentfdata;
		$student->class="";
		$student->section="";
		$student->shift="";
		$student->session="";
		$student->regiNo="";
		$fees=array();
			//return View::Make('app.feeviewstd',compact('classes','student','fees'));
		return View('app.feeviewstd',compact('classes','student','fees'));
	}
	public function stdfeeviewpost()
	{
		$classes          = ClassModel::pluck('name','code');
		$student          = new studentfdata;
		$student->class   = Input::get('class');
		$student->section = Input::get('section');
		$student->shift   = Input::get('shift');
		$student->session = Input::get('session');
		$student->regiNo  = Input::get('student');
		
		$fees=DB::Table('stdBill')
		->select(DB::RAW("billNo,payableAmount,paidAmount,dueAmount,DATE_FORMAT(payDate,'%D %M,%Y') AS date"))
		->where('class',Input::get('class'))
		->where('regiNo',Input::get('student'))
		->get();
		$totals = FeeCol::select(DB::RAW('IFNULL(sum(payableAmount),0) as payTotal,IFNULL(sum(paidAmount),0) as paiTotal,(IFNULL(sum(payableAmount),0)- IFNULL(sum(paidAmount),0)) as dueamount'))
		->where('class',Input::get('class'))
		->where('regiNo',Input::get('student'))
		->first();

			//return View::Make('app.feeviewstd',compact('classes','student','fees','totals'));
		return View('app.feeviewstd',compact('classes','student','fees','totals'));
	}
	public function stdfeesdelete($billNo)
	{
		try {
			DB::transaction(function() use ($billNo)
			{
				FeeCol::where('billNo',$billNo)->delete();
				FeeHistory::where('billNo',$billNo)->delete();

			});
			return Redirect::to('/fees/view')->with("success","Fees deleted succesfull.");
		}
		catch(\Exception $e)
		{

			return Redirect::to('/fees/view')->withErrors( $e->getMessage())->withInput();
		}

	}
	public function reportstd($regiNo)
	{

		$datas=DB::Table('stdBill')
		->select(DB::RAW("payableAmount,paidAmount,dueAmount,DATE_FORMAT(payDate,'%D %M,%Y') AS date"))
		->where('regiNo',$regiNo)
		->get();
		$totals = FeeCol::select(DB::RAW('IFNULL(sum(payableAmount),0) as payTotal,IFNULL(sum(paidAmount),0) as paiTotal,(IFNULL(sum(payableAmount),0)- IFNULL(sum(paidAmount),0)) as dueamount'))
		->where('regiNo',$regiNo)
		->first();
		$stdinfo=DB::table('Student')
		->join('Class', 'Student.class', '=', 'Class.code')
		->select('Student.regiNo', 'Student.rollNo', 'Student.firstName', 'Student.middleName', 'Student.lastName',
			'Student.section','Student.shift','Student.session','Class.Name as class')
		->where('isActive','Yes')
		->where('Student.regiNo',$regiNo)
		->first();
		$institute=Institute::select('*')->first();
		$rdata =array('payTotal'=>$totals->payTotal,'paiTotal'=>$totals->paiTotal,'dueAmount'=>$totals->dueamount);
		$pdf = \PDF::loadView('app.feestdreportprint',compact('datas','rdata','stdinfo','institute'));
		return $pdf->stream('student-Payments.pdf');

	}
	public function report()
	{
			//return View::Make('app.feesreport');
		return View('app.feesreport');
	}
	public function reportprint($sDate,$eDate)
	{
		$datas= FeeCol::select(DB::RAW('IFNULL(sum(payableAmount),0) as payTotal,IFNULL(sum(paidAmount),0) as paiTotal,(IFNULL(sum(payableAmount),0)- IFNULL(sum(paidAmount),0)) as dueamount'))
		->whereDate('created_at', '>=', date($sDate))
		->whereDate('created_at', '<=', date($eDate))
		->first();
		$institute=Institute::select('*')->first();
		$rdata =array('sDate'=>$this->getAppdate($sDate),'eDate'=>$this->getAppdate($eDate));
		$pdf = \PDF::loadView('app.feesreportprint',compact('datas','rdata','institute'));
		return $pdf->stream('fee-collection-report.pdf');
	}

	public function billDetails($billNo)
	{
		$billDeatils = FeeHistory::select("*")
		->where('billNo',$billNo)
		->get();
		return $billDeatils;
	}
	private function  parseAppDate($datestr)
	{
		$date = explode('/', $datestr);
		return $date[2].'-'.$date[1].'-'.$date[0];
	}
	private function  getAppdate($datestr)
	{
		$date = explode('-', $datestr);
		return $date[2].'/'.$date[1].'/'.$date[0];
	}


	public function classreportindex()
	{
		$classes = ClassModel::pluck('name','code');
		$class   = '';
		$section = '';
		$month   = '';
		$session = '';
		$year    = '';
		$student = new studentfdata;
		$student->class = "";
		$student->section = "";
		$student->shift = "";
		$student->session = "";
		$student->regiNo = "";
		$fees = array();
		$paid_student = array();
		$resultArray  = array();
		return View('app.feestdreportclass',compact('classes','student','fees','totals','class','section','month','session','paid_student','year','resultArray'));
	}

	public function classview(){

		$classes = ClassModel::pluck('name','code');
		$student = new studentfdata;
		$student->class=Input::get('class');
		$student->section=Input::get('section');
		$student->shift=Input::get('shift');
		$student->session=Input::get('session');
		$student->regiNo=Input::get('student');
		$feeyear = Input::get('year') ;

		$student_all =	DB::table('Student')->select( '*')->where('isActive','Yes')->where('class','=',Input::get('class'));
		if(Input::get('section')!='' && Input::get('direct')!='yes'){
			$student_all =$student_all->where('section','=',Input::get('section'));
		}
		  //  if(Input::get('direct')=='yes'){
		$student_all =$student_all->where('session','=',$student->session)->get();
	        //  }
	          //echo '<pre>';print_r($student_all);
	          //exit;
		if(count($student_all)>0){
			$i=0;
			foreach($student_all as $stdfees){

				$student =	DB::table('billHistory')->leftJoin('stdBill', 'billHistory.billNo', '=', 'stdBill.billNo')
				->select( 'billHistory.billNo','billHistory.month','billHistory.fee','billHistory.lateFee','stdBill.class as class1','stdBill.payableAmount','stdBill.billNo','stdBill.payDate','stdBill.regiNo')
					// ->whereYear('stdBill.payDate', '=', 2017)
				->where('stdBill.regiNo','=',$stdfees->regiNo)->whereYear('stdBill.payDate', '=', Input::get('year'))->where('billHistory.month','=',Input::get('month'))->where('billHistory.month','<>','-1')
					//->orderby('stdBill.payDate')
				->get();

				if(count($student)>0 ){

					foreach($student as $rey){

						$status[] = "paid".'_'.$stdfees->regiNo."_";

						$resultArray[$i] = get_object_vars($stdfees);

						array_push($resultArray[$i],'Paid',$rey->payDate,$rey->billNo,$rey->fee);
						$i++;
					}

				}else{
					$status[$i] = "unpaid".'_'.$stdfees->regiNo."_";
					$resultArray[] = get_object_vars($stdfees);
					array_push($resultArray[$i],'unPaid');
					$i++;
				}

			}
		}
		else{
			$resultArray = array();
		}

		$class   = Input::get('class');
		$month   = Input::get('month');
		$section = Input::get('section');
		$session = Input::get('session');
		$year    = Input::get('year');
	       // echo "<pre>";print_r($resultArray);
	        // exit;
		return View('app.feestdreportclass',compact('resultArray','class','month','section','classes','session','year'));
	}

	public function ictcorefees(){


	        //echo "<pre>";print_r(Input::get());
		$classes          = ClassModel::pluck('name','code');
		$student          = new studentfdata;
		$student->class   = Input::get('class');
		$student->section = Input::get('section');
		$student->shift   = Input::get('shift');
		$student->session = Input::get('session');
		$student->regiNo  = Input::get('student');
		$feeyear          = Input::get('year') ;
		if(Input::get('all')=='yes'){
	          //$student_all =unserialize(Input::get('result'));

		}else{
			$student_all =	DB::table('Student')->select( '*')->where('isActive','Yes')->where('class','=',Input::get('class'))->where('section','=',Input::get('section'))->where('session','=',$student->session)->get();
		}
	        //$data = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", Input::get('result'));

		$ictcore_fees    = Ictcore_fees::select("*")->first();
							// echo "<pre>";print_r($student_all);
							// exit;
		if(count($student_all)>0){
			$i=0;


			$ictcore_integration = Ictcore_integration::select("*")->first();
			if(!empty($ictcore_integration) && $ictcore_integration->ictcore_url && $ictcore_integration->ictcore_user && $ictcore_integration->ictcore_password){ 
				$ict  = new ictcoreController();
				$data = array(
					'name' => 'Fee Notification',
					'description' => 'this is Fee Notifacation Group',
					);

				$group_id= $ict->ictcore_api('groups','POST',$data );

			}else{

				return Redirect::to('/fees/classreport')->withErrors("Please Add ictcore integration in Setting Menu");

			}
			foreach($student_all as $stdfees){

				$student =	DB::table('billHistory')->leftJoin('stdBill', 'billHistory.billNo', '=', 'stdBill.billNo')
				->select( 'billHistory.billNo','billHistory.month','billHistory.fee','billHistory.lateFee','stdBill.class as class1','stdBill.payableAmount','stdBill.billNo','stdBill.payDate','stdBill.regiNo')
						// ->whereYear('stdBill.payDate', '=', 2017)
				->where('stdBill.regiNo','=',$stdfees->regiNo)->whereYear('stdBill.payDate', '=', Input::get('year'))->where('billHistory.month','=',Input::get('month'))->where('billHistory.month','<>','-1')
						//->orderby('stdBill.payDate')
				->get();

				if(count($student)>0 ){
								//$resultArray = get_object_vars($stdfees)
				}else{
					$data = array(
						'first_name' => $stdfees->firstName,
						'last_name' =>  $stdfees->lastName,
						'phone'     =>  $stdfees->fatherCellNo,
						'email'     => '',
						);

					$contact_id = $ict->ictcore_api('contacts','POST',$data );

					$group = $ict->ictcore_api('contacts/'.$contact_id.'/link/'.$group_id,'PUT',$data=array() );

							//$resultArray[] = get_object_vars($stdfees);
				}

			}
		}
		else{
			$resultArray = array();
		}
		$data = array(
			'program_id' => $ictcore_fees->ictcore_program_id,
			'group_id' => $group_id,
			'delay' => '',
			'try_allowed' => '',
			'account_id' => 1,
			'status' => '',
			);
		$campaign_id = $ict->ictcore_api('campaigns','POST',$data );
		$campaign_id = $ict->ictcore_api('campaigns/'.$campaign_id.'/start','PUT',$data=array() );
		          //echo "<pre>";print_r($data);

		return Redirect::to('/fees/classreport')->with("success", "Voice campaign Created Succesfully.");
			//return View('app.feestdreportclass',compact('resultArray','class','month','section','classes','session','year'));
	}

	public function fee_detail()
	{
		$action = Input::get('action');
		if($action!=''):
			$now   = Carbon::now();
		$year  =  $now->year;
		$month =  $now->month;
	          // $all_section =	DB::table('section')->select( '*')->get();
		$all_section =	DB::table('Class')->select( '*')->get();
			//$student_all =	DB::table('Student')->select( '*')->where('class','=',Input::get('class'))->where('section','=',Input::get('section'))->where('session','=',$student->session)->get();
		$ourallpaid =0;
		$ourallunpaid=0;
		$unpaidArray=array();
		$resultArray=array();
		if(count($all_section)>0){
			$i=0;
			$paid =0;
			$unpaid=0;
			$total_s=0;
			foreach($all_section as $section){

				$student_all =	DB::table('Student')
				->join('section','Student.section','=','section.id')
				->select( 'Student.*','section.name as section_name')->where('class','=',$section->code)/*->where('section','=',$section->id)/**/->where('session','=',$year)
	              //->where('Student.session','=',$year)
				->where('Student.isActive','=','Yes')
				->get();

	         // $unpaidArray[$section->code.'_'.$section->name."_".'unpaid']=0;
			  //$resultArray[$section->code.'_'.$section->name."_".'paid'] =  0;

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
							//$resultArray[$section->code.'_'.$section->name."_".'paid'] =  ++$paid;
								$resultArray[$section->code.'_'.$section->name."_".'paid_'.$stdfees->regiNo] =  $stdfees;
							}
						}else{

							$unpaidArray[$section->code.'_'.$section->name."_".'unpaid_'.$stdfees->regiNo]=$stdfees;
					//$ourallunpaid =++$ourallunpaid;
						}
					//$resultArray[$section->code.'_'.$section->name."_".'total']=++$total_s;
					}
				}else{
	          //$resultArray[$section->code.'_'.$section->name."_".'total']=0;
	          //$resultArray[$section->code.'_'.$section->name."_".'unpaid']=array();
			 // $unpaidArray[$section->code.'_'.$section->name."_".'paid'] =  array();

				}
				//$resultArray[] = get_object_vars($section);
				//array_push($resultArray[$i],$total,$paid,$unpaid);
	            //$scetionarray[] = array('section'=>$section->name,'class'=>$section->code);
	            //$resultArray1[] = array('total'=> $resultArray[$section->code.'_'.$section->name."_".'total'],'unpaid'=>$resultArray[$section->code.'_'.$section->name."_".'unpaid'],'paid'=>$resultArray[$section->code.'_'.$section->name."_".'paid']);

			}

		}
		else{
			//$resultArray = array();
		}
		if($action=='unpaid'){
			$fee_detail = $unpaidArray;
			$status = 'Unpaid';
			session()->forget('upaid');
			session(['upaid' => $fee_detail]);
			
			$defulters = array();
			/*$i=0;
			foreach($fee_detail as $defulter){
            $defulters[$i]=$defulter->fatherCellNo;
            $i++;
			}*/
			//echo "<pre>";print_r($defulters);
			//exit;

		}else{
			$fee_detail = $resultArray;
			$status = 'Paid';
		}

		$month_n = $now->format('F');

	             //echo "<pre>";print_r( $attendances_detail);
		return View('app.fee_detail', compact('fee_detail','status','month_n','year'));

		endif;
	}
		/**
		* Send notification for all unpaid student
		***/
	public function sendnotification()
	{

       $notification='';
       $student_all =	\Session::get('upaid');
       echo Input::get('action');
		switch (Input::get('action')) {
			case 'sms':
				//
			$notification  = 'sms';
			$send_sms_notification = $this->send_sms_notification($student_all );
				return $send_sms_notification;
				exit;
				break;

			case 'voice':
				// 
			$notification = 'voice';
			$send_voice_notification = $this->send_voice_notification($student_all );
				return $send_voice_notification;
				exit;
				break;
		}

		/*if(!empty($student_all)){
			$ict  = new ictcoreController();
			$i=0;
			$attendance_noti     = DB::table('notification_type')->where('notification','fess')->first();
			$ictcore_fees        = Ictcore_fees::select("*")->first();
			$ictcore_integration = Ictcore_integration::select("*")->where('type',$attendance_noti->type)->first();
			if($ictcore_integration->method=="telenor"){
				$group_id = $ict->telenor_apis('group','','','','','');
			}else{
				if(!empty($ictcore_integration) && $ictcore_integration->ictcore_url && $ictcore_integration->ictcore_user && $ictcore_integration->ictcore_password){ 

					$data = array(
						'name' => 'Fee Notification',
						'description' => 'fee notification',
						);

					echo  $group_id= $ict->ictcore_api('groups','POST',$data );
				}else{
					return Redirect::to('fee_detail?action=unpaid')->withErrors("Please Add ictcore integration in Setting Menu");
					exit();
				}
			}
			$contacts = array();
			$contacts1 = array();
			foreach($student_all as $stdfees)
			{
				if (preg_match("~^0\d+$~", $stdfees->fatherCellNo)) {
					$to = preg_replace('/0/', '92', $stdfees->fatherCellNo, 1);
				}else {
					$to =$stdfees->fatherCellNo;  
				}

				$data = array(
					//'registrationNumber' =>$stdfees->regiNo,
					'first_name'         => $stdfees->firstName,
					'last_name'          =>  $stdfees->lastName,
					'phone'              =>  NULL,
					'email'              => '',
					);
				if($ictcore_integration->method=="telenor"){
					$contacts1[] = $to;
					if(strlen(trim($to))==12){
						$contacts[] = $to;
					}
				}else{
					$contact_id = $ict->ictcore_api('contacts','POST',$data );

					$group = $ict->ictcore_api('contacts/'.$contact_id.'/link/'.$group_id,'PUT',$data=array() );
				}
			}
			if($ictcore_integration->method=="telenor" && !empty($contacts)){
				$comseprated= implode(',',$contacts);
                     
				$group_contact_id = $ict->telenor_apis('add_contact',$group_id,$comseprated,'','','');
			}
		}else{
			return Redirect::to('fee_detail?action=unpaid')->withErrors("Student not found");
		}

		if($ictcore_integration->method=="telenor"){
			$fee_msg = DB::table('ictcore_fees');
			if($fee_msg->count()>0 && $fee_msg->first()->description!=''){
				$msg = $fee_msg->first()->description;
			}else{
				$msg = "please submit your child  fee for this month";
			}
			
			//$group_id='410598';
			$campaign      = $ict->telenor_apis('campaign_create',$group_id,'',$msg,$fee_msg->first()->telenor_file_id,$attendance_noti->type);
			// echo $campaign;
			// print_r($campaign);
			 //exit;
			// $this->info('Notification sended successfully'.$campaign);

			$send_campaign = $ict->telenor_apis('send_msg','','','','',$campaign);
	           //	echo $send_campaign;
			 //print_r($send_campaign);	exit;
			session()->forget('upaid');
			return Redirect::to('fee_detail?action=unpaid')->with('success',"Notification sended");

		}else{
			//echo 'sded';
			if(!empty($ictcore_fees) && $ictcore_fees->ictcore_program_id!=''){
				$data = array(
					'program_id' => $ictcore_fees->ictcore_program_id,
					'group_id' => $group_id,
					'delay' => '',
					'try_allowed' => '',
					'account_id' => 1,
					);
				//$campaign_id = $ict->ictcore_api('campaigns','POST',$data );
				//$campaign_id = $ict->ictcore_api('campaigns/'.$campaign_id.'/start','PUT',$data=array() );
				//session()->forget('upaid');
				return Redirect::to('fee_detail?action=unpaid')->with('success',"Notification sended");

			}
			//echo 'testing';
		}*/
	}
    
    public function send_sms_notification($student_all)
    {
		if(!empty($student_all)){
			$ict  = new ictcoreController();
			$i=0;
			$attendance_noti     = DB::table('notification_type')->where('notification','fess')->first();
			$ictcore_fees        = Ictcore_fees::select("*")->first();
			$ictcore_integration = Ictcore_integration::select("*")->where('type','sms');
			//echo $ictcore_integration->method;
			//exit;
			if($ictcore_integration->count()>0){
				$ictcore_integration = $ictcore_integration->first();
			}else{
				return Redirect::to('fee_detail?action=unpaid')->withErrors("Sms credential not found");
			}
				$group_id = $ict->telenor_apis('group','','','','','');
				$contacts = array();
				$contacts1 = array();
				$i=0;
			foreach($student_all as $stdfees)
			{
				if (preg_match("~^0\d+$~", $stdfees->fatherCellNo)) {
					$to = preg_replace('/0/', '92', $stdfees->fatherCellNo, 1);
				}else {
					$to =$stdfees->fatherCellNo;  
				}
				$contacts1[] = $to;
				if(strlen(trim($to))==12){
					$contacts[] = $to;
					$i++;
				}
				
				/*if($i==3){
					break;
				}*/
			}
				$comseprated= implode(',',$contacts);
				$group_contact_id = $ict->telenor_apis('add_contact',$group_id,$comseprated,'','','');
		}else{
			return Redirect::to('fee_detail?action=unpaid')->withErrors("Student not found");
		}
			$fee_msg = DB::table('ictcore_fees');
			if($fee_msg->count()>0 && $fee_msg->first()->description!=''){
				$msg = $fee_msg->first()->description;
			}else{
				$msg = "please submit your child  fee for this month";
			}
			$campaign = $ict->telenor_apis('campaign_create',$group_id,'',$msg,$fee_msg->first()->telenor_file_id,'sms');
			$send_campaign = $ict->telenor_apis('send_msg','','','','',$campaign);
			session()->forget('upaid');
			return Redirect::to('fee_detail?action=unpaid')->with('success',"Notification sended");
    }
    public function send_voice_notification($student_all)
    {
    	if(!empty($student_all)){
			$ict  = new ictcoreController();
			$i=0;
			$attendance_noti     = DB::table('notification_type')->where('notification','fess')->first();
			$ictcore_fees        = Ictcore_fees::select("*")->first();
			$ictcore_integration = Ictcore_integration::select("*")->where('type','voice')->first();
			if(!empty($ictcore_integration) && $ictcore_integration->ictcore_url && $ictcore_integration->ictcore_user && $ictcore_integration->ictcore_password){ 
				$data = array(
					'name' => 'Fee Notification',
					'description' => 'fee notification',
					);

				 $group_id= $ict->ictcore_api('groups','POST',$data );
			}else{
				return Redirect::to('fee_detail?action=unpaid')->withErrors("Please Add ictcore integration in Setting Menu");
				exit();
			}
			
			//$i=0;
			$contacts = array();
			$contacts1 = array();
			$i=0;
			foreach($student_all as $stdfees)
			{
				if (preg_match("~^0\d+$~", $stdfees->fatherCellNo)) {
					$to = preg_replace('/0/', '92', $stdfees->fatherCellNo, 1);
				}else {
					$to =$stdfees->fatherCellNo;  
				}
				$data = array(
					//'registrationNumber' =>$stdfees->regiNo,
					'first_name'         => $stdfees->firstName,
					'last_name'          =>  $stdfees->lastName,
					'phone'              =>  $to,
					'email'              => '',
					);
					
				    if(strlen(trim($to))==12){
				    	$contact_id = $ict->ictcore_api('contacts','POST',$data );
						$group = $ict->ictcore_api('contacts/'.$contact_id.'/link/'.$group_id,'PUT',$data=array() );
						$i++;
				    }
					/*if($i==3){
						break;
					}*/
			}
		}else{
			return Redirect::to('fee_detail?action=unpaid')->withErrors("Student not found");
		}
			if(!empty($ictcore_fees) && $ictcore_fees->ictcore_program_id!=''){
				$data = array(
					'program_id' => $ictcore_fees->ictcore_program_id,
					'group_id' => $group_id,
					'delay' => '',
					'try_allowed' => '',
					'account_id' => 1,
					);
				$campaign_id = $ict->ictcore_api('campaigns','POST',$data );
				$campaign_id = $ict->ictcore_api('campaigns/'.$campaign_id.'/start','PUT',$data=array() );
				return Redirect::to('fee_detail?action=unpaid')->with('success',"Notification sended");
			}
    }

}
