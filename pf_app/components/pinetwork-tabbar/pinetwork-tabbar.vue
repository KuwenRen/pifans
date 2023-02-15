<template>
	<view class="sy-tabs">
		<div class="sy-tabs__header" :class="`sy-tabs__header-${size}`">
			<view class="header-item" :class="{active: index == active}" v-for="(item, index) in data" :key="index"
				@click="tabIndex(index)">
				<text v-if="item.id==0">{{$t(item.typename)}}</text>
				<text v-else>{{item.typename}}</text>
			</view>
		</div>
		<view class="tt"></view>
		<div v-if="type == 'default'" class="sy-tabs__body"
			:class="{none: !data[active].list || data[active].list.length == 0, grid: grid }">
			<view class="body-item" v-for="(item, index) in data[active].list" :key="index">
				<slot v-bind:item="item"></slot>
			</view>
			<u-empty v-if="!data[active].list || data[active].list.length == 0" :text="$t('empty')"></u-empty>
		</div>
	</view>
</template>

<script>
	export default {
		name: "pinetwork-tabbar",
		props: {
			value: {
				type: Number | String,
				default: 1
			},
			data: Array,
			type: {
				type: String,
				default: 'default'
			},
			grid: {
				type: Boolean,
				default: false
			},
			size: {
				type: String,
				default: 'middle'
			}
		},
		data() {
			return {
				active: 0
			};
		},
		methods: {
			tabIndex(index) {
				if (this.data[index].url) {
					uni.navigateTo({
						url: this.data[index].url
					})
				} else {
					this.active = index
				}
			}
		},
		watch: {
			value: {
				handler(cur) {
					const index = this.data.findIndex(item => item.key == cur);
					this.active = index == -1 ? 0 : index;
				},
				immediate: true
			},
			active(cur) {
				this.$emit('input', this.data[this.active].key);
				this.$emit('tabChange', this.data[this.active]);
			}
		}
	}
</script>

<style lang="scss" scoped>
	.sy-tabs {
		.tt {
			border-bottom: 1rpx solid #3A3A52;
		}

		.sy-tabs__header {
			overflow: auto;
			padding-bottom: 24rpx;
			display: flex;

			.header-item {
				position: relative;
				margin-right: 32rpx;
				font-size: 28rpx;
				line-height: 40rpx;
				color: rgba(255, 255, 255, 0.8);
				white-space: nowrap;

				&.active {
					font-size: 32rpx;
					font-weight: 600;
					color: #F585FF;

					&::after {
						content: "";
						position: absolute;
						left: 0;
						bottom: -25rpx;
						width: 100%;
						height: 6rpx;
						border-radius: 2rpx;
						background: linear-gradient(265deg, #E323FF 0%, #9041FF 100%);
					}
				}
			}

			&.sy-tabs__header-large {
				padding-bottom: 30rpx;

				.header-item {
					font-size: 32rpx;
					line-height: 50rpx;

					&.active {
						font-size: 36rpx;

						&::after {
							bottom: -31rpx;
							height: 8rpx;
						}
					}
				}
			}
		}

		.sy-tabs__body {
			min-height: 360rpx;

			&.none {
				display: flex;
				justify-content: center;
				align-items: center;
			}

			&.grid {
				margin: 16rpx -16rpx -16rpx;
				display: flex;
				flex-wrap: wrap;

				.body-item {
					flex: 0 0 33.33333333%;
					max-width: 33.33333333%;
					padding: 16rpx;
					box-sizing: border-box;
				}
			}
		}
	}
</style>
