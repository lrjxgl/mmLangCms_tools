
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
import com.deitui.morelang.index.model.ArticleCommentModel;
import com.model.Help;

@RestController
@CrossOrigin("*")
public class ArticleCommentController {
	
	@RequestMapping("/article_comment/index")
	public String Index(
		@RequestParam(value="per_page",defaultValue="0") int per_page,
		@RequestParam(value="limit",defaultValue="0") int limit
	) {
		ArticleCommentModel am=new ArticleCommentModel();
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
	
	@RequestMapping("/article_comment/show")
	public String Show(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		ArticleCommentModel am=new ArticleCommentModel();
		Map data=am.where("id="+id).selectRow();
			
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/article_comment/add")
	public String Add(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		ArticleCommentModel am=new ArticleCommentModel();
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
	
	@RequestMapping("/article_comment/save")
	public String Save(
		@RequestParam(value="token",defaultValue="") String token,
		@RequestParam(value="id",defaultValue="0") int id,
@RequestParam(value="userid",defaultValue="0") int userid,
@RequestParam(value="status",defaultValue="0") int status,
@RequestParam(value="objectid",defaultValue="0") int objectid,
@RequestParam(value="pid",defaultValue="0") int pid,
@RequestParam(value="content",defaultValue="") String content,
@RequestParam(value="ip",defaultValue="") String ip,
@RequestParam(value="ip_city",defaultValue="") String ip_city,
@RequestParam(value="imgsdata",defaultValue="") String imgsdata
	) {
		Map indata= new HashMap();
		indata.put("id", id);
indata.put("userid", userid);
indata.put("status", status);
indata.put("objectid", objectid);
indata.put("pid", pid);
indata.put("content", content);
indata.put("ip", ip);
indata.put("ip_city", ip_city);
indata.put("imgsdata", imgsdata);

		ArticleCommentModel am=new ArticleCommentModel();
		if(id==0) {
			am.insert(indata);
		}else {
			am.update(indata, "id="+id);
		}
		return Help.success(0, "保存成功");
	}
	
	@RequestMapping("/article_comment/status")
	public String Status(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		ArticleCommentModel am=new ArticleCommentModel();
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
	
	@RequestMapping("/article_comment/recommend")
	public String recommend(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		ArticleCommentModel am=new ArticleCommentModel();
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
	
	@RequestMapping("/article_comment/delete")
	public String delete(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id	
	) {
		ArticleCommentModel am=new ArticleCommentModel();
		Map indata= new HashMap();
		indata.put("status", 11);
		am.update(indata, "id="+id);
		return Help.success(0, "删除成功");
	}
	
	
	
}

