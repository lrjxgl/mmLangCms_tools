<template>
	<view>
		<div class="tabs-border">
			<div @click="gourl('index')" class="item active">列表</div>
			<div @click="gourl('add')" class="item">添加</div>
		</div>
		<div class="search-form">
			<form @submit="search">
				<div class="flex flex-ai-center">
					<div class="none">
						<input type="text" name="recommend" v-model="recommend" />
						<input type="text" name="type" v-model="type" />
					</div>

					<text class="mgr-5">ID:</text>
					<input class="w100 mgr-5 input-text" type="text" name="catid" v-model="id" />
					<text>状态：</text>
					<select v-model="type" class="w100 mgr-5">
						<option value="all">全部</option>
						<option value="new">未审核</option>
						<option value="pass">已审核</option>
						<option value="forbid">已禁止</option>
					</select>
					<text class="mgr-5">分类：</text>
					<input type="text" class="none" name="catid" v-model="catid" />
					<select v-model="catid"  class="input-flex-select mgr-5 w150">
						<option value="0">请选择</option>

						<block v-for="(one,oi) in catList" :key="one.catid">
							<option :value="one.catid">{{one.cname}}</option>
							<block v-for="(two,bi) in one.child" :key="two.catid">
								<option :value="two.catid">|--{{two.cname}}</option>
								<block v-for="(three,ci) in two.child" :key="three.catid">
									<option :value="three.catid">|----{{three.cname}}</option>
								</block>
							</block>
						</block>

					</select>
					<text class="mgr-5">主题：</text>
<input type="text" class="w150 mgr-5 input-text" name="title" v-model="title" />
					

					<button form-type="submit" class="btn">搜索</button>
					<div class="flex-1"></div>
				</div>
			</form>
		</div>
		 <table class="tbs">
<thead>  <tr>
   <td>catid</td>
   <td>表名</td>
   <td>上级分类</td>
   <td>名称</td>
   <td>排序</td>
   <td>类型</td>
   <td>栏目模板</td>
   <td>列表模板</td>
   <td>详情模板</td>
<td>操作</td></tr>
  </tr>
</thead> <tr v-for="(item,index) in list" :key="index">
   <td>{{item.catid}}</td>
   <td>{{item.tablename}}</td>
   <td>{{item.pid}}</td>
   <td>{{item.cname}}</td>
   <td>{{item.orderindex}}</td>
   <td>{{item.type_id}}</td>
   <td>{{item.cat_tpl}}</td>
   <td>{{item.list_tpl}}</td>
   <td>{{item.show_tpl}}</td>
<td>
	<div class="btn-small mgr-5" @click="goAdd(item.catid)">编辑</div>

	 
					<div class="btn-small btn-danger" @click="del(item)">删除</div>
</td>
  </tr>
 </table>

		<div class="flex row-box">
				<div :class="item.per_page==aPage?'cl-red':''" class="pd-10 pointer" v-for="(item,index) in pageList" @click="setPage(item.per_page)" :key="index">{{item.value}}</div>
			</div>
	</view>
</template>
<script>
	export default {
		data: function() {
			return {
				pageLoad: false,
				list: [],
				per_page: 0,
				isFirst: true,
				pageList:[],
				aPage:0
			}
		},
		onLoad: function() {
			this.getPage();
		},
		onReachBottom: function() {
			this.getList();
		},
		onPullDownRefresh: function() {
			this.getPage();
			uni.stopPullDownRefresh();
		},
		onShareAppMessage: function() {

		},
		onShareTimeline: function() {

		},
		methods: {
			gourl: function(url) {
				uni.navigateTo({
					url: url
				})
			},
			setPage:function(per_page){
				 
				var that=this;
				that.aPage=per_page;
				that.per_page=per_page;
				that.isFirst=true;
				that.getList();
			}, 
			getPage: function() {
				var that = this;
				that.app.get({
					url: that.app.apiHost + "/admin/category/index",
					success: function(res) {
						that.pageLoad = true;
						that.list = res.list;
						that.per_page = res.per_page;
						that.pageList=that.app.pageList(res.rscount,res.limit,res.per_page);
					}
				})
			},
			getList: function() {
				var that = this;
				if (that.per_page == 0 && !that.isFirst) {
					return false;
				}
				that.app.get({
					url: that.app.apiHost + "/admin/category/index",
					data: {
						per_page: that.per_page
					},
					success: function(res) {
						that.per_page = res.per_page;
						if (that.isFirst) {
							that.list = res.list;
							that.isFirst = false;
						} else {
							for (var i in res.list) {
								that.list.push(res.list[i]);
							}
						}

					}
				})
			},
			toggleStatus:function(item){
				var that=this;
				that.app.get({
					url:that.app.apiHost+"/admin/category/status",
					data:{
						catid:item.catid
					},
					success:function(res){
						item.status=res.status;
					}
				})
			},
			toggleRecommend:function(item){
				var that=this;
				that.app.get({
					url:that.app.apiHost+"/admin/category/recommend",
					data:{
						catid:item.catid
					},
					success:function(res){
						item.is_recommend=res.is_recommend;
					}
				})
			},
			del:function(item){
				var that=this;
				uni.showModal({
					content:"确认删除吗",
					success:function(res){
						if(!res.confirm){
							return false;
						}
						that.app.get({
							url:that.app.apiHost+"/admin/category/delete",
							data:{
								catid:item.catid
							},
							success:function(res){
								if(res.error){
									return false;
								}
								var list=[];
								for(var i in that.list){
									if(that.list[i].catid!=item.catid){
										list.push(that.list[i]);
									}
								}
								that.list=list;
							}
						})
					}
				})
			},
			goAdd:function(catid){
				uni.navigateTo({
					url:"add?catid="+catid
				})
			},
			goShow:function(id){
				uni.navigateTo({
					url:"show?catid="+id
				})
			},
			search: function(e) {
				var that = this;
				
				that.app.get({
					url: that.app.apiHost + "/admin/category/index",
					data: e.detail.value,
					success:function(res) {
						that.list = res.list;
					}
				})
			}
		},
	}
</script>

<style>
</style>