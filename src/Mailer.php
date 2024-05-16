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

	static function getFromParametersByUsermailer(Usermailer $usermailer) : array
	{
		$parameters = static::getMailerParametersByUsermailer($usermailer);

		return [
			'address' => $parameters['username'],
			'name' => $parameters['username']
		];
	}

	static function getMailerByUsermailer(Usermailer $usermailer) : LaravelMailer
	{
		$parameters = static::getMailerParametersByUsermailer($usermailer);

		$mailerKey = 'bronzamailer' . $usermailer->getKey();

		Config::set('mail.mailers.' . $mailerKey, $parameters);

		$mailer = Mail::mailer($mailerKey);

		$mailer->alwaysFrom($parameters['username']);

		return Mail::mailer($mailerKey);
	}

	static function getUsermailerByUserId(int $userId)
	{
		return Usermailer::where('user_id', $userId)->first();
	}

	static function getFromParametersByUserId(int $userId) : ? array
	{
		if(! $usermailer = static::getUsermailerByUserId($userId))
			abort(403, _('mailers.yourCurrentUserHasNotMailerSettings'));

		return static::getFromParametersByUsermailer($usermailer);

	}

	static function getMailerByUserId(int $userId) : ? LaravelMailer
	{
		if(! $usermailer = static::getUsermailerByUserId($userId))
			abort(403, _('mailers.yourCurrentUserHasNotMailerSettings'));

		return static::getMailerByUsermailer($usermailer);
	}

	static function getMailerEmailByUserId(int $userId) : string
	{
		if(! $usermailer = static::getUsermailerByUserId($userId))
			abort(403, _('mailers.yourCurrentUserHasNotMailerSettings'));

		return $usermailer->username;
	}

	static function getFromParametersByLoggedUser() : ? array
	{
		if(! $userId = Auth::id())
			throw new \Exception('no logged user');

		return static::getFromParametersByUserId($userId);
	}

	static function getMailerByLoggedUser() : ? LaravelMailer
	{
		if(! $userId = Auth::id())
			throw new \Exception('no logged user');

		return static::getMailerByUserId($userId);
	}

	static function getMailerEmailByLoggedUser()
	{
		if(! $userId = Auth::id())
			throw new \Exception('no logged user');

		return static::getMailerEmailByUserId($userId);		
	}
}