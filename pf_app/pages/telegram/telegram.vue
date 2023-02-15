<template>
	<u-list class="telegram container" @scrolltolower="getListHandler">
		<u-navbar :title="$t('search')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view class="warp">
			<view class="telegram-search">
				<view class="telegram-search__input">
					<u-input v-model="search" :placeholder="$t('请输入搜索内容')" color="#ffffff" size="large" />
					<u-button :text="$t('search')" color="linear-gradient(to right, #9041FF, #E323FF)" @click="init" />
				</view>

			</view>
			<!-- <view class="sy-header">
				<view class="sy-header__inner">{{$t('menu.telegram')}}</view>
			</view> -->
			<sy-tabs class="article-header" type="custom" size="large" :data="config" @tabChange="tabChange"></sy-tabs>
			<view class="" v-if="active==4">
				<view class="telegram-list" v-if="list.length !== 0">
					<telegram-item v-for="item in list" :key="item.id" :data="item" />
					<sy-loadmore v-if="status !== 'none'" :status="status" />
				</view>
				<u-empty v-else style="margin-top: 100rpx;" :text="$t('empty')"/>
			</view>
			
			<view  v-if="active==2" style="margin-top: 30rpx;">
				<view class="ecology-list">
					<view class="ecology-list__item" v-for="(item, index) in list" :key="index">
						<encology-item :data="item" />
					</view>
				</view>
				<sy-loadmore style="margin-bottom: 120rpx;" v-if="status !== 'none'" :status="status" />		
				<u-empty v-else-if="list.length==0" style="margin-top: 100rpx;" :text="$t('empty')" />
			</view>
			
			<view v-if="active==3">
				<view v-if="list.length !== 0" class="article-list">
					<article-card type="Knowledge" :data="item" v-for="item in list" :key="item.id" />
					<sy-loadmore style="margin-bottom: 120rpx;" v-if="status !== 'none'" :status="status" />
				</view>
				<u-empty v-else style="margin-top: 100rpx;" :text="$t('empty')"/>
			</view>
			
			<view v-if="active==1">
				<view v-if="list.length !== 0" class="article-list">
					<article-item type="Information" :data="item" v-for="item in list"></article-item>
					<sy-loadmore style="margin-bottom: 120rpx;" v-if="status !== 'none'" :status="status" />
				</view>
				<u-empty v-else style="margin-top: 100rpx;" :text="$t('empty')"/>
			</view>
			
			
		</view>
	</u-list>
</template>

<script>
	import { mapState } from 'vuex';
	import { getTelegramListApi, getArticleDetailApi , news_getInformationList , news_getEcologyList , news_getKnowledgeList , news_getTelegramList } from '@/api/index.js';
	export default {
		data() {
			return {
				active:1,
				words: [
					{ word1: '中国', word2: 'China' },
					{ word1: '美国', word2: 'American' },
				],
				search: '',
				status: 'none',
				count: 0,
				list: [],
				page: 0,
				config: [
					{ key: 1, name: '资讯' },
					{ key: 2, name: '生态' },
					{
						key: 3,
						name: '知识',
					},
					{
						key: 4,
						name: '电报推荐'
					},
				],
			}
		},
		methods: {
			searchHandler(word) {
				this.search = word;
				this.init();
			},
			getListHandler() {
				let { search, list, page } = this;
				let api;
				if(!search.trim()) return
				if(this.active==2){
					api = news_getEcologyList
				}
				if(this.active==3){
					api = news_getKnowledgeList
				}
				if(this.active==4){
					api = getTelegramListApi
				}
				if(this.active==1){
					api = news_getInformationList
				}
				(this.status !== 'nomore') && api({ search, page: page + 1, listRows: 12,keywords:search }).then(({code, data:{ data, hot_data }, msg}) => {
					
					if (code == 1) {
						if(this.active==4){
							if (data.data.length == 0) {
								this.status = 'nomore';
							} else {
								this.page = page + 1;
								this.list = list.concat(data.data);
							}
						}else{
							if (data.length == 0) {
								this.status = 'nomore';
							} else {
								this.page = page + 1;
								this.list = list.concat(data);
							}
						}
					}
				})
			},
			init() {
				this.status = 'none';
				this.page = 0;
				this.list = [];
				this.getListHandler();
			},
			tabChange(item) {
				this.active = item.key;
				this.init();
			},
		},
		computed: {
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn'? 1 : 2;
			}
		},
		created() {
			this.init();
		}
	}
</script>

<style lang="scss" scoped>
	.ecology-list {
		margin: -16rpx;
		display: flex;
		flex-wrap: wrap;
		.ecology-list__item {
			flex: 0 0 33.33333333%;
			max-width: 33.33333333%;
			padding: 16rpx;
			box-sizing: border-box;
		}
	}
	
	.article-list {
		display: flex;
		flex-direction: column;
	}
	
	.telegram {
		.warp {
			padding-top: 170rpx;
			.telegram-search {
				.telegram-search__input {
					margin-bottom: 16rpx;
					display: flex;
					.u-input {
						border-top-right-radius: 0;
						border-bottom-right-radius: 0;
					}
					.u-button {
						width: 180rpx;
						height: auto;
						border-top-left-radius: 0;
						border-bottom-left-radius: 0;
					}
					.u-border {
						border-color: transparent!important;
					}
				}
				.telegram-search__words {
					margin-bottom: 32rpx;
					display: flex;
					font-size: 28rpx;
					line-height: 1;
					color: #FFFFFF;
					.word-item {
						margin-right: 8rpx;
					}
				}
			}
			.sy-header {
				margin-bottom: 0;
			}	
		}
	}
</style>
