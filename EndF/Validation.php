<?php
namespace EndF;


class Validation
{
    private $rules = array();
    private $errors = array();

    public function setRule($rule, $value, $params = null, $name = null)
    {
        $this->rules[] = array('val' => $value, 'rule' => $rule, 'par' => $params, 'name' => $name);
        return $this;
    }

    public static function matches($value1, $value2)
    {
        return $value1 == $value2;
    }

    public static function minLength($value, $length)
    {
        return (mb_strlen($value) >= $length);
    }

    public function validate()
    {
        $this->errors = array();
        if(count($this->rules) > 0){
            foreach($this->rules as $rule){
                if(!$this->$rule['rule']($rule['value'], $rule['par'])){
                    if($rule['name']){
                        $this->errors[] = $rule['name'];
                    } else {
                        $this->errors[] = $rule['rule'];
                    }
                }
            }
        }

        return (bool)!count($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function __call($a, $b)
    {
        throw new \Exception('Invalid validation rule!', 500);
    }

    public static function custom($a, $b)
    {
        if ($a instanceof \Closure) {
            return (boolean)call_user_func($a, $b);
        } else {
            throw new \Exception('Invalid custom validation rule', 500);
        }
    }
}