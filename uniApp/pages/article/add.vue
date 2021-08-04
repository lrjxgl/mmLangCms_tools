<template>
	<view>
	<div class="tabs-border">
			<div @click="gourl('index')" class="item">列表</div>
			<div @click="gourl('add')" class="item active">添加</div>
		</div>
<form @submit="submit">
<input type="text" name="id" class="none" v-model="data.id" >
 <table class="table-add">  <tr>
						<td>标题：</td>		
						<td><input class="input-text" type="text" name="title"   v-model="data.title" ></td>
						</tr>
  <tr>
						<td>分类：</td>		
						<td><input class="input-text" type="text" name="catid"   v-model="data.catid" ></td>
						</tr>
  <tr>
						<td>一级分类：</td>		
						<td><input class="input-text" type="text" name="catid_top"   v-model="data.catid_top" ></td>
						</tr>
  <tr>
						<td>二级分类：</td>		
						<td><input class="input-text" type="text" name="catid_2nd"   v-model="data.catid_2nd" ></td>
						</tr>
  <tr>
						<td>喜欢数：</td>		
						<td><input class="input-text" type="text" name="love_num"   v-model="data.love_num" ></td>
						</tr>
  <tr>
						<td>收藏数：</td>		
						<td><input class="input-text" type="text" name="fav_num"   v-model="data.fav_num" ></td>
						</tr>
  <tr>
						<td>描述：</td>		
						<td><input class="input-text" type="text" name="description"   v-model="data.description" ></td>
						</tr>
  <tr>
						<td>状态：</td>		
						<td>
						<input type="text" class="none" name="status" v-model="data.status" />
						<radio-group>
							<radio type="text" class="mgr-5" checked="data.status==1"  value="1" > 上线</radio>
							<radio type="text" checked="data.status!=1"    value="2" > 下线</radio>
						</radio-group>	
						</td>
						</tr>
  <tr>
						<td>是否推荐：</td>		
						<td>
						<input type="text" class="none" name="isrecommend" v-model="data.isrecommend" />
						<radio-group>
							<radio type="text" class="mgr-5" checked="data.isrecommend==1"  value="1" > 是</radio>
							<radio type="text" class="mgr-5" checked="data.isrecommend!=1"    value="0" > 否</radio>
						</radio-group>	
						</td>
						</tr>

					<tr>
							<td>图片：</td>
							<td>
								<input class="none" type="text" name="imgurl" v-model="data.imgurl" />
								<sky-upimg @call-parent="setImgurl" :imgurl="data.imgurl" :trueimgurl="data.trueimgurl">
								</sky-upimg>
							</td>
						</tr>
				
  <tr>
						<td>创建时间：</td>		
						<td><input class="input-text" type="text" name="createtime"   v-model="data.createtime" ></td>
						</tr>
  <tr>
						<td>评论数：</td>		
						<td><input class="input-text" type="text" name="comment_num"   v-model="data.comment_num" ></td>
						</tr>
  <tr>
						<td>tpl：</td>		
						<td><input class="input-text" type="text" name="tpl"   v-model="data.tpl" ></td>
						</tr>
  <tr>
						<td>author：</td>		
						<td><input class="input-text" type="text" name="author"   v-model="data.author" ></td>
						</tr>
  <tr>
						<td>最新：</td>		
						<td>
						<input type="text" class="none" name="isnew" v-model="data.isnew" />
						<radio-group>
							<radio type="text" class="mgr-5" checked="data.isnew==1"  value="1" > 是</radio>
							<radio type="text" class="mgr-5" checked="data.isnew!=1"    value="0" > 否</radio>
						</radio-group>	
						</td>
						</tr>
  <tr>
						<td>最热：</td>		
						<td><input class="input-text" type="text" name="ishot"   v-model="data.ishot" ></td>
						</tr>
  <tr>
						<td>访问数：</td>		
						<td><input class="input-text" type="text" name="view_num"   v-model="data.view_num" ></td>
						</tr>
  <tr>
						<td>产品价格：</td>		
						<td><input class="input-text" type="text" name="price"   v-model="data.price" ></td>
						</tr>
  <tr>
						<td>市场价格：</td>		
						<td><input class="input-text" type="text" name="market_price"   v-model="data.market_price" ></td>
						</tr>
  <tr>
						<td>库存数：</td>		
						<td><input class="input-text" type="text" name="total_num"   v-model="data.total_num" ></td>
						</tr>
  <tr>
						<td>销售数：</td>		
						<td><input class="input-text" type="text" name="sold_num"   v-model="data.sold_num" ></td>
						</tr>
  <tr>
						<td>grade：</td>		
						<td><input class="input-text" type="text" name="grade"   v-model="data.grade" ></td>
						</tr>
  <tr>
						<td>下载地址：</td>		
						<td><input class="input-text" type="text" name="downurl"   v-model="data.downurl" ></td>
						</tr>
  <tr>
						<td>downsize：</td>		
						<td><input class="input-text" type="text" name="downsize"   v-model="data.downsize" ></td>
						</tr>
  <tr>
						<td>orderindex：</td>		
						<td><input class="input-text" type="text" name="orderindex"   v-model="data.orderindex" ></td>
						</tr>
  <tr>
						<td>videourl：</td>		
						<td><input class="input-text" type="text" name="videourl"   v-model="data.videourl" ></td>
						</tr>
  <tr>
						<td>是否首页：</td>		
						<td><input class="input-text" type="text" name="isindex"   v-model="data.isindex" ></td>
						</tr>
  <tr>
						<td>tags：</td>		
						<td><input class="input-text" type="text" name="tags"   v-model="data.tags" ></td>
						</tr>
  <tr>
						<td>图集：</td>		
						<td><input class="input-text" type="text" name="imgsdata"   v-model="data.imgsdata" ></td>
						</tr>
  <tr>
						<td>updatetime：</td>		
						<td><input class="input-text" type="text" name="updatetime"   v-model="data.updatetime" ></td>
						</tr>
</table> <button form-type="submit" class="btn-row-submit">保存</button> 
</form>
	</view>
</template>
<script>
	import upimgBox from "../../components/upimgbox.vue";
	import skyUpimg from "../../components/skyupimg.vue";
	export default {
		components: {
			upimgBox,
			skyUpimg
		},
		data: function() {
			return {
				'.id.': 0,
				pageLoad: false,
				data: {},
				imgsList: []
			}
		},
		onLoad: function(ops) {
			if (ops.'.id.' != undefined) {
				this.'.id.' = ops.id;
			}
			console.log("onLoad")
			this.getPage();
		},
		methods: {
			gourl: function(url) {
				uni.navigateTo({
					url: url
				})
			},
			setImgs: function(e) {
				this.data.imgsdata = e;
			},
			setImgurl: function(e) {
				this.data.imgurl = e;
			},
			getPage: function() {
				var that = this;
				that.app.get({
					url: that.app.apiHost + "/admin/article/add",
					data: {
						'.id.': this.'.id.'
					},
					success: function(res) {
						if (Object.keys(res.data).length > 0) {
							that.data = res.data;
							that.imgsList = res.imgsdata;
						} else {
							that.data = {
								
id: "0",
title: "",
catid: "0",
catid_top: "0",
catid_2nd: "0",
love_num: "0",
fav_num: "0",
description: "",
status: "0",
isrecommend: "0",
imgurl: "",
createtime: "",
comment_num: "0",
tpl: "",
author: "",
isnew: "0",
ishot: "0",
view_num: "0",
price: "0",
market_price: "0",
total_num: "0",
sold_num: "0",
grade: "0",
downurl: "",
downsize: "0",
orderindex: "0",
videourl: "",
isindex: "0",
tags: "",
imgsdata: "",
updatetime: "",

							}
						}

						that.pageLoad = true;
					}
				})
			},
			submit: function(e) {
				var that = this;
				that.app.post({
					url: that.app.apiHost + "/admin/article/save",
					data: e.detail.value,
					success: function(res) {
						uni.showToast({
							title: res.message
						})
					}
				})

			}
		}
	}
</script>

<style>
</style>