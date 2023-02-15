<template>
	<navigator class="article-card" :url="`/pages/article/detail?id=${data.id}&type=${type}`">
		<u-image class="cover" width="100%" height="500rpx" :src="baseUrl+data.image" mode="aspectFill"></u-image>
		<view class="warp">
			<u-text lines="2" :text="data['title'+localeIndex]||data['title']" color="#FFFFFF" size="36rpx"
				lineHeight="44rpx" blod>
			</u-text>
			<u-text lines="2" margin="16rpx 0 24rpx" v-html="data['content'+localeIndex]||data['content']"
				color="rgba(255,255,255,0.6)" size="28rpx" lineHeight="32rpx" style="
				overflow: hidden;
				white-space: nowrap;
				text-overflow: ellipsis;
				color:#FFFFFF;
				height: 46px;
				display: block;
			"></u-text>
			<view class="quota">
				<view class="watchs">
					<i class="iconfont icon-liulanliang"></i>
					{{data.watchs}}
				</view>
				<view class="gives">
					<i class="iconfont icon-dianzan"></i>
					{{data.gives}}
				</view>
				<!-- 		<view class="comment">
					<i class="iconfont icon-dianzan"></i>
					{{data.gives}}
				</view> -->
				<view class="date">
					<i class="iconfont icon-date"></i>
					{{data.createtime*1000 | formatTime()}}
				</view>
			</view>
		</view>
	</navigator>
</template>

<script>
	import {
		mapState
	} from 'vuex';
	import {
		formatTime
	} from '../../utils/date.js';
	import {
		baseUrl
	} from '../../api/http.js';
	export default {
		name: "article-card",
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
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn' ? 1 : 2;
			},
		}
	}
</script>

<style lang="scss" scoped>
	.article-card {
		position: relative;
		margin-bottom: 40rpx;
		border-radius: 8rpx;
		overflow: hidden;

		.warp {
			position: absolute;
			left: 0;
			right: 0;
			bottom: 0;
			padding: 32rpx;
			background: rgba(#000000, 0.6);

			.quota {
				display: flex;
				font-size: 24rpx;
				line-height: 28rpx;
				color: rgba(255, 255, 255, 0.6);

				.iconfont {
					margin-right: 6rpx;
					font-size: 24rpx;
				}

				.watchs {
					margin-right: 24rpx;
				}

				.date {
					margin-left: auto;
				}
			}
		}
	}
</style>
