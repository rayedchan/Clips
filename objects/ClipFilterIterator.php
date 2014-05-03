<?php

include_once 'objects/FilterRule.php';

/**
 * This class extends the SPL FilterIterator. It is used to
 * filter a specific category of clips based on given filter rules.
 * @author rayedchan
 */
class ClipFilterIterator extends FilterIterator 
{
    // Field to store all the unwanted values
    private $unwanted_values = array();
    
    // Array<FilterRule> object for filtering unwanted values
    private $filter_rules;
    
    /**
     * Constructor
     * @param   iterator            $iterator       A pointer to move to the next element in a collection
     * @param   Array<FilterRule>   $filter_rules   An array of FilterRule object in order to have dynamic rules
     */
    public function __construct(Iterator $iterator, $filter_rules)
    {
        // call parent class constructor
        parent::__construct($iterator);
        
        // Set filter rules
        $this->filter_rules = $filter_rules; 
    }
    
    /**
     * Get all the values that were filtered out
     * @return an array of unwanted values
     */
    public function getUnwantedValues()
    {
        return $this->unwanted_values;
    }
    
    /**
     * Filter out the invalid clips. This method gets call internally
     * whenever the iterator moves to the next valid element
     * @return bool true for valid clip; false otherwise 
     */
    public function accept() 
    {
        $current_clip = $this->current(); // Get the current element of iterator
        
        // Iterate each FilterRule object
        foreach($this->filter_rules as $filter_rule)
        {
            $attribute_name = $filter_rule->getClipAttributeName();
            $value = $current_clip[$attribute_name];
           
            // Title length is a derived field
            if($attribute_name === 'title')
            {
                $value = strlen($value); // Length of title of a clip
            }
            
            // Check if value passes a filter rule; if yes, invalid clip
            if($filter_rule->doesValuePassConstraint($value))
            {
                array_push($this->unwanted_values, $current_clip); // Add unwanted clip to array
                return false;
            }
        }

        // Valid clip; Did not comply to any filter rules 
        return true;
    }
}
?>