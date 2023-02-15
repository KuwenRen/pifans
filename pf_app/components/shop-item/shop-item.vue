<!-- 商家 -->
<template>
	<view class="shop-item" @click="goPath">
		<image class="face" :src="baseUrl+item.image"></image>
		<view class="txt">
			<view class="name">
				<text class="t">{{item.name}}</text>
				<text class="l">{{item.country_name}}</text>
			</view>
			<view class="address">
				<text>{{item.provice_name}}</text>
				<text class="l">{{item.business_type_name}}</text>
			</view>
			<view class="major">
				<text style="flex-shrink: 0;">【主营产品】</text>
				<text class="l">{{item.describe}}</text>
			</view>
			<view class="preview">
				<view class="count">
					<image class="icon" src="../../static/img/see.png" mode="widthFix"></image>
					<text>{{item.watch_number}}</text>
				</view>
				<text>{{item.province}}</text>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		baseUrl
	} from "@/api/http.js";
	export default {
		props: {
			item: {
				type: Object,
				default: () => {
					return {
						id: 1
					}
				}
			}
		},
		data() {
			return {
				baseUrl
			}
		},
		methods: {
			goPath() {
				let merchantId = this.item.id;
				uni.navigateTo({
					url: `/pages/shopInfo/shopInfo?merchantId=${merchantId}`
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.shop-item {
		margin-top: 32rpx;
		padding: 16rpx;
		box-sizing: border-box;
		width: 100%;
		display: flex;
		font-size: 32rpx;
		background-color: #14142B;

		.face {
			margin-right: 30rpx;
			width: 220rpx;
			height: 180rpx;
			flex-shrink: 0;
		}

		.txt {
			width: 100%;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			overflow: hidden;

			.name {
				display: flex;
				justify-content: space-between;

				.t {
					width: 70%;
					text-overflow: ellipsis;
					overflow: hidden;
					white-space: nowrap;
					// color: #D0D0D6;
					color: #fff;
					font-size: 32rpx;
				}

				.l {
					color: #D0D0D6;
					font-size: 16rpx;
					background-image: linear-gradient(to top, #9041FF, #E323FF);
					flex-shrink: 0;
					display: flex;
					width: 90rpx;
					height: 40rpx;
					justify-content: center;
					align-items: center;
				}
			}

			.address {
				color: #7A7A92;
				font-size: 16rpx;

				.l {
					margin-left: 48rpx;
					color: #E5554A;
				}
			}

			.major {
				display: flex;
				color: #7A7A92;
				font-size: 16rpx;

				.l {
					margin-left: 32rpx;
					display: -webkit-box;
					overflow: hidden;
					text-overflow: ellipsis;
					-webkit-box-orient: vertical;
					-webkit-line-clamp: 2;
					text-align: justify;
				}
			}

			.preview {
				width: 100%;
				display: flex;
				justify-content: space-between;
				color: #D0D0D6;
				font-size: 16rpx;

				.count {
					display: flex;
					align-items: center;
					color: #7A7A92;

					.icon {
						margin-right: 8rpx;
						width: 40rpx;
					}
				}
			}
		}
	}
</style>
