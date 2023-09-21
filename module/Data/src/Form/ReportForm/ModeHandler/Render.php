<?php
namespace Data\Form\ReportForm\ModeHandler;

use Data\Form\ReportForm\ModeHandlerInterface;
use Data\Form\ReportForm\FieldContainer;
use Data\Form\ReportForm\FormModifier;
use Data\Form\ReportForm\FieldInterface;

use Base\Service\FormFieldAttributeService;

/**
 * A mode handler to render the form to the screen.
 *
 * @package Form
 * @subpackage ModeHandler
 */
class Render implements ModeHandlerInterface
{
	protected $hookTypes = [
		'validateForce' => 'blur',
		'validateForceImmediate' => 'keypress',
		'validateSoft' => 'blur',
		'valueFormat' => 'keyup',
		'valueList' => 'focus blur',
		'autoFill' => 'blur',
		'autoTab' => 'keyup',
		'customFunction' => 'blur', /** Kev batch4 1 **/
	];

	/**
	 * This is the beginning tabIndex for form input elements.
	 *
	 * The 10 is there for shift-tabbing to layout elements.
	 *
	 * @todo It's a magic number... so sue me.
	 * @var integer
	 */
	protected $tabIndex = 10;

	public function __construct(FieldContainer $fieldContainer, FormModifier $formModifier)
	{
		$this->fieldContainer = $fieldContainer;
		$this->formModifier = $formModifier;
	}

	public function addField(FieldInterface $field)
	{
		$fieldName = $this->renderField($field);
		return ["tabIndex" => $this->tabIndex - 1, "fieldName" => $fieldName];
	}

	public function preFormProcess() { }
	public function postFormProcess() { }

	public function getAdditionalScript()
	{
		return $this->renderClientHooks();
	}

	protected function renderField(FieldInterface $field)
	{
		$name = $this->fieldContainer->getFieldName($field);
		$id = $this->fieldContainer->getFieldId($field);
		$fieldType = $field->getInputType();

		switch ($fieldType) {
			case 'checkboxes':
				$fieldType = 'checkbox';
				$name .= '[]';
				break;
		}

		$attributes = [];
		if ($fieldType != 'textarea') {
			$tag = 'input';
			$attributes['type'] = $fieldType;
		} else {
			$tag = 'textarea';
		}
		$attributes['name'] = $name;
		$attributes['id'] = $id;
		$attributes['data-clickaction'] = 'input-action';

		$tabIndex = true;
		$fieldValue = $fieldDefaultValue = null;
		$isDefaultSelection = false;

		$modGroups = [$field->getOptions()];
		$modGroups[] = $this->formModifier->getFieldAttributes($name, $field->getId());
		
		// If this is a duplicate page and the field is not grouping on repeats then disable it.
		if ($this->fieldContainer->getMode() == 'duplicate' && stripos($id, '-t') === false) {
			$modGroups[] = [
				'isAvailable' => false,
			];
			unset(
				$attributes['name'],
				$attributes['id']
			);
		}

		foreach ($modGroups as $fieldModifiers) {
			if (empty($fieldModifiers)) {
				continue;
			}

			if (isset($fieldModifiers['value'])) {
				$fieldValue = $fieldModifiers['value'];
			}
			if (isset($fieldModifiers['defaultValue'])) {
				$fieldDefaultValue = $fieldModifiers['defaultValue'];
			}
			if (!empty($fieldModifiers['isDefaultSelection'])) {
				$isDefaultSelection = true;
			} elseif (isset($fieldModifiers['isDefaultSelection'])) {
				$isDefaultSelection = false;
			}

			if (isset($fieldModifiers[FormFieldAttributeService::ATTRIBUTE_AVAILABLE]) 
					&& !$fieldModifiers[FormFieldAttributeService::ATTRIBUTE_AVAILABLE]) {
				$attributes['disabled'] = 'disabled';
				$attributes['class'] = '';
				$tabIndex = false;
			} else {
				if (isset($fieldModifiers[FormFieldAttributeService::ATTRIBUTE_REQUIRED])
						&& $fieldModifiers[FormFieldAttributeService::ATTRIBUTE_REQUIRED]) {
					$attributes['class'] = 'required';
				} elseif (isset($fieldModifiers[FormFieldAttributeService::ATTRIBUTE_SKIPPED])
						&& $fieldModifiers[FormFieldAttributeService::ATTRIBUTE_SKIPPED]) {
					$attributes['class'] = 'skipped';
					$tabIndex = false;
				} elseif (isset($fieldModifiers[FormFieldAttributeService::ATTRIBUTE_HIGHLIGHTED])
						&& $fieldModifiers[FormFieldAttributeService::ATTRIBUTE_HIGHLIGHTED]) {
					$attributes['class'] = 'highlighted';
				}
			}
		}

		if ($tabIndex) {
			$tabIndex = $this->tabIndex ++;
		} else {
			$tabIndex = -999;
		}
		$attributes['tabIndex'] = $tabIndex;

		switch ($fieldType) {
			case 'textarea':
				// Do nothing
				break;

			case 'checkbox':
			case 'radio':
				$attributes['value'] = $fieldDefaultValue;

				if ($fieldDefaultValue === $fieldValue || ($fieldValue === null && $isDefaultSelection)) {
					$attributes['checked'] = 'checked';
				}

				if (!empty($attributes['id'])) {
					$attributes['id'] .= '-v' . $attributes['value'];
				}
				break;

			default:
				$attributes['value'] = (isset($fieldValue) && $fieldValue != '') ? $fieldValue : $fieldDefaultValue;
		}

		if (!empty($attributes['value'])
			&& (preg_match('#^&\{(.+)\}$#', $attributes['value'], $instanceGroupMatch)
			   || (preg_match('#^&\{(.+)\}$#', $fieldDefaultValue, $instanceGroupMatch))
		)) {
			/**
			 * even if $fieldValue is not empty (and $fieldDefaultValue contains instance group)
			 * we should run function getExternalGroupId()
			 * in order to put the instanceGroup into array $this->_fieldExternalGroupIds so
			 * next time when $fieldValue will be empty we will have proper $externalGroupId
			 */
			$externalGroupId = $this->fieldContainer->getExternalGroupId($instanceGroupMatch[1]);
			if (empty($fieldValue)) {
				$attributes['value'] = $externalGroupId;
			}
		}
		$this->echoElement($tag, $attributes, $fieldValue);
		return $attributes['name'];
	}

	protected function echoElement($tag, $attributes, $fieldValue)
	{
		echo '<', $tag;
		foreach ($attributes as $name => $value) {
			echo ' ', $name, '="', htmlspecialchars($value), '"';
		}
		if ($tag == 'textarea') {
			echo '>', $fieldValue, '</', $tag, '>';
		} else {
			echo ' />';
		}
	}

	/**
	 *
	 * @return string
	 * @todo Write all event types to field definition at once field[xyz] = {validateForce: ..., ...};
	 */
	protected function renderClientHooks()
	{
		$output = '';
		$events = '';
		$fieldHooks = '';
		foreach ($this->fieldContainer->getFields() as $field) {
			$clientHooks = $field->getFunctionalityHooks();
			if (empty($clientHooks)) {
				continue;
			}

			$name = $this->fieldContainer->getFieldName($field);
			$id = $this->fieldContainer->getFieldId($field);
			$fieldObjectInitialized = false;

			foreach ($this->hookTypes as $hookType => $eventType) {
				if (empty($clientHooks[$hookType])) {
					continue;
				}

				if (!$fieldObjectInitialized) {
					$fieldHooks .= "fields['{$name}'] = {};\n";
					$fieldObjectInitialized = true;
				}

				$hooks = $clientHooks[$hookType];
				$fieldHookFunctions = [];
				switch ($hookType) {
					case 'autoFill':
						$value = "['" . implode("', '", $hooks) . "']";

						if (!empty($clientHooks['valueList'])) {
							$value .= ", eCrash.defineValueList('" . implode("', '", $clientHooks['valueList']) . "')";
							if (count($clientHooks['valueList']) > 1) {
								// make sure that field also updates this one.
								$events .= "$('#{$clientHooks['valueList'][1]}').bind('{$eventType}', eCrash.fakeEvent('{$name}', 'blur'));\n";
							}
						}

						$fieldHookFunctions[] = "eCrash.{$hookType}({$value})";
						break;

					case 'valueList':
						$value = "eCrash.defineValueList('" . implode("', '", $hooks) . "')";

						$fieldHookFunctions[] = "eCrash.{$hookType}({$value})";
						break;

					default:
						foreach ($hooks as $hook) {
							$value = 'eCrash.' . $hook;

							$fieldHookFunctions[] = "eCrash.{$hookType}({$value})";
						}
						break;
				}
				if (!empty($fieldHookFunctions)) {
					$fieldHooks .= "fields['{$name}'].{$hookType} = [" . implode(', ', $fieldHookFunctions) . "];\n";
				}
			}
		}

		$globalFunction = '';
		if ($this->formModifier->hasGlobalFunctions()) {
			$globalFunction .= 'gFunc.push(eCrash.' . implode(', eCrash.', $this->formModifier->getGlobalFunctions()) . ')' . "\n";
		}

		$rawScript = '';
		if ($this->formModifier->hasRawScripts()) {
			$rawScript = implode("\n", $this->formModifier->getRawScripts()) . "\n";
		}

		if (!empty($fieldHooks) || !empty($events)) {
			$output .= '$(document).ready(function() { ' . "\n"
				. 'var eCrash = window.eCrash;' . "\n"
				. 'var fields = eCrash.fields;' . "\n"
				. 'var gFunc = eCrash.globalFunction;' . "\n"
				. $fieldHooks
				. $events
				. $globalFunction
				. $rawScript
				. ' });';
		}

		return $output;
	}
}
