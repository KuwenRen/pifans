<!-- 头部 -->
<template>
	<view class="u-header">
		<image v-if="showBack" class="icon" src="../../static/img/left.png" mode="widthFix" @click="goBack"></image>
		<text>{{title}}</text>
		<view class="right">
			<slot></slot>
		</view>
	</view>
</template>

<script>
	export default {
		props: {
			title: String,
			showBack: {
				type: [Boolean, String],
				default: true
			}
		},
		methods: {
			/* 返回 */
			goBack() {
				uni.navigateBack();
			},
		}
	}
</script>

<style lang="scss" scoped>
	.u-header {
		position: sticky;
		z-index: 99;
		top: 0;
		width: 100%;
		/* #ifdef APP-PLUS */
		height: calc(90rpx + var(--status-bar-height));
		/* #endif */
		/* #ifdef H5 */
		height: calc(90px + var(--status-bar-height));
		/* #endif */

		display: flex;
		align-items: center;
		justify-content: center;
		color: #fff;
		background-color: #050523;

		.icon {
			position: absolute;
			left: 30rpx;
			width: 20rpx;
		}

		.right {
			position: absolute;
			right: 30rpx;
			display: flex;


		}
	}
</style>
