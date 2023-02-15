<template>
	<view class="chat-item" @click="goPath">
		<image class="face" mode="aspectFill" :src="showImg(item.show_image)"></image>
		<view class="txt">
			<view class="top">
				<text class="name">{{item.group_name}}</text>
				<text class="count">{{item.group_user_number}}人</text>
			</view>
			<view class="btm">
				<text class="label">{{item.group_type_name}}</text>
				<text class="time">{{item.last_send_time}}</text>
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
			item: Object,
			type: {
				type: Number,
				default: 0
			}
		},
		data() {
			return {
				baseUrl
			}
		},
		methods: {
			showImg(url) {
				if (url.indexOf('http') >= 0) {
					return url
				} else {
					return this.baseUrl + url
				}
			},
			/* 进入聊天室 */
			goPath() {
				let roomId = this.item.id;
				let roomName = this.item.group_name;
				let roomUserCount = this.item.group_user_number ?? 0
				if (this.type == 1) {
					if (!this.$store.state.token) {
						uni.navigateTo({
							url: '/pages/my/login'
						})
						return
					}
					uni.navigateTo({
						url: `/pages/chat/chat_new?roomId=${roomId}&name=${roomName}&count=${roomUserCount}`
					})
				} else {
					uni.navigateTo({
						url: `/pages/chat/chat?roomId=${roomId}&name=${roomName}`
					})
				}
			}
		}
	}
</script>

<style lang="scss" scoped>
	.chat-item {
		margin-top: 32rpx;
		padding: 16rpx;
		display: flex;
		background-color: #14142B;
		border-radius: 4rpx;

		.face {
			margin-right: 16rpx;
			width: 120rpx;
			height: 120rpx;
			flex-shrink: 0;
			border-radius: 4rpx;
		}

		.txt {
			width: 100%;
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			overflow: hidden;

			.top {
				display: flex;
				justify-content: space-between;

				.name {
					color: #D0D0D6;
					text-overflow: ellipsis;
					overflow: hidden;
					white-space: nowrap;
				}

				.count {
					margin-left: 48rpx;
					flex-shrink: 0;
					color: #F585FF;
					font-size: 24rpx;
				}
			}

			.btm {
				display: flex;
				justify-content: space-between;
				align-items: center;

				.label {
					padding: 4rpx 20rpx;
					color: #8D3FFA;
					font-size: 24rpx;
					border: 1px solid #8D3FFA;
				}

				.time {
					color: #46466A;
					font-size: 24rpx;
				}
			}
		}
	}
</style>
