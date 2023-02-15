<!-- 
@author x 
@title 商家信息
 -->
<template>
	<view class="shop-details">
		<u-header :title="$t('商家详情')"></u-header>
		<view style="padding: 0 32rpx;">
			<view class="logo">
				<!-- <image class="icon" :src="baseUrl+info.image" mode="aspectFill"></image> -->
				<swiper class="swiper" circular :indicator-dots="indicatorDots" :autoplay="autoplay"
					:interval="interval" :duration="duration">
					<swiper-item v-for="(item,index) in goods_images">
						<image class="icon" :src="baseUrl+item" mode="aspectFill"></image>
					</swiper-item>
				</swiper>
			</view>
			<!-- 商家名称 -->
			<view class="name">
				<text>{{info.name}}</text>
				<view class="evaluate">
					<view class="star">
						<image v-for="i in info.score" class="icon" src="../../static/img/star1.png"></image>
						<image v-for="k in (5 - info.score)" class="icon" src="../../static/img/star0.png"></image>
					</view>
					<view class="count">
						<text>全部评论</text>
						<text class="t">{{info.comment_number}}条</text>
					</view>
				</view>
				<text class="tt">{{info.describe}}</text>
			</view>
			<view class="item">
				<view class="i">
					<view class="txt">
						<image class="icon" src="../../static/img/s1.png" mode="heightFix"></image>
						<text>{{info.address}}</text>
					</view>
				</view>
				<view class="i">
					<view class="txt">
						<image class="icon" src="../../static/img/s2.png" mode="heightFix"></image>
						<text>{{info.phone}}</text>
					</view>
				</view>
				<view v-if="info.email" class="i">
					<view class="txt">
						<image class="icon" src="../../static/img/s3.png" mode="heightFix"></image>
						<text>{{info.email}}</text>
					</view>
				</view>
			<!-- 	<view class="i">
					<view class="txt">
						<image class="icon" src="../../static/img/s4.png" mode="heightFix"></image>
						<text></text>
					</view>
				</view> -->
			</view>
			<view class="item">
				<view class="i" v-if="info.social_account_type">
					<text class="t">社交账号</text>
					<view class="txt">
						<text>{{info.social_account}}</text>
					</view>
				</view>
				<view class="i" v-if="info.merchant_type_id">
					<text class="t">支持货币</text>
					<view class="txt">
						<text>{{info.merchant_type_name}}</text>
					</view>
				</view>
				<!-- 				<view class="i">
					<text class="t">主营产品</text>
					<view class="txt">
						<text>{{info.business}}</text>
					</view>
				</view> -->
				<view class="i">
					<text class="t">商家类别</text>
					<view class="txt">
						<text>{{info.business_type_name}}</text>
					</view>
				</view>
			</view>
			<view class="title">
				<text>商品详情</text>
			</view>
			<view class="rich-content">
				<rich-text :nodes="info.main_goods"></rich-text>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mechantInfo,
	} from '@/api/shop'
	import {
		baseUrl
	} from "@/api/http.js";
	export default {
		data() {
			return {
				indicatorDots: true,
				autoplay: false,
				interval: 2000,
				duration: 500,
				merchantId: 0,
				info: {}, //详情
				baseUrl,
				goods_images: []
			}
		},
		onLoad(params) {
			this.merchantId = params.merchantId || 1;
			this.mechantInfo();
		},
		methods: {
			/* 商家详情 */
			mechantInfo() {
				mechantInfo({
					merchant_id: this.merchantId
				}).then(res => {
					this.info = res.data;
					this.goods_images = res.data.images
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.swiper {
		height: 300rpx;
		width: 100%;
	}

	.rich-content {
		width: 100%;
		height: auto;
		background-color: #efefef;
	}

	.swiper-item {
		display: block;
		height: 300rpx;
		line-height: 300rpx;
		text-align: center;
	}

	.swiper-list {
		margin-top: 40rpx;
		margin-bottom: 0;
	}

	.logo {
		margin-bottom: 24rpx;
		width: 100%;

		.icon {
			width: 100%;
		}
	}

	.name {
		padding: 16rpx;
		display: flex;
		flex-direction: column;
		color: #D0D0D6;
		background-color: #14142B;

		.tt {
			overflow: hidden;
			/*超出部分隐藏*/
			text-overflow: ellipsis;
			/*超出部分省略号表示*/
			white-space: nowrap;
			/*强制单行显示*/
			display: inline-block;
			/*转换为行内块元素*/
			color: #fff;
			width: 100%;
		}

		.evaluate {
			display: flex;
			align-items: center;
			justify-content: space-between;

			.star {
				display: flex;

				.icon {
					margin-right: 4rpx;
					width: 40rpx;
					height: 40rpx;
				}
			}

			.count {
				font-size: 24rpx;
				color: #7A7A92;

				.t {
					color: #099BFF;
					font-size: 32rpx;
					font-weight: bold;
				}
			}
		}
	}

	.item {
		margin-top: 32rpx;
		background-color: #14142B;
		border-radius: 4rpx;

		.i {
			padding: 24rpx 16rpx;
			display: flex;
			align-items: center;
			border-bottom: 1px solid rgba(#fff, 0.2);

			.t {
				margin-right: 24rpx;
				color: #7A7A92;
				font-size: 24rpx;
			}

			.txt {
				display: flex;
				align-items: center;
				color: #D0D0D6;
				font-size: 24rpx;

				.icon {
					margin-right: 16rpx;
					height: 32rpx;
					flex-shrink: 0;
				}
			}
		}

		.i:last-child {
			border-bottom: none;
		}
	}

	.title {
		padding: 16rpx 0;
		text-align: center;
		color: #7A7A92;
		font-size: 24rpx;
	}
</style>
