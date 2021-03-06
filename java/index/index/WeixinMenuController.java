
package com.deitui.morelang.index.index;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.deitui.morelang.index.model.WeixinMenuModel;
import com.model.Help;

@RestController
@CrossOrigin("*")
public class WeixinMenuController {
	
	@RequestMapping("/weixin_menu/index")
	public String Index(
		@RequestParam(value="per_page",defaultValue="0") int per_page,
		@RequestParam(value="limit",defaultValue="0") int limit
	) {
		WeixinMenuModel am=new WeixinMenuModel();
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
	
	@RequestMapping("/weixin_menu/show")
	public String Show(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		WeixinMenuModel am=new WeixinMenuModel();
		Map data=am.where("id="+id).selectRow();
			
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/weixin_menu/add")
	public String Add(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		WeixinMenuModel am=new WeixinMenuModel();
		Map data=new HashMap();
		if(id!=0) {
			data=am.where("id="+id).selectRow();
			
		}
		
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/weixin_menu/save")
	public String Save(
		@RequestParam(value="token",defaultValue="") String token,
		@RequestParam(value="id",defaultValue="0") int id,
@RequestParam(value="userid",defaultValue="0") int userid,
@RequestParam(value="wid",defaultValue="0") int wid,
@RequestParam(value="pid",defaultValue="0") int pid,
@RequestParam(value="orderindex",defaultValue="0") int orderindex,
@RequestParam(value="title",defaultValue="") String title,
@RequestParam(value="w_type",defaultValue="") String w_type,
@RequestParam(value="w_key",defaultValue="") String w_key,
@RequestParam(value="w_url",defaultValue="") String w_url,
@RequestParam(value="sc_id",defaultValue="0") int sc_id,
@RequestParam(value="shopid",defaultValue="0") int shopid,
@RequestParam(value="content",defaultValue="") String content
	) {
		Map indata= new HashMap();
		indata.put("id", id);
indata.put("userid", userid);
indata.put("wid", wid);
indata.put("pid", pid);
indata.put("orderindex", orderindex);
indata.put("title", title);
indata.put("w_type", w_type);
indata.put("w_key", w_key);
indata.put("w_url", w_url);
indata.put("sc_id", sc_id);
indata.put("shopid", shopid);
indata.put("content", content);

		WeixinMenuModel am=new WeixinMenuModel();
		if(id==0) {
			am.insert(indata);
		}else {
			am.update(indata, "id="+id);
		}
		return Help.success(0, "????????????");
	}
	
	@RequestMapping("/weixin_menu/status")
	public String Status(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		WeixinMenuModel am=new WeixinMenuModel();
		Map row=am.where("id="+id).selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("status")==1) {
			status=2;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("status", status);
		am.update(indata,"id="+id);
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("status", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/weixin_menu/recommend")
	public String recommend(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		WeixinMenuModel am=new WeixinMenuModel();
		Map row=am.where("id="+id).selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("is_recommend")==1) {
			status=0;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("is_recommend", status);
		am.update(indata,"id="+id);
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("is_recommend", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/weixin_menu/delete")
	public String delete(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id	
	) {
		WeixinMenuModel am=new WeixinMenuModel();
		Map indata= new HashMap();
		indata.put("status", 11);
		am.update(indata, "id="+id);
		return Help.success(0, "????????????");
	}
	
	
	
}

