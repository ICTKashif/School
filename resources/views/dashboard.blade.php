@extends('layouts.master')
@section("style")
<link href="{{ URL::asset('/css/custom.min.css')}}" rel='stylesheet'>
<link href="{{ URL::asset('/font-awesome/css/font-awesome.min.css')}}" rel='stylesheet'>

<style>
.fc-today{
  background-color: #2AA2E6;
  color:#fff;


}
.fc-button-today
{
  display: none;
}
.green{
  color: #1ABB9C;
}

</style>
@stop
@section('content')
@if (Session::get('accessdined'))
<div class="alert alert-danger">
  <button data-dismiss="alert" class="close" type="button">×</button>
  <strong>Process Faild.</strong> {{ Session::get('accessdined')}}

</div>
@endif

<div class="row">
  <div class="col-md-12 col-sm-12 col-xs-12">
    <!-- /top tiles -->
    <div class="row tile_count text-center">
      <div class="col-md-6 col-sm-6 col-xs-6 tile_stats_count">
         <a href="{{url('/class/list')}}">
        <span class="count_top"><i class="fa fa-2x fa-home green"></i>Class</span>
        <div class="count red">{{$total['class']}}</div>
      </a>
      </div>
         <div class="col-md-6 col-sm-6 col-xs-6 tile_stats_count">
         <a href="{{url('/student/list')}}">
        <span class="count_top"><i class="fa fa-2x fa-users green"></i> Students</span>
        <div class="count blue">{{$total['student']}}</div>
      </a>
      </div>

      <div class="col-md-6 col-sm-6 col-xs-6  tile_stats_countw">
        <a href="{{url('/attendance_detail?action=absent')}}">
        <span class="count_top"><i class="fa fa-2x fa-calendar green"></i> Absent Student <small>(today)</small> </span>
        <div class="count yellow" style="font-size: 40px;">{{$total['totalabsent']}}</div>
      </a>
      </div>

         
      @if(Auth::user()->group=='Admin')
      <div class="col-md-6 col-sm-6 col-xs-6 tile_stats_count">
       <a href="{{url('/fee_detail?action=paid')}}">
        <span class="count_top"><i class="fa fa-2x fa-check-circle green"></i> Fee Paid <small>({{$month_n}})</small></span>
        <div class="count yellow">{{$ourallpaid}}</div>
      </a>
      </div>
      @endif

      <div class="col-md-6 col-sm-6 col-xs-6 tile_stats_countw">
         <a href="{{url('/attendance_detail?action=late')}}">
        <span class="count_top"><i class="fa fa-2x fa-bell-o green"></i>Late Student <small>(today)</small> </span>
        <div class="count blue" style="font-size: 40px;">{{$total['totallate']}}</div>
      </a>
      </div>

       @if(Auth::user()->group=='Admin')
      <div class="col-md-6 col-sm-6 col-xs-6 tile_stats_count">
        <a href="{{url('/fee_detail?action=unpaid')}}">
        <span class="count_top"><i class="fa fa-2x fa-bullhorn green"></i> Fee UnPaid <small>({{$month_n}})</small></span>
        <div class="count blue">{{$ourallunpaid}}</div>
     </a>
      </div>
      @endif

      
    </div>

  
      
    </div>

    </div>

   
     <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-body">
                        <!-- THE CALENDAR -->
                        <div id="calendar"></div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            <div class="col-md-6">
            @if(request()->getHttpHost()=='localhost' || request()->getHttpHost()=='school.ictcore.org')
            <a href='{{url("attendance/today_delete")}}' class="btn btn-danger">Clear today attendance</a>
            @endif
                <div class="box box-info">
                    <div class="box-body" style="max-height: 342px;">
                        <canvas id="attendanceChart" style="width: 400px; height: 150px;"></canvas>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
       
       
 @if(Auth::user()->group=='Admin')
    <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
         
        <h2>Fee Detail <small> {{$month_n}}</small></h2>
         <table id="feeList" class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <th>Class</th>
                  <th>Number of paid</th>
                  <th>Number of Upaid</th>
                  <th>Number of Student</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
              <?php $i=0; 
              //echo "<pre>".$i;print_r($scetionarray);
              //exit;
              ?>
              @foreach($scetionarray as $section)
               
                <tr>
                  <td>{{$section['section']}}</td>
              
                  <td>{{$resultArray1[$i]['paid']}}</td>
                  <td>{{$resultArray1[$i]['unpaid']}}</td>
                  <td>{{$resultArray1[$i]['total']}}</td>
                  <td><a href="{{url('/fees/classreport?class_id='.$section['class'].'&month='.$month.'&year='.$year.'&direct=yes')}}">veiw detail</a></td>
                 
                </tbody>
                <?php $i++; ?>
                @endforeach
              </table>
      </div>
      @endif
     <?php /* <div class="col-md-6 col-sm-6 col-xs-6">
         <h2>Attendance Detail  <small> today</small></h2>
         <table id="feeList" class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <th>Class</th>
                  <th>Number of Student</th>
                  <th>Total Attendance</th>
                  <th>Number of Paresnt</th>
                  <th>Number of Absent</th>
                  <th>Number of Leaves</th>
                  <th>Action</th>
                
                </tr>
              </thead>
              <tbody>
              <?php $i=0; 
              //echo "<pre>".$i;print_r($scetionarray);
              //exit;
              ?>
              @foreach($attendances_b as $attendance)
               
                <tr>
                  <td>{{$attendance['class']}}</td>
                  <td>{{$attendance['total_student']}}</td>
                  <td>{{$attendance['total_attendance']}}</td>
                  <td>{{$attendance['present']}}</td>
                  <td>{{$attendance['absent']}}</td>
                  <td> @if($attendance['leaves']==''){{  0 }} @else {{ $attendance['leaves'] }} @endif </td>
                  <td></td>
                 
                </tbody>
                <?php $i++; ?>
                @endforeach
              </table>
      </div> */ ?>
    </div>



    <!-- /top tiles -->
    <!-- Graph start -->
    <?php /*<div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Accounting Report<small>(Monthly)</small></h2>
            <label class="total_bal">
              Balance: {{$balance}}
            </label>
            <div class="clearfix"></div>
          </div>
          <div class="x_content"><iframe class="chartjs-hidden-iframe" style="width: 100%; display: block; border: 0px; height: 0px; margin: 0px; position: absolute; left: 0px; right: 0px; top: 0px; bottom: 0px;"></iframe>
            <canvas height="136" id="lineChart" width="821" style="width: 821px; height: 136px;"></canvas>
          </div>
        </div>
      </div>

    </div> */ ?>
@stop
@section("script")
<script src="{{url('/js/Chart.min.js')}}"></script>

<script script type="text/javascript">
 
  $(document).ready(function () {

        $('#calendar').fullCalendar({
        header: {
            left: 'prev,next',
            center: 'title',
        },
        today: 'true',
        height: 300,
   <?php if($json_event_data!=''){ ?>
    events: /*[
    {
      title  : 'event1',
      start  : '2018-10-01'
    },
    {
      title  : 'event2',
      start  : '2018-10-05',
      end    : '2018-10-07'
    },
    {
      title  : 'event3',
      start  : '2018-10-09T12:30:00',
      allDay : false // will make the time show
    }
  ]*/
  <?php echo $json_event_data;
   }
   ?>
 
    });

           var ctx = document.getElementById('attendanceChart').getContext('2d');
            //var attendanceChart = new Chart(ctx, config);
            var myChart = new Chart(ctx, {
    type: 'line',
    data: {
          labels: ["<?php echo join($class, '","')?>"],
        datasets: [{
                    label: 'Present',
                    data: ["<?php echo join($present, '","')?>"],
                    backgroundColor:  "rgb(54, 162, 235)",
                    borderColor:  "rgb(54, 162, 235)",
                    fill: false,
                    pointRadius: 6,
                    pointHoverRadius: 20,
                }, {
                    label: 'Absent',
                    data: ["<?php echo join($absent, '","')?>"],
                    backgroundColor: "rgb(255, 99, 132)",
                    borderColor: "rgb(255, 99, 132)",
                    fill: false,
                    pointRadius: 6,
                    pointHoverRadius: 20,

                }
                ]
            },
    options: {
      responsive: true,
       hover: {
                    mode: 'index'
                },
        scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Class'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Attendace'
                        }
                    }]
                },
                title: {
                    display: true,
                    text: 'Students Today\'s Attendance'
                }
    }
});
        });
  



Chart.defaults.global.legend = {
  enabled: false
};
// Line chart
   var ctx = document.getElementById("lineChart");
   var lineChart = new Chart(ctx, {
     type: 'line',
     data: {
       labels: ["<?php echo join($incomes['key'], '","')?>"],
       datasets: [{
         label: "Income",
         backgroundColor: "rgba(38, 185, 154, 0.31)",
         borderColor: "rgba(38, 185, 154, 0.7)",
         pointBorderColor: "rgba(38, 185, 154, 0.7)",
         pointBackgroundColor: "rgba(38, 185, 154, 0.7)",
         pointHoverBackgroundColor: "#fff",
         pointHoverBorderColor: "rgba(220,220,220,1)",
         pointBorderWidth: 1,
         data: [<?php echo join($incomes['value'], ',')?>]
       }, {
         label: "Expence",
         backgroundColor: "rgba(3, 88, 106, 0.3)",
         borderColor: "rgba(3, 88, 106, 0.70)",
         pointBorderColor: "rgba(3, 88, 106, 0.70)",
         pointBackgroundColor: "rgba(3, 88, 106, 0.70)",
         pointHoverBackgroundColor: "#fff",
         pointHoverBorderColor: "rgba(151,187,205,1)",
         pointBorderWidth: 1,
         data: [<?php echo join($expences['value'], ',')?>]
       }]
     },
   });


</script>
@stop
