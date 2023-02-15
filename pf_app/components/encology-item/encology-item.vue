<template>
	<view class="encology-item" @click="toDetail">
		<u--image :src="baseUrl+data.image" width="200rpx" height="200rpx" radius="8rpx"></u--image>
		<u-text margin="16rpx 0 12rpx 0" align="center" wordWrap="anywhere" size="24rpx" lineHeight="36rpx" color="#FFFFFF" :text="data['title'+localeIndex]"></u-text>
		<u-rate :count="5" v-model="data.star" inactiveColor="rgba(255,255,255,0.6)" activeColor="#FFAA05" disabled></u-rate>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import { baseUrl } from '@/api/http.js'
	export default {
		name:"encology-item",
		props: {
			data: Object
		},
		data() {
			return {
				baseUrl
			};
		},
		methods: {
			toDetail() {
				let { id } = this.data;
				uni.navigateTo({ url: `/pages/ecology/detail?id=${id}` })
			}
		},
		computed: {
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn'? 1 : 2;
			},
		},
	}
</script>

<style lang="scss" scoped>
	.encology-item {
		display: flex;
		align-items: center;
		flex-direction: column;
		.title {
			margin-top: 16rpx;
		}
	}
</style>
