<?php

include_once 'exceptions/FileNotFoundException.php';
include_once 'exceptions/EntityNotAFileException.php';
include_once 'exceptions/FileNotReadableException.php';
include_once 'exceptions/FileNotWritableException.php';
include_once 'exceptions/FilePermissionDeniedException.php';
include_once 'objects/ClipFilterIterator.php';

/**
 * Contains methods related to handling files
 * @author rayedchan
 */
class FileUtility
{
    /**
     * Parses a comma separated value (csv) file.
     * This assumes a header is defined as the first row in the csv file.
     * An associative array is built between the header row (Keys) 
     * and the non-header rows (Values). 
     * @param   string  $file_name   The path to file    
     * @param   char    $delimiter   Separator for each field in a given line     
     * @return  An array containing associative array in each slot
     * @throws FileNotFoundException if file does not exist 
     * @throws EntityNotAFileException if entity is not a file
     * @throws FileNotReadableException if file is not readable (no read file permission for user)
     */
    public static function parseCSVFileToArray($file_name, $delimiter)
    {
        $file_resource = null;
        
        try
        {
            // Check existence of file
            if(!file_exists($file_name))
            {
                throw new FileNotFoundException("File '$file_name' does not exist or permission denied for user.");
            }

            // Check if the entity is a file or not; E.g. User could be providing a path to a directory
            if(!is_file($file_name))
            {
                throw new EntityNotAFileException("'$file_name' is not a file.");
            }
            
            // Check if the file is readable
            if(!is_readable($file_name))
            {
               throw new FileNotReadableException("'$file_name' is not readable."); 
            }

            // Open file in read mode
            $file_resource = fopen($file_name, 'r');

            // Store the header row (First row in csv file which specified the column names) into a variable
            $headerRow = null;

            // Variable to store all the clips extracted from csv file
            $clips = array(); // Each entry, which is going to reprsent a single clip, in this array is going to store an associative array

            // Read the csv file line by line until end of file or fgetcsv() returns false
            while($line_as_array = fgetcsv($file_resource, $delimiter)) // Parses line into a number index array
            {
                // First line is the header row
                if($headerRow == null)
                {
                    $headerRow = $line_as_array;
                }

                // Other lines represent a clip record
                else
                {
                    $clipRecord = $line_as_array; 

                    // Create an associative array using the header row as the indexes and the corresponding clip property as values
                    $clip_associative_array = array_combine($headerRow, $clipRecord); // First param are the keys, Second param are the values
                    
                    // Add to clips array
                    array_push($clips, $clip_associative_array);
                }
            }

            return $clips;
        }
        
 
        finally 
        {    
            // Close file
            if($file_resource != null)
            {
                fclose($file_resource);
            }
        } 
    }
    
    /**
     * Create the output files based on the results of the FilterIterator
     * If output files do not exist, they will be created. 
     * If the output files exist, the content will be overwritten. 
     * @param   file resource       $destination_path_valid_csv      Destination path of valid.csv   
     * @param   file resource       $destination_path_invalid_csv    Destination path of invalid.csv
     * @param   ClipFilterIterator  $clip_iterator                   ClipFilterIteratorIterator which extends SPL FilterIterator
     * @throws  FileNotWritableException if file is not writable 
     * @throws  EntityNotAFileException if entity is not a file
     * @throws  FilePermissionDeniedException if user does not have the permission to create a file in a certain location
     */
    public static function produceOutputFiles($destination_path_valid_csv, $destination_path_invalid_csv, $clip_iterator)
    {
        $file_pointer_valid_clips = null;
        $file_pointer_invalid_clips = null;
        
        try
        {
            // Check if entity exists and is a file
            FileUtility::isExisitngFileWritable($destination_path_valid_csv);
            FileUtility::isExisitngFileWritable($destination_path_invalid_csv);

            // File stream for the new output files; Create files if they do not exist
            $file_pointer_valid_clips = fopen($destination_path_valid_csv, 'w');
            $file_pointer_invalid_clips = fopen($destination_path_invalid_csv, 'w');

            // Create the header row of the csv file
            // PHP_EOL = end of line character of your OS platform
            fwrite($file_pointer_valid_clips, "id" . PHP_EOL); 
            fwrite($file_pointer_invalid_clips, "id" . PHP_EOL);

            // Iterate all the valid clips 
            // Internally all entries are being iterated
            foreach($clip_iterator as $valid_clip)
            {
                $clip_id = $valid_clip['id'];
                fwrite($file_pointer_valid_clips, $clip_id . PHP_EOL);
            }

            // Side Note for self: Writing invalid clips to file could 
            // have been implemented inside the accept method of ClipFilterIterator
            // Hence only one iteration is needed for producing the output results.
            // My current implementation has a worst case performance if all clips are invalid. 
            
            // Get all invalid clips after iterator reaches end; method call from ClipFilter
            $invalid_clip = $clip_iterator->getUnwantedValues();

            // Iterate all the invalid clips
            foreach($invalid_clip as $invalid_clip)
            {          
                $clip_id = $invalid_clip['id'];
                fwrite($file_pointer_invalid_clips, $clip_id . PHP_EOL);  
            }
        }
        
        // Close output files
        finally
        {
            if($file_pointer_valid_clips != null)
            {
                fclose($file_pointer_valid_clips);
            }
                       
            if($file_pointer_invalid_clips != null)
            {
                fclose($file_pointer_invalid_clips);
            }
            
        }
    }
    
    /**
     * Determine if entity is a file and is writable
     * @param file resource $file_name  Path of file
     * @throws FileNotWritableException if file is not writable 
     * @throws EntityNotAFileException if entity is not a file
     */
    private static function isExisitngFileWritable($file_name)
    {
        // Check if file exists
        if(file_exists($file_name))
        {
            // Check if the file is not writable
            if(!is_writable($file_name))
            {
                throw new FileNotWritableException("'$file_name' is not writable.");
            }
            
            // Check if the entity is a file or not; E.g. User could be providing a path to a directory
            if(!is_file($file_name))
            {
                throw new EntityNotAFileException("'$file_name' is not a file.");
            }
        }
    }
}
?>

