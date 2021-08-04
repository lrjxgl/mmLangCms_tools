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
					<input class="w100 mgr-5 input-text" type="text" name="id" v-model="id" />
					<text>状态：</text>
					<select v-model="type" class="w100 mgr-5">
						<option value="all">全部</option>
						<option value="new">未审核</option>
						<option value="pass">已审核</option>
						<option value="forbid">已禁止</option>
					</select>
					
					
					

					<button form-type="submit" class="btn">搜索</button>
					<div class="flex-1"></div>
				</div>
			</form>
		</div>
		 <table class="tbs">
<thead>  <tr>
   <td>id</td>
   <td>orderindex</td>
   <td>k</td>
<td>操作</td></tr>
  </tr>
</thead> <tr v-for="(item,index) in list" :key="index">
   <td>{{item.id}}</td>
   <td>{{item.orderindex}}</td>
   <td>{{item.k}}</td>
<td>
	<div class="btn-small mgr-5" @click="goAdd(item.id)">编辑</div>

	 
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
					url: that.app.apiHost + "/admin/queue/index",
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
					url: that.app.apiHost + "/admin/queue/index",
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
					url:that.app.apiHost+"/admin/queue/status",
					data:{
						id:item.id
					},
					success:function(res){
						item.status=res.status;
					}
				})
			},
			toggleRecommend:function(item){
				var that=this;
				that.app.get({
					url:that.app.apiHost+"/admin/queue/recommend",
					data:{
						id:item.id
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
							url:that.app.apiHost+"/admin/queue/delete",
							data:{
								id:item.id
							},
							success:function(res){
								if(res.error){
									return false;
								}
								var list=[];
								for(var i in that.list){
									if(that.list[i].id!=item.id){
										list.push(that.list[i]);
									}
								}
								that.list=list;
							}
						})
					}
				})
			},
			goAdd:function(id){
				uni.navigateTo({
					url:"add?id="+id
				})
			},
			goShow:function(id){
				uni.navigateTo({
					url:"show?id="+id
				})
			},
			search: function(e) {
				var that = this;
				
				that.app.get({
					url: that.app.apiHost + "/admin/queue/index",
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