
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
import com.deitui.morelang.index.model.SiteModel;
import com.model.Help;

@RestController
@CrossOrigin("*")
public class SiteController {
	
	@RequestMapping("/admin/site/index")
	public String Index(
		@RequestParam(value="per_page",defaultValue="0") int per_page,
		@RequestParam(value="limit",defaultValue="0") int limit
	) {
		SiteModel am=new SiteModel();
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
	
	@RequestMapping("/admin/site/show")
	public String Show(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="siteid",defaultValue="0") int siteid
	) {
		SiteModel am=new SiteModel();
		Map data=am.where("siteid="+siteid).selectRow();
			
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/site/add")
	public String Add(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="siteid",defaultValue="0") int siteid
	) {
		SiteModel am=new SiteModel();
		Map data=new HashMap();
		if(siteid!=0) {
			data=am.where("siteid="+siteid).selectRow();
			
		}
		
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/site/save")
	public String Save(
		@RequestParam(value="token",defaultValue="") String token,
		@RequestParam(value="siteid",defaultValue="0") int siteid,
@RequestParam(value="sitename",defaultValue="") String sitename,
@RequestParam(value="domain",defaultValue="") String domain,
@RequestParam(value="title",defaultValue="") String title,
@RequestParam(value="keywords",defaultValue="") String keywords,
@RequestParam(value="description",defaultValue="") String description,
@RequestParam(value="close_why",defaultValue="") String close_why,
@RequestParam(value="logo",defaultValue="") String logo,
@RequestParam(value="icp",defaultValue="") String icp,
@RequestParam(value="status",defaultValue="0") int status,
@RequestParam(value="template",defaultValue="") String template,
@RequestParam(value="statjs",defaultValue="") String statjs,
@RequestParam(value="index_template",defaultValue="") String index_template,
@RequestParam(value="wap_template",defaultValue="") String wap_template,
@RequestParam(value="wapbg",defaultValue="") String wapbg
	) {
		Map indata= new HashMap();
		indata.put("siteid", siteid);
indata.put("sitename", sitename);
indata.put("domain", domain);
indata.put("title", title);
indata.put("keywords", keywords);
indata.put("description", description);
indata.put("close_why", close_why);
indata.put("logo", logo);
indata.put("icp", icp);
indata.put("status", status);
indata.put("template", template);
indata.put("statjs", statjs);
indata.put("index_template", index_template);
indata.put("wap_template", wap_template);
indata.put("wapbg", wapbg);

		SiteModel am=new SiteModel();
		if(siteid==0) {
			am.insert(indata);
		}else {
			am.update(indata, "siteid="+siteid);
		}
		return Help.success(0, "????????????");
	}
	
	@RequestMapping("/admin/site/status")
	public String Status(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="siteid",defaultValue="0") int siteid
	) {
		SiteModel am=new SiteModel();
		Map row=am.where("siteid="+siteid).selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("status")==1) {
			status=2;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("status", status);
		am.update(indata,"siteid="+siteid);
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("status", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/site/recommend")
	public String recommend(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="siteid",defaultValue="0") int siteid
	) {
		SiteModel am=new SiteModel();
		Map row=am.where("siteid="+siteid).selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("is_recommend")==1) {
			status=0;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("is_recommend", status);
		am.update(indata,"siteid="+siteid);
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("is_recommend", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/admin/site/delete")
	public String delete(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="siteid",defaultValue="0") int siteid	
	) {
		SiteModel am=new SiteModel();
		Map indata= new HashMap();
		indata.put("status", 11);
		am.update(indata, "siteid="+siteid);
		return Help.success(0, "????????????");
	}
	
	
	
}

