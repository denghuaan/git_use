<?php
// 父类控制器    底层
// error_reporting(0);
error_reporting(~E_NOTICE); 	  //排错
class Control{

	private static $control;   //控制器
	private static $action;	   //行为
	private $smarty;
	
	public function __construct()
	{
		$this->smarty = new Smarty();
		$this->smarty->template_dir='view';  //告诉视图层
	}

	static  function run()
	{
		// 里面不能用$this
		// self是静态方法，只能调用静态属性
		
		// 显示哪一个控制器
		// 接收控制器参数  收行为参数
		$control =  self::$control = isset($_REQUEST['control'])?$_REQUEST['control']:'user';    //设置默认控制器
		$action = self::$action = isset($_REQUEST['action'])?$_REQUEST['action']:'register';     //设置默认行为
		
		//判断控制器文件和行为存不存在
		if(!file_exists("control/$control.php")){
			exit("控制器:".$control."不存在");
		}
		include_once  "control/$control.php"; 
		$control_obj = new $control();             //这里的$control是一个变量  类是抽象的，对象是具体的
		
		if(!method_exists($control_obj, $action)){
			exit("方法: $action 不存在");
		}
		$control_obj->$action();				   //这里的	$action 也是一个变量
		
	}

	//显示前端页面
	public function display($html_path="")         
	{
		//判断需不需要用smarty模板   空为不需要
		if (empty($html_path)) {            
			include_once "view/".self::$control."/".self::$action.".html";
		}else{
			/*$html_path = self::$control.'/'.self::$action.".html";*/
			$this->smarty->display($html_path);
		}
		
		
	}

	public function assign($name,$value)
	{
		$this->smarty->assign($name,$value);        //$name 模板要用到的变量名字     $value变量
	}
	
	//调用模型层    
	public function model($mname)
	{
		$mname = $mname."Model";
		include "model/$mname.php";
		return new $mname();
	}
	
	// 响应数据
	public function return_status($msg,$status=1,$data=[])
	{
		echo json_encode([
			'status'=>$status,   //状态
 			'msg'=>$msg,		 //提示信息
 			'data'=>$data		 //数据
		]);exit();
	}
	
	
}


?>