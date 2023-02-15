<template>
	<view class="container">
		<u-navbar :title="$t('聊天室')" bgColor="#14142B" leftIconColor="#F585FF" autoBack />
		<view class="page">
			<view class="tbs">
				<text @click="ac=0" :class="['tbs-item',ac==0?'tbs-item-ac':'']">{{$t('中文')}}</text>
				<text @click="ac=1" :class="['tbs-item',ac==1?'tbs-item-ac':'']">{{$t('省份')}}</text>
				<text @click="ac=2" :class="['tbs-item',ac==2?'tbs-item-ac':'']">English</text>
				<text @click="toGroupChat" :class="['tbs-item',ac==3?'tbs-item-ac':'']">{{$t('群聊天')}}</text>
			</view>
			<view class="item" v-for="item in list[ac]" @click="toChat(item)">
				<text>{{$t(item.name)}}</text>
				<u-icon name="arrow-right" color="#FFF"></u-icon>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		GatewayserverGetProvinceList
	} from '../../api/index.js'
	export default {
		data() {
			return {
				ac: 0,
				list: [
					[{
						name: "中文",
						id: 1
					}],
					[],
					[{
						name: "English",
						id: 2
					}],
				]
			}
		},
		onLoad() {
			this.GatewayserverGetProvinceList()
		},
		methods: {
			toGroupChat(){
				uni.navigateTo({
					url: "/pages/chat-room/chat-room"
				})
			},
			toChat(item) {
				uni.navigateTo({
					url: `/pages/chat/chat?id=${item.id}&name=${item.name}`
				})
			},
			async GatewayserverGetProvinceList() {
				let res = await GatewayserverGetProvinceList()
				if (res.code == 1) {
					this.list[1] = res.data
				}
			},
		}
	}
</script>

<style>
	.tbs {
		display: flex;
		align-items: center;
		justify-content: space-between;
		color: #fff;
		font-size: 28rpx;
		border-bottom: 1px solid #404065;
	}

	.tbs-item-ac {
		border-bottom: 4rpx solid #F585FF;
		color: #F585FF;
		font-size: 30rpx;
	}

	.tbs-item {
		padding: 30rpx 20rpx;
	}

	.page {
		color: #FFF;
		padding: 0 30rpx;
	}

	.item {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 30rpx 0;
	}
</style>
