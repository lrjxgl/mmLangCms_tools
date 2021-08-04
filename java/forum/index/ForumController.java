
package com.deitui.morelang.forum.index;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

import com.alibaba.fastjson.JSON;
import com.alibaba.fastjson.JSONObject;
import com.deitui.morelang.forum.model.ForumModel;
import com.model.Help;

@RestController
@CrossOrigin("*")
public class ForumController {
	
	@RequestMapping("/forum/index")
	public String Index(
		@RequestParam(value="per_page",defaultValue="0") int per_page,
		@RequestParam(value="limit",defaultValue="0") int limit
	) {
		ForumModel am=new ForumModel();
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
	
	@RequestMapping("/forum/show")
	public String Show(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		ForumModel am=new ForumModel();
		Map data=am.where("id="+id).selectRow();
		data.put("imgurl", Help.image_site(data.get("imgurl")+""));	
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/forum/add")
	public String Add(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		ForumModel am=new ForumModel();
		Map data=new HashMap();
		if(id!=0) {
			data=am.where("id="+id).selectRow();
			data.put("trueimgurl", Help.image_site(data.get("imgurl")+""));
		}
		
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/forum/save")
	public String Save(
		@RequestParam(value="token",defaultValue="") String token,
		@RequestParam(value="id",defaultValue="0") int id,
@RequestParam(value="title",defaultValue="") String title,
@RequestParam(value="userid",defaultValue="0") int userid,
@RequestParam(value="gid",defaultValue="0") int gid,
@RequestParam(value="catid",defaultValue="0") int catid,
@RequestParam(value="forward_num",defaultValue="0") int forward_num,
@RequestParam(value="keywords",defaultValue="") String keywords,
@RequestParam(value="description",defaultValue="") String description,
@RequestParam(value="status",defaultValue="0") int status,
@RequestParam(value="imgurl",defaultValue="") String imgurl,
@RequestParam(value="last_time",defaultValue="0") int last_time,
@RequestParam(value="grade",defaultValue="0") int grade,
@RequestParam(value="tags",defaultValue="") String tags,
@RequestParam(value="videourl",defaultValue="") String videourl,
@RequestParam(value="money",defaultValue="0") Double money,
@RequestParam(value="gold",defaultValue="0") int gold,
@RequestParam(value="imgsdata",defaultValue="") String imgsdata
	) {
		Map indata= new HashMap();
		indata.put("id", id);
indata.put("title", title);
indata.put("userid", userid);
indata.put("gid", gid);
indata.put("catid", catid);
indata.put("forward_num", forward_num);
indata.put("keywords", keywords);
indata.put("description", description);
indata.put("status", status);
indata.put("imgurl", imgurl);
indata.put("last_time", last_time);
indata.put("grade", grade);
indata.put("tags", tags);
indata.put("videourl", videourl);
indata.put("money", money);
indata.put("gold", gold);
indata.put("imgsdata", imgsdata);

		ForumModel am=new ForumModel();
		if(id==0) {
			am.insert(indata);
		}else {
			am.update(indata, "id="+id);
		}
		return Help.success(0, "保存成功");
	}
	
	@RequestMapping("/forum/status")
	public String Status(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		ForumModel am=new ForumModel();
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
	
	@RequestMapping("/forum/recommend")
	public String recommend(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		ForumModel am=new ForumModel();
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
	
	@RequestMapping("/forum/delete")
	public String delete(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id	
	) {
		ForumModel am=new ForumModel();
		Map indata= new HashMap();
		indata.put("status", 11);
		am.update(indata, "id="+id);
		return Help.success(0, "删除成功");
	}
	
	
	
}

