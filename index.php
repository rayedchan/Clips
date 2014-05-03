<?php
// This is the test driver 

// Set this runtime configuration so PHP can handle
// Unix, MS-Dos or Macintosh line-ending conventions
// The clips.csv that was provided to me contained ^M 
// which probably is Windows or Mac representation of newline character  
ini_set("auto_detect_line_endings", true);

try
{
    // Include other class files
    include_once 'utilities/FileUtility.php';
    include_once 'objects/ClipFilterIterator.php';
    include_once 'objects/FilterRule.php';
    include_once 'exceptions/FileNotFoundException.php';
    include_once 'exceptions/EntityNotAFileException.php';
    include_once 'exceptions/FileNotReadableException.php';
    include_once 'exceptions/FileNotWritableException.php';
    include_once 'exceptions/FilePermissionDeniedException.php';

    // Check the number of arguments provided in command line in correct
    if($argc != 3)
    {
        throw new Exception("Usage: php index.php [Path of input csv file] [Directory path to output csv files]");
    }
    
    // Absolute path to csv file as input 
    $path_to_input_csv_file = $argv[1];

    // Directory to output valid.csv and invalid.csv files
    $output_directory= $argv[2];
    
    // Check if the output destination path is a directory
    if(!is_dir($output_directory))
    {
        throw new Exception("$output_directory is not a directory.");
    }
    
    // Determine if files can be created in the folder
    // Read and write permissions are needed to output files into a directory
    if(!is_readable($output_directory) || !is_writable($output_directory))
    {
        throw new Exception("Permission denied for directory $output_director.");
    }
    
    // Ouput destination path of valid.csv
    $destination_path_valid_csv = $output_directory . "/valid.csv";

    // Ouput destination path of invalid.csv
    $destination_path_invalid_csv = $output_directory . "/invalid.csv";

    // Comma is used as the separator in csv file
    $delimiter = ','; 

    // Retrieve all clips from method call that parses csv file into an array of associaitve arrays (one associaitve array = clip)
    $clips_array = FileUtility::parseCSVFileToArray($path_to_input_csv_file, $delimiter);

    // Create dynamic rules for clips; Clips that meet any one of these filter rules are filtered out
    // These filter rules are basically the negation of the given rules of a valid clip 
    // Save comparsion operations by this way rather than checking a clip that meets all rules 
    $filter_rule1 = new FilterRule("privacy", "!==" , "anybody"); // Check if clip is not public
    $filter_rule2 = new FilterRule("total_likes","<=", 10);  // Check if clip has less than or equal 10 likes
    $filter_rule3 = new FilterRule("total_plays","<=", 200); // Check if clip has less than or equal 200 plays
    $filter_rule4 = new FilterRule("title",">=", 30); // Title length is derived in ClipFilter class; Check if clip's title is greater than or equal to 30 characters

    // Create an array of FilterRule objects
    $filter_rules_array = array($filter_rule1, $filter_rule2, $filter_rule3, $filter_rule4); 

    // Creates a ClipFilter object which is a custom extension of SPL FilterIterator class
    $clip_iterator = new ClipFilterIterator(new ArrayIterator($clips_array), $filter_rules_array);

    // Output results to files
    FileUtility::produceOutputFiles($destination_path_valid_csv, $destination_path_invalid_csv, $clip_iterator);
}

// Catch specific exceptions related to file handling       
catch(FileNotWritableException $e)
{
    echo "$e" . PHP_EOL;
}

catch(FileNotReadableException $e)
{
    echo "$e" . PHP_EOL;
}

catch(EntityNotAFileException $e)
{
    echo "$e" . PHP_EOL;
}

catch(FileNotFoundException $e)
{
    echo "$e" . PHP_EOL;;
}

// Catch all other exceptions 
catch(Exception $e)
{
    echo "$e" . PHP_EOL;
}

?>