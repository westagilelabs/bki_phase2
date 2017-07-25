<?php defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('set_time_limit', 0);
require_once('fpdf181/fpdf.php');
require_once('FPDI/fpdi.php');
require('class.pdf2text.php');
require_once('S3.php');
include 'vendor/autoload.php';//http://www.pdfparser.org/documentation

class SplitPDF {	
	
	private $bucket = "bbatchelor-uploads";
	
	function __construct($config = array())
	{
		log_message('debug', 'SplitPDF Class Initialized');
	}
	
	function split_pdf($filename, $end_directory = false,$fname,$it = false)
	{
		
		$filename = urldecode($filename);
		$fname = urldecode($fname);
		$folders = [];
		$file_names	 = [];	
		$ff = [];
		$fd = [];
		$mm = [];
		$format = 0;
		//echo $filename;
		$this->CI =&get_instance();
		$this->CI->load->model('usermodel');
			
		$parser = new \Smalot\PdfParser\Parser();
		try{
		$pdf    = $parser->parseFile($filename);
		}catch(Exception $e){
			//echo "<pre>";var_dump($e);exit;
			return false;
		}
		//exit;
		$data = $pdf->getText();			
		
		//checking for AP EFT file
		$data1 = explode("Seq",$data);
		$dc = count($data1);
		if($dc > 1){
			$format = 1;			
		}
		else{
			//PR EFT file
			$data2 = explode("30058",$data);
			
			if(count($data2) > 1){
				$format = 2;
				$d2c = count($data2);
				for($i=1;$i<$d2c;$i++){
					$a = trim($data2[$i]);
					
					$empid = $a[0].$a[1].$a[2].$a[3];
					//explode for EMPloyee name
					$b = explode($empid,$a);
					$c = explode(" ",$b[1]);
					$n = $c[0]." ".$c[1]." ".$c[2];
					$folders[] = intval($empid);
					$mm[$empid] = $n;
				}
			}
			
			//For file 'W2 sample for BB'
			if($folders[0] == 0  && !empty($folders)){
				$mm = [];
				$format = 1;
				$p = 1;
				//e Employee's name, address, and ZIP code
				$data3 = explode("Employee",$data);	
				$d3c = count($data3);
				//var_dump($d3c);exit;
				for($i=0;$i<$d3c;$i++){
					if($i%9 == 0)
					{
						$eid = "";
						$tte = "";
						$tempd = preg_split('/\s/',trim($data3[$i]));
						$tc = count($tempd);
						$n = $tempd[$tc-6]." ".$tempd[$tc-4]." ".$tempd[$tc-3];
						if($n !="For EMPLOYEE'S RECORDS" && $n !='name, address, and ZIP' && $n !="For EMPLOYEE'S RECORDS (See" && $n !="Copy 2 To Be" && $n!='7261860XI GA')
						{
							$narr[] = $n;
						}
						$tte = (string)(trim($n));
						//$ch  = explode(" ",$tte);
						
						//$d = $ch[0]." ".$ch[1]." ".$ch[2];
						//echo "Before DB fetch <br/>";
						//var_dump($tte);
						//echo "<br/>";
						if($tte != 'EFT Transmission' && $tte != '20 Locality' && $tte != '20 Locality ' && $tte !='7261860XI GA')
							$eid = $this->CI->usermodel->getEmpIdByName($tte);
						
						//var_dump($eid);
						//echo "<br/>";
						//FIX EMPID ISSUE
						if(!empty($eid) && $eid != NULL && $eid != 0)
						{							
							$ff[$p] = $eid;
						}
						else{	
							if($tte != 'EFT Transmission' && $tte != '20 Locality' && $tte != '20 Locality ' && $tte !='7261860XI GA')						
								$file_names[] = $tte;
						}
						$p++;//page iteration	
					}					
				}

				$folders = $ff;
				//var_dump($folders); exit;
			}//folder[0] ends			
		}//else ends
//echo "<pre>";var_dump($file_names); exit;

		if($format ==2){
			$ii=1;
			$empscnt = $this->CI->usermodel->getUsers();
			foreach($folders as $f)
			{
				if(in_array($f,$empscnt)){
					$ab[$ii] = $f; 
					unset($mm[$f]);
				}
				else{
					$file_names[] = $f;
				}
				$ii++;
			}
			$folders = $ab;
		}
//var_dump($file_names);exit;


		if($format == 1){
			for($i=0;$i<$dc; $i++){
				if($dc % 2 !=0){ //for odd indexes
					$d1 = preg_split('/\s/',trim($data1[$i]));					
					///lets assume first name and last name we got here
					$name = trim($d1[0]." ".$d1[1]);
					if($name != 'EFT Transmission')
						$eid = $this->CI->usermodel->getEmpIdByName($name);	
										
					if(!empty($eid))
					{
						$folders[$i] = $eid;
					}
					else{
						if($name != 'EFT Transmission' && $name != '20 Locality' && $name != '20 Locality ' && $name !='7261860XI GA')
							$file_names[] = $name;
					}
				}			
			}
		}
//var_dump($file_names);exit;	
		try{
			$pdf = new FPDI();
			$pagecount = $pdf->setSourceFile($filename); // How many pages?
		}catch(Exception $e){
			return false;
		}
			$re = $this->CI->usermodel->checkEmpIds($folders);
			
		$s3 = new S3();
		$skip = true;
		if($it){ //retry time uploads
			$skip = true;
		}
		else if($pagecount != count($folders))
		{
			if(count($re) > 0 && !$it){ //first uploads
				$skip = false;
			}		
			else if(count($re) == 0 && $it){
				$skip = true;
			}
			else if(count($re) == 0 && !$it){
				$skip = false;
			}
		}
		else if($pagecount == count($folders))
		{
			$skip = true;
		}
		
		
		//for first format setting values	
		/*
		echo "folders : ";var_dump($folders);echo "<br/>";
		echo "pagecount  : ";var_dump($pagecount);echo "<br/>";
		echo "format : ";var_dump($format);echo "<br/>";
		echo "re : ";var_dump(count($re));echo "<br/>";
		echo "it : "; var_dump($it);echo "<br/>";
		echo "skip : ";var_dump($skip);//exit;
		echo "Employee Names : ";var_dump($file_names);
		var_dump($folders);var_dump(empty($folders));exit;*/
		
		/*##### If file format is invalid OR USER's are not matched #### */
		
		if(empty($folders)){
			$return = array("value"=>false,"message"=>"No users matched with the file provided","retry"=>0);
			return json_encode($return);
		}
		if(($format == 1 || $format == 2)&& $skip )
		{
			$i=0;
			//fk is page number , fv is folder value			
			foreach ($folders as $fk=>$fv) {
				//var_dump($fk);exit;
				$new_pdf = new FPDI();
				$new_pdf->AddPage();
				$new_pdf->setSourceFile($filename);
				$new_pdf->useTemplate($new_pdf->importPage($fk));
				try {					
					$new_path = "split/".$fv;
					$dest = $fv."/";
					if (!is_dir($new_path))
					{
						mkdir($new_path, 0777, true);
					}					
					$end_directory = $new_path."/";
					$date = date('Y-m-d');
					$new_filename = $end_directory.str_replace('.pdf', '', $fname).'_'.$date.".pdf";
					$new_filename1 = $dest.str_replace('.pdf', '', $fname).$i.'_'.$date.".pdf";
					$new_pdf->Output($new_filename, "F");					
					$s3->putObjectFile($new_filename ,$this->bucket ,$new_filename1 ,S3::ACL_PUBLIC_READ );
					
				} catch (Exception $e) {
					return false;
					//echo "<pre>";var_dump($e);exit;
				}
				$i++;
			}
			//exit;
			return true;
		}
		else if($format == 0){
			//otherfile format
			//return false;			
			$return = array("value"=>false,"message"=>"Invalid File Format","retry"=>0);
			return json_encode($return);
		}
		else{
			//If page count is not matched with users count			
			if (!is_dir("temp_uploads"))
			{
				mkdir("temp_uploads", 0777, true);
			}
			$aaa = explode(".",$fname);
			$fpath = $fname;
			$return = array("value"=>false,"retry"=>1,"fpath"=>$fpath,"data"=>$file_names,"unames"=>$mm);
			$s = move_uploaded_file($filename,"temp_uploads/".$fpath);
			//var_dump($return);exit;
			return json_encode($return);
		}
	}
	 

}
