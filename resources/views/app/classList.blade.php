@extends('layouts.master')
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
  <strong>Process Success.</strong><br>{{ Session::get('success')}}<br>
</div>

@endif
<div class="row">
  <div class="box col-md-12">
    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-home"></i> Class List</h2>
      </div>
      <div class="box-content">
        <table id="classList" class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th style="width:20%">Code</th>
              <th style="width:30%">Name</th>
              <th style="width:30%">Description</th>
              <th style="width:5%">Students</th>
              <th style="width:15%">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($Classes as $class)

            <tr>
              <td>{{$class->code}}</td>
              <td>{{$class->name}}</td>
              <td>{{$class->description}}</td>
              <td>{{$class->students}}</td>

              <td>
                <a title='Edit' class='btn btn-info' href='{{url("/class/edit")}}/{{$class->id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>&nbsp&nbsp<a title='Delete' class='btn btn-danger' href='{{url("/class/delete")}}/{{$class->id}}' onclick="return confirm('Are you sure you want to delete this Class?');"> <i class="glyphicon glyphicon-trash icon-white"></i></a>
              </td>
              @endforeach
            </tbody>
          </table>
          <br><br>


        </div>
      </div>
    </div>
  </div>
  @stop
  @section('script')
  <script type="text/javascript">
  $( document ).ready(function() {
    $('#classList').dataTable();
  });
  </script>
  @stop
