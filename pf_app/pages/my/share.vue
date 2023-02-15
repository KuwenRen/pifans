<template>
	<view class="share container">
		<u-navbar :title="$t('my.sharePosters')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed>
			<view slot="right" class="right-icon" @click="share">
				{{$t('share1')}}
				<u-icon name="share" color="#F585FF" size="28"></u-icon>
			</view>
		</u-navbar>
		<view v-if="imageUrl" class="warp">
			<u-image  class="img" width="750rpx" height="1624rpx" :src="baseUrl+imageUrl"></u-image>
			<!-- <view class="banner">{{$t('slogan')}}</view> -->			
		</view>
	</view>
</template>

<script>
	import { baseUrl } from "@/api/http.js";
	import { getShareCodeApi } from "@/api/user.js";
	import setClipboardData from '../../utils/setClipboardData.js'
	export default {
		data() {
			return {
				baseUrl,
				imageUrl: ''
			}
		},
		methods: {
			share(){
				let {invite_code} = JSON.parse(uni.getStorageSync("userInfo"))
				setClipboardData('https://pifans.app/register/?invite_code='+invite_code)
				uni.showToast({
					icon:"none",
					title:this.$t("copys")
				})
			},
			init() {
				getShareCodeApi().then(({code, data, msg}) => {
					if (code == 1) {
						this.imageUrl = data;
					}else {
						msg && nui.$u.toast(msg);
					}
				})
			}
		},
		created() {
			this.init();
		}
	}
</script>

<style lang="scss" scoped>
	.right-icon{
		color: #F585FF;
		display: flex;
		align-items: center;
	}
	.share {
		display: flex;
		flex-direction: column;
		align-content: center;
		justify-content: center;
		.warp {
			padding: 0!important;
			position: relative;
			width: 100%;
			height: 1624rpx;
			background-position: center;
			background-size: contain;
			.img {
				background: transparent;
			}
			.banner {
				position: absolute;
				top: 280rpx;
				padding: 24rpx 0;
				font-size: 36rpx;
				line-height: 50rpx;
				text-align: center;
				color: #FFFFFF;
				width: 100%;
				border-top: 1px solid;
				border-bottom: 1px solid;
				border-image: linear-gradient(87deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.34), rgba(255, 255, 255, 0)) 1 1;
				background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.18) 53%, rgba(255, 255, 255, 0) 100%);
			}
		}
	}
</style>
