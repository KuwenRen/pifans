<!-- 
@author x
@title 群聊天信息
 -->
<template>
	<view class="chat-group-info">
		<u-header :title="$t('群聊信息')"></u-header>
		<view class="list">
			<view v-for="(item,key) in info.group_users" :key="key" class="item">
				<image class="avatar" :src="baseUrl+item.avatar" mode="aspectFill"></image>
				<text class="t">{{item.nickname}}</text>
			</view>
			<view v-if="info.group_users.length>=20" class="tips">
				<text>查看更多信息&gt;</text>
			</view>
		</view>
		<view style="margin: 20rpx 0;">
			<view class="i-item" @click="changeNmae">
				<text>{{$t('群名称')}}</text>
				<view class="icon">
					<text>{{info.group_name}}</text>
					<image class="icon-img" src="../../static/img/r.png" mode="widthFix"></image>
				</view>
			</view>
			<view class="i-item" @click="changePower">
				<text>{{$t('群主管理权转让')}}</text>
				<image class="icon-img" src="../../static/img/r.png" mode="widthFix"></image>
			</view>
			<view class="i-item">
				<text>{{$t('是否仅群主/群管理员可修改群聊名称')}}</text>
				<text>
					<switch :checked="info.is_enable_switch" />
				</text>
			</view>
			<view class="i-item" @click="changePersonalName">
				<text>{{$t('个人信息')}}</text>
				<image class="icon-img" src="../../static/img/r.png" mode="widthFix"></image>
			</view>
		</view>
		<view class="i-item" style="color: #F72C19;justify-content: center;" @click="exitSubmit">
			<text>{{$t('退出群聊')}}</text>
		</view>
	</view>
</template>

<script>
	import {
		baseUrl
	} from "@/api/http.js";
	import {
		chatInfo,
		exitChatroom
	} from '@/api/chat'
	export default {
		data() {
			return {
				baseUrl,
				roomId: 0,
				info: {
					group_users: []
				},
			}
		},
		onLoad({
			roomId
		}) {
			this.roomId = roomId;
			this.chatInfo();
		},
		methods: {
			chatInfo() {
				chatInfo({
					chat_room_id: this.roomId
				}).then(res => {
					this.info = res.data;
				})
			},
			/* 退出 */
			exitSubmit() {
				exitChatroom({
					chat_room_id: this.roomId
				}).then(res => {
					if (res.code == 0) {
						uni.showToast({
							title: res.msg,
							icon: 'error'
						})
					} else {
						uni.showToast({
							title: res.msg,
							icon: 'success'
						})
						setTimeout(() => {
							uni.reLaunch({
								url: '/pages/index/index'
							})
						}, 800)
					}
				})
			},
			/* 修改群名称 */
			changeNmae() {
				uni.navigateTo({
					url: `/pages/chat/chat-group-info-change?roomId=${this.roomId}`
				})
			},
			/* 修改个人信息 */
			changePersonalName() {
				uni.navigateTo({
					url: `/pages/chat/chat-personal-info-change?roomId=${this.roomId}`
				})
			},
			/* 转让权限 */
			changePower() {
				uni.navigateTo({
					url: `/pages/chat/chat-power-change?roomId=${this.roomId}`
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.chat-group-info {
		width: 100%;
		min-height: 100vh;
		background-color: $main-bg-color;

		.list {
			padding: 26rpx 0;
			display: flex;
			flex-wrap: wrap;
			background-color: #14142B;

			.item {
				margin-bottom: 26rpx;
				width: 20%;
				display: flex;
				flex-direction: column;
				align-items: center;
				font-size: 0;

				.avatar {
					width: 80rpx;
					height: 80rpx;
					border-radius: 50%;
				}

				.t {
					color: #7A7A92;
					font-size: 23rpx;
					overflow: hidden;
					/*超出部分隐藏*/
					text-overflow: ellipsis;
					/*超出部分省略号表示*/
					white-space: nowrap;
					/*强制单行显示*/
					display: inline-block;
					/*转换为行内块元素*/
					color: #bdc3c7;
					width: 100rpx;
					display: block;
					text-align: center;
				}
			}

			.tips {
				width: 100%;
				text-align: center;
				color: #46466A;
				font-size: 20rpx;
			}
		}

		.i-item {
			padding: 26rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;
			color: #fff;
			font-size: 26rpx;
			background-color: #14142B;
			border-bottom: 1px solid rgba(#fff, 0.2);

			.icon {
				font-size: 20rpx;
				color: #7A7A92;
				display: flex;
				align-items: center;
			}

			.icon-img {
				width: 32rpx;
			}
		}

		.i-item:last-child {
			border-bottom: none;
		}
	}
</style>
