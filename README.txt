Instructions
Attached is a csv file: clips.csv. 
Your job is to write code that will load in clips.csv and analyze the data against the rules listed below. 
Your code should output the results into two files; valid.csv will contain a list of clip_ids's that passed the tests 
and invalid.csv will contain a list of clip_id's that failed the tests. requirements are to use php, utilize the SPL FilterIterator,
and handle exceptions if a file cannot be read in or written to.
 
Rules:
1.	The clip must be public (privacy == anybody)
2.	The clip must have over 10 likes and over 200 plays
3.	The clip title must be under 30 characters
 
Operating System: Oracle Linux 6
PHP Version: 5.5.11
Apache: 2.4.9

Command line interface
Usage: 
php index.php [Path of input csv file] [Directory path to output csv files]

index.php = The test driver execution script
Path of input csv file = The path to clips.csv
Directory path to output csv files = Create or overwrite the valid.csv and invalid.csv in the specified folder.
