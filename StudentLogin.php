<?php  header('Content-Type: text/html; charset=UTF-8');
	include "Snoopy.class.php";
	include 'simple_html_dom.php';

	if (!isset($_REQUEST['ID']) || !isset($_REQUEST['password'])) {
		$GPA = array('GPA' => NULL, 'error' => array('code' => '100', 'description' => '学号密码不完整'));
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
    	$GPA = array('success' => true, 'error' => NULL);
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;

	}else if (strpos($result1, "密码错误") || strpos($result1, "用户名不存在")) {
		$GPA = array('success' => false, 'error' => array('code' => '101', 'description' => '学号密码错误'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else if(strpos($result1,"现在控制学号访问")){
		$GPA = array('success' => false, 'error' => array('code' => '102', 'description' => '现在控制学号访问'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else if(strpos($result1,"学籍状态")){
		$GPA = array('success' => false, 'error' => array('code' => '103', 'description' => '学籍状态错误'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else if(strpos($result1,"欠费")){
		$GPA = array('success' => false, 'error' => array('code' => '104', 'description' => '欠费'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else{
		$GPA = array('success' => false, 'error' => array('code' => '105', 'description' => '异常错误'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}
?>