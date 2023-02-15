<!-- 
@author x 
@title 修改个人信息
 -->
<template>
	<view class="chat-personal-info-change">
		<u-header title=""></u-header>
		<view class="title">
			<text>{{$t('修改个人信息')}}</text>
			<!-- <text class="t">修改个人信息后，将在群内通知其他成员</text> -->
		</view>
		<view style="margin: 60rpx 0;">
			<view class="item" @click="chooseImg">
				<block v-if="avatar">
					<image class="show-icon" :src="baseUrl+avatar" mode="widthFix"></image>
				</block>
				<block v-else>
					<image class="show-icon" src="../../static/img/show-icon.jpg" mode="widthFix"></image>
				</block>
				<view class="icon">
					<text>{{$t('选择个人头像')}}</text>
					<image style="width: 40rpx;height: 40rpx;margin-left:10rpx ;" src="../../static/img/right.png"
						mode=""></image>
				</view>
			</view>
			<view class="item">
				<text>{{$t('修改昵称')}}</text>
				<view class="icon">
					<input style="text-align: right;" type="text" v-model="nickname" :placeholder="$t('请输入您的昵称')">
				</view>
			</view>
		</view>
		<view class="mask" v-if="show_avatar">
			<image @tap="cancel" class="cancel" src="../../static/img/cancel.png" mode=""></image>
			<view class="mask-content" @tap.stop>
				<image @click="choose_avatar(index)" :class="choose_index==index?'icon1':'icon'"
					v-for="(item,index) in avatars" :src="item" mode="">
				</image>
				<view class="btn" @click="confirm">
					<view class="confitm-btn">确认</view>
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
		mapState
	} from 'vuex';
	import {
		getChatUserInfo,
		editChatUserInfo
	} from '@/api/chat'
	import {
		baseUrl
	} from "@/api/http.js";
	export default {
		data() {
			return {
				baseUrl,
				roomId: 0,
				user: {},
				nickname: '',
				avatar: '',
				user_id: 0,
				avatars: [
					'https://pifans.app/resource/icon1.png',
					'https://pifans.app/resource/icon2.png',
					'https://pifans.app/resource/icon3.png',
					'https://pifans.app/resource/icon4.png',
					'https://pifans.app/resource/icon5.png',
				],
				choose_index: 0,
				show_avatar: 0
			}
		},
		computed: {
			...mapState({
				locale: state => state.locale,
				userInfo: state => state.userInfo
			}),
			localeIndex() {
				return this.locale == 'zh-cn' ? 1 : 2;
			},
		},
		created() {
			let user_id = this.userInfo.user_id
			this.user_id = user_id
			this.getChatUserInfo(user_id)
		},
		methods: {
			cancel() {
				this.show_avatar = 0
			},
			confirm() {
				this.avatar = this.avatars[this.choose_index]
				this.show_avatar = 0
			},
			choose_avatar(item) {
				this.choose_index = item
			},
			submit() {
				let tips = '消息提示';
				let nickname = '请输入昵称';
				let avatar = '请选择头像';
				let confirmText = '确定'
				let cancelText = '取消'
				let feedback = '成功';
				if (this.localeIndex == 2) {
					tips = 'Tips'
					nickname = 'Please Enter Your Nickname'
					avatar = 'Please Choose Your Avatar'
					confirmText = 'Yes'
					cancelText = 'No'
					feedback = 'success'
				}
				let that = this
				if (!that.avatar) {
					uni.showModal({
						content: avatar,
						title: tips,
						confirmColor: '#6ab04c',
						cancelText: cancelText,
						confirmText: confirmText,
						showCancel: false
					})
				}
				if (!that.nickname) {
					uni.showModal({
						content: nickname,
						title: tips,
						confirmColor: '#6ab04c',
						cancelText: cancelText,
						confirmText: confirmText,
						showCancel: false
					})
				}
				editChatUserInfo({
					avatar: that.avatar,
					nickname: that.nickname,
					group_id: that.roomId,
					user_id: that.user_id
				}).then(res => {
					if (res.code == 1) {
						uni.showToast({
							title: feedback
						})
						that.getChatUserInfo(that.user_id)
					}
				})
			},
			chooseImg1() {
				this.show_avatar = 1
			},
			chooseImg() {
				let that = this
				uni.chooseImage({
					count: 1,
					sizeType: ['compressed'],
					sourceType: ['album', 'camera'],
					success: function(res) {
						that.uploadImage(res.tempFilePaths)
					}
				});
			},
			uploadImage(tempFilePaths) {
				let that = this;
				uni.uploadFile({
					url: 'https://www.pifan.club/api/file/uploadImgs', //接口地址
					filePath: tempFilePaths[0],
					name: 'file',
					success: (res) => {
						let data = JSON.parse(res.data)
						if (data.code == 1) {
							that.avatar = data.data
						}
					}
				});
			},
			getChatUserInfo(user_id) {
				getChatUserInfo({
					user_id: user_id,
					group_id: this.roomId
				}).then(res => {
					if (res.code == 1) {
						this.nickname = res.data.nickname
						this.avatar = res.data.avatar
					}
				})
			}
		},
		onLoad({
			roomId
		}) {
			this.roomId = roomId
		}
	}
</script>

<style lang="scss" scoped>
	.chat-personal-info-change {
		width: 100%;
		min-height: 100vh;
		background-color: $main-bg-color;

		.mask {
			position: fixed;
			width: 100vw;
			height: 380rpx;
			background: #fff;
			left: 0rpx;
			bottom: 0rpx;
			font-size: 26rpx;
			text-align: center;
			// box-shadow: 2px -3px 100px -5px #FFFFFF;
			display: flex;
			justify-content: center;
			align-items: center;
			z-index: 99;
			border-top-left-radius: 15rpx;
			border-top-right-radius: 15px;

			.cancel {
				position: absolute;
				right: 20rpx;
				top: 20rpx;
				width: 50rpx;
				height: 50rpx;
			}

			.mask-content {
				width: 80%;
				height: auto;
				position: absolute;
				top: 50%;
				left: 50%;
				transform: translate(-50%, -50%);
				border-radius: 12rpx;
				background-color: #ffffff;

				.btn {
					background-color: #E323FF;
					text-align: center;
					line-height: 80rpx;
					color: #fff;
					margin-top: 40rpx;
					border-radius: 4rpx;
				}
			}

			.icon {
				width: 120rpx;
				height: 120rpx;
			}

			.icon1 {
				width: 120rpx;
				height: 120rpx;
				border: 1rpx solid #E323FF;
				border-radius: 50%;
			}
		}

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

		.item:first-child {
			border-top: 1px solid rgba(#fff, 0.2);
		}

		.item {
			padding: 30rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;
			border-bottom: 1px solid rgba(#fff, 0.2);
			color: #fff;
			font-size: 30rpx;

			.icon {
				color: #7A7A92;
				font-size: 26rpx;
				display: flex;
				align-items: center;
				justify-content: center;
			}

			.show-icon {
				width: 110rpx;
			}
		}
	}
</style>
