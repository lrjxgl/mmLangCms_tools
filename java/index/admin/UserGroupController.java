
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
import com.deitui.morelang.index.model.UserGroupModel;
import com.model.Help;

@RestController
@CrossOrigin("*")
public class UserGroupController {
	
	@RequestMapping("/admin/user_group/index")
	public String Index(
		@RequestParam(value="per_page",defaultValue="0") int per_page,
		@RequestParam(value="limit",defaultValue="0") int limit
	) {
		UserGroupModel am=new UserGroupModel();
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
	
	@RequestMapping("/admin/user_group/show")
	public String Show(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="groupid",defaultValue="0") int groupid
	) {
		UserGroupModel am=new UserGroupModel();
		Map data=am.where("groupid="+groupid).selectRow();
			
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/user_group/add")
	public String Add(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="groupid",defaultValue="0") int groupid
	) {
		UserGroupModel am=new UserGroupModel();
		Map data=new HashMap();
		if(groupid!=0) {
			data=am.where("groupid="+groupid).selectRow();
			
		}
		
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/user_group/save")
	public String Save(
		@RequestParam(value="token",defaultValue="") String token,
		@RequestParam(value="groupid",defaultValue="0") int groupid,
@RequestParam(value="groupname",defaultValue="") String groupname,
@RequestParam(value="access",defaultValue="") String access
	) {
		Map indata= new HashMap();
		indata.put("groupid", groupid);
indata.put("groupname", groupname);
indata.put("access", access);

		UserGroupModel am=new UserGroupModel();
		if(groupid==0) {
			am.insert(indata);
		}else {
			am.update(indata, "groupid="+groupid);
		}
		return Help.success(0, "保存成功");
	}
	
	@RequestMapping("/admin/user_group/status")
	public String Status(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="groupid",defaultValue="0") int groupid
	) {
		UserGroupModel am=new UserGroupModel();
		Map row=am.where("groupid="+groupid).selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("status")==1) {
			status=2;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("status", status);
		am.update(indata,"groupid="+groupid);
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("status", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/user_group/recommend")
	public String recommend(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="groupid",defaultValue="0") int groupid
	) {
		UserGroupModel am=new UserGroupModel();
		Map row=am.where("groupid="+groupid).selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("is_recommend")==1) {
			status=0;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("is_recommend", status);
		am.update(indata,"groupid="+groupid);
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("is_recommend", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/user_group/delete")
	public String delete(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="groupid",defaultValue="0") int groupid	
	) {
		UserGroupModel am=new UserGroupModel();
		Map indata= new HashMap();
		indata.put("status", 11);
		am.update(indata, "groupid="+groupid);
		return Help.success(0, "删除成功");
	}
	
	
	
}

