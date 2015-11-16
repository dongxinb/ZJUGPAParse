<?php  header('Content-Type: text/html; charset=UTF-8');
	include "Snoopy.class.php";
	include 'simple_html_dom.php';

	if (!isset($_REQUEST['ID']) || !isset($_REQUEST['password']) || !isset($_REQUEST['timestamp']) || !isset($_REQUEST['verify'])) {
		$GPA = array('GPA' => NULL, 'error' => array('code' => '100', 'description' => '数据不完整'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
		exit();
	}
	$stuID = $_REQUEST['ID'];
	$password = $_REQUEST['password'];
	$year = "2014-2015";
	if (isset($_REQUEST['year']))
		$year = $_REQUEST['year'];
	
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

    	$examResult = array();

		$submit_url = "http://jwbinfosys.zju.edu.cn/xskscx.aspx?xh=".$stuID;

		$snoopy->fetch($submit_url);
		$result1 = mb_convert_encoding($snoopy->results, "UTF-8", "GB2312");
		$html = new simple_html_dom();

		$html->load($result1);
		$ret = $html->find('input[name=__VIEWSTATE]');
		$ret = $ret[0];
		$viewstate = $ret->value;


		$para = array(
			'__VIEWSTATE' => $viewstate,
			'__EVENTTARGET' => 'xqd',
			'xnd' => $year,
			'xqd' => mb_convert_encoding("1|冬", "GB2312", "UTF-8")
		);
		$snoopy->submit($submit_url, $para);
		$result1 = mb_convert_encoding($snoopy->results, "UTF-8", "GB2312");


		$html->load($result1);
		$ret = $html->find('input[name=__VIEWSTATE]');
		$ret = $ret[0];
		$viewstate = $ret->value;
		$ret = $html->find('table[id=DataGrid1]');
		

		$ret = $ret[0];
		$tempResult = array();
		for ($i = 1; $i < count($ret->children()); $i ++) { 
				$temp1 = $ret->children($i);
				$exam = array();
				$exam["examID"] = $temp1->children(0)->plaintext;
				$exam["examName"] = $temp1->children(1)->plaintext;
				$exam["examCredit"] = $temp1->children(2)->plaintext;
				$exam["examFlag"] = $temp1->children(3)->plaintext;
				if ($exam["examFlag"] == '&nbsp;') {
					$exam["examFlag"] = "";
				}
				$exam["examTerm"] = $temp1->children(5)->plaintext;
				$exam["examTime"] = $temp1->children(6)->plaintext;
				$exam["examPlace"] = $temp1->children(7)->plaintext;
				$exam["examSeat"] = $temp1->children(8)->plaintext;
				array_push($tempResult, $exam);
					// $exam['1|秋'] = $exam;
			}
		$examResult['1|冬'] = $tempResult;
		



		$para['xqd'] = mb_convert_encoding("1|秋", "GB2312", "UTF-8");
		$para['__VIEWSTATE'] = $viewstate;

		$snoopy->submit($submit_url, $para);
		$result1 = mb_convert_encoding($snoopy->results, "UTF-8", "GB2312");

		$html->load($result1);
		$ret = $html->find('input[name=__VIEWSTATE]');
		$ret = $ret[0];
		$viewstate = $ret->value;
		$ret = $html->find('table[id=DataGrid1]');
		$ret = $ret[0];
		$tempResult = array();
		for ($i = 1; $i < count($ret->children()); $i ++) { 
				$temp1 = $ret->children($i);
				$exam = array();
				$exam["examID"] = $temp1->children(0)->plaintext;
				$exam["examName"] = $temp1->children(1)->plaintext;
				$exam["examCredit"] = $temp1->children(2)->plaintext;
				$exam["examFlag"] = $temp1->children(3)->plaintext;
				if ($exam["examFlag"] == '&nbsp;') {
					$exam["examFlag"] = "";
				}
				$exam["examTerm"] = $temp1->children(5)->plaintext;
				$exam["examTime"] = $temp1->children(6)->plaintext;
				$exam["examPlace"] = $temp1->children(7)->plaintext;
				$exam["examSeat"] = $temp1->children(8)->plaintext;
				array_push($tempResult, $exam);
			}

		//修改顺序
		$tt = array_shift($examResult);
		$examResult['1|秋'] = $tempResult;
		$examResult['1|冬'] = $tt;




		$para['xqd'] = mb_convert_encoding('1|短', "GB2312", "UTF-8");
		$para['__VIEWSTATE'] = $viewstate;
		$snoopy->submit($submit_url, $para);
		$result1 = mb_convert_encoding($snoopy->results, "UTF-8", "GB2312");

		$html->load($result1);
		$ret = $html->find('input[name=__VIEWSTATE]');
		$ret = $ret[0];
		$viewstate = $ret->value;
		$ret = $html->find('table[id=DataGrid1]');
		$ret = $ret[0];
		$tempResult = array();
		for ($i = 1; $i < count($ret->children()); $i ++) { 
				$temp1 = $ret->children($i);
				$exam = array();
				$exam["examID"] = $temp1->children(0)->plaintext;
				$exam["examName"] = $temp1->children(1)->plaintext;
				$exam["examCredit"] = $temp1->children(2)->plaintext;
				$exam["examFlag"] = $temp1->children(3)->plaintext;
				if ($exam["examFlag"] == '&nbsp;') {
					$exam["examFlag"] = "";
				}
				$exam["examTerm"] = $temp1->children(5)->plaintext;
				$exam["examTime"] = $temp1->children(6)->plaintext;
				$exam["examPlace"] = $temp1->children(7)->plaintext;
				$exam["examSeat"] = $temp1->children(8)->plaintext;
				array_push($tempResult, $exam);
			}
		$examResult['1|短'] = $tempResult;



		$para['xqd'] = mb_convert_encoding('1|暑', "GB2312", "UTF-8");
		$para['__VIEWSTATE'] = $viewstate;
		$snoopy->submit($submit_url, $para);
		$result1 = mb_convert_encoding($snoopy->results, "UTF-8", "GB2312");

		$html->load($result1);
		$ret = $html->find('input[name=__VIEWSTATE]');
		$ret = $ret[0];
		$viewstate = $ret->value;
		$ret = $html->find('table[id=DataGrid1]');
		$ret = $ret[0];
		$tempResult = array();
		for ($i = 1; $i < count($ret->children()); $i ++) { 
				$temp1 = $ret->children($i);
				$exam = array();
				$exam["examID"] = $temp1->children(0)->plaintext;
				$exam["examName"] = $temp1->children(1)->plaintext;
				$exam["examCredit"] = $temp1->children(2)->plaintext;
				$exam["examFlag"] = $temp1->children(3)->plaintext;
				if ($exam["examFlag"] == '&nbsp;') {
					$exam["examFlag"] = "";
				}
				$exam["examTerm"] = $temp1->children(5)->plaintext;
				$exam["examTime"] = $temp1->children(6)->plaintext;
				$exam["examPlace"] = $temp1->children(7)->plaintext;
				$exam["examSeat"] = $temp1->children(8)->plaintext;
				array_push($tempResult, $exam);
			}
		$examResult['1|暑'] = $tempResult;



		$para['xqd'] = mb_convert_encoding('2|春', "GB2312", "UTF-8");
		$para['__VIEWSTATE'] = $viewstate;
		$snoopy->submit($submit_url, $para);
		$result1 = mb_convert_encoding($snoopy->results, "UTF-8", "GB2312");

		$html->load($result1);
		$ret = $html->find('input[name=__VIEWSTATE]');
		$ret = $ret[0];
		$viewstate = $ret->value;
		$ret = $html->find('table[id=DataGrid1]');
		$ret = $ret[0];
		$tempResult = array();
		for ($i = 1; $i < count($ret->children()); $i ++) { 
				$temp1 = $ret->children($i);
				$exam = array();
				$exam["examID"] = $temp1->children(0)->plaintext;
				$exam["examName"] = $temp1->children(1)->plaintext;
				$exam["examCredit"] = $temp1->children(2)->plaintext;
				$exam["examFlag"] = $temp1->children(3)->plaintext;
				if ($exam["examFlag"] == '&nbsp;') {
					$exam["examFlag"] = "";
				}
				$exam["examTerm"] = $temp1->children(5)->plaintext;
				$exam["examTime"] = $temp1->children(6)->plaintext;
				$exam["examPlace"] = $temp1->children(7)->plaintext;
				$exam["examSeat"] = $temp1->children(8)->plaintext;
				array_push($tempResult, $exam);
			}
		$examResult['2|春'] = $tempResult;



		$para['xqd'] = mb_convert_encoding('2|夏', "GB2312", "UTF-8");
		$para['__VIEWSTATE'] = $viewstate;
		$snoopy->submit($submit_url, $para);
		$result1 = mb_convert_encoding($snoopy->results, "UTF-8", "GB2312");

		$html->load($result1);
		$ret = $html->find('input[name=__VIEWSTATE]');
		$ret = $ret[0];
		$viewstate = $ret->value;
		$ret = $html->find('table[id=DataGrid1]');
		$ret = $ret[0];
		$tempResult = array();
		for ($i = 1; $i < count($ret->children()); $i ++) { 
				$temp1 = $ret->children($i);
				$exam = array();
				$exam["examID"] = $temp1->children(0)->plaintext;
				$exam["examName"] = $temp1->children(1)->plaintext;
				$exam["examCredit"] = $temp1->children(2)->plaintext;
				$exam["examFlag"] = $temp1->children(3)->plaintext;
				if ($exam["examFlag"] == '&nbsp;') {
					$exam["examFlag"] = "";
				}
				$exam["examTerm"] = $temp1->children(5)->plaintext;
				$exam["examTime"] = $temp1->children(6)->plaintext;
				$exam["examPlace"] = $temp1->children(7)->plaintext;
				$exam["examSeat"] = $temp1->children(8)->plaintext;
				array_push($tempResult, $exam);
			}
		$examResult['2|夏'] = $tempResult;



		$para['xqd'] = mb_convert_encoding('2|短', "GB2312", "UTF-8");
		$para['__VIEWSTATE'] = $viewstate;
		$snoopy->submit($submit_url, $para);
		$result1 = mb_convert_encoding($snoopy->results, "UTF-8", "GB2312");

		$html->load($result1);
		$ret = $html->find('input[name=__VIEWSTATE]');
		$ret = $ret[0];
		$viewstate = $ret->value;
		$ret = $html->find('table[id=DataGrid1]');
		$ret = $ret[0];
		$tempResult = array();
		for ($i = 1; $i < count($ret->children()); $i ++) { 
				$temp1 = $ret->children($i);
				$exam = array();
				$exam["examID"] = $temp1->children(0)->plaintext;
				$exam["examName"] = $temp1->children(1)->plaintext;
				$exam["examCredit"] = $temp1->children(2)->plaintext;
				$exam["examFlag"] = $temp1->children(3)->plaintext;
				if ($exam["examFlag"] == '&nbsp;') {
					$exam["examFlag"] = "";
				}
				$exam["examTerm"] = $temp1->children(5)->plaintext;
				$exam["examTime"] = $temp1->children(6)->plaintext;
				$exam["examPlace"] = $temp1->children(7)->plaintext;
				$exam["examSeat"] = $temp1->children(8)->plaintext;
				array_push($tempResult, $exam);
			}
		$examResult['2|短'] = $tempResult;

		$exam = array('exam' => $examResult, 'error' => NULL);

		$result = json_encode($exam);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);



		print($result);

	}else if (strpos($result1, "密码错误") || strpos($result1, "用户名不存在")) {
		$GPA = array('exam' => false, 'error' => array('code' => '101', 'description' => '学号密码错误'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else if(strpos($result1,"现在控制学号访问")){
		$GPA = array('exam' => false, 'error' => array('code' => '102', 'description' => '现在控制学号访问'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else if(strpos($result1,"学籍状态")){
		$GPA = array('exam' => false, 'error' => array('code' => '103', 'description' => '学籍状态错误'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else if(strpos($result1,"欠费")){
		$GPA = array('exam' => false, 'error' => array('code' => '104', 'description' => '欠费'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else{
		$GPA = array('exam' => false, 'error' => array('code' => '105', 'description' => '异常错误'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}
?>