<!-- 
@author x 
@title pinetwork列表
 -->
<template>
	<view class="pinetwork">
		<u-header title="Pinetwork"></u-header>
		<view style="padding: 32rpx;">
			<u-search color="#ffffff" bgColor="#323256" searchIconColor="#C4C4C4" borderColor="#404065"
				placeholderColor="rgba(255,255,255,0.8)" :placeholder="$t('searchPlace')" height="80" shape="square"
				:showAction="false" disabled border-color @click="searchHnadler"></u-search>
			<!-- 分类 -->
			<view class="category">
				<!-- <view class="item" @click="categoryClick('新闻')">
					<image class="icon" src="../../static/img/pinetwork/news.png" mode="heightFix"></image>
					<text>新闻</text>
				</view> -->
				<view class="item" @click="categoryClick('商家')">
					<image class="icon" src="../../static/img/pinetwork/business.png" mode="heightFix"></image>
					<text class="typename-tt">{{$t('商家')}}</text>
				</view>
				<view class="item" @click="categoryClick('地图')">
					<image class="icon" src="../../static/img/pinetwork/map.png" mode="heightFix"></image>
					<text class="typename-tt">{{$t("地图")}}</text>
				</view>
				<view class="item" @click="categoryClick('生态')">
					<image class="icon" src="../../static/img/pinetwork/shengtai.png" mode="heightFix"></image>
					<text class="typename-tt">{{$t('生态')}}</text>
				</view>
				<!-- 	<view class="item" @click="categoryClick('教程')">
					<image class="icon" src="../../static/img/pinetwork/teach.png" mode="heightFix"></image>
					<text>教程</text>
				</view> -->
				<view v-for="(item,key) in categorylist" :key="key" class="item" @click="goPinetworkDetail(item)">
					<image class="icon" :src="baseUrl+item.bg_image" mode="heightFix"></image>
					<text class="typename-tt">{{item.typename}}</text>
				</view>
				<view class="item" @click="categoryClick('上级')">
					<image class="icon" src="../../static/img/pinetwork/shangji.png" mode="heightFix"></image>
					<text class="typename-tt">{{$t('上级')}}</text>
				</view>
				<view class="item" @click="categoryClick('浏览')">
					<image class="icon" src="../../static/img/pinetwork/watching.png" mode="heightFix"></image>
					<text class="typename-tt">{{$t('浏览')}}</text>
				</view>
				<view class="item" @click="categoryClick('节点')">
					<image class="icon" src="../../static/img/pinetwork/node.png" mode="heightFix"></image>
					<text class="typename-tt">{{$t('节点')}}</text>
				</view>
			</view>
			<!-- 新闻 -->
			<view class="label" @click="newsClick">
				<view class="label-content">
					<image class="icon" src="../../static/news/2.png"></image>
					<text class="label-tt">{{$t('新闻区')}}</text>
					<text class="label-tt1" v-if="localeIndex==1">点击获取更多新闻资讯</text>
					<text class="label-tt2" v-else>Click to get more news</text>
					<image class="icon1" src="../../static/news/5.png" mode=""></image>
				</view>
			</view>
			<!-- 推荐的新闻列表 -->
			<view style="margin-bottom: 32rpx;">
				<news-item v-for="(item,key) in newlist" :item="item" :key="key"></news-item>
			</view>
			<!-- #ifdef H5 -->
			<sy-map @mapClick="goMap" el="homeMap" type="simple" height="400rpx"></sy-map>
			<!-- #endif -->
			<!-- #ifdef APP-PLUS -->
			<sy-map-app el="homeMap" type="simple" height="400rpx"></sy-map-app>
			<!-- #endif -->
			<view class="label" @click="goShop">
				<view class="label-content">
					<image class="icon" src="../../static/news/3.png"></image>
					<text class="label-tt-shop">{{$t('商家')}}</text>
					<text class="label-tt1-shop" v-if="localeIndex==1">点击获取更多商家信息</text>
					<text class="label-tt2-shop" v-else>Click to get more store message</text>
				</view>
			</view>
			<!-- 推荐的商家列表 -->
			<view class="shops">
				<shop-item v-for="(item,key) in shoplist" :item="item" :key="key"></shop-item>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';
	import {
		typelist,
		getPinetworkList
	} from '@/api/pinetwork'
	import {
		getMechantList,
	} from '@/api/shop'
	import {
		baseUrl
	} from "@/api/http.js";
	import {
		getArticleListApi
	} from '@/api/index.js'
	export default {
		data() {
			return {
				searchWords: '', //搜索关键词
				categorylist: [],
				shoplist: [], //商家列表
				newlist: [], //新闻列表
				baseUrl,
			}
		},
		created() {
			this.typelist();
			this.getMechantList();
		},
		onLoad() {
			this.getArticleListApi(1);
		},
		computed: {
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn' ? 1 : 2;
			},
		},
		methods: {
			goMap() {
				uni.navigateTo({
					url: "/pages/map/index"
				})
				// this.$emit('changeTab', 'map') //跳转地图页
			},
			/* 资讯 */
			newsClick() {
				uni.navigateTo({
					url: '/pages/article/information'
				})
			},
			tabChange(item) {
				this.getArticleListApi(item.key);
			},
			categoryClick(item) {
				if (item == '商家') {
					uni.navigateTo({
						// url: '/pages/shoplist/shoplist',
						url: '/pages/shop-search/shop-search',
					})
				} else if (item == '地图') {
					uni.navigateTo({
						// url: '/pages/index/index?tab=map&index=map'
						url: '/pages/map/index'
					})
				} else if (item == '新闻') {
					uni.navigateTo({
						url: '/pages/web3/web3'
					})
				} else if (item == '上级') {
					uni.navigateTo({
						url: '/pages/labelComp/labelComp'
					})
				} else if (item == '生态') {
					uni.navigateTo({
						url: '/pages/ecology/index'
					})
				} else if (item == '教程') {
					uni.navigateTo({
						url: '/pages/article/knowledge'
					})
				} else if (item == '浏览') {
					window.location.href = "https://minepi.com/blockexplorer";
				} else if (item == '节点') {
					uni.navigateTo({
						url: '/pages/shuju/shuju'
					})
				}
			},
			goPinetworkDetail(item) {
				let title = '查询中'
				if (this.localeIndex == 2) {
					title = 'searching'
				}
				uni.showLoading({
					title: title,
					mask: true
				})
				this.getArticleListApi(item.id)
			},
			/* 分类列表 */
			typelist() {
				typelist().then(res => {
					this.categorylist = res.data;
				})
			},
			/* 数据列表 */
			getArticleListApi(type) {
				getArticleListApi('Information', {
					type: type,
					page: 1,
					listRows: 12
				}).then(res => {
					uni.hideLoading();
					this.newlist = res.data.data.data;
				})
			},
			/* 商家列表 */
			getMechantList() {
				getMechantList({
					page: 1,
					limit: 10
				}).then(res => {
					this.shoplist = res.data.list;
				})
			},
			/* 前往商家列表 */
			goShop() {
				uni.navigateTo({
					url: '/pages/shoplist/shoplist?showBack=true'
				})
			},
			/* 搜索 */
			searchHnadler() {
				uni.navigateTo({
					url: '/pages/search/search'
				})
			},
			/* 前往新闻列表 */
			goWeb3() {
				uni.navigateTo({
					url: '/pages/web3/web3'
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	/* 新闻 */
	.news {
		color: #fff;

		.news-title {
			display: flex;
			align-items: center;
			justify-content: space-between;

			.t {
				font-weight: bold;
				letter-spacing: 2px;
			}

			.more {
				color: #D0D0D6;
				font-size: 28rpx;
			}
		}
	}


	.category {
		margin-top: 32rpx;
		display: grid;
		grid-template-columns: repeat(5, 20fr);
		grid-gap: 24rpx;

		.item {
			padding: 24rpx 0;
			display: flex;
			flex-direction: column;
			align-items: center;
			color: #fff;
			font-size: 32rpx;
			background-color: #14142B;
			border-radius: 2px;

			.icon {
				margin-bottom: 4rpx;
				height: 40rpx;
			}


			.typename-tt {
				overflow: hidden;
				/*超出部分隐藏*/
				text-overflow: ellipsis;
				/*超出部分省略号表示*/
				white-space: nowrap;
				/*强制单行显示*/
				display: inline-block;
				/*转换为行内块元素*/
				color: #fff;
				width: 120rpx;
				display: block;
				text-align: center;
				margin-top: 10rpx;
			}
		}
	}

	.label {
		margin-top: 32rpx;
		font-size: 30rpx;

		.label-content {
			position: relative;

			.icon {
				width: 100%;
				height: 80rpx;
				z-index: 1;
			}

			.icon1 {
				height: 36rpx;
				width: 24rpx;
				position: absolute;
				z-index: 2;
				right: 30rpx;
				line-height: 40rpx;
				top: 22rpx;
			}

			.label-tt {
				color: #fff;
				position: absolute;
				z-index: 2;
				left: 40rpx;
				height: 80rpx;
				line-height: 80rpx;
				font-size: 32rpx;
			}

			.label-tt1 {
				color: #fff;
				position: absolute;
				z-index: 2;
				right: 140rpx;
				height: 80rpx;
				line-height: 80rpx;
				letter-spacing: 4rpx;
			}

			.label-tt2 {
				color: #fff;
				position: absolute;
				z-index: 2;
				right: 100rpx;
				height: 80rpx;
				line-height: 80rpx;
				letter-spacing: 2rpx;
			}

			.label-tt-shop {
				color: #fff;
				position: absolute;
				z-index: 2;
				left: 20rpx;
				height: 80rpx;
				line-height: 80rpx;
				font-size: 32rpx;
			}

			.label-tt1-shop {
				color: #fff;
				position: absolute;
				z-index: 2;
				left: 170rpx;
				height: 80rpx;
				line-height: 80rpx;
				letter-spacing: 4rpx;
			}

			.label-tt2-shop {
				color: #fff;
				position: absolute;
				z-index: 2;
				left: 150rpx;
				height: 80rpx;
				line-height: 80rpx;
				letter-spacing: 2rpx;
			}
		}


	}
</style>
