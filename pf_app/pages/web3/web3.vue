<!-- 
@author x 
@title web3列表
 -->
<template>
	<view class="web3">
		<u-header title="Web3" :showBack="showBack">
			<template>
				<u-icon :size="22" name="search" color="#fff" @click="goSearch"></u-icon>
			</template>
		</u-header>
		<view class="u-sticky">
			<u-search color="#ffffff" bgColor="#323256" searchIconColor="#C4C4C4" borderColor="#404065"
				placeholderColor="rgba(255,255,255,0.8)" :placeholder="$t('searchPlace')" height="80" shape="square"
				:showAction="searchWords!=''" v-model="searchWords" border-color @custom="search"></u-search>
			<!-- 分类 -->
			<view class="category">
				<view v-for="(item,key) in categorylist" :item="item" class="item" @click="categoryClick(item.id,key)">
					<image class="bg" :src="baseUrl+item.bg_image" mode="aspectFill"></image>
					<text class="name">{{item.typename}}</text>
				</view>
			</view>
		</view>
		<!-- <u-list :showScrollbar="false" @scrolltolower="scrolltolower"> -->
		<view class="list">
			<view v-for="(item,key) in list">
				<web3-item @Detail="getDetail" :item="item"></web3-item>
			</view>
		</view>
		<view class="tips">
			<text>{{$t('没有更多')}}</text>
		</view>
		<!-- </u-list> -->
	</view>
</template>

<script>
	import {
		typelist,
		getWebList
	} from '@/api/web3'
	import {
		baseUrl
	} from "@/api/http.js";
	export default {
		data() {
			return {
				showBack: false,
				list: [],
				searchWords: '',
				typeId: null, //分类id
				page: 1,
				limit: 10,
				categorylist: [],
				baseUrl
			}
		},
		watch: {
			typeId() {
				this.search();
			}
		},
		created() {
			this.typelist();
			this.getWebList();
		},
		onLoad(options) {
			this.showBack = options.showBack;
		},
		onReachBottom() {
			//触底加载
			this.scrolltolower();
		},
		methods: {
			getDetail(e) {
				uni.navigateTo({
					url: '/pages/web3/detail?id=' + e + '&type=0'
				})
			},
			/* 分类点击 */
			categoryClick(id, i) {
				if (i < 3) {
					this.typeId = id;
				} else {
					if (i == 4) {
						uni.navigateTo({
							url: '/pages/jiaoyisuo/jiaoyisuo'
						})
					} else {
						uni.navigateTo({
							url: '/pages/article/knowledge?type=' + i
						})
					}
				}
			},
			/* 前往搜索 */
			goSearch() {
				uni.navigateTo({
					url: '/pages/search/search'
				})
			},
			search() {
				this.page = 1;
				this.getWebList();
			},
			/* 分类列表 */
			typelist() {
				typelist().then(res => {
					this.categorylist = res.data;
				})
			},
			/* 滚动到底部 */
			scrolltolower() {
				this.page++;
				this.getWebList();
			},
			/* 数据列表 */
			getWebList() {
				getWebList({
					type_id: this.typeId,
					page: this.page,
					limit: this.limit,
					keyword: this.searchWords
				}).then(res => {
					if (this.page == 1) {
						this.list = [];
					}
					if (res.data.list.length <= 0) {
						return
					}
					if (this.page == 1) {
						this.list = res.data.list;
					} else {
						this.list = this.list.concat(res.data.list);
					}
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.web3 {
		width: 100%;

		.u-sticky {
			padding: 0 30rpx;
			background-color: #000000;
		}

		.category {
			padding: 30rpx 0;
			display: grid;
			grid-template-columns: repeat(3, 33fr);
			grid-gap: 20rpx;
			background-color: $main-bg-color;

			.item {
				position: relative;
				height: 90rpx;
				display: flex;
				align-items: center;
				justify-content: center;
				color: #fff;
				font-size: 32rpx;
				letter-spacing: 2px;
				border-radius: 2px;
				overflow: hidden;

				.bg {
					position: absolute;
					width: 100%;
					height: 100%;
					z-index: 0;
				}

				.name {
					position: relative;
					z-index: 1;
					overflow: hidden;
					/*超出部分隐藏*/
					text-overflow: ellipsis;
					/*超出部分省略号表示*/
					white-space: nowrap;
					/*强制单行显示*/
					display: inline-block;
					/*转换为行内块元素*/
					color: #fff;
					width: 200rpx;
					display: block;
					text-align: center;
				}
			}
		}

		.list {
			padding: 0 30rpx;
			overflow: hidden;
		}
	}

	.tips {
		padding: 32rpx 0;
		color: #D0D0D6;
		text-align: center;
		font-size: 24rpx;
	}
</style>
