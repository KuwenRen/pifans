<template>
	<view>
		<!-- 今天 12：00 -->
		<view class="time">{{ (data.isTime?data.time:'')|timeInit('M-D h:m')}}</view>
		<view :class="data.location=='right'?'msg-item-me':'msg-item'">
			<view class="nickname">
				{{data.username}}
			</view>
			<u-avatar :src="baseUrl+data.avatar" size="80rpx"></u-avatar>
			<!-- 文本内容 -->
			<view class="sanjiao" v-if="data.chat_type==1"></view>
			<view class="item-content" v-if="data.chat_type==1">{{data.content}}</view>
			<!-- 图片内容 -->
			<image class="iten-image" @click="tabImg" v-else :src="data.content" mode="aspectFill"></image>
		</view>

	</view>
</template>

<script>
	import {
		baseUrl
	} from "@/api/http.js";
	import {
		timeInit
	} from '@/utils/filters.js'
	export default {
		props: ["data"],
		data() {
			return {
				user: {},
				baseUrl
			}
		},
		filters: {
			timeInit
		},
		methods: {
			tabImg() {
				this.$emit('tabImg')
			},
			toLy() {
				uni.navigateTo({
					url: "/pages/leavingMessage/leavingMessage"
				})
			}
		},
		mounted() {
			this.user = uni.getStorageSync('login')
		},

	}
</script>

<style>
	.nickname {
		color: #CCC;
		position: absolute;
		z-index: 2;
		left: 100rpx;
		top: -30rpx;
		font-size: 24rpx;
	}

	.msg-item-me .nickname {
		display: none;
	}

	.avatar {
		width: 80rpx;
		height: 80rpx;
		border-radius: 50%;
		flex-shrink: 0;
	}

	.msg-item {
		display: flex;
		/* margin: 20rpx 0; */
		/* flex-direction: row-reverse; */
		position: relative;
	}

	.msg-item-me {
		display: flex;
		/* margin: 20rpx 0; */
		flex-direction: row-reverse;
		position: relative;
	}

	.item-content {
		font-size: 30rpx;
		padding: 20rpx;
		border-bottom-right-radius: 10rpx;
		border-bottom-left-radius: 10rpx;
		max-width: 460rpx;
	}

	.msg-item .sanjiao {
		border-width: 14rpx 14rpx 14rpx 14rpx;
		border-color: #e5e6ea #e5e6ea transparent transparent;
		border-style: solid;
		height: 0;
		width: 0;
		margin-right: -1px;
	}

	.msg-item .item-content {
		border-top-right-radius: 10rpx;
		background-color: #e5e6ea;
	}

	.msg-item-me .item-content {
		border-top-left-radius: 10rpx;
		background-color: #25D4D0;
		color: #FFF;
	}

	.msg-item-me .sanjiao {
		border-width: 14rpx 14rpx 14rpx 14rpx;
		border-color: #25D4D0 transparent transparent #25D4D0;
		border-style: solid;
		height: 0;
		width: 0;
		margin-left: -1px;
	}

	.time {
		text-align: center;
		font-size: 28rpx;
		color: #666;
		padding: 30rpx;
	}

	.m-ts {
		text-align: center;
		font-size: 26rpx;
		color: #666;
		padding: 20rpx 0;
	}

	.m-ts-wapper {
		margin: 20rpx 0;
	}

	.xian {
		height: 1px;
		background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(0, 0, 0, 1), rgba(0, 0, 0, 0));
	}

	.iten-image {
		width: 300rpx;
		margin: 0 20rpx;
		height: 300rpx;
	}

	.ly-wapper {
		display: flex;
		flex-direction: column;
		align-items: center;
		padding: 50rpx 0;
	}

	.ly-dq {
		font-size: 26rpx;
	}

	.ly-num {
		font-size: 60rpx;
		margin: 30rpx;
		color: #FF8023;
	}

	.ly-btn {
		width: 600rpx;
		background-color: #FF8023;
		height: 80rpx;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 30rpx;
		color: #FFF;
		border-radius: 10rpx;
	}
</style>
