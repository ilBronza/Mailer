<?php

namespace IlBronza\Mailer\Http\Controllers\Senders;

use App\Mail\StandardEmail;
use IlBronza\Mailer\Mailer;

abstract class SendStandardEmailController extends SendCustomEmailController
{
	public $cc = [];
	public $mailerId;
	public $translatableTitle = 'mailer::mailer.emailSending';

	public function getEmailClass() : string
	{
		return StandardEmail::class;
	}

	public function getCC() : array
	{
		return $this->cc;
	}

	public function getTranslatableTitle() : string
	{
		return $this->translatableTitle;
	}

	public function getTitle() : string
	{
		return __(
			$this->getTranslatabletitle()
		);
	}

	public function getMailerId()
	{
		if(is_null($this->mailerId))
			return Auth::id();

		return $this->mailerId;
	}

	public function getMailer() : \Illuminate\Mail\Mailer
	{
		return Mailer::getMailerByUserId(
			$this->getMailerId()
		);
	}

	public function getFromParameters() : array
	{
		return Mailer::getFromParametersByUserId(
			$this->getMailerId()
		);
	}

	/**
	 * return the email sender form action
	 *
	 * @return string
	 **/
	abstract function getAction() : string;

	/**
	 * return the email subject
	 *
	 * @return string
	 **/
	abstract function getSubject() : string;

	/**
	 * return the email body
	 *
	 * @return string
	 **/
	abstract function getBody() : string;

	/**
	 * return the email destinataries array
	 *
	 * @return string
	 **/
	abstract function getEmailsArray() : array;


	abstract function getReturnUrl() : string;


}

