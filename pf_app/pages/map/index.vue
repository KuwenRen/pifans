<template>
	<view class="map container">
		<!-- #ifdef H5 -->
		<sy-map el="mapContainer" @mapClick="mapClick" height="calc(100vh - 50px)" @makerClick="makerClick"
			@changeIndex="changeIndex"></sy-map>
		<!-- #endif -->
		
		<!-- #ifdef APP-PLUS -->
		<sy-map-app el="mapContainer" @mapClick="mapClick" @makerClick="makerClick" @changeIndex="changeIndex">
		</sy-map-app>
		<!-- #endif -->
		<view class="map-banner" v-if="curUser&&curUser.type!='shop'">
			<image class="map-banner__avatar" width="100rpx" height="100rpx"
				:src="`../../static/img/user/icon${curUser.avatar_code}.png`" mode=""></image>
			<view class="map-banner__info">
				<u-text lines="1" :text="curUser.nickname" color="#EE95FD" size="36rpx" lineHeight="50rpx" blod>
				</u-text>
				<u-text margin="8rpx 0" lines="1" :text="curUser.declaration" color="#FFFFFF" size="32rpx"
					lineHeight="44rpx"></u-text>
				<view class="desc">
					<view>{{curUser.wx_number}}</view>
					<view>{{curUser.address}}</view>
				</view>
			</view>
		</view>
		<view class="map-banner map-banner2" @click="toEdit" v-if="curUser&&curUser.type=='shop'">
			<image class="map-banner__avatar" width="100rpx" height="100rpx" :src="curUser.image" mode="aspectFit">
			</image>
			<view class="map-banner__info">
				<u-text lines="1" :text="curUser.name" color="#FF7756" size="28rpx" lineHeight="50rpx" blod></u-text>
				<u-text margin="8rpx 0" lines="1" :text="curUser.declaration" color="#FFFFFF" size="32rpx"
					lineHeight="44rpx"></u-text>
				<u-rate :disabled="true" :count="5" :allowHalf="true" v-model="curUser.score" activeColor="#FFD422">
				</u-rate>
				<view class="desc">
					<view>{{curUser.address}}</view>
				</view>
				<view class="comment-wapper">
					<view style="margin-right: 50rpx;">
						<text>{{$t('全部评论')}}</text>
						<text style="color: #099BFF;font-size: 34rpx;margin-left: 4rpx;">{{curUser.count}}</text>
					</view>
					<view class="rl" @click.stop="shopRl">
						<view class="yuan" style="margin-right: 10rpx;"><text style="margin-top: -6rpx;">·</text></view>
						{{curUser.status==0?$t('认领'):$t('已认领')}}
					</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';
	import {
		merchantDetail,
		MerchantUpClaim
	} from '@/api/index.js';
	export default {
		name: "mapTab",
		data() {
			return {
				curUser: null
			};
		},
		methods: {
			async shopRl() {
				if (this.curUser.status != 0) return
				uni.showModal({
					cancelText: this.$t("取消"),
					confirmText: this.$t("确定"),
					content: this.$t("是否认领该商家?"),
					success: async ({
						confirm
					}) => {
						if (confirm) {
							let res = await MerchantUpClaim({
								id: this.curUser.id
							})
							if (res.code == 1) {
								uni.showToast({
									title: res.msg
								})
								this.curUser = null
							}
						}

					}
				})

			},

			toEdit() {
				let _this = this
				let arr = [this.$t('商家详情'), this.$t('编辑资料')]
				if (this.curUser.user_id != this.userInfo.id) {
					arr.pop()
				}
				uni.showActionSheet({
					itemList: arr,
					success: ({
						tapIndex
					}) => {
						if (tapIndex == 0) {
							uni.navigateTo({
								url: "/pages/shopInfo/shopInfo?id=" + this.curUser.id
							})
						} else {
							uni.navigateTo({
								url: "/pages/editShop/editShop?id=" + this.curUser.id
							})
						}
						this.curUser = null
						console.log(res)
					}
				})


			},
			async makerClick(user) {
				if (user.type == 'shop') {
					let res = await merchantDetail({
						id: user.id
					})
					if (res.code == 1) {
						user = {
							...user,
							...res.data
						}
					}

				}
				if (!this.token || !user) return;
				this.curUser = user;
				console.log(user)
			},
			mapClick() {
				this.curUser = null;
			},

			changeIndex(v) {
				this.$emit('changeIndex', v);
			}
		},
		computed: {
			...mapState({
				locale: state => state.locale,
				token: state => state.token,
				userInfo: state => state.userInfo,
			}),
		},
		mounted() {

		}
	}
</script>

<style lang="scss" scoped>
	.comment-wapper {
		display: flex;
		align-items: flex-end;
		color: #EEB6B1;
		font-size: 24rpx;
	}

	.yuan {
		width: 26rpx;
		height: 26rpx;
		border-radius: 50%;
		border: 2px solid #000;
		display: flex;
		align-items: center;
		justify-content: center;
		color: #000;
		font-weight: bold;
	}

	.rl {
		font-size: 28rpx;
		color: #000;
		display: flex;
		align-items: center;
	}

	.map {
		// padding-top: 96upx;
		box-sizing: border-box;

		.map-banner2 {
			background: #b8543e !important;
		}

		.map-banner {
			padding: 0 40rpx;
			display: flex;
			align-items: center;
			position: fixed;
			bottom: 180rpx;
			width: 100%;
			height: 218rpx;
			background: url(../../static/img/map-banner.png) no-repeat;
			background-size: 100% 100%;
			box-sizing: border-box;
			z-index: 999;

			.map-banner__avatar {
				width: 148rpx;
				height: 148rpx;
				border-radius: 50%;
				margin-right: 24rpx;
			}

			.map-banner__info {
				.desc {
					display: flex;
					color: rgba(255, 255, 255, 0.6);
					font-size: 28rpx;
					line-height: 40rpx;
				}
			}
		}
	}
</style>
