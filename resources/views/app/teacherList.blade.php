@extends('layouts.master')
@section('style')
    <link href="/css/bootstrap-datepicker.css" rel="stylesheet">

@stop
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>Process Success.</strong><br>{{ Session::get('success')}}<br>
</div>

@endif
@if (Session::get('error'))
    <div class="alert alert-warning">
        <button data-dismiss="alert" class="close" type="button">×</button>
        <strong> {{ Session::get('error')}}</strong>

    </div>
@endif
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-book"></i> Teacher List</h2>

            </div>
            <div class="box-content">

                <div class="row">
                    <div class="col-md-12">

                     <!--   <form role="form" action="/student/list" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="class">Class</label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
                                              <?php /*  {!!  Form::select('class',$classes,$formdata->class,['class'=>'form-control','required'=>'true']) !!} */ ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="section">Section</label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <?php  /*$data=[
                                                        'A'=>'A',
                                                        'B'=>'B',
                                                        'C'=>'C',
                                                        'D'=>'D',
                                                        'E'=>'E',
                                                        'F'=>'F',
                                                        'G'=>'G',
                                                        'H'=>'H',
                                                        'I'=>'I',
                                                        'J'=>'J'
                                                ]; */?>
                                               <?php /* {!! Form::select('section',$data,$formdata->section,['class'=>'form-control','required'=>'true']) !!}  */?>


                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="shift">Shift</label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <?php /* $data=[
                                                        'Day'=>'Day',
                                                        'Morning'=>'Morning'
                                                ]; */?>
                                               <?php /* {!! Form::select('shift',$data,$formdata->shift,['class'=>'form-control','required'=>'true']) !!}
           
*/?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group ">
                                            <label for="session">session</label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                                               <?php /* <input type="text" value="{{date('Y')}}" id="session" required="true" class="form-control datepicker2" name="session" value="{{$formdata->session}}"   data-date-format="yyyy"> */ ?>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-primary pull-right"  type="submit"><i class="glyphicon glyphicon-th"></i>Get List</button>

                                </div>
                            </div>
                            <br>
                        </form>-->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
              <table id="studentList" class="table table-striped table-bordered table-hover">
                                                         <thead>
                                                             <tr>
                                                              <th>Name</th>
                                                                 <th>Gender</th>
                                                                  <th>Religion</th>
                                                                   <th>Phone</th>
                                                                   <th>Guardian's Contact</th>
                                                                 <th>Present Address</th>
                                                                  <th>Action</th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                           @foreach($teachers as $teacher)
                                                             <tr>
                                                                 
                                                               <td>{{$teacher->firstName}}  {{$teacher->lastName}}</td>
                                                               <td>{{$teacher->gender}}</td>
                                                                  <td>{{$teacher->religion}}</td>
                                                                   <td>{{$teacher->phone}}</td>
                                                                  <td>{{$teacher->fatherCellNo }}</td>
                                                                  <td>{{$teacher->presentAddress}}</td>
                                                       <td>
                                                  <a title='View' class='btn btn-success' href='{{url("/teacher/view")}}/{{$teacher->id}}'> <i class="glyphicon glyphicon-zoom-in icon-white"></i></a>&nbsp&nbsp<a title='Edit' class='btn btn-info' href='{{url("/teacher/edit")}}/{{$teacher->id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>&nbsp&nbsp<a title='Delete' class='btn btn-danger' href='{{url("/teacher/delete")}}/{{$teacher->id}}' onclick="return confirm('Are you sure you want to delete this Teacher?');"> <i class="glyphicon glyphicon-trash icon-white"></i></a>&nbsp&nbsp<a title='view timetable' class='btn btn-success' href='{{url("/teacher/view-timetable")}}/{{$teacher->id}}'> <i class="glyphicon glyphicon-eye-open icon-white"></i></a>
                                                  &nbsp&nbsp <a title='Mobile Access' class='btn btn-success' href='{{url("/teacher/access")}}/{{$teacher->id}}'> <i class="glyphicon glyphicon-phone"></i></a>

                                                               </td>
                                                           @endforeach
                                                           </tbody>
                                </table>
                        </div>
                    </div>
                                <br><br>


        </div>
    </div>
</div>
</div>
@stop
@section('script')
    <script src="/js/bootstrap-datepicker.js"></script>
<script type="text/javascript">
    $( document ).ready(function() {
        $('#studentList').dataTable();
        $(".datepicker2").datepicker( {
            format: " yyyy", // Notice the Extra space at the beginning
            viewMode: "years",
            minViewMode: "years",
            autoclose:true

        });
    });
</script>
@stop
