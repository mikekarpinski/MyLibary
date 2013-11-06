<?php
// MyLibary by Michal Karpinski
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

Class ImageManipulation
{

	// Image type support JPG / GIF / PNG  

	protected $image_path;
	protected $image_type;
	protected $image;
	protected $imagex;
	protected $imagey;
	protected $fail;
	protected $image_ratio;

	function __construct($path)
	{
		$this->image_path = $path;
		$this->image_type = exif_imagetype($this->image_path);

		$this->CheckImageType();
		$this->GetImageSize();
		
		return True;	
	}

	function __destruct() 
	{
		if (!$this->fail)
			{
				$this->CreateImage();
			}
   	}

    protected function GetImageSize()
    {
    	$this->imagex = imagesx($this->image);
		$this->imagey = imagesy($this->image);
    }

    protected function CheckImageType()
    {
    	switch ($this->image_type) {
			case 1:
				$this->image = imagecreatefromgif($this->image_path);
				break;
			
			case 2:
				$this->image = imagecreatefromjpeg($this->image_path);
				break;

			case 3:
				$this->image = imagecreatefrompng($this->image_path);
				break;	
			
			default:
				$this->SetFail();
				die("Wrong image type");
				break;
		}
    }

    protected function CheckBackgroundShade($x,$y,$width)
	{
		$start_x = $x;
		$start_y = $y;
		$color_sum = 0;
		$counter = $width / 10;
		for ($i=0; $i < $counter; $i++) 
		{ 
			$color_index = imagecolorat($this->image, $start_x + ($i*10), $start_y);
			$color_tran = imagecolorsforindex($this->image, $color_index);
			if ((array_sum($color_tran)) > 382)
			{$color_sum++;}
		}
		if ($color_sum > ($counter / 2))
		{
			return False;
		}
		else
		{
			return True;
		}
	}

    protected function CreateImage()
    {
       header('content-type: image/jpeg');
       imagejpeg($this->image, '', 100);
       imagedestroy($this->image);
    }

    protected function SetFail()
    {
    	$this->fail = 1;
    }

    protected function SetImageCenter()
    {
    	$centerx =  ($this->imagex / 2);
    	$centery =  ($this->imagey / 2);
    	return [$centerx,$centery];
    }

    protected function SetImageRatio()
    {
    	if (!$this->imagex > $this->imagey)
    	{
    		$this->image_ratio = $this->imagex / $this->imagey;
    	}
    	else
    	{
    		$this->image_ratio = $this->imagey / $this->imagex;
    	}
    }

	public function ImageInfo()
	{
		echo "Image width:  ". imagesx($this->image). "<br />";
		echo "Image height: ". imagesy($this->image). "<br />";
	}

	public function AddWaterMark($string)
	{
		$white = imagecolorallocate($this->image, 255, 255, 255);
		$black = imagecolorallocate($this->image, 0, 0, 0);
		
		if($this->CheckBackgroundShade(10,$this->imagey,100) == True)
			{$text_color = $white;}
		else		
			{$text_color = $black;}

		
		// GD Font Location fix - http://php.net/manual/en/function.imagettftext.php 
		$fontpath = realpath(dirname(__FILE__));
		putenv('GDFONTPATH='.$fontpath."/fonts");
		

		$font = 'GeosansLight';
		imagettftext($this->image, 15, 0, 10, $this->imagey - 10, $text_color, $font, $string . " " . date("Y"));
	}


	public function ImageCrop($width, $height, $force = False )
	{
		//$this->SetFail();
		//Checks if width and height is smaller then image, unless $force is set to True
		if ((($width > $this->imagex) OR ($height > $this->imagey)) AND ($force != True))
		{
			$this->SetFail();
			echo "Width or Height is bigger then image, use force if you still want to crop image";
		}

		list($centerx,$centery) = $this->SetImageCenter();
		/*
		$white = imagecolorallocate($this->image, 255, 255, 255);



		    imagerectangle(
			$this->image, 
			$centerx - ($width/2), 
			$centery - ($height/2),
			$centerx + ($width/2),
			$centery + ($height/2),
			$white);
		
		
		$new_dimensions = 
		[
			$centerx - ($width/2 ), 
			$centery - ($height/2),
			$centerx + ($width/2 ),
			$centery + ($height/2)
		];
		*/
		$this->SetImageRatio();
		
		$new_image = imagecreatetruecolor($width, $height);
		
		imagecopyresampled (
						   $new_image, // Destination image link resource.
						   $this->image, // Source image link resource.
						   0, // x-coordinate of destination point.
						   0, // y-coordinate of destination point.
						  0,//$centerx - ($width / 2) , // x-coordinate of source point.
						  0,// $centery - ($height / 2), // y-coordinate of source point.
						   $width, // Destination width.
						   $height, // Destination height.
						   $this->imagex, // Source width.
			 			   $this->imagey // Source height.
						   );
		


		
		$this->image = $new_image;
		//echo "New image {$width}, {$height} </br>";
		//echo "Old image {$this->imagex}, {$this->imagex} "; 

	}

	
}

?>