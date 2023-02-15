<!-- 新闻 -->
<template>
	<view class="news-item" @click="goPath">
		<image class="face" :src="baseUrl+item.image" mode="aspectFill"></image>
		<view class="txt">
			<text v-if="localeIndex==1">{{item.title1}}</text>
			<text v-if="localeIndex==2">{{item.title2}}</text>
			<view class="txt-btm">
				<view class="count">
					<view class="watchs">
						<i class="iconfont icon-liulanliang"></i>
						{{item.watchs}}
					</view>
					<view class="gives">
						<i class="iconfont icon-dianzan"></i>
						{{item.gives}}
					</view>
				</view>
				<view class="date">
					<i class="iconfont icon-date"></i>
					{{item.createtime*1000 | formatTime()}}
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';
	import {
		baseUrl
	} from "@/api/http.js";
	import {
		formatTime
	} from '../../utils/date.js';
	export default {
		props: {
			item: Object
		},
		filters: {
			formatTime
		},
		data() {
			return {
				baseUrl
			}
		},
		computed: {
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn' ? 1 : 2;
			},
		},
		methods: {
			goPath() {
				uni.navigateTo({
					url: `/pages/article/detail?id=${this.item.id}&type=Information`
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.news-item {
		margin-top: 32rpx;
		padding: 16rpx;
		box-sizing: border-box;
		width: 100%;
		display: flex;
		background-color: #14142B;

		.txt-btm {
			display: flex;
			justify-content: space-between;
			width: 100%;

			.count {
				display: flex;
				align-items: center;
				color: #7A7A92;

				.icon {
					margin-right: 8rpx;
					width: 40rpx;
				}
			}
		}

		.face {
			margin-right: 16rpx;
			width: 220rpx;
			height: 160rpx;
			flex-shrink: 0;
			border-radius: 2px;
		}

		.txt {
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			color: #fff;
			font-size: 32rpx;
			width: 100%;

			.date {
				font-size: 24rpx;
				color: #46466A;
			}
		}
	}
</style>
