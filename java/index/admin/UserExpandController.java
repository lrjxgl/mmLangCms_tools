
package com.deitui.morelang.index.admin;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.deitui.morelang.index.model.UserExpandModel;
import com.model.Help;

@RestController
@CrossOrigin("*")
public class UserExpandController {
	
	@RequestMapping("/admin/user_expand/index")
	public String Index(
		@RequestParam(value="per_page",defaultValue="0") int per_page,
		@RequestParam(value="limit",defaultValue="0") int limit
	) {
		UserExpandModel am=new UserExpandModel();
		String where=" 1 ";
		int start=per_page;
		if(limit==0){
			limit=24;
		}
		List list=am.where(where).limit(start,limit).Dselect(); 
		int rscount=Integer.parseInt(am.where(where).fields("count(*)").selectOne());
		per_page=per_page+limit;
		per_page=per_page>rscount?0:per_page;
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("list", list);
		redata.put("rscount", rscount);
        redata.put("per_page", per_page);
		redata.put("limit",limit);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/user_expand/show")
	public String Show(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="uid",defaultValue="0") int uid
	) {
		UserExpandModel am=new UserExpandModel();
		Map data=am.where("uid="+uid).selectRow();
			
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/user_expand/add")
	public String Add(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="uid",defaultValue="0") int uid
	) {
		UserExpandModel am=new UserExpandModel();
		Map data=new HashMap();
		if(uid!=0) {
			data=am.where("uid="+uid).selectRow();
			
		}
		
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/user_expand/save")
	public String Save(
		@RequestParam(value="token",defaultValue="") String token,
		@RequestParam(value="uid",defaultValue="0") int uid,
@RequestParam(value="content",defaultValue="") String content
	) {
		Map indata= new HashMap();
		indata.put("uid", uid);
indata.put("content", content);

		UserExpandModel am=new UserExpandModel();
		if(uid==0) {
			am.insert(indata);
		}else {
			am.update(indata, "uid="+uid);
		}
		return Help.success(0, "保存成功");
	}
	
	@RequestMapping("/admin/user_expand/status")
	public String Status(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="uid",defaultValue="0") int uid
	) {
		UserExpandModel am=new UserExpandModel();
		Map row=am.where("uid="+uid).selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("status")==1) {
			status=2;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("status", status);
		am.update(indata,"uid="+uid);
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("status", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/user_expand/recommend")
	public String recommend(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="uid",defaultValue="0") int uid
	) {
		UserExpandModel am=new UserExpandModel();
		Map row=am.where("uid="+uid).selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("is_recommend")==1) {
			status=0;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("is_recommend", status);
		am.update(indata,"uid="+uid);
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("is_recommend", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/user_expand/delete")
	public String delete(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="uid",defaultValue="0") int uid	
	) {
		UserExpandModel am=new UserExpandModel();
		Map indata= new HashMap();
		indata.put("status", 11);
		am.update(indata, "uid="+uid);
		return Help.success(0, "删除成功");
	}
	
	
	
}

