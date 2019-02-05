@extends('layouts.master')
@section('style')
    <link href="{{url('/css/bootstrap-datepicker.css')}}" rel="stylesheet">
          <link href="/css/timetable.css" rel="stylesheet">

@stop
@section('content')
    @if (Session::get('success'))
        <div class="alert alert-success">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <strong>Process Success.</strong> {{ Session::get('success')}}<br><a href="/teacher/list">View List</a><br>

        </div>
    @endif
<div class="row">
  <div class="box col-md-12">
    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-user"></i> @if($class=='') Teacher Timetable @else Student Timetable @endif</h2>

      </div>
      <div class="box-content">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
          <strong>Whoops!</strong> There were some problems with your input.<br><br>
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
  <ul class="nav nav-pills">
    <li class="active noround"><a data-toggle="pill" href="#home">Monday</a></li>
    <li class="noround"><a data-toggle="pill" href="#menu1">Tuesday</a></li>
    <li class="noround"><a data-toggle="pill" href="#menu2">Wednesday</a></li>
    <li class="noround"><a data-toggle="pill" href="#menu3">Thursday</a></li>
     <li class="noround"><a data-toggle="pill" href="#menu4">Friday</a></li>
      <li class="noround"><a data-toggle="pill" href="#menu5">Sturday</a></li>
       <li class="noround"><a data-toggle="pill" href="#menu6">Sunday</a></li>
  </ul>
<br>
<br>
<br>
  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
   
      <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='monday')
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>{{$teacher->classname }}</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
    <div id="menu1" class="tab-pane fade">
   
       <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='tuesday')
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>{{$teacher->classname }}</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
    <div id="menu2" class="tab-pane fade">
      
       <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='wednesday')
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>{{$teacher->classname }}</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
    <div id="menu3" class="tab-pane fade">
     
       <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='thursday')
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>{{$teacher->classname }}</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
    <div id="menu4" class="tab-pane fade">
      
     <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='friday')
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>{{$teacher->classname }}</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
       <div id="menu5" class="tab-pane fade">
     
       <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='saturday')
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>{{$teacher->classname }}</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
       <div id="menu6" class="tab-pane fade">
     
       <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='sunday')
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>{{$teacher->classname }}</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
  </div>
@stop
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
<script>
 $('#timepicker1').timepicker();
  $('#timepicker2').timepicker();
</script>
@stop