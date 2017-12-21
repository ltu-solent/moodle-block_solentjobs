<?php
class block_solentjobs extends block_base {
    public function init() {
        $this->title = get_string('solentjobs', 'block_solentjobs');
    }

	public function get_content() {
    if ($this->content !== null) {
      return $this->content;
    }	
	
	global $CFG, $DB, $COURSE, $OUTPUT;	
	
	$current_url = basename($_SERVER['REQUEST_URI']);
	$url = '';
	$cat_id = '';
	$this->content =  new stdClass;
	
	if(ISSET($_POST['school'])){
		$cat_id = $_POST['school'];
	}else{
		if($COURSE->id == 6181 || $COURSE->id == 24233){
			$cat_id = 'PJ';
		}else{
			
			$category = $DB->get_record_sql('	SELECT idnumber, name from {course_categories} 
												WHERE id = (
														SELECT cc.parent FROM {course_categories} cc
														JOIN {course} c ON c.category = cc.id
														where c.id = ?)', array($COURSE->id)); 	
			if($category){
				$cat_id = $category->idnumber;		
			}
		}
	}	
	
	$this->content->text   .= "<p class='top-five'>Latest jobs in:</p>";
if($current_url == 'my'){	
	$this->content->text .= "<form action='"; $this->content->text .= $_SERVER['PHP_SELF']; $this->content->text .=	"' method='post'><select id='school' name='school' style='width:100%;' onchange='submit()'>";	
}else{
	$this->content->text .= "<form action='"; $this->content->text .= $_SERVER['REQUEST_URI']; $this->content->text .=	"' method='post'><select id='school' name='school' style='width:100%;' onchange='submit()'>";
}
	$this->content->text .=	"<option value='SADF'"; if($cat_id == 'SADF'){$this->content->text .=   "selected='selected'";} $this->content->text .= ">Art, Design and Fashion</option>";
	$this->content->text .=	"<option value='SBL'"; if($cat_id == 'SBL'){$this->content->text .=   "selected='selected'";} $this->content->text .= ">Business and Law</option>";
	$this->content->text .=	"<option value='SCM'"; if($cat_id == 'SCM'){$this->content->text .=   "selected='selected'";} $this->content->text .= ">Communications and Marketing</option>";
	$this->content->text .=	"<option value='SMSEW'"; if(($cat_id == 'SMSEW') || ($cat_id == 'SMSE')){$this->content->text .=   "selected='selected'";} $this->content->text .= ">Maritime Science and Engineering</option>";
	$this->content->text .=	"<option value='SMAT'"; if($cat_id == 'SMAT'){$this->content->text .=   "selected='selected'";} $this->content->text .= ">Media Arts and Technology</option>";
	$this->content->text .=	"<option value='SSHSS'"; if($cat_id == 'SSHSS'){$this->content->text .=   "selected='selected'";} $this->content->text .= ">Sport, Health and Social Sciences</option>";
	$this->content->text .=	"<option value='CJ'"; if(($cat_id == 'CJ' || $cat_id == '')){$this->content->text .=  "selected='selected'";} $this->content->text .= ">Campus Jobs</option>";
	$this->content->text .=	"<option value='LJ'"; if($cat_id == 'LJ'){$this->content->text .=  "selected='selected'";} $this->content->text .= ">Latest Jobs</option>";
	$this->content->text .=	"<option value='PJ'"; if($cat_id == 'PJ'){$this->content->text .=  "selected='selected'";} $this->content->text .= ">Placement Jobs</option>";
	$this->content->text .=	"</select></form>";
	
	if($cat_id == 'SADF'){ //art design and fashion
		$url = "http://graduatejobs.solent.ac.uk/widget/jobs/;i=3";			
	} elseif($cat_id == 'SBL'){ //business and law
		$url = "http://graduatejobs.solent.ac.uk/widget/jobs/;i=5";		
	}elseif($cat_id == 'SCM'){ //communications and marketing
		$url = "http://graduatejobs.solent.ac.uk/widget/jobs/;i=6";		
	}elseif($cat_id == 'SMSE'){ //Maritime Science and Engineering
		$url = "http://graduatejobs.solent.ac.uk/widget/jobs/;i=8";		
	}elseif($cat_id == 'SMSEW'){ //Maritime Science and Engineering (WMA)
		$url = "http://graduatejobs.solent.ac.uk/widget/jobs/;i=8";		
	}elseif($cat_id == 'SMAT'){ //media arts and techonology
		$url = "http://graduatejobs.solent.ac.uk/widget/jobs/;i=4";		
	}elseif($cat_id == 'SSHSS'){ //Sport, Health and Social Sciences
		$url = "http://graduatejobs.solent.ac.uk/widget/jobs/;i=7";		
	}elseif($cat_id == 'CJ'){
		$url = "http://graduatejobs.solent.ac.uk/widget/jobs/;i=1";		
	}elseif($cat_id == 'LJ'){
		$url = "http://graduatejobs.solent.ac.uk/widget/jobs/;i=2";		
	}elseif($cat_id == 'PJ'){
		$url = "http://graduatejobs.solent.ac.uk/widget/jobs/;i=9";
	}else{ //campus jobs
		$url = "http://graduatejobs.solent.ac.uk/widget/jobs/;i=1";	
	}
	
// set time out	
	$context = stream_context_create(array('http'=> array('timeout' => 2 )));
	
	$fp = fopen($url, 'r', false, $context);
	if ( !$fp ) {
	  $this->content->text .= "<p>The jobs feed is currently unavailable.</p><p>Please visit the Solent Jobs Homepage for the latest vacancies.</p>";	
	  
	}
	else {
		$info = stream_get_meta_data($fp);		
		$xml = simplexml_load_file($url);
		$count = 0;		
	
	$this->content->text .= "<div class='div-jobs''>";
	foreach($xml as $job=>$value){	
		if($count < 5){
			$this->content->text .= "<span><img alt='' class='smallicon navicon' title='' src='" . $OUTPUT->pix_url('i/navigationitem') . "'><a class='jobs-listing-link' href='" . $value->DetailsUrl . "' target='_blank'>" . $value->Title . "</a></span>";						
		}
		$count++;
	} 
	$this->content->text .= "</div>";
	}	
	
	//$this->content->text .=	"<div id='div-button-jobs'><a id='button-jobs' href='http://graduatejobs.solent.ac.uk' target='_blank'>Solent Jobs Homepage</a></div>";	
	$this->content->text .=	"<div id='jobs-footer'><a href='http://graduatejobs.solent.ac.uk' target='_blank'>Solent Jobs Homepage...</a></div>";	
	
    return $this->content;
  }
}  