<template>
	<view class="article container">
		<u-navbar :title="$t('menu.information')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<pinetwork-tabbar class="article-header" type="custom" size="large" :data="categorylist" @tabChange="tabChange"></pinetwork-tabbar>
		<!-- <sy-tabs class="article-header" type="custom" size="large" :data="categorylist" @tabChange="tabChange"></sy-tabs> -->
		<u-list class="article-warp" @scrolltolower="getListHandler">
			<view v-if="list.length !== 0" class="article-list">
				<article-card :data="item" v-for="item in list" :key="item.id" />
				<sy-loadmore style="margin-bottom: 60rpx;" v-if="status !== 'none'" :status="status" />
			</view>
			<u-empty v-else style="margin-top: 100rpx;" :text="$t('empty')"/>
		</u-list>
	</view>
</template>

<script>
	import {
		typelist
	} from '@/api/pinetwork'
	import { mapState } from 'vuex';
	import { getArticleListApi } from '@/api/index.js'
	export default {
		data() {
			return {
				active: 1,
				config: [
					{ key: 1, name: 'article.pinetwork' },
					{ key: 2, name: 'article.cryptocurrency' },
					{
						key: 3,
						name: 'Pi数据',
						url:"/pages/shuju/shuju"
					},
					{
						key: 4,
						name: '免费'
					},
				],
				piData:{
					id: 0,
					typename: 'Pi数据',
					url:"/pages/shuju/shuju"
				},
				categorylist:[],
				status: 'none',
				list: [],
				page: 0
			}
		},
		methods: {   
			/* 分类列表 */
			typelist() {
				typelist().then(res => {
					let tt={
						id: 0,
						typename: 'Pi数据',
						url:"/pages/shuju/shuju"
					};
					let data=res.data;
					data.push(this.piData);
					this.categorylist=data
				})
			},
			getListHandler() {          
				const self = this;
				let { list, page } = this;
				(this.status !== 'nomore') && getArticleListApi('Information' ,{ type: this.active, page: page + 1, listRows: 12 }).then(({code, data:{ data, hot_data }, msg}) => {
					if (code == 1) {
						if (data.data.length == 0) {
							self.status = 'nomore';
						} else {
							self.page = page + 1;
							self.list = list.concat(data.data);
						}
					} else {
						uni.$u.toast(msg);
					}
				})
			},
			tabChange(item) {
				this.active = item.id;
				this.init();
			},
			init() {
				this.status = 'none';
				this.page = 0;
				this.list = [];
				this.getListHandler();
			}
		},
		computed: {
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn'? 1 : 2;
			},
		},
		created() {
			this.init();
			this.typelist();
		},
	}
</script>

<style lang="scss" scoped>
	.article {
		//padding-top: calc(64rpx + var(--status-bar-height));
		padding-top: 170rpx;
		padding-left: 40rpx;
		padding-right: 40rpx;
		padding-bottom: 50px;
		box-sizing: border-box;
		.article-warp {
			margin-top: 32rpx;
			height: calc(100vh - 50px - 154rpx)!important;
		}
		.article-list {
			display: flex;
			flex-direction: column;
		}
	}
</style>
