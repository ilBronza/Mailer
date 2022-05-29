<?php

namespace IlBronza\Mailer\Models;

use IlBronza\AccountManager\Models\User;
use IlBronza\CRUD\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Usermailer extends BaseModel
{
	public function user()
	{
		return $this->belongsTo(User::class);
	}
}