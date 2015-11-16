<?php  header('Content-Type: text/html; charset=UTF-8');
	include "Snoopy.class.php";
	include 'simple_html_dom.php';

	function convertTime($d, $t) {
		$a = 0;
		if ($d == '一') {
			$a = 0;
		}else if ($d == '二') {
			$a = 1;
		}else if ($d == '三') {
			$a = 2;
		}else if ($d == '四') {
			$a = 3;
		}else if ($d == '五') {
			$a = 4;
		}else if ($d == '六') {
			$a = 5;
		}else if ($d == '日') {
			$a = 6;
		}
		$r = explode(',', $t);
		for ($i = 0; $i < count($r); $i ++) { 
			$r[$i] = $r[$i] + $a * 13;
		}
		return $r;
	}

	if (!isset($_REQUEST['ID']) || !isset($_REQUEST['password']) || !isset($_REQUEST['timestamp']) || !isset($_REQUEST['verify'])) {
		$class = array('class' => NULL, 'error' => array('code' => '100', 'description' => '接口数据不完整'));
		$result = json_encode($class);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
		exit();
	}
	$stuID = $_REQUEST['ID'];
	$password = $_REQUEST['password'];
	$year = "";
	$term = "";

	$timestamp = $_REQUEST['timestamp'];
	$verify = $_REQUEST['verify'];
	$sta = "iZJU_zhexun";
	$tmpStr = $stuID.$timestamp.$sta;
	$tmpStr = md5($tmpStr);
	if ($tmpStr != $_REQUEST['verify']) {
		$GPA = array('GPA' => NULL, 'error' => array('code' => '0', 'description' => '接口验证失败'));
		$result = json_encode($GPA);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
		exit();
	}

	if (isset($_REQUEST['year'])) {
		$year = $_REQUEST['year'];
	}
	if (isset($_REQUEST['year'])) {
		$term = $_REQUEST['term'];
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
    	if (intval($term) == 1) {
    		$term = "1|秋、冬";
    	}else if (intval($term) == 2) {
    		$term = "2|春、夏";
    	}
		$submit_url = "http://jwbinfosys.zju.edu.cn/xskbcx.aspx?xh=".$stuID;



		$para = array(
			'__VIEWSTATE' => 'dDwtMTMxNDQ3ODA5NTt0PDtsPGk8MT47PjtsPHQ8O2w8aTwxPjtpPDM+O2k8NT47aTw4PjtpPDEwPjtpPDEyPjtpPDE0PjtpPDE2PjtpPDE4PjtpPDIyPjtpPDI2PjtpPDI4Pjs+O2w8dDx0PDs7bDxpPDA+Oz4+Ozs+O3Q8dDxwPHA8bDxEYXRhVGV4dEZpZWxkO0RhdGFWYWx1ZUZpZWxkOz47bDx4bjt4bjs+Pjs+O3Q8aTwzPjtAPDIwMTQtMjAxNTsyMDEzLTIwMTQ7MjAxMi0yMDEzOz47QDwyMDE0LTIwMTU7MjAxMy0yMDE0OzIwMTItMjAxMzs+PjtsPGk8MD47Pj47Oz47dDx0PHA8cDxsPERhdGFUZXh0RmllbGQ7RGF0YVZhbHVlRmllbGQ7PjtsPGR5eHE7eHExOz4+Oz47dDxpPDI+O0A856eL44CB5YasO+aYpeOAgeWkjzs+O0A8MXznp4vjgIHlhqw7MnzmmKXjgIHlpI87Pj47bDxpPDE+Oz4+Ozs+O3Q8cDxwPGw8VGV4dDs+O2w85a2m5Y+377yaMzEyMDEwMjE0MDs+Pjs+Ozs+O3Q8cDxwPGw8VGV4dDs+O2w85aeT5ZCN77ya6JGj6ZGr5a6dOz4+Oz47Oz47dDxwPHA8bDxUZXh0Oz47bDzlrabpmaLvvJrorqHnrpfmnLrnp5HlrabkuI7mioDmnK/lrabpmaI7Pj47Pjs7Pjt0PHA8cDxsPFRleHQ7PjtsPOexuyjkuJPkuJop77ya6L2v5Lu25bel56iLOz4+Oz47Oz47dDxwPHA8bDxUZXh0Oz47bDzooYzmlL/nj63vvJrova/ku7blt6XnqIsxMjAxOz4+Oz47Oz47dDxwPHA8bDxUZXh0Oz47bDxcZTs+Pjs+Ozs+O3Q8QDA8cDxwPGw8VmlzaWJsZTtQYWdlQ291bnQ7XyFJdGVtQ291bnQ7XyFEYXRhU291cmNlSXRlbUNvdW50O0RhdGFLZXlzOz47bDxvPHQ+O2k8MT47aTwwPjtpPDA+O2w8Pjs+Pjs+Ozs7Ozs7Ozs7Oz47Oz47dDxAMDxwPHA8bDxQYWdlQ291bnQ7XyFJdGVtQ291bnQ7XyFEYXRhU291cmNlSXRlbUNvdW50O0RhdGFLZXlzOz47bDxpPDE+O2k8MD47aTwwPjtsPD47Pj47Pjs7Ozs7Ozs7Ozs+Ozs+O3Q8O2w8aTwzPjs+O2w8dDxAMDw7Ozs7Ozs7Ozs7Pjs7Pjs+Pjs+Pjs+Pjs+vwiXWf19I1O7gKH1XCUwW2GTymY=',
			'__EVENTTARGET' => 'xqd',
			'xnd' => $year,
			'xxms' => mb_convert_encoding('列表', "GB2312", "UTF-8"),
			'xqd' => mb_convert_encoding($term, "GB2312", "UTF-8")

		);
		$snoopy->submit($submit_url,$para);
		$result2 = mb_convert_encoding($snoopy->results, "UTF-8", "GB2312");

		// print($result2);
		if (strpos($result2, "20")) {
			$html = new simple_html_dom();
			$html->load($result2);
			$ret = $html->find('option[selected=selected]');
			$ret = $ret[1];

			if ($ret->value != $term) {
				$ret = $html->find('input[name=__VIEWSTATE]');
				$ret = $ret[0];
				$viewstate = $ret->value;

				$para['__VIEWSTATE'] = $viewstate;
				$snoopy->submit($submit_url, $para);
				$result2 = mb_convert_encoding($snoopy->results, "UTF-8", "GB2312");
			}


			$courseResult = array();

			
			$html->load($result2);

			$ret = $html->find('table[id=xsgrid]');
			$ret = $ret[0];
			for ($i = 1; $i < count($ret->children()); $i ++) { 
				$temp1 = $ret->children($i);
				$course = array();
				$course["ID"] = $temp1->children(0)->plaintext;
				$course["course"] = $temp1->children(1)->plaintext;
				$course["teacher"] = $temp1->children(2)->plaintext;
				$course["term"] = $temp1->children(3)->plaintext;
				$course["timeOri"] = $temp1->children(4)->plaintext;
				$course["roomOri"] = $temp1->children(5)->plaintext;

				$search = "/周(.*)第(.*)节(.*)/U";
				preg_match_all($search, $temp1->children(4)->plaintext, $m);
				// var_dump($m);
				$classroom = explode("<br>", $temp1->children(5)->innertext);
				// var_dump($classroom);

				$time = array();
				for ($j = 0; $j < count($m[1]); $j ++) { 
					$r = convertTime($m[1][$j], $m[2][$j]);
					$c;
					if (count($m[1]) != count($classroom)) {
						$c = $course["roomOri"];
					}else {
						$c = $classroom[$j];
					}
					$temp = array('time' => $r, 'room' => $c);
					array_push($time, $temp);
				}
				$course["time"] = $time;
				// var_dump($time);

				array_push($courseResult, $course);
			}
			$class = array('class' => $courseResult, 'error' => NULL);
			$result = json_encode($class);
			$result = str_replace('\r\n', ' ', $result);
			$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
			print($result);
		}else {
			$class = array('class' => NULL, 'error' => array('code' => '106', 'description' => '获取课表失败'));
			$result = json_encode($class);
			$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
			print $result;
		}

	}else if (strpos($result1, "密码错误") || strpos($result1, "用户名不存在")) {
		$class = array('class' => false, 'error' => array('code' => '101', 'description' => '学号密码错误'));
		$result = json_encode($class);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else if(strpos($result1,"现在控制学号访问")){
		$class = array('class' => false, 'error' => array('code' => '102', 'description' => '现在控制学号访问'));
		$result = json_encode($class);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else if(strpos($result1,"学籍状态")){
		$class = array('class' => false, 'error' => array('code' => '103', 'description' => '学籍状态错误'));
		$result = json_encode($class);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else if(strpos($result1,"欠费")){
		$class = array('class' => false, 'error' => array('code' => '104', 'description' => '欠费'));
		$result = json_encode($class);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}else{
		$class = array('class' => false, 'error' => array('code' => '105', 'description' => '异常错误'));
		$result = json_encode($class);
		$result = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2BE', 'UTF-8', pack('H4', '\\1'))", $result);
		print $result;
	}
?>