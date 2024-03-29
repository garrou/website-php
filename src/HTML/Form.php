<?php

namespace App\HTML;

use DateTimeInterface;

class Form
{

    private $data;

    private array $errors;
    
    /**
     * __construct
     *
     * @param  mixed $data
     * @param  array $errors
     * @return void
     */
    public function __construct($data, array $errors)
    {
        $this->data = $data;
        $this->errors = $errors;
    }
    
    /**
     * Generate input html
     *
     * @param  string $key key label
     * @param  string $label input label
     * @return string
     */
    public function input(string $key, string $label): string
    {
        $value = $this->getValue($key);
        $required = $key === 'link' ? '' : 'required';
        $type = $key === 'password' ? 'password' : 'text';
        
        return <<<HTML
            <div class="form-group">
                <label for="field$key" class="font-weight-bold">$label</label>
                <input type="$type" id="field{$key}" class="{$this->getInputClass($key)} " name="$key" value="$value" {$required} autofocus>
                {$this->getErrorFeedback($key)}
            </div>
        HTML;
    }
    
    /**
     * Generate a textarea
     *
     * @param  string $key textarea name tag
     * @param  string $label textarea label tag
     * @return string a textarea
     */
    public function textarea(string $key, string $label): string
    {
        $value = $this->getValue($key);
        
        return <<<HTML
            <div class="form-group">
                <label for="field$key" class="font-weight-bold">$label</label>
                <textarea type="text" id="field{$key}" class="{$this->getInputClass($key)}" name="$key" rows="10" cols="40" required>$value</textarea>
                {$this->getErrorFeedback($key)}
            </div>
        HTML;
    }
    
    /**
     * getValue
     *
     * @param  string $key
     * @return mixed
     */
    private function getValue(string $key): ?string
    {
        if (is_array($this->data)) {
            return $this->data[$key] ?? null;
        }
        // make string in format getSomething (get + uppercase)
        $method = 'get' . str_replace(' ', '', ucwords(str_replace('_',' ',$key)));
        $value = $this->data->$method();

        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        return $value;
    }
    
    /**
     * getInputClass
     *
     * @param  string $key
     * @return string
     */
    private function getInputClass(string $key): string
    {
        $class = 'form-control';
        if (isset($this->errors[$key])) {
            $class .= ' is-invalid';
        }
        return $class;
    }
    
    /**
     * getErrorFeedback
     *
     * @param  string $key
     * @return string
     */
    private function getErrorFeedback(string $key): string
    {
        if (isset($this->errors[$key])) {
            if (is_array($this->errors[$key])) {
                $error = implode('<br/>', $this->errors[$key]);
            } else {
                $error = $this->errors[$key];
            }
            return '<div class="invalid-feedback">' . $error . '</div>';
        }
        return '';
    }
}
