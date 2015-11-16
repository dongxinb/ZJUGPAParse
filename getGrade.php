<?php  header('Content-Type: text/html; charset=UTF-8');
	include "Snoopy.class.php";
	include 'simple_html_dom.php';

	if (!isset($_REQUEST['ID']) || !isset($_REQUEST['password']) || !isset($_REQUEST['timestamp']) || !isset($_REQUEST['verify'])) {
		$GPA = array('GPA' => NULL, 'error' => array('code' => '100', 'description' => '接口数据不完整'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
		exit();
	}
	$stuID = $_REQUEST['ID'];
	$password = $_REQUEST['password'];

	$timestamp = $_REQUEST['timestamp'];
	$verify = $_REQUEST['verify'];
	$sta = "iZJU_zhexun";
	// var_dump(time());
	// $tmpArr = array($stuID, $timestamp, $sta);
	$tmpStr = $stuID.$timestamp.$sta;
	$tmpStr = md5($tmpStr);
	if ($tmpStr != $_REQUEST['verify']) {
		$GPA = array('GPA' => NULL, 'error' => array('code' => '0', 'description' => '接口验证失败'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
		exit();
	}
	
	$snoopy = new Snoopy;
	$submit_url = "http://jwbinfosys.zju.edu.cn/default2.aspx";
    $postData = array(
    	'__EVENTTARGET' => 'Button1',
		'__EVENTARGUMENT' => '',
		'__VIEWSTATE' => 'dDwxNTc0MzA5MTU4Ozs+RGE82+DpWCQpVjFtEpHZ1UJYg8w=',
		'TextBox1' => $stuID,
		'TextBox2' => $password,
		'RadioButtonList1' => '学生',
		'_eventId' => 'submit',
		'Text1' => ''
     );   
    $snoopy->submit($submit_url,$postData);
    
    //$result1 = $snoopy->results;
    $result1 = mb_convert_encoding($snoopy->results, "UTF-8", "GB2312");;
    
    //echo $result1;
    if (strpos($result1,"个人信息")){	

		$submit_url = "http://jwbinfosys.zju.edu.cn/xscj.aspx?xh=".$stuID;
		$getGradePost = array(
			'__VIEWSTATE' => 'dDwxMjA1NjA5MjcxO3Q8O2w8aTwxPjs+O2w8dDw7bDxpPDI+O2k8NT47aTwyMz47aTwyNT47aTwzOT47aTw0MT47aTw0Mz47aTw0NT47PjtsPHQ8dDw7dDxpPDE1PjtAPFxlOzIwMDEtMjAwMjsyMDAyLTIwMDM7MjAwMy0yMDA0OzIwMDQtMjAwNTsyMDA1LTIwMDY7MjAwNi0yMDA3OzIwMDctMjAwODsyMDA4LTIwMDk7MjAwOS0yMDEwOzIwMTAtMjAxMTsyMDExLTIwMTI7MjAxMi0yMDEzOzIwMTMtMjAxNDsyMDE0LTIwMTU7PjtAPFxlOzIwMDEtMjAwMjsyMDAyLTIwMDM7MjAwMy0yMDA0OzIwMDQtMjAwNTsyMDA1LTIwMDY7MjAwNi0yMDA3OzIwMDctMjAwODsyMDA4LTIwMDk7MjAwOS0yMDEwOzIwMTAtMjAxMTsyMDExLTIwMTI7MjAxMi0yMDEzOzIwMTMtMjAxNDsyMDE0LTIwMTU7Pj47Pjs7Pjt0PHQ8cDxwPGw8RGF0YVRleHRGaWVsZDtEYXRhVmFsdWVGaWVsZDs+O2w8eHhxO3hxMTs+Pjs+O3Q8aTw4PjtAPFxlO+enizvlhqw755+tO+aakTvmmKU75aSPO+efrTs+O0A8XGU7MXznp4s7MXzlhqw7MXznn607MXzmmpE7MnzmmKU7MnzlpI87Mnznn607Pj47Pjs7Pjt0PHA8O3A8bDxvbmNsaWNrOz47bDx3aW5kb3cucHJpbnQoKVw7Oz4+Pjs7Pjt0PHA8O3A8bDxvbmNsaWNrOz47bDx3aW5kb3cuY2xvc2UoKVw7Oz4+Pjs7Pjt0PEAwPDs7Ozs7Ozs7Ozs+Ozs+O3Q8QDA8Ozs7Ozs7Ozs7Oz47Oz47dDxAMDw7Ozs7Ozs7Ozs7Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPFpKRFg7Pj47Pjs7Pjs+Pjs+Pjs+u+mkiSBSDpwz5k4RDPZWljMCNUs=',
			'ddlXN' => '',
			'ddlXQ' => '',
			'Button2' => '在校学习成绩查询'
		);
		$snoopy->submit($submit_url,$getGradePost);
		$result2 = mb_convert_encoding($snoopy->results, "UTF-8", "GB2312");
		if (strpos($result2, "20")) {
			$gradeResult = array();

			$html = new simple_html_dom();
			$html->load($result2);

			$ret = $html->find('table[id=DataGrid1]');
			// $temp = $ret->children(1);	
			$ret = $ret[0];
			for ($i = 1; $i < count($ret->children()); $i ++) { 
				$temp1 = $ret->children($i);
				$course = array();
				//course id
				$course["courseID"] = $temp1->children(0)->plaintext;

				$course["year"] = substr($course["courseID"], 1, 9);
				$course["term"] = substr($course["courseID"], 11, 1);

				//course name
				$course["courseName"] = $temp1->children(1)->plaintext;
				//course grade
				if ($temp1->children(2)->plaintext == "&nbsp;") {
					$course["courseGrade"] = "0";
				}else {
					$course["courseGrade"] = $temp1->children(2)->plaintext;
				}
				//course credit
				$course["courseCredit"] = $temp1->children(3)->plaintext;
				//course GPA
				if ($temp1->children(4)->plaintext == "&nbsp;") {
					$course["courseGPA"] = "0";
				}else {
					$course["courseGPA"] = $temp1->children(4)->plaintext;
				}
				//course makeup
				if ($temp1->children(5)->plaintext == "&nbsp;") {
					$course["courseMakeUp"] = "";
				}else {
					$course["courseMakeUp"] = $temp1->children(5)->plaintext;
				}
				array_push($gradeResult, $course);
			}
			$GPA = array('GPA' => $gradeResult, 'error' => NULL);
			$result = json_encode($GPA);
			$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
			print($result);
		}else {
			$GPA = array('GPA' => NULL, 'error' => array('code' => '106', 'description' => '获取成绩失败'));
			$result = json_encode($GPA);
			$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
			print $result;
		}

	}else if (strpos($result1, "密码错误") || strpos($result1, "用户名不存在")) {
		$GPA = array('GPA' => false, 'error' => array('code' => '101', 'description' => '学号密码错误'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else if(strpos($result1,"现在控制学号访问")){
		$GPA = array('GPA' => false, 'error' => array('code' => '102', 'description' => '现在控制学号访问'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else if(strpos($result1,"学籍状态")){
		$GPA = array('GPA' => false, 'error' => array('code' => '103', 'description' => '学籍状态错误'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else if(strpos($result1,"欠费")){
		$GPA = array('GPA' => false, 'error' => array('code' => '104', 'description' => '欠费'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else{
		$GPA = array('GPA' => false, 'error' => array('code' => '105', 'description' => '异常错误'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}
?>