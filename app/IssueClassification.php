<?php

namespace Ermtool;

use Illuminate\Database\Eloquent\Model;

class IssueClassification extends Model
{
    protected $table = 'issue_classifications';
    protected $fillable = ['name','name_en'];
}
