
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
import com.deitui.morelang.index.model.TableModel;
import com.model.Help;

@RestController
@CrossOrigin("*")
public class TableController {
	
	@RequestMapping("/table/index")
	public String Index(
		@RequestParam(value="per_page",defaultValue="0") int per_page,
		@RequestParam(value="limit",defaultValue="0") int limit
	) {
		TableModel am=new TableModel();
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
	
	@RequestMapping("/table/show")
	public String Show(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="tableid",defaultValue="0") int tableid
	) {
		TableModel am=new TableModel();
		Map data=am.where("tableid="+tableid).selectRow();
			
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/table/add")
	public String Add(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="tableid",defaultValue="0") int tableid
	) {
		TableModel am=new TableModel();
		Map data=new HashMap();
		if(tableid!=0) {
			data=am.where("tableid="+tableid).selectRow();
			
		}
		
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/table/save")
	public String Save(
		@RequestParam(value="token",defaultValue="") String token,
		@RequestParam(value="tableid",defaultValue="0") int tableid,
@RequestParam(value="title",defaultValue="") String title,
@RequestParam(value="tablename",defaultValue="") String tablename,
@RequestParam(value="description",defaultValue="") String description,
@RequestParam(value="status",defaultValue="0") int status,
@RequestParam(value="showTpl",defaultValue="") String showTpl,
@RequestParam(value="listTpl",defaultValue="") String listTpl,
@RequestParam(value="addTpl",defaultValue="") String addTpl
	) {
		Map indata= new HashMap();
		indata.put("tableid", tableid);
indata.put("title", title);
indata.put("tablename", tablename);
indata.put("description", description);
indata.put("status", status);
indata.put("showTpl", showTpl);
indata.put("listTpl", listTpl);
indata.put("addTpl", addTpl);

		TableModel am=new TableModel();
		if(tableid==0) {
			am.insert(indata);
		}else {
			am.update(indata, "tableid="+tableid);
		}
		return Help.success(0, "????????????");
	}
	
	@RequestMapping("/table/status")
	public String Status(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="tableid",defaultValue="0") int tableid
	) {
		TableModel am=new TableModel();
		Map row=am.where("tableid="+tableid).selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("status")==1) {
			status=2;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("status", status);
		am.update(indata,"tableid="+tableid);
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("status", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/table/recommend")
	public String recommend(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="tableid",defaultValue="0") int tableid
	) {
		TableModel am=new TableModel();
		Map row=am.where("tableid="+tableid).selectRow(); 
		JSONObject json=(JSONObject) new JSONObject().toJSON(row);
		int status=0;
		if(json.getIntValue("is_recommend")==1) {
			status=0;
		}else {
			status=1;
		}
		Map indata=new HashMap();
		indata.put("is_recommend", status);
		am.update(indata,"tableid="+tableid);
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("is_recommend", status);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/table/delete")
	public String delete(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="tableid",defaultValue="0") int tableid	
	) {
		TableModel am=new TableModel();
		Map indata= new HashMap();
		indata.put("status", 11);
		am.update(indata, "tableid="+tableid);
		return Help.success(0, "????????????");
	}
	
	
	
}

