<?php
class FTPStandalone
{
	private $resource = null;
	private $connected = false;
	private $path = "";
	private $tmp_file = "";
	public $full_tmp_file = "";
	function __construct($ip, $port, $user, $pass, $path, $tmp_file = "/tmp/tmp")
	{
		$this->tmp_file = $tmp_file;
		$this->full_tmp_file = $_SERVER['DOCUMENT_ROOT'].$tmp_file;
		$this->path = $path;
		$this->resource = ftp_connect($ip, $port);
		$this->connected = ftp_login($this->resource, $user, $pass);
		
	}

	function  __destruct()
	{
		if(!empty($this->resource))
		{
			ftp_close($this->resource);
		}
	}

	public function create_file($destination, $content = "", $override = false)
	{
		$from = $_SERVER['DOCUMENT_ROOT'].$this->tmp_file;
		$fp =	fopen($from, "w+");
				fwrite($fp, $content);
				fclose($fp);
		$return = $this->move_file($from, $destination, $override);
		$fp =	fopen($from, "w+");
				fwrite($fp, "");
				fclose($fp);
		return $return;
	}

	public function mkdir($dir, $mod = 0755)
	{
		if(ftp_mkdir($this->resource, $dir))
		{
			ftp_chmod($this->resource, $mod, $dir);
			return true;
		}
	}

	public function copy($source, $destination)
	{
		$content = file_get_contents($source);
		return $this->create_file($destination, $content);
	}

	public function check_upload_dir($filename, $umask = false)
	{
		$filename = str_replace($_SERVER['DOCUMENT_ROOT'], "", $filename);
		if(!is_file($_SERVER['DOCUMENT_ROOT']."/".$filename))
		{
			$directory = $this->path;
			$checkDir = "";
			$dirArr = explode("/", $filename);
			foreach($dirArr AS $dir)
			{
				$directory .= "/".$dir;
				$checkDir .= "/".$dir;
				if(!is_dir($_SERVER['DOCUMENT_ROOT']."/".$checkDir))
				{
					if($umask == false)
					{
						$umask = 0777;
					}
					$this->mkdir($directory,$umask);

				}
			}
		}
		return $this->path.$filename;
	}

	public function delete_dir($filename)
	{
		$filename = str_replace($_SERVER['DOCUMENT_ROOT'], "", $filename);
		if(!is_dir($_SERVER['DOCUMENT_ROOT']."/".$filename))
		{
			return false;
		}
		if(ftp_rmdir($this->resource, $this->path.$filename))
		{
			return true;
		}
		return false;
	}

	public function rename($filename, $new_filename)
	{
		$filename = str_replace($_SERVER['DOCUMENT_ROOT'], "", $filename);
		$new_filename = str_replace($_SERVER['DOCUMENT_ROOT'], "", $new_filename);
		if(!is_file($_SERVER['DOCUMENT_ROOT']."/".$filename) && !is_dir($_SERVER['DOCUMENT_ROOT']."/".$filename))
		{
			return false;
		}
		if(ftp_rename($this->resource, $this->path.$filename, $this->path.$new_filename))
		{
			return true;
		}

		return false;
	}

	public function delete_file($filename)
	{
		$filename = str_replace($_SERVER['DOCUMENT_ROOT'], "", $filename);
		if(!is_file($_SERVER['DOCUMENT_ROOT']."/".$filename))
		{
			return false;
		}
		if(ftp_delete($this->resource, $this->path.$filename))
		{
			return true;
		}
		return false;
	}

	public function move_file($from, $to, $override = false)
	{
		if(!is_file($from))
		{
			return false;
		}
		$to = str_replace($_SERVER['DOCUMENT_ROOT'], "", $to);
		if($override == true)
		{
			$this->delete_file($to);
		}
		
		if(file_exists($_SERVER['DOCUMENT_ROOT']."/".$to))
		{
			return false;
		}

		$this->check_upload_dir( substr($to, 0, strrpos($to, "/") ) );
		if(ftp_put ($this->resource, $this->path.$to, $from, FTP_BINARY))
		{
			return true;
		}
		return false;
	}

	public function chmod($file, $permissions = 0777)
	{
		$file = str_replace($_SERVER['DOCUMENT_ROOT'], "", $file);
		if(!preg_match("/^".preg_quote($this->path)."/iUs",$file))
		{
			$file = $this->path.$file;
		}
		//echo "'".$file."' with perm: ".$permissions;
		if(ftp_chmod ($this->resource, $file, $permissions))
		{
			return true;
		}
		return false;
	}
}

?>
