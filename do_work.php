<?php
// We need all three arguments and when running `php file.php` in command line, the file.php counts as an arg
if($argc < 4)
{
    // Tell them how to use the script
    usage();
}

try
{
    // Load our dirs into an array
    $dirs = load_dirs($argv);

    // Grab our source files arr
    $src_files = get_files_arr($dirs['source']);

    // Grab our target files arr
    $target_files = get_files_arr($dirs['target']);

    // See what sticks between them
    $result_files = array_intersect($target_files, $src_files);

    // Loop the result files for copying
    foreach ($result_files as $file_name)
    {
        // Assemble the existing target file path
        $target_file_path = realpath($dirs['target'] . DIRECTORY_SEPARATOR . $file_name);

        // Assemble the result path (to be)
        $result_file_path = $dirs['result'] . DIRECTORY_SEPARATOR . $file_name;

        try
        {
            // Grab our binary
            $file = file_get_contents($target_file_path);

            // Push our binary, and check for false return
            if (! file_put_contents($result_file_path, $file))
            {
                throw new Exception("File write failed!");
            }
        }
        catch(Exception $e)
        {
            // Swallow any exceptions and hopefully continue working
            // But let the CLI know
            print("!!! We were unable to copy '{$target_file_path}' to '{$result_file_path}'\n");
        }

        // Success, so print to CLI so they can follow status
        print("Successfully copied '{$target_file_path}' to '{$result_file_path}'\n");

    }
}
catch (Exception $e)
{
    print("Something terrible has happened and the task could not be completed. Error: " . $e->getMessage());
    exit(0);
}

print("Job Complete\n");
exit(1);

/**
 * @param $dir
 * @return array
 */
function get_files_arr($dir)
{
    // Get the list of files in a directory as an array
    $files = scandir($dir);

    // Filter out the self and parent links
    $files = array_diff($files, array('.', '..'));

    return $files;
}

/**
 * @param array $argv
 * @return array
 */
function load_dirs($argv = array())
{
    // Load an empty array for the dirs...
    $dirs = array();

    // Loop our arguments provided
    foreach($argv as $arg)
    {
        // explode all the arguments with a '=' separator
        $arg_arr = explode('=', $arg);

        // If we have two array elements, we're in business
        // This makes sure we ignore other arguments
        if (isset($arg_arr[0]) && isset($arg_arr[1]))
        {
            // If this is the result dir path, do special stuff
            if ($arg_arr[0] == 'result')
            {
                // Check for existence on the result dir already
                if (! file_exists($arg_arr[1]))
                {
                    // Doesn't exist, so lets make it
                    // php's mkdir is sketchy, sorry non-linux CLI folks....
                    exec("mkdir {$arg_arr[1]}");
                }
            }

            // Load the real path for each dir
            $dir = realpath($arg_arr[1]);

            // Stick the dirs in with their keys as provided
            $dirs[$arg_arr[0]] = $dir;
        }
    }

    return $dirs;
}

/**
 * Explains usage in case you forgot
 */
function usage()
{
    // Print our helpers if they didn't supply an arg
    print("\nUSAGE\n");
    print("`source=dir1`        - the directory that has all the needles, the files we know about\n");
    print("`target=dir2`        - the directory that contains the haystack, the files we know about plus other stuff\n");
    print("`result=new_dir`     - is the directory that will contain copies of all the files from the target directory that match the names of the files in the source directory");
    exit(1);
}