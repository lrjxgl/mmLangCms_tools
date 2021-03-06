
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
import com.deitui.morelang.index.model.DataapiModel;
import com.model.Help;

@RestController
@CrossOrigin("*")
public class DataapiController {
	
	@RequestMapping("/dataapi/index")
	public String Index(
		@RequestParam(value="per_page",defaultValue="0") int per_page,
		@RequestParam(value="limit",defaultValue="0") int limit
	) {
		DataapiModel am=new DataapiModel();
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
	
	@RequestMapping("/dataapi/show")
	public String Show(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		DataapiModel am=new DataapiModel();
		Map data=am.where("id="+id).selectRow();
			
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/dataapi/add")
	public String Add(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		DataapiModel am=new DataapiModel();
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
	
	@RequestMapping("/dataapi/save")
	public String Save(
		@RequestParam(value="token",defaultValue="") String token,
		@RequestParam(value="id",defaultValue="0") int id,
@RequestParam(value="title",defaultValue="") String title,
@RequestParam(value="word",defaultValue="") String word,
@RequestParam(value="type_id",defaultValue="0") int type_id,
@RequestParam(value="equation",defaultValue="") String equation,
@RequestParam(value="info",defaultValue="") String info,
@RequestParam(value="content",defaultValue="") String content,
@RequestParam(value="status",defaultValue="0") int status
	) {
		Map indata= new HashMap();
		indata.put("id", id);
indata.put("title", title);
indata.put("word", word);
indata.put("type_id", type_id);
indata.put("equation", equation);
indata.put("info", info);
indata.put("content", content);
indata.put("status", status);

		DataapiModel am=new DataapiModel();
		if(id==0) {
			am.insert(indata);
		}else {
			am.update(indata, "id="+id);
		}
		return Help.success(0, "????????????");
	}
	
	@RequestMapping("/dataapi/status")
	public String Status(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		DataapiModel am=new DataapiModel();
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
	
	@RequestMapping("/dataapi/recommend")
	public String recommend(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		DataapiModel am=new DataapiModel();
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
	
	@RequestMapping("/dataapi/delete")
	public String delete(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id	
	) {
		DataapiModel am=new DataapiModel();
		Map indata= new HashMap();
		indata.put("status", 11);
		am.update(indata, "id="+id);
		return Help.success(0, "????????????");
	}
	
	
	
}

