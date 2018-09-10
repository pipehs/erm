<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;
use DB;
use stdClass;
use Auth;
use Carbon;

class IssueClassification extends Model
{
    protected $table = 'issue_classifications';
    protected $fillable = ['name','name_en'];

    public static function getIssueClassificationByName($name)
    {
    	return DB::table('issue_classifications')
    			->where('name','=',$name)
    			->first(['*']);
    }
}
