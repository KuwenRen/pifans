<template>
	<view class="home-card">
		<view class="home-card__header">
			<view class="title">{{title}} </view>
			<view v-if="more" class="more" @click="moreHandler">
				<view style="margin-right: 8rpx;">{{$t(moreText)}}</view>
				<u-icon name="arrow-right" size="30rpx" />
			</view>
		</view>
		<view class="home-card__body">
			<slot></slot>
		</view>
	</view>
</template>

<script>
	export default {
		name: "home-card",
		props: {
			title: {
				type: String,
				default: ""
			},
			more: {
				type: String,
				default: "more"
			},
			moreText: {
				type: String,
				default: 'more'
			}
		},
		data() {
			return {

			};
		},
		methods: {
			 moreHandler () {
				 if (this.more == 'custom') {
					this.$emit('moreClick');
				 } else {
					uni.navigateTo({ url: this.more });
				 }
			 }
		}
	}
</script>

<style lang="scss" scoped>
.home-card {
	padding-top: 64rpx;
	.home-card__header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		.title {
			font-size: 36rpx;
			font-weight: 600;
			line-height: 50rpx;
			color: #FFFFFF;
		}
		.more {
			display: flex;
			align-items: center;
			font-size: 28rpx;
			line-height: 40rpx;
			color: rgba(255, 255, 255, 0.6);
		}
	}
	.home-card__body {
		margin-top: 24rpx;
	}
}
</style>
