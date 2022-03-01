<?php

namespace IlBronza\Mailer;

use IlBronza\Mailer\MailerFieldset;
use IlBronza\Mailer\Traits\MailerButtonsTrait;
use Illuminate\Database\Eloquent\Model;
use \IlBronza\MailerField\MailerField;

class Mailer
{
	use MailerButtonsTrait;

	public $method = 'POST';
	public $action;
	public $model;

	public $title;
	public $intro;

	public $cancelButton = true;
	public $cancelHref;

	public $card = false;
	public $cardClasses = [];

	public $fieldsets = [];
	public $fields;

	public $htmlClasses = [];

	public $mustShowLabel;
	public $mustShowPlaceholder = true;

	public $displayAsSwitcher = false;
	public $orientation = 'uk-mailer-horizontal';
	public $translateLegend = true;

	public $submitButtonText;

	public $collapse = true;
	public $divider = true;

	public $allDatabaseFields = [];


	public $closureButtons;

	public function __construct()
	{
		$this->fields = collect();

		$this->closureButtons = collect();
	}

	public function setTranslateLegend(bool $value)
	{
		$this->translateLegend = $value;
	}

	public function translateLegend()
	{
		return $this->translateLegend;
	}

	public function setTitle(string $title)
	{
		$this->title = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setIntro(string $intro)
	{
		$this->intro = $intro;
	}

	public function getIntro()
	{
		return $this->intro;
	}

	public function addCardClasses(array $classes = [])
	{
		$this->cardClasses = array_merge(
			$this->cardClasses,
			$classes
		);
	}

	public function getCardClasses()
	{
		return $this->cardClasses;
	}

	public function hasCard(bool $value = null)
	{
		if(is_null($value))
			return $this->card;

		$this->card = $value;
	}

	public function assignModel(Model $model)
	{
		$this->model = $model;
	}

	public function addMailerField(MailerField $mailerField)
	{
		$this->fields->push($mailerField);

		$mailerField->setMailer($this);

		return $this;
	}

	public function addMailerFieldToFieldset(MailerField $mailerField, string $fieldset)
	{
		if(! $this->fieldsets[$fieldset])
			$this->addMailerFieldset($fieldset);

		$this->fieldsets[$fieldset]->addMailerField($mailerField);

		$mailerField->setMailer($this);
	}

	public function setDivider(bool $value)
	{
		return $this->divider = $value;
	}

	public function hasDivider()
	{
		return $this->divider;
	}

	public function addMailerFieldset(string $name, array $parameters = [])
	{
		$fieldset = new MailerFieldset($name, $this, $parameters);

		$this->fieldsets[$name] = $fieldset;
		$fieldset->setDivider($this->hasDivider());

		return $fieldset;
	}

	public function hasFieldsets()
	{
		return !! count($this->fieldsets);
	}

	public function flattenFieldsets()
	{
		foreach($this->fieldsets as $fieldset)
			foreach($fieldset->fields as $field)
				$this->fields->push($field);

		$this->fieldsets = [];
	}

	public function getMethodAttribute()
	{
		if(! in_array($method = $this->getMethod(), ['GET', 'POST']))
			return 'POST';

		return $method;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function setAction(string $action)
	{
		$this->action = $action;
	}

	public function getAction()
	{
		return $this->action;
	}

	public function getCancelHref()
	{
		if($this->cancelHref)
			return $this->cancelHref;

		return url()->previous();
	}

	public function getBackToListUrl()
	{
		return $this->backToListUrl ?? false;
	}

	public function setBackToListUrl(string $url)
	{
		$this->backToListUrl = $url;
	}

	public function getMailerOrientationClass()
	{
		return $this->orientation;
	}

	public function setVerticalMailer()
	{
		$this->orientation = 'uk-mailer-stacked';
	}

	public function setStackedMailer()
	{
		$this->orientation = 'uk-mailer-stacked';
	}

	public function mustShowLabel(bool $mustShowLabel = null)
	{
		if(is_null($mustShowLabel))
			return $this->mustShowLabel;

		$this->mustShowLabel = $mustShowLabel;
	}

	public function mustShowPlaceholder(bool $mustShowPlaceholder = null)
	{
		if(is_null($mustShowPlaceholder))
			return $this->mustShowPlaceholder;

		$this->mustShowPlaceholder = $mustShowPlaceholder;
	}

	static function createFromArray(array $parameters)
	{
		$field = new static();

		foreach($parameters as $name => $value)
			$field->$name = $value;

		return $field;
	}

	public function getFieldByName(string $name)
	{
		foreach($this->fields as $field)
			if($field->name == $name)
				return $field;

		foreach($this->fieldsets as $fieldset)
			foreach($fieldset->fields as $field)
				if($field->name == $name)
					return $field;

		return false;
	}

	public function displayAsSwitcher(bool $status = true)
	{
		$this->displayAsSwitcher = $status;
	}

	public function mustDisplayAsSwitcher()
	{
		return $this->displayAsSwitcher;
	}

	public function render()
	{
		return view("mailer::uikit.mailer", ['mailer' => $this]);
	}

	public function hasCollapse()
	{
		return $this->collapse;
	}

	public function setAllDatabaseFields(array $allDatabaseFields)
	{
		$this->allDatabaseFields = $allDatabaseFields;
	}

	public function getDatabaseField(string $name)
	{
		return $this->allDatabaseFields[$name] ?? null;
	}
}