<template>
	<view class="web3-item" @click="clickTo(item.id)">
		<text class="name">{{item.title}}</text>
		<view class="btm">
			<text v-if="item.typename" class="label">{{item.typename}}</text>
			<text class="time">{{item.format_time}}</text>
		</view>
	</view>
</template>

<script>
	export default {
		props: {
			item: Object
		},
		data() {
			return {

			}
		},
		methods: {
			clickTo(id){
				this.$emit('Detail',id)
			}
		}
	}
</script>

<style lang="scss" scoped>
	.web3-item {
		margin-top: 32rpx;
		padding: 16rpx;
		display: flex;
		background-color: #14142B;
		border-radius: 4rpx;
		flex-direction: column;

		.name {
			margin-bottom: 16rpx;
			color: #D0D0D6;
		}

		.btm {
			display: flex;
			justify-content: space-between;
			align-items: center;

			.label {
				padding: 4rpx 20rpx;
				color: #8D3FFA;
				font-size: 24rpx;
				border: 1px solid #8D3FFA;
			}

			.time {
				color: #46466A;
				font-size: 24rpx;
			}
		}
	}
</style>
