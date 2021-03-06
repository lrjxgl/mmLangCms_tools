
package com.deitui.morelang.forum.admin;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.deitui.morelang.forum.model.ForumTagsModel;
import com.model.Help;

@RestController
@CrossOrigin("*")
public class ForumTagsController {
	
	@RequestMapping("/admin/forum_tags/index")
	public String Index(
		@RequestParam(value="per_page",defaultValue="0") int per_page,
		@RequestParam(value="limit",defaultValue="0") int limit
	) {
		ForumTagsModel am=new ForumTagsModel();
		String where="status in(0,1,2) ";
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
	
	@RequestMapping("/admin/forum_tags/show")
	public String Show(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="tagid",defaultValue="0") int tagid
	) {
		ForumTagsModel am=new ForumTagsModel();
		Map data=am.where("tagid="+tagid).selectRow();
			
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/forum_tags/add")
	public String Add(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="tagid",defaultValue="0") int tagid
	) {
		ForumTagsModel am=new ForumTagsModel();
		Map data=new HashMap();
		if(tagid!=0) {
			data=am.where("tagid="+tagid).selectRow();
			
		}
		
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/forum_tags/save")
	public String Save(
		@RequestParam(value="token",defaultValue="") String token,
		@RequestParam(value="tagid",defaultValue="0") int tagid,
@RequestParam(value="title",defaultValue="") String title,
@RequestParam(value="status",defaultValue="0") int status,
@RequestParam(value="total_num",defaultValue="0") int total_num,
@RequestParam(value="gkey",defaultValue="") String gkey,
@RequestParam(value="gnum",defaultValue="0") int gnum
	) {
		Map indata= new HashMap();
		indata.put("tagid", tagid);
indata.put("title", title);
indata.put("status", status);
indata.put("total_num", total_num);
indata.put("gkey", gkey);
indata.put("gnum", gnum);

		ForumTagsModel am=new ForumTagsModel();
		if(tagid==0) {
			am.insert(indata);
		}else {
			am.update(indata, "tagid="+tagid);
		}
		return Help.success(0, "????????????");
	}
	
	@RequestMapping("/admin/forum_tags/status")
	public String Status(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="tagid",defaultValue="0") int tagid
	) {
		ForumTagsModel am=new ForumTagsModel();
		Map row=am.where("tagid="+tagid).selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("status")==1) {
			status=2;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("status", status);
		am.update(indata,"tagid="+tagid);
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("status", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/forum_tags/recommend")
	public String recommend(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="tagid",defaultValue="0") int tagid
	) {
		ForumTagsModel am=new ForumTagsModel();
		Map row=am.where("tagid="+tagid).selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("is_recommend")==1) {
			status=0;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("is_recommend", status);
		am.update(indata,"tagid="+tagid);
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("is_recommend", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/forum_tags/delete")
	public String delete(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="tagid",defaultValue="0") int tagid	
	) {
		ForumTagsModel am=new ForumTagsModel();
		Map indata= new HashMap();
		indata.put("status", 11);
		am.update(indata, "tagid="+tagid);
		return Help.success(0, "????????????");
	}
	
	
	
}

