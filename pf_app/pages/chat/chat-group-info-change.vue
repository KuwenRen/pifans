<!-- 
@author x 
@title 修改群聊名称
 -->
<template>
	<view class="chat-personal-info-change">
		<u-header title=""></u-header>
		<view class="title">
			<text>{{$t('修改群聊名称')}}</text>
			<!-- <text class="t">修改群聊名称后，将在群内通知其他成员</text> -->
		</view>
		<view style="margin: 60rpx 0;">
			<view class="item">
				<image class="avatar" :src="info.show_image" mode="aspectFill"></image>
				<view class="icon">
					<input v-model="info.group_name" />
				</view>
			</view>
		</view>
		<view style="padding: 0 100px;">
			<u-button customStyle="margin-top: 32rpx" :text="$t('save')"
				color="linear-gradient(to right, #9041FF, #E323FF)" @click="submit"></u-button>
		</view>
	</view>
</template>

<script>
	import {
		chatInfo,
		updChatGroupName
	} from '@/api/chat'
	export default {
		data() {
			return {
				roomId: 0,
				info: {
					group_name: ''
				}
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
			submit() {
				uni.setStorageSync('group_name', this.info.group_name)
				updChatGroupName({
					chat_room_id: this.roomId,
					group_name: this.info.group_name
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
							uni.navigateBack();
						}, 800)
					}
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.chat-personal-info-change {
		width: 100%;
		min-height: 100vh;
		background-color: $main-bg-color;

		.title {
			display: flex;
			flex-direction: column;
			align-items: center;
			color: #fff;
			font-size: 30rpx;

			.t {
				color: #7A7A92;
				font-size: 26rpx;
			}
		}

		.item {
			padding: 30rpx;
			display: flex;
			align-items: center;
			border-bottom: 1px solid rgba(#fff, 0.2);
			border-top: 1px solid rgba(#fff, 0.2);
			font-size: 0;

			.avatar {
				margin-right: 16rpx;
				width: 80rpx;
				height: 80rpx;
				border-radius: 4px;
			}

			.icon {
				color: #7A7A92;
				font-size: 26rpx;
			}
		}
	}
</style>
