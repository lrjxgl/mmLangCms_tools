<?php
namespace app\index\admin;
use support\Request;
use support\DB;
use ext\DBS;
use ext\UserAccess;
use ext\Help;
class Tixian
{
	
	/*@@index@@*/    
    public function index(Request $request)
    {
	    $start=$request->get("per_page");
        $limit=12;
        $fm=DBS::MM("index","Tixian");
        $where="status in(0,1,2) ";
		$list=$fm
                ->offset($start)
                ->limit($limit)
                ->whereRaw($where)
				->orderBy("id","desc")
                ->get();
        $list=$fm->Dselect($list);
        $rscount=$fm->whereRaw($where)->count();
        $per_page=$start+$limit;
        $per_page=$per_page>$rscount?0:$per_page;
        $redata=[
            "error" => 0, 
            "message" => "success",
            "list"=>$list,
            "per_page"=>$per_page,
            "rscount"=>$rscount

        ];
		return json($redata); 
         
		   
    }

    /*@@add@@*/
	public function add(Request $request){
        

        $id=intval($request->get("id"));
        $data=[];
        if($id){
            $fm=DBS::MM("index","Tixian");
            $data=$fm->find($id);
            
        }
        $redata=[
            "error" => 0, 
            "message" => "success",
            "data"=>$data 
        ];
		return json($redata);       
    } 
    
	
    /*@@save@@*/
	public function save(Request $request){
       

        $id=intval($request->post("id"));
        $data=[];
        $fm=DBS::MM("index","Tixian");
        $indata=[];
        //处理发布内容
        
$indata["objectid"]=intval($request->post("objectid","0"));
$indata["k"]=$request->post("k","");
$indata["money"]=floatval($request->post("money","0"));
$indata["status"]=intval($request->post("status","0"));
$indata["info"]=$request->post("info","");
$indata["reply"]=$request->post("reply","");
$indata["redateline"]=intval($request->post("redateline","0"));
$indata["siteid"]=intval($request->post("siteid","0"));
$indata["yhk_name"]=$request->post("yhk_name","");
$indata["yhk_haoma"]=$request->post("yhk_haoma","");
$indata["yhk_huming"]=$request->post("yhk_huming","");
$indata["telephone"]=$request->post("telephone","");
$indata["yhk_address"]=$request->post("yhk_address","");
$indata["adminid"]=intval($request->post("adminid","0"));
$indata["paytype"]=$request->post("paytype","");
$indata["bankid"]=intval($request->post("bankid","0"));
        if($id){
            $row=$fm->find($id);
            
        }
        if($id){
            
            $fm->where("id",$id)->update($indata);
        }else{       
            
            $indata["createtime"]=date("Y-m-d H:i:s");
            
			
            $id=$fm->insertGetId($indata);
        }
      
       
        $redata=[
            "error" => 0, 
            "message" => "保存成功",
            "insert_id"=>$id
        ];
		return json($redata); 
    }

    /*@@status@@*/
    public function Status(Request $request){
		

        $id=$request->get("id");
        $fm=DBS::MM("index","Tixian");
        $row=$fm->where("id",$id)->first();
		
        if($row->status==1){
            $status=2;
        }else{
            $status=1;
        }
        $up=$fm->find($id);
        $up->status=$status;
        $up->save();
        $redata=[
            "error" => 0, 
            "message" => "success",
            "status"=>$status
        ];
		return json($redata); 
    }

    /*@@delete@@*/
    public function delete(Request $request){
		

        $id=$request->get("id");
        $fm=DBS::MM("index","Tixian");
        $row=$fm->find($id); 
        
        $row->status=11;
        $row->save();
        $redata=[
            "error" => 0, 
            "message" => "success"
        ];
		return json($redata); 
    }
      
}

?>