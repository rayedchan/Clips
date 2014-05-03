<?php

/**
 * This class represents a filter rule
 * @author rayedchan
 */
class FilterRule
{
    private $constraint_value;
    private $comparison_operator;
    private $clip_attribute_name; 
       
    /**
     * Constructor
     * @param   string      $clip_attribute_name    Name of a clip attribute defined in csv file
     * @param   string      $comparison_operator    A comparsion operator E.g. ">="
     * @param   string|int  $constraint_value       Constraint value
     * Should have validation on comparsion operator
     */
    public function __construct($clip_attribute_name, $comparison_operator, $constraint_value)
    {
        $this->clip_attribute_name = $clip_attribute_name;
        $this->constraint_value = $constraint_value;
        $this->comparison_operator = $comparison_operator;
    }
    
    /**
     * Determines if the given value passes a constraint
     * @param  int|string   
     * @return bool true if value meets constraint; false otherwise
     * @throw   Exception if comparsion operatior is not supported
     */
    public function doesValuePassConstraint($value)
    {
        switch ($this->comparison_operator) 
        {
            case "==":  return $value == $this->constraint_value;
            case "!=":  return $value != $this->constraint_value;
            case ">=":  return $value >= $this->constraint_value;
            case "<=":  return $value <= $this->constraint_value;
            case ">":   return $value >  $this->constraint_value;
            case "<":   return $value <  $this->constraint_value;
            case "===": return $value ===  $this->constraint_value;
            case "!==": return $value !==  $this->constraint_value;
        }
        
        throw new Exception($this->comparison_operator . " operator not supported."); 
    }
    
    // Getter Methods
    public function getConstraintValue()
    {
        return $this->constraint_value;
    }
    
    public function getComparsionOperator()
    {
        return $this->comparison_operator;
    }
     
    public function getClipAttributeName()
    {
        return $this->clip_attribute_name;
    }
    
    // Setter Methods    
    public function setConstraintValue($constraint_value)
    {
        $this->constraint_value = $constraint_value;
    }
    
    public function setComparsionOperator($comparison_operator)
    {
        $this->comparison_operator = $comparison_operator;
    }
       
    public function setClipAttributeName()
    {
        $this->clip_attribute_name = clip_attribute_name;
    }
}

?>

