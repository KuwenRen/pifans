<template>
	<view class="article-item">
		<navigator class="warp" :url="`/pages/article/detail?type=${type}&id=${data.id}`">
			<image class="cover" :src="baseUrl+data.image" mode="aspectFill"></image>
			<view class="info">
				<u-text :text="titleComputed" color="#FFFFFF" size="28rpx" lineHeight="40rpx"></u-text>
				<view class="quota">
					<view class="watchs">
						<i class="iconfont icon-liulanliang"></i>
						{{data.watchs}}
					</view>
					<view class="gives">
						<i class="iconfont icon-dianzan"></i>
						{{data.gives}}
					</view>
					<view class="date" v-if="typeof data.createtime==='string'">
						<i class="iconfont icon-date"></i>
						{{data.createtime}}
					</view>
					<view class="date" v-else>
						<i class="iconfont icon-date"></i>
						{{data.createtime*1000 | formatTime()}}
					</view>
				</view>
			</view>
		</navigator>
	</view>
</template>

<script>
	import { formatTime } from '../../utils/date.js';
	import { baseUrl } from '../../api/http.js';
	export default {
		name:"article-item",
		props: {
			type: {
				type: String,
				default: 'Information'
			},
			data: Object
		},
		filters: {
			formatTime
		},
		data() {
			return {
				baseUrl
			};
		},
		computed: {
			titleComputed () {
				return this.$i18n.locale === 'zh-cn'? this.data.title1: this.data.title2;
			}
		}
	}
</script>

<style lang="scss" scoped>
	.article-item {
		padding: 34rpx 0;
		border-bottom: 1px solid #3A3A52;
		.warp {
			padding: 0 !important;
			display: flex;
			.cover {
				flex: none;
				margin-right: 16rpx;
				width: 180rpx;
				height: 136rpx;
				border-radius: 8rpx;
			}
			.info {
				flex: 1 1 0;
				padding-bottom: 34rpx;
				position: relative;
				.quota {
					position: absolute;
					bottom: 0;
					margin-top: auto;
					width: 100%;
					display: flex;
					font-size: 24rpx;
					line-height: 34rpx;
					color: rgba(255, 255, 255, 0.6);
					.gives {
						margin-left: 20rpx;
					}
					.date {
						margin-left: auto;
					}
					.iconfont {
						margin-right: 6rpx;
						font-size: 28rpx;
					}
				}
			}
		}
	}
</style>
