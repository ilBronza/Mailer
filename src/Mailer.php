<?php

namespace IlBronza\Mailer;

use Auth;
use IlBronza\Mailer\Models\Usermailer;
use Illuminate\Mail\Mailer as LaravelMailer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class Mailer
{
	static function getMailerParametersByUsermailer(Usermailer $usermailer) : array
	{
		$mailerParameters = [];

		foreach([
			'transport',
			'port',
			'host',
			'username',
			'password',
			'encryption'
		] as $attribute)
			$mailerParameters[$attribute] = $usermailer->$attribute;

		return $mailerParameters;
	}

	static function getMailerByUsermailer(Usermailer $usermailer) : LaravelMailer
	{
		$parameters = static::getMailerParametersByUsermailer($usermailer);

		$mailerKey = 'bronzamailer' . $usermailer->getKey();

		Config::set('mail.mailers.' . $mailerKey, $parameters);

		return Mail::mailer($mailerKey);
	}

	static function getUsermailerByUserId(int $userId)
	{
		return Usermailer::where('user_id', $userId)->first();
	}

	static function getMailerByUserId(int $userId) : ? LaravelMailer
	{
		if(! $usermailer = static::getUsermailerByUserId($userId))
			return null;

		return static::getMailerByUsermailer($usermailer);
	}

	static function getMailerByLoggedUser() : ? LaravelMailer
	{
		if(! $userId = Auth::id())
			return null;

		return static::getMailerByUserId($userId);
	}
}