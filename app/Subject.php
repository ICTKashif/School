<?php
namespace App;
class Subject extends \Eloquent {
	protected $table = 'Subject';
protected $fillable = ['name','description','class','gradeSystem'];
}
