<?php
namespace Data\Form\ReportForm;

class FormModifier
{
    protected $globalFunctionsReturned = [];
    protected $globalFunctions = [];
    protected $rawScriptsReturned = [];
    protected $rawScripts = [];
    protected $pages = [];
    protected $fieldModifiers = [];
    protected $globalFieldModifiers = [];

    public function addGlobalFunction($globalFunction)
    {
        $this->globalFunctions[] = $globalFunction;
    }
    
    public function addGlobalFieldModifier($attribute, $value)
    {
        $this->globalFieldModifiers[$attribute] = $value;
    }

    public function addRawScript($rawScript)
    {
        $this->rawScripts[] = $rawScript;
    }

    public function setPages(Array $pages)
    {
        ksort($pages);
        $this->pages = $pages;
    }

    public function setFieldAttribute($field, $attribute, $value)
    {
        $this->fieldModifiers[$field][$attribute] = $value;
    }

    public function hasGlobalFunctions()
    {
        return !empty($this->globalFunctions);
    }

    public function getGlobalFunctions()
    {
        $this->globalFunctionsReturned = array_merge(
            $this->globalFunctionsReturned,
            $this->globalFunctions
        );

        $globalFunctions = $this->globalFunctions;
        $this->globalFunctions = [];

        return $globalFunctions;
    }

    public function hasRawScripts()
    {
        return !empty($this->rawScripts);
    }

    public function getRawScripts()
    {
        $this->rawScriptsReturned = array_merge(
            $this->rawScriptsReturned,
            $this->rawScripts
        );

        $rawScripts = $this->rawScripts;
        $this->rawScripts = [];

        return $rawScripts;
    }

    public function getPages()
    {
        return $this->pages;
    }

    public function getFieldAttributes($fieldInstanceId, $fieldId)
    {
        $modifiers = [];
        
        if (isset($this->fieldModifiers[$fieldId])) {
            $modifiers = $this->fieldModifiers[$fieldId];
        }
        
        if (isset($this->fieldModifiers[$fieldInstanceId])) {
            $modifiers = array_merge(
                $modifiers,
                $this->fieldModifiers[$fieldInstanceId]
            );
        }
        
        return array_merge($modifiers, $this->globalFieldModifiers);
    }
}
