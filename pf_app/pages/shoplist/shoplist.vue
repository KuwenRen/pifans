<!-- 
@author x 
@title 商家列表
 -->
<template>
	<view class="shoplist">
		<u-header :showBack="showBack" :title="$t('商家')">
			<template>
				<image @click="goSearch" style="width: 40rpx;height: 40rpx;" src="../../static/img/sx.png"></image>
				<!-- <u-icon :size="22" name="search" color="#fff" @click="goSearch"></u-icon> -->
			</template>
		</u-header>

		<u-list @scrolltolower="scrolltolower">
			<view style="padding: 0 30rpx;">
				<!-- #ifdef H5 -->
				<sy-map @mapClick="goMap" el="homeMap" type="simple" height="400rpx"></sy-map>
				<!-- #endif -->
				<!-- #ifdef APP-PLUS -->
				<sy-map-app el="homeMap" type="simple" height="400rpx"></sy-map-app>
				<!-- #endif -->
			</view>
			<view style="padding: 0 30rpx;">
				<template v-for="(item,key) in list">
					<shop-item :item="item"></shop-item>
				</template>
				<view class="tips">
					<text>没有更多了~</text>
				</view>
			</view>
			
		</u-list>
	</view>
</template>

<script>
	import {
		getMechantList,
	} from '@/api/shop'
	export default {
		data() {
			return {
				page: 1,
				limit: 30,
				list: [], //商家列表
				showBack: false
			}
		},
		created() {
			this.getMechantList();
		},
		onLoad(options) {
			this.showBack = options.showBack;
		},
		methods: {
			goMap() {
				this.$emit('changeTab', 'map') //跳转地图页
			},
			/* 滚动到底部 */
			scrolltolower() {
				this.page++;
				this.getMechantList();
			},
			/* 商家列表 */
			getMechantList() {
				uni.showLoading({
					
				})
				getMechantList({
					page: this.page,
					limit: this.limit
				}).then(res => {
					uni.hideLoading()
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
			},
			/* 搜索 */
			goSearch() {
				uni.navigateTo({
					url: '/pages/shop-search/shop-search'
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.tips {
		padding: 32rpx 0;
		// color: #D0D0D6;
		color: #fff;
		text-align: center;
		font-size: 24rpx;
		height: 150rpx;
	}
</style>
