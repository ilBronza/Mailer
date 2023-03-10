<?php

namespace IlBronza\Mailer\Http\Controllers\Senders;

use App\Http\Controllers\Controller;
use App\Mail\StandardEmail;
use IlBronza\FormField\FormField;
use IlBronza\Form\Form;
use IlBronza\Mailer\Mailer;
use IlBronza\Ukn\Ukn;
use Illuminate\Http\Request;

abstract class SendCustomEmailController extends Controller
{
	public $allowExtraEmails = true;

	/**
	 * return the email sender form action
	 *
	 * @return string
	 **/
	abstract function getAction() : string;

	/**
	 * return the email title
	 *
	 * @return string
	 **/
	abstract function getTitle() : string;

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


	abstract function getMailer() : \Illuminate\Mail\Mailer;

	abstract function getReturnUrl() : string;

	/**
	 * return from parameters in the form of array formed by:
	 * [
	 *		'name' => 'Mario Abitudinario',
	 *		'address' => 'mario@abitudinario.it'
	 * ]
	 *
	 **/
	abstract function getFromParameters() : array;
	abstract function getCC() : array;
	abstract function getEmailClass() : string;



	public function allowExtraEmails() : bool
	{
		return !! $this->allowExtraEmails;
	}




	public function renderForm()
	{
        $this->form = Form::createFromArray([
            'action' => $this->getAction(),
            'method' => 'POST'
        ]);

        $this->form->setTitle(
        	$this->getTitle()
        );

        $this->form->card = true;


		$this->addressesFieldset = $this->form->addFormFieldset('email_addresses');
		$this->addressesFieldset->setWidth(2);

		$this->bodyFieldset = $this->form->addFormFieldset('message');
		$this->bodyFieldset->setWidth(2);


        $this->bodyFieldset->addFormField(
                FormField::createFromArray([
                    'name' => 'subject',
                    'type' => 'text',
                    'value' => $this->getSubject()
                ])
            );

        $this->bodyFieldset->addFormField(
                FormField::createFromArray([
                    'name' => 'body',
                    'type' => 'texteditor',
                    'editor' => true,
                    'value' => $this->getBody()
                ])
            );

        $this->addressesFieldset->addFormField(
                FormField::createFromArray([
                    'name' => 'emails',
                    'type' => 'checkbox',
                    'list' => $this->getEmailsArray(),
                    'value' => $this->getEmailsArray(),
                    'multiple' => true,
                    'mustTranslateLabel' => false
                ]));

        if($this->allowExtraEmails())
        	$this->addressesFieldset->addFormField(
                FormField::createFromArray([
                    'name' => 'extraemails',
                    'label' => 'Extra Emails',
                    'type' => 'json',
                    'fields' => [
                        'email' => ['text' => 'string|nullable']
                    ]
                ])
            );

        $this->form->setSubmitButtonText('ASDASD QWEASD');


        // $this->form->addExtraView('innerBottom', 'emails._attachments', ['match' => $match]);

        return view('form::uikit.form', [
        	'form' => $this->form
        ]);
	}


	public function mergeRequestEmails(Request $request)
	{
		$extraEmails = [];

		foreach($request->extraemails ?? [] as $extraemail)
			$extraEmails[] = $extraemail['email'];

		return collect(array_merge(
					$request->emails,
					$extraEmails
				));
	}

	public function getEmailsByRequest(Request $request)
	{
        if(! $this->allowExtraEmails())
        	return $request->emails;

        return $this->mergeRequestEmails($request);
	}

	public function validateEmails(Request $request)
	{
		$validEmails = $this->getEmailsArray();

		$request->validate([
			'emails' => 'array|nullable|in:' . implode(",", $validEmails),
			'subject' => 'string|required|max:255',
			'extraemails' => 'array|nullable',
			'extraemails.*.email' => 'email|nullable',
			'body' => 'string|required|max:10240'
		]);

		$this->emails = $this->getEmailsByRequest($request);

		if(count($this->emails) == 0)
		{
			Ukn::e('You must set at least one email');

			return back();
		}
	}

	public function getBodyFromRequest(Request $request) : string
	{
		return $request->body;
	}

	public function getSubjectFromRequest(Request $request) : string
	{
		return $request->subject;
	}

	public function performSending(Request $request)
	{
		$this->validateEmails($request);

        $this->mailer = $this->getMailer();

        $this->emailClass = $this->getEmailClass();

		try
		{
			foreach($this->emails as $email)
			{
				try
				{
					$mail = $this->mailer->to($email);

					foreach($this->getCC() as $cc)
						$mail->cc($cc);

					$mail->send(
						(new $this->emailClass(
							$this->getBodyFromRequest($request),
							$this->getSubjectFromRequest($request),
							$this->getFromParameters()
						))
					);

					Ukn::s(__('mailer::mailer.emailSentTo', ['emailAddress' => $email]));
				}
				catch(\Exception $e)
				{
					Ukn::e(__('mailer::mailer.emailFailedTo', ['emailAddress' => $email]));
				}
			}
		}
		catch(\Exception $e)
		{
			Ukn::e($e->getMessage());
		}

		return redirect()->to(
			$this->getReturnUrl()
		);		
	}
}

