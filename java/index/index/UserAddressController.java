
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
import com.deitui.morelang.index.model.UserAddressModel;
import com.model.Help;

@RestController
@CrossOrigin("*")
public class UserAddressController {
	
	@RequestMapping("/user_address/index")
	public String Index(
		@RequestParam(value="per_page",defaultValue="0") int per_page,
		@RequestParam(value="limit",defaultValue="0") int limit
	) {
		UserAddressModel am=new UserAddressModel();
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
	
	@RequestMapping("/user_address/show")
	public String Show(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		UserAddressModel am=new UserAddressModel();
		Map data=am.where("id="+id).selectRow();
			
		Map<String,Object> redata=new HashMap<String,Object>();
        redata.put("error",0);
        redata.put("message","succcess");
        redata.put("data", data);
        return JSON.toJSONString(redata);
	}
	
	@RequestMapping("/user_address/add")
	public String Add(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		UserAddressModel am=new UserAddressModel();
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
	
	@RequestMapping("/user_address/save")
	public String Save(
		@RequestParam(value="token",defaultValue="") String token,
		@RequestParam(value="id",defaultValue="0") int id,
@RequestParam(value="userid",defaultValue="0") int userid,
@RequestParam(value="address",defaultValue="") String address,
@RequestParam(value="telephone",defaultValue="") String telephone,
@RequestParam(value="truename",defaultValue="") String truename,
@RequestParam(value="status",defaultValue="0") int status,
@RequestParam(value="zip_code",defaultValue="") String zip_code,
@RequestParam(value="lastid",defaultValue="0") int lastid,
@RequestParam(value="province_id",defaultValue="0") int province_id,
@RequestParam(value="city_id",defaultValue="0") int city_id,
@RequestParam(value="town_id",defaultValue="0") int town_id,
@RequestParam(value="pct_address",defaultValue="") String pct_address,
@RequestParam(value="lat",defaultValue="0") Double lat,
@RequestParam(value="lng",defaultValue="0") Double lng
	) {
		Map indata= new HashMap();
		indata.put("id", id);
indata.put("userid", userid);
indata.put("address", address);
indata.put("telephone", telephone);
indata.put("truename", truename);
indata.put("status", status);
indata.put("zip_code", zip_code);
indata.put("lastid", lastid);
indata.put("province_id", province_id);
indata.put("city_id", city_id);
indata.put("town_id", town_id);
indata.put("pct_address", pct_address);
indata.put("lat", lat);
indata.put("lng", lng);

		UserAddressModel am=new UserAddressModel();
		if(id==0) {
			am.insert(indata);
		}else {
			am.update(indata, "id="+id);
		}
		return Help.success(0, "保存成功");
	}
	
	@RequestMapping("/user_address/status")
	public String Status(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		UserAddressModel am=new UserAddressModel();
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
	
	@RequestMapping("/user_address/recommend")
	public String recommend(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id
	) {
		UserAddressModel am=new UserAddressModel();
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
	
	@RequestMapping("/user_address/delete")
	public String delete(
			@RequestParam(value="token",defaultValue="") String token,
			@RequestParam(value="id",defaultValue="0") int id	
	) {
		UserAddressModel am=new UserAddressModel();
		Map indata= new HashMap();
		indata.put("status", 11);
		am.update(indata, "id="+id);
		return Help.success(0, "删除成功");
	}
	
	
	
}

