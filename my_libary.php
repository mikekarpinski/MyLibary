<?php

Class FileUpload
{
	private $fieldname;
	private $filename;
	private $filetmpname;
	private $filesize;
	private $filetype;

	private $path;
	private $fullpath;
	public $image_types_array = ["image/gif","image/jpeg","image/jpg","image/pjpeg","image/x-png","image/png"];

	function __construct($fieldname, $path = "") 
	{
		$this->fieldname = $fieldname;

		$this->filename    = $_FILES[$this->fieldname]["name"];
		$this->filetype    = $_FILES[$this->fieldname]["type"];
		$this->filesize    = $_FILES[$this->fieldname]["size"];
		$this->filetmpname = $_FILES[$this->fieldname]["tmp_name"];

		$this->path 	 = $path;
		$this->fullpath  = $this->path . $this->filename;
	}

	public function CheckForErrors()
	{
		if ($_FILES["file"]["error"] > 0)
		    {
			    echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
			    die("File upload failed");
		    }
		else
			{
				return 1;
			}
	}

	public function DisplayFileInfo()
	{
		echo "--- File Info --- \n<br />";
		echo "File name: " . $this->filename . "\n<br />";
		echo "File tmp name: " . $this->filetmpname . "\n<br />";
		echo "File type: " . $this->filetype . "\n<br />";
		echo "Full path: "   . $this->fullpath . "\n<br />";
		echo "------------------ \n<br />";
	}

	public function Restrictions($type = "", $size = 0)
	{
		$type_result = 0;
		$size_result = 0;
		
		if (!empty($type))
		{
			switch ($type) {
				case 'image':
					$types_array = $this->image_types_array;
					break;
				
				default:
					$types_array = $this->image_types_array;
					break;
			}
			
			if(in_array($this->filetype,$types_array ))
				{
					$type_result = 1;
				}
		}
		else
		{
			$type_result = 1;
		}

		if($size > 0)
		{
			if (($this->filesize / 1024) <= $size)
			{
				$size_result = 1;
			}
		}
		else
		{
			$size_result = 1;
		}

		if (($size_result == 1) AND ($type_result == 1))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	public function CheckIfFileExists()
	{
		if(file_exists($this->fullpath))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	public function Upload($force = 0)
	{
		if (($this->CheckForErrors() == 1) AND ($this->Restrictions() == 1))
		{
			if (($this->CheckIfFileExists() == 0) OR ($force == 1))
			{
				if (move_uploaded_file($this->filetmpname,$this->fullpath) === True)
				{
					return 1;	
				}
				else
				{
					return 0;
				}
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 0;
		}
	}
}

Class Debug
{
    public function server_info()
	{
		var_dump($_SERVER);
	}
	
	public function post_info()
	{
		var_dump($_POST);
	}

	public function get_info()
	{
		var_dump($_GET);
	}

	public function file_creation()
	{
		$file   = "/home/karp/php/test.txt";
		$handle = fopen($file, 'w');
		if ($handle === false)
		{
			echo "File creation failed.";
		}
		else
		{
			echo "File creation succesful.";
		}
	}

	public function pingDomain($domain)
	{
	    $starttime = microtime(true);
	    $file      = fsockopen ($domain, 80, $errno, $errstr, 1);
	    $stoptime  = microtime(true);
	    $status    = 0;

	    if (!$file)
	    {
	        fclose($file);
	        echo "Site not working";
	    }
	    else
	    {
	    	echo "Site working";
	    }
	    
	}

	public function php_info()
	{
		echo phpinfo();
	}
}

Class Mail
{
		private $senders_email;
		private $senders_name;
		public  $senders_phone;
		
		private $receiver;
		private $subject;
		private $message;
		private $headers;
		
		private $redirect_successful;
		private $redirect_failed;
		
		function __construct() 
		{
		     $this->redirect_successful = $_SERVER['HTTP_REFERER'];
		     $this->redirect_failed           = $_SERVER['HTTP_REFERER'];
		}
		
		private function post_vars($post)
		{
			if ( (isset($post['email'])) AND (isset($post['email'])) AND (isset($post['email'])) AND (isset($post['email'])) )
			    {	
				    $this->receiver	         = filter_var($post['receiver'], FILTER_SANITIZE_STRING);
				    $this->senders_email 	 = filter_var($post['email'], FILTER_SANITIZE_STRING);
				    $this->senders_name      = filter_var($post['name'], FILTER_SANITIZE_STRING);
				    $this->senders_phone 	 = filter_var($post['phone'], FILTER_SANITIZE_STRING);
				    $this->message           = filter_var($post['message'], FILTER_SANITIZE_STRING);			
			    }
			else
			    {
				    echo "FAIL";
				   //   header("Location: ". $this->redirect_failed);
			    }
			
		}
		
		private function message_constuct()
		{
			$this->headers = 'From: '. $this->sender . "\r\n" . 'Reply-To: '. $this->sender . "\r\n";
			$this->message .= "\n Message from: " . $this->senders_name; 
			$this->message .= "\n Phone number: " . $this->senders_phone;
			$this->message .= "\n Email: " .$this->senders_email;
		}
			
		public function send()
		{
			try
				{
					$post = $_POST;
					$this->post_vars($_POST);
					$this->message_constuct();
					mail($this->receiver, $this->subject, $this->message, $this->headers);
				}
			
			catch(Exception $e)
				{
					echo 'Caught exception: ',  $e->getMessage(), "\n";
					header("Location: ". $this->redirect_failed);
					echo "FAIL";
					die();
				}
			
			header("Location: ". $this->redirect_successful);
			die("OK");	
		}
}

?>