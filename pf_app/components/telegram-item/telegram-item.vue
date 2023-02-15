<template>
	<view class="telegram-item">
		<navigator class="title" @click="nav(data.url)">
			{{ titleComputed }}
		</navigator>
		<view class="country">
			<u--image style="margin-top: 8rpx;margin-right: 16rpx;" :src="baseUrl+data.country.image" width="40rpx" height="28rpx" radius="1px"></u--image>
			<view class="label">{{countryComputed}}</view>
		</view>
	</view>
</template>

<script>
	import { baseUrl } from '@/api/http.js';
	export default {
		name: "telegram-item",
		props: {
			data: Object
		},
		data() {
			return {
				baseUrl
			};
		},
		methods: {
			nav(url) {
				if (url.includes('http://') || url.includes('https://')) {
					location.href = url;
				} else {
					location.href = `https://${url}`
				}
			}
		},
		computed: {
			titleComputed () {
				let { title1, title2 } = this.data;
				return this.$i18n.locale == 'zh-cn'? title1: title2;
			},
			countryComputed () {
				let { name1, name2 } = this.data.country;
				return this.$i18n.locale == 'zh-cn'? name1: name2;
			}
		}
	}
</script>

<style lang="scss" scoped>
	.telegram-item {
		display: flex;
		justify-content: space-between;
		padding: 34rpx 0;
		border-bottom: 1px solid #3A3A52;
		.title {
			flex: 1 1 0;
			font-size: 32rpx;
			line-height: 44rpx;
			color: rgba(255, 255, 255, 1);
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}
		.country {
			flex: 0 0 auto;
			margin-left: 40rpx;
			display: flex;
			align-items: center;
			color: rgba(255, 255, 255, 0.8);
			.label {
				
			}
		}
	}
</style>
