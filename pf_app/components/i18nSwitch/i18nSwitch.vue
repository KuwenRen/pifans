<template>
	<view :class="`i18n-switch ${type}`" @click="switchLang">
		<view class="title">{{ title }}</view>
		<i class="iconfont icon-lang"></i>
	</view>
</template>

<script>
	import { mapMutations } from 'vuex';
	import store from '@/store/index.js';
	export default {
		name:"i18nSwitch",
		props: {
			type: {
				type: String,
				default: 'default'
			}
		},
		data() {
			return {
				
			};
		},
		methods: {
			...mapMutations(['updateLocale']),
			switchLang() {
				this.$i18n.locale = this.$i18n.locale == 'zh-cn'? 'en-us': 'zh-cn';
				this.updateLocale(this.$i18n.locale);
			}
		},
		computed: {
			title () {
				return this.$i18n.locale == 'zh-cn'? '简体-中文': 'English';
			}
		},
	}
</script>

<style lang="scss" scoped>
	.i18n-switch {
		display: flex;
		align-items: center;
		color: #FFFFFF;
		font-size: 32rpx;
		line-height: 40rpx;
		&.default {
			.title {
				margin-right: 10rpx;
			}
			.iconfont {
				font-size: 28rpx;
			}
		}
		&.button {
			padding: 20rpx 40rpx;
			border: 1px solid #404065;
			border-radius: 8rpx;
			.title {
				margin-right: 34rpx;
			}
			.iconfont {
				font-size: 28rpx;
			}
		}
	}
</style>
