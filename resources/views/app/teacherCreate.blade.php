@extends('layouts.master')
@section('style')
<link href="{{url('/css/bootstrap-datepicker.css')}}" rel="stylesheet">
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
        <h2><i class="glyphicon glyphicon-user"></i> Teacher Add</h2>

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








        <form role="form" action="{{url('/teacher/create')}}" method="post" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
          <div class="row">
            <div class="col-md-12">
              <h3 class="text-info"> Teacher Detail</h3>
              <hr>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="fname">First Name</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <input type="text" class="form-control" required name="fname" placeholder="First Name">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="lname">Last Name</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <input type="text" class="form-control" required name="lname" placeholder="Last Name">
                  </div>
                </div>
              </div>

            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="gender">Gender</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="gender" class="form-control" required >

                      <option value="Male">Male</option>
                      <option value="Female">Female</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="religion">Religion</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="religion" class="form-control"  >
                      <option value="Islam" selected >Islam</option>
                      <option value="Hindu">Hindu</option>
                      <option value="Cristian">Cristian</option>
                      <option value="Buddhist">Buddhist</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="bloodgroup">Bloodgroup</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="bloodgroup" class="form-control"  >
                    <option value=''>--- Select Bloodgroup---</option>
                      <option value="A+">A+</option>
                      <option value="A-">A-</option>
                      <option value="B+">B+</option>
                      <option value="B-">B-</option>
                      <option value="AB+">AB+</option>
                      <option value="AB-">AB-</option>
                      <option value="O+">O+</option>
                      <option value="O-">O-</option>
                    </select>

                  </select>
                </div>
              </div>
            </div>

          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-4">
              <div class="form-group">
                <label for="nationality">Nationality</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control" value="Pakistani"   name="nationality" placeholder="Nationality">
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group ">
                <label for="dob">Date Of Birth</label>
                <div class="input-group">

                  <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                  <input type="text"   class="form-control datepicker" name="dob" required  data-date-format="dd/mm/yyyy">
                </div>


              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group ">
                <label for="photo">Photo</label>
                <input id="photo" name="photo"  type="file">
              </div>
            </div>

          </div>
        </div>
        <div class="row">
          <div class="col-md-12">


            <div class="col-md-4">
              <div class="form-group">
                <label for="extraActivity">Phone </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control" required  name="phne" placeholder="Enter Phone NO">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="remarks">Email </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control"  name="emails" placeholder="Enter Email">
                </div>
              </div>
            </div>
          </div>
        </div>



        <div class="row">
          <div class="col-md-12">
            <h3 class="text-info"> Guardian's Detail</h3>
            <hr>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-6">
              <div class="form-group">
                <label for="fatherName">Father's Name </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control" required  name="fatherName" placeholder="Name">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="fatherCellNo">Father's Mobile No </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control"  required name="fatherCellNo" placeholder="+8801xxxxxxxxx">
                </div>
              </div>
            </div>
          
          </div>
        </div>
       
        <div class="row">
          <div class="col-md-12">
            <h3 class="text-info"> Address Detail</h3>
            <hr>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-6">
              <div class="form-group">
                <label for="presentAddress">Present Address</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
                  <textarea type="text" class="form-control" required name="presentAddress" placeholder="Address"></textarea>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="parmanentAddress">Permanent Address</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
                  <textarea type="text" class="form-control"  name="parmanentAddress" placeholder="Address"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="clearfix"></div>

        <div class="form-group">
          <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-plus"></i>Add</button>
          <br>
        </div>
      </form>






    </div>
  </div>
</div>
</div>
@stop
@section('script')
<!--<script>
// Add MOre fields script
/*$(document).ready(function(){
    
    $('#add').click(function(){
        
        var inp = $('#box');
        
        var i = $('input').size() + 1;
        
        $('<div id="box' + i +'"><input type="text" id="name" class="name" name="name' + i +'" placeholder="Input '+i+'"/><i class="glyphicon glyphicon-minus add"  id="remove"></i> </div>').appendTo(inp);
        
        i++;
        
    });
    
    
    
    $('body').on('click','#remove',function(){
        
        $(this).parent('div').remove();

        
    });

        
});*/

</script>-->
<script src="{{url('/js/bootstrap-datepicker.js')}}"></script>
<script type="text/javascript">
 /*var getStdRegiRollNo = function(){
   var aclass = $('#class').val();
   var session = $('#session').val().trim();
   var section=$('#section').val().trim();
   $.ajax({
     url: '/student/getRegi/'+aclass+'/'+session+'/'+section,
     data: {
       format: 'json'
     },
     error: function(error) {
       alert(error);
     },
     dataType: 'json',
     success: function(data) {
       $('#regiNo').val(data[0]);
       $('#rollNo').val(data[1]);
     },
     type: 'GET'
   });
 };*/
$( document ).ready(function() {
  //getStdRegiRollNo();
  $('.datepicker').datepicker({autoclose:true});
  $(".datepicker2").datepicker( {
    format: " yyyy", // Notice the Extra space at the beginning
    viewMode: "years",
    minViewMode: "years",
    autoclose:true
  }).on('changeDate', function (ev) {
    getStdRegiRollNo();
  });
  /*$('#class').on('change',function() {
    getStdRegiRollNo();
  });
  $('#section').on('change',function() {
    getStdRegiRollNo();
  });*/
});

</script>
@stop
