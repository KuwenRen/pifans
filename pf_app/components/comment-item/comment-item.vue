<template>
	<view class="comment-item">
		<view class="comment-item__header">
			<u-avatar class="advar" v-if="data.user.avatar" size="32rpx" :src="baseUrl+data.user.avatar"></u-avatar>
			<u-avatar class="advar" v-else size="32rpx" :src="`/static/img/user/icon${data.user.avatar_code}.png`"></u-avatar>
			<view class="name">
				{{$t('article.Username') +':'}}{{data.user.username}}
			</view>
			
		</view>
		<view class="comment-item__body">
			{{data.content || data.comment}}
		</view>
		<view class="comment-item__footer">
			<view class="footer-item">
				<i class="iconfont icon-date"></i>
				<u-text color="rgba(255,255,255,0.6)" size="28rpx" lineHeight="1" mode="date" :text="data.createtime"></u-text>
			</view>
		<!-- 	<view class="footer-item" style="color: #FF5C5C;" @click="delHandler(data.id)">
				<u-icon style="margin-right: 8rpx;" color="#FF5C5C" size="28rpx" name="trash"></u-icon>
				{{$t('del')}}
			</view> -->
		</view>
	</view>
</template>

<script>
	import { baseUrl } from '@/api/http.js';
	export default {
		name:"comment-item",
		props: {
			data: Object,
		},
		data() {
			return {
				baseUrl
			};
		}
	}
</script>

<style lang="scss" scoped>
	.comment-item {
		margin-bottom: 32rpx;
		border-bottom: 1rpx solid rgba(255, 255, 255, 0.3);
		.comment-item__header {
			display: flex;
			align-items: center;
			.advar {
				margin-right: 16rpx;
			}
			.name {
				margin-right: 8rpx;
				font-size: 28rpx;
				line-height: 40rpx;
				color: rgba(255,255,255,0.8);
			}
			.number {
				font-size: 24rpx;
				line-height: 36rpx;
				color: rgba(255,255,255,0.6);
			}
		}
		.comment-item__body {
			margin-top: 24rpx;
			font-size: 32rpx;
			line-height: 40rpx;
			color: rgba(255,255,255,0.8);
		}
		.comment-item__footer {
			padding: 24rpx 0;
			display: flex;
			justify-content: space-between;
			.footer-item {
				display: flex;
				align-items: center;
				font-size: 28rpx;
				line-height: 40rpx;
				.iconfont {
					margin-top: 2rpx;
					margin-right: 8rpx;
					color: rgba(255,255,255,0.6);
					font-size: 28rpx;
				}
			}
		}
	}
</style>
