<template>
	<view class="about container" >
		<u-navbar :title="$t('aboutUs')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view class="warp">
			<view class="sy-header">
				<view class="sy-header__inner">{{$t('aboutUs')}}</view>
			</view>
			<view class="about-content">
				<rich-text :nodes="content"></rich-text>
			</view>
		</view>
	</view>
</template>

<script>
	import { mapState } from 'vuex';
	import { baseUrl } from '@/api/http.js';
	import { getAboutApi } from '@/api/index.js';
	export default {
		data() {
			return {
				content: ''
			}
		},
		methods: {
			init() {
				getAboutApi().then(({code, data:{ company_about }, msg}) => {
					if(code == 1) {
						this.content = company_about;
					} else {
						msg && uni.$u.toast(msg)
					}
				})
			}
		},
		computed: {
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn'? 1 : 2;
			}
		},
		created() {
			this.init()
		}
	}
</script>

<style lang="scss" scoped>
	.about {
		.warp {
			padding-top: 170rpx;
			.sy-header {
				margin-bottom: 32rpx;
			}
			.about-content {
				font-size: 32rpx;
				font-family: PingFang SC-Regular, PingFang SC;
				font-weight: 400;
				color: rgba(255, 255, 255, 0.8);
				line-height: 44rpx;
			}
		}
	}
</style>
