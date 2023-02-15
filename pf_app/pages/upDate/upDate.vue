<template>
	<view class="my-page">
		<u-navbar @leftClick="leftClick" :title="$t('版本更新')" leftIconColor="#F585FF" bgColor="rgb(20, 20, 43)"></u-navbar>
		<view class="logo-wapper">
			<image src="@/static/img/logo.png" mode="aspectFit" class="logo"></image>
			<view class="logo-r">
				<view class="">
					<text>{{$t('当前版本')}}:</text>
					<text style="color: #F585FF;">V2.1.0</text>
				</view>
				<view class="time">
					{{$t("派发布日期")}}:2022.06.28
				</view>
			</view>
		</view>
		<view class="ts">
			{{$t("最新版本为2.1.0，当前提示最新版本")}}
		</view>
		<view class="btn" @click="tabbtn">
			{{$t("暂不更新")}}
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				
			}
		},
		methods: {
			leftClick(){
				uni.navigateBack()
			},
			tabbtn(){
				uni.navigateBack()
			}
		}
	}
</script>

<style>
.my-page{
	background-color: #050523;
	min-height: 100vh;
	color: #CCC;
}
.logo{
	width: 126rpx;
	height: 126rpx;
	border-radius: 50%;
	margin-right: 30rpx;
}
.logo-wapper{
	display: flex;
	align-items: center;
	padding: 30rpx;
}
.time{
	margin-top: 20rpx;
	font-size: 28rpx;
}
.ts{
	text-align: center;
	margin: 50rpx 30rpx;
}
.btn{
	width: 690rpx;
	color: #F585FF;
	text-align: center;
	height: 100rpx;
	line-height: 100rpx;
	background-color: #323256;
	margin: 150rpx auto;
	border-radius: 20rpx;
}
</style>
