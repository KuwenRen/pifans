<template>
	<view class="container">
		<u-navbar title="404" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<u-empty style="margin-top:300rpx;" :text="$t('lostpage')" />
		<u-button class="custom-style" text="返回首页" @click="toINdex" type="primary"></u-button>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				
			}
		},
		onLoad() {
			
		},
		methods: {
			toINdex(){	
				location.href = 'http://www.pifans.club/m';
			}
		}
	}
</script>

<style>
	.custom-style{
		width: 32%;
		margin: 30px auto;
	}

</style>
