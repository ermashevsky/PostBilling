<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * TheBizzTech
 *
 * An open source library built for Codeigniter to read CSV files into associated arrays
 *
 * @author		Jason Michels
 * @link		http://thebizztech.com
 */


class Getcsv {
	
	private $file_path = "";
    private $handle = "";

	public function set_file_path($file_path)
    {
        $this->file_path = $file_path;
        return $this;
    }

    private function get_handle()
    {
        $this->handle = fopen($this->file_path, "r");
        return $this;
    }

    private function close_csv()
    {
        fclose($this->handle);
        return $this;
    }

    //this is the most current function to use
    public function get_array()
    {
        $this->get_handle();

        $row = 0;
        while (($data = fgetcsv($this->handle, 0, ",")) !== FALSE) 
        {
            if($row == 0)
            {
                foreach ($data as $key => $value)
                {
                    $title[$key] = trim($value); //this extracts the titles from the first row and builds array
                }
            }
            else
            {
                $new_row = $row - 1; //this is needed so that the returned array starts at 0 instead of 1
                foreach($title as $key => $value) //this assumes there are as many columns as their are title columns
                {
                    $result[$new_row][$value] = trim($data[$key]);
                }
            }
            $row++;
        }
        $this->close_csv();
        return $result;
    }



// --------------------------------Main Functions Above----------------------------------------------------- //

    //This function is being left in incase I ever need it
    function get_csv_array()
    {
        $row = 0;
        if (($handle = fopen($this->file_path, "r")) !== FALSE) 
        {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
            {
                $final_array[$row] = $data;
                $row++;
            }
            fclose($handle);
        }
        return $final_array;
    }
	
    //Probably not going to use this much but would be helpful if there are not as many title columns as total columns and I wanted to pull out just specific titles
    function get_csv_assoc_array($questions)
    {
        $row = 0;
        if (($handle = fopen($this->file_path, "r")) !== FALSE) 
        {
            while (($data = fgetcsv($handle, "", ",")) !== FALSE) 
            {
                if($row == 0)
                {
                    foreach ($questions as $key => $value)
                    {
                        foreach($data as $d_key => $d_value)
                        {
                            if($data[$d_key] == $value)
                            {
                                $q_location[$value] = $d_key;
                            }
                        }
                    }
                }
                else
                {
                    foreach ($questions as $key => $value)
                    {
                        $new_row = $row -1;
                        $final_array[$new_row][$value] = trim($data[$q_location[$value]]);
                    }
                }

                $row++;
            }
            fclose($handle);
        }
        return $final_array;
    }
    
} //End of class

//Here is the end of the getcsv.php class