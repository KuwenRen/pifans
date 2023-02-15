<template>
	<view class="my container">
		<view class="my-logo">
			<image class="logo" src="/static/img/home-logo.png" mode="aspectFit" />
			<i18nSwitch />
		</view>
		<view class="my-header">
			<u-avatar class="advar" :src="`/static/img/user/icon${userInfo?userInfo.avatar_code: 0}.png`" size="60">
			</u-avatar>
			<view class="info" v-if="token">
				<view class="name">
					{{$t('my.Username')}}：{{userInfo.nickname}}
					<image class="icon-img" v-if="userInfo.badge" v-for="item in userInfo.badge" :key="item.id"
						:src="baseUrl+item.image" mode="aspectFit"></image>
				</view>
				<view class="number">{{$t('my.fansId')}}：{{userInfo.user_number}}</view>
				<view class="number">{{$t('my.Invitation_code')}}：{{userInfo.invite_code}}</view>
			</view>
			<view class="info" v-else>
				<navigator class="name" url="/pages/my/login">{{$t('login')}}</navigator>
			</view>
			<view class="setting-wapper">
				<view class="qd-wapper" v-if="token">
					<text>{{count}}</text>
					<view v-if="signed" class="qd-btn" @click="userSign">{{$t("签到")}}</view>
					<view v-else class="qd-btn1">{{$t("今日已签到")}}</view>
				</view>
				<u-icon @click="navTo('/pages/my/setting')" class="set" name="setting-fill"
					color="rgba(255,255,255,0.6)" size="44rpx"></u-icon>
			</view>
		</view>
		<view class="my-share" @click="navTo('/pages/my/share')">
			<view>{{$t('my.sharePosters')}}</view>
			<u-icon name="arrow-right" color="#fff" size="24rpx"></u-icon>
		</view>
		<view class="my-card">
			<view class="my-card__header">{{$t('my.service')}}</view>
			<view class="my-card__body" style="padding: 0 20rpx 32rpx;">
				<view class="severs-warp">
					<view class="sever-item" v-for="item in serves" :key="item.path" @click="navTo(item.path)">
						<u--image class="icon" width="66rpx" height="72rpx" :src="item.icon" mode="widthFix"></u--image>
						<view class="text">{{$t(item.text)}}</view>
					</view>
				</view>
			</view>
		</view>
		<!-- <u-image class="my-adver" width="100%" height="180rpx" src="/static/adver/my-adver.png"></u-image> -->
		<view class="my-card">
			<view class="my-card__header">
				{{$t('my.setting')}}
			</view>
			<view class="my-card__body" style="padding: 0 20rpx 32rpx;">
				<view class="setting-warp">
					<navigator class="setting-item" url="/pages/my/forget">
						<view class="text">{{$t('passwordBack')}}</view>
						<u-icon name="arrow-right" color="#fff" size="24rpx"></u-icon>
					</navigator>
					<navigator class="setting-item" url="/pages/my/change">
						<view class="text">{{$t('passwordChange')}}</view>
						<u-icon name="arrow-right" color="#fff" size="24rpx"></u-icon>
					</navigator>
					<view class="setting-item">
						<view class="text">{{$t('是否隐藏地理位置')}}</view>
						<switch @change="changeAdd" color="#E323FF" :checked="isAdd" />
					</view>
					<navigator class="setting-item" url="/pages/upDate/upDate">
						<view class="text">{{$t('检查更新')}}</view>
						<text>V2.1.0</text>
					</navigator>
				</view>
			</view>






		</view>
		<u-button customStyle="margin-top: 48rpx" :text="$t('logout')" color="#14142B" @click="logoutHandler"
			v-if="token"></u-button>
	</view>
</template>

<script>
	import {
		mapState,
		mapMutations
	} from 'vuex';
	import {
		getUserInfoApi,
		getPowerApi,
		userSign
	} from '@/api/user.js'
	import {
		baseUrl
	} from '@/api/http.js';
	export default {
		name: "my",
		data() {
			return {
				signed: 0,
				count: 0,
				isAdd: true,
				baseUrl,
				//token:'',
				serves: [{
						path: '/pages/my/fans',
						icon: '/static/img/my/fans.png',
						text: 'my.fans'
					},
					{
						path: '/pages/my/power',
						icon: '/static/img/my/power.png',
						text: 'my.power'
					},
					{
						path: '/pages/my/comment',
						icon: '/static/img/my/comment.png',
						text: 'my.comment'
					},
					{
						path: '/pages/my/give',
						icon: '/static/img/my/give.png',
						text: 'my.like'
					},
					{
						path: '/pages/my/collect',
						icon: '/static/img/my/collect.png',
						text: 'my.bookmarks'
					},
					{
						path: '/pages/my/public',
						icon: '/static/img/my/public.png',
						text: 'menu.public'
					},
					{
						path: '/pages/my/leave',
						icon: '/static/img/my/leave.png',
						text: 'leave.title'
					},
					{
						path: '/pages/my/about',
						icon: '/static/img/my/about.png',
						text: 'aboutUs'
					},
					{
						path: '/pages/index/index?tab=map&type=2',
						icon: '/static/img/shangjia.png',
						text: '商家'
					},
					{
						path: 'http://pifans.app/uploads/pifans.apk',
						icon: '/static/img/my/load.png',
						text: 'ecology.download'
					},
					{
						path: '/pages/my/share',
						icon: '/static/img/my/share.png',
						text: 'my.shareCode'
					}
				],
				settings: [{
						path: '/pages/my/forget',
						text: 'passwordBack'
					},
					{
						path: '/pages/my/change',
						text: 'passwordChange'
					},
				]
			};
		},
		mounted() {
			this.isAdd = uni.getStorageSync("isAdd")
			this.getListHandler()
			this.getUserInfo()
		},
		methods: {
			...mapMutations(['updateToken', 'updateUserInfo']),
			navTo(path) {
				if (path.indexOf('http') !== -1 && path.indexOf('https' !== -1)) {
					// #ifdef H5
					location.href = 'http://www.pifan.club/uploads/pifans.apk';
					// #endif
					// #ifdef APP-PLUS 
					plus.runtime.openURL('http://www.pifan.club/uploads/pifans.apk');
					// #endif
				} else {
					if (this.token) {
						if (path.includes("index/index")) {
							uni.reLaunch({
								url: path
							})
							console.log(path)
						} else {
							uni.navigateTo({
								url: path
							});
						}
					} else {
						uni.navigateTo({
							url: '/pages/my/login'
						})
					}
				}
			},
			async userSign() {
				let res = await userSign()
				if (res.code == 1) {
					this.count += 5
					this.getUserInfo()
					uni.showToast({
						icon: "none",
						title: this.$t("签到成功")
					})
				} else {
					uni.showToast({
						icon: "none",
						title: this.$t(res.msg)
					})
				}
			},
			getListHandler() {
				let {
					list,
					page
				} = this;
				(this.status !== 'nomore') && getPowerApi({
					page: page + 1,
					listRows: 10
				}).then(({
					code,
					data: {
						data,
						total_powder_value
					},
					msg
				}) => {
					if (code == 1) {
						// if (data.length == 0) {
						// 	this.status = 'nomore';
						// } else {
						// 	this.page = page + 1;
						// 	this.list = list.concat(data);
						// }
						this.count = total_powder_value;
						console.log(total_powder_value)
					} else {
						uni.$u.toast(msg);
					}
				})
			},
			changeAdd(e) {
				uni.setStorageSync("isAdd", e.detail.value)
			},
			logoutHandler() {
				this.updateToken(undefined);
				this.updateUserInfo(undefined);
			},
			async getUserInfo() {
				let res = await getUserInfoApi()
				if (res.code == 1) {
					if (res.data.signed == 1) {
						this.signed = 0
					} else {
						this.signed = 1
					}
				}
			},
		},
		computed: {
			...mapState({
				token: state => state.token,
				userInfo: state => state.userInfo
			})
		}
	}
</script>

<style lang="scss" scoped>
	.icon-img {
		width: 30rpx;
		height: 30rpx;
		margin-left: 10rpx;
	}

	.setting-wapper {
		color: #E323FF;
		display: flex;
	}

	.qd-wapper {
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		margin-right: 20rpx;
	}

	.qd-btn {
		color: #FFF;
		padding: 0 16rpx;
		border-radius: 100vh;
		font-size: 24rpx;
		margin-top: 10rpx;
		background: linear-gradient(265deg, #E323FF 0%, #9041FF 100%);
	}

	.qd-btn1 {
		color: #FFF;
		padding: 0 16rpx;
		border-radius: 100vh;
		font-size: 24rpx;
		margin-top: 10rpx;
		background: linear-gradient(265deg, #95a5a6 0%, #bdc3c7 100%);
	}

	.my {
		padding-left: 40rpx;
		padding-right: 40rpx;
		padding-bottom: 70px;
		padding-top: 30upx;
		box-sizing: border-box;

		.my-logo {
			padding: 40rpx 0;
			display: flex;
			align-items: center;
			justify-content: space-between;
			height: 88rpx;

			.logo {
				margin-left: 8rpx;
				width: 309rpx;
				height: 46rpx;
			}
		}

		.my-header {
			display: flex;
			align-items: center;

			.advar {
				margin-right: 24rpx;
				width: 128rpx;
				height: 128rpx;
				border-radius: 50%;
				// border: 1px solid #FFFFFF;
			}

			.info {
				flex: 1 1 0;
				display: flex;
				flex-direction: column;
				justify-content: space-around;

				.name {
					display: flex;
					font-size: 36rpx;
					align-items: center;
					line-height: 50rpx;
					color: rgba(255, 255, 255, 1);
				}

				.number {
					font-size: 28rpx;
					line-height: 36rpx;
					color: rgba(255, 255, 255, 0.6);
				}
			}

			.set {
				margin-left: auto;
			}
		}

		.my-share {
			margin-top: 44rpx;
			display: flex;
			justify-content: space-between;
			padding: 24rpx 34rpx;
			font-size: 28rpx;
			line-height: 40rpx;
			color: #FFFFFF;
			background: linear-gradient(265deg, #E323FF 0%, #9041FF 100%);
			border-radius: 12rpx;
		}

		.my-adver {
			margin-top: 24rpx;
			margin-bottom: 24rpx;
		}

		.my-card {
			margin-top: 24rpx;
			background: #14142B;
			border-radius: 12rpx;
			overflow: hidden;

			.my-card__header {
				padding: 24rpx;
				font-size: 28rpx;
				font-weight: 600;
				line-height: 40rpx;
				color: #FFFFFF;
				border-bottom: 1px solid #232344;
			}

			.severs-warp {
				display: flex;
				flex-wrap: wrap;
				margin: 0 -24rpx;
				padding-left: 24rpx;
				padding-right: 24rpx;
				padding-bottom: 24rpx;

				.sever-item {
					margin-top: 32rpx;
					display: flex;
					flex-direction: column;
					align-items: center;
					justify-content: center;
					width: 0;
					padding: 0 24rpx;
					flex: 0 0 25%;
					box-sizing: border-box;

					.text {
						width: 100%;
						word-break: break-all;
						margin-top: 16rpx;
						font-size: 28rpx;
						line-height: 40rpx;
						text-align: center;
						color: rgba(255, 255, 255, 0.8);
					}
				}
			}

			.setting-warp {
				.setting-item {
					display: flex;
					justify-content: space-between;
					align-items: center;
					padding: 24rpx;
					font-size: 28rpx;
					line-height: 40rpx;
					color: rgba(255, 255, 255, 0.8);
					border-bottom: 1px solid #232344;

					&:last-child {
						border-bottom: none;
					}
				}
			}
		}
	}
</style>
