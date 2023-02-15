<template>
	<view class="article-detail container">
		<u-navbar :title="$t('article.detail')"  bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder>
			<view slot="right" class="right-icon" @click="share">
				{{$t('share1')}}
				<u-icon name="share" color="#F585FF" size="28"></u-icon>
			</view>
		</u-navbar>
		<view class="warp">
			<view class="article-detail__header">
				<u-text lines="2" :text="detail['title'+localeIndex]" color="#F585FF" size="48rpx" lineHeight="56rpx" blod></u-text>
				<view class="quota">
					<view class="watchs">
						<i class="iconfont icon-liulanliang"></i>
						{{detail.watchs}}
					</view>
					<view class="gives">
						<i class="iconfont icon-dianzan"></i>
						{{detail.gives}}
					</view>
					<view class="comment">
						<i class="iconfont icon-pinglun"></i>
						{{detail.comments}}
					</view>
					<view class="date">
						<i class="iconfont icon-date"></i>
						{{detail.createtime*1000 | formatTime()}}
					</view>
				</view>
			</view>
			<view class="article-detail__body">
				<rich-text :nodes="detail['content'+localeIndex]"></rich-text>
			</view>
			<view class="article-detail__footer">
				<view class="operate">
					<view class="operate-item" @click="nav(detail.pre_id)">
						<u-icon name="arrow-left" size="32rpx"></u-icon>
						<view style="margin-left: 24rpx;">{{$t('article.prev')}}</view>
					</view>
					<view class="operate-item" @click="nav(detail.next_id)">
						<view style="margin-right: 24rpx;">{{$t('article.next')}}</view>
						<u-icon name="arrow-right" size="32rpx"></u-icon>
					</view>
				</view>
				<view class="comment">
					<view class="comment-title">{{$t('article.comment')}}</view>
					<view class="comment-body">
						<comment-item v-for="item in detail[commentName]" :key="item.id" :data="item"></comment-item>
					</view>
					<navigator class="comment-all" :url="`/pages/article/comment?id=${id}&type=${type}`">
						<view>
							{{$t('all')+' '}}{{$t('article.comment')}} ({{detail.comments}})
						</view>
						<u-icon style="margin-top: 4rpx;margin-left: 20rpx;" color="24rpx" name="arrow-right"></u-icon>
					</navigator>
				</view>
				<comment-bar :id="detail.id" :type="type" :give="detail.is_give == 1" :collect="detail.is_collect == 1" @success="init"></comment-bar>
			</view>
		</view>
	</view>
</template>

<script>
	import setClipboardData from '../../utils/setClipboardData.js'
	import { mapState } from 'vuex';
	import { baseUrl } from '@/api/http.js';
	import { formatTime } from '../../utils/date.js';
	import { getArticleDetailApi } from '@/api/index.js';
	export default {
		filters: {
			formatTime
		},
		data() {
			return {
				isPop:true,
				id: '',
				type: '',
				detail: {}
			}
		},
		methods: {
			share(){
				setClipboardData(location.href)
				uni.showToast({
					icon:"none",
					title:this.$t("copys")
				})
			},
			init() {
				getArticleDetailApi(this.type, { id: this.id, type: 0 }).then(({code, data, msg}) => {
					if (code == 1) {
						this.detail = data
					} else {
						msg && uni.$u.toast(msg);
					}
				})
			},
			nav(id) {
				id && uni.navigateTo({
					url: `/pages/article/detail?type=${this.type}&id=${id}`
				})
			}
		},
		computed: {
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn'? 1 : 2;
			},
			commentName() {
				return this.type == 'Information'? 'information_comment' : 'know_ledge_comment';
			},
		},
		onLoad({ id, type }) {
			this.id = id;
			this.type = type;
			this.init();
		}
	}
</script>

<style lang="scss" scoped>
	.right-icon{
		color: #F585FF;
		display: flex;
		align-items: center;
	}
	.article-detail {
		.warp {
			padding-top: 170rpx;
			padding-bottom: 140rpx;
			.article-detail__header {
				padding-bottom: 32rpx;
				border-bottom: 1rpx solid #404065;
				.quota {
					margin-top: 32rpx;
					display: flex;
					font-size: 24rpx;
					line-height: 28rpx;
					color: rgba(255,255,255,0.6);
					.iconfont {
						margin-right: 6rpx;
						font-size: 24rpx;
					}
					.watchs, .gives {
						margin-right: 24rpx;
					}
					.date {
						margin-left: auto;
					}
				}
			}
			.article-detail__body {
				padding: 32rpx 0;
				color: rgba(255,255,255,0.6);
			}
			.article-detail__footer {
				.operate {
					margin-bottom: 48rpx;
					display: flex;
					justify-content: space-between;
					.operate-item {
						display: flex;
						padding: 24rpx 48rpx;
						font-size: 32rpx;
						line-height: 40rpx;
						color: rgba(255,255,255,0.6);
						background-color: #191932;
						border-radius: 8rpx;
					}
				}
				.comment {
					padding-bottom: 48rpx;
					.comment-title {
						font-size: 32rpx;
						line-height: 40rpx;
						color: #F585FF;
					}
					.comment-body {
						padding-top: 32rpx;
					}
					.comment-all {
						display: flex;
						justify-content: center;
						align-items: center;
						padding: 22rpx;
						text-align: center;
						color: #F585FF;
						background-color: rgba(245,133,255,0.3);
						border-radius: 8rpx;
					}
				}
			}
		}
	}
</style>
