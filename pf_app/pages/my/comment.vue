<template>
	<u-list class="comment container" @scrolltolower="getListHandler">
		<u-navbar :title="$t('my.comment')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view class="warp">
			<sy-tabs type="custom" size="large" :data="config" @tabChange="tabChange"></sy-tabs>
			<view  v-if="list.length !== 0" class="comment-list">
				<view class="comment-item" v-for="(item,index) in list" :key="index">
					<view class="comment-item__header">
						<u-text lines="1" color="#FFFFFF" size="16" lineHeight="22" :text="item['title'+localeIndex]" bold block></u-text>
						<view class="category">{{$t('category')}}：{{filterName(item.type)}}</view>
						<view class="quota">
							<view class="quota-item"><i class="iconfont icon-liulanliang"></i> {{item.watchs}}</view>
							<view class="quota-item"><i class="iconfont icon-dianzan"></i>{{item.gives}}</view>
							<view class="quota-item"><i class="iconfont icon-pinglun"></i>{{item[commentName()].length}}</view>
							<view class="quota-item">
								<i class="iconfont icon-date"></i>
								<u-text color="rgba(255,255,255,0.6)" size="24rpx" lineHeight="1" mode="date" :text="item.createtime"></u-text>
							</view>
						</view>
					</view>
					<view class="comment-item__body">
						<view class="comment-child" v-for="cItem in item[commentName()]" :key="cItem.id">
							<view class="comment-child__header">
								<u-avatar class="advar" size="32rpx" :src="`/static/img/user/icon${cItem.user.avatar_code}.png`"></u-avatar>
								<view class="name">
									{{cItem.user.username}}
								</view>
								<view class="number">
									{{cItem.user.user_number}}
								</view>
							</view>
							<view class="comment-child__body">
								{{cItem.content}}
							</view>
							<view class="comment-child__footer">
								<view class="footer-item">
									<u-icon style="margin-right: 8rpx;" color="rgba(255,255,255,0.6)" size="28rpx" name="clock"></u-icon>
									<u-text color="rgba(255,255,255,0.6)" size="28rpx" lineHeight="1" mode="date" :text="item.createtime"></u-text>
								</view>
								<view class="footer-item" style="color: #FF5C5C;" @click="delHandler(cItem.id)">
									<u-icon style="margin-right: 8rpx;" color="#FF5C5C" size="28rpx" name="trash"></u-icon>
									{{$t('del')}}
								</view>
							</view>
						</view>
					</view>				
				</view>
				<sy-loadmore v-if="status !== 'none'" :status="status" />
			</view>
			<u-empty v-else style="margin-top: 100rpx;" :text="$t('empty')"/>			
		</view>

	</u-list>
</template>

<script>
	import { mapState } from 'vuex';
	import { getCommentApi, delCommentApi } from '@/api/user.js'
	export default {
		data() {
			return {
				active: 'Information',
				config: [
					{ key: 'Information', name: 'my.newsComments' },
					{ key: 'Knowledge', name: 'my.skillsComments' },
				],
				status: 'none',
				list: [],
				page: 0
			}
		},
		methods: {
			getListHandler() {
				let { list, page } = this;
				(this.status !== 'nomore') && getCommentApi(this.active, { page: page + 1, listRows: 10 }).then(({code, data:{ data }, msg}) => {
					if (code == 1) {
						if (data.length == 0) {
							this.status = 'nomore';
						} else {
							this.page = page + 1;
							this.list = list.concat(data);
						}
					} else {
						uni.$u.toast(msg);
					}
				})
			},
			tabChange(item) {
				this.active = item.key;
				this.status = 'none';
				this.page = 0;
				this.list = [];
				this.getListHandler();
			},
			filterName (type) {
			    if (this.active == 'Information') {
			      return this.$t('menu.information')+' | '+ (type == 1?'PINETWORK':this.$t('article.cryptocurrency'));
			    } else {
			      return this.$t('menu.knowledge')+' | '+ (type == 1?this.$t('article.wallet'):(type == 2?this.$t('article.exchange'):this.$t('article.tools')));
			    }
			},
			commentName() {
				return this.active == 'Information'? 'information_comment' : 'know_ledge_comment';
			},
			delHandler(id) {
				delCommentApi(this.active, { id }).then(({code, msg}) => {
					if(code == 1) this.init();
					uni.$u.toast(msg);
				})
			},
			init() {
				this.status = 'none';
				this.page = 0;
				this.list = [];
				this.getListHandler();
			}
		},
		computed: {
			...mapState({
				locale: state => state.locale
			}),
			localeIndex() {
				return this.locale == 'zh-cn'? 1 : 2;
			},
		},
		created() {
			this.init();
		},
	}
</script>

<style lang="scss" scoped>
	.comment {
		.warp {
			padding-top: 170rpx;
			padding-bottom: 60rpx;
		}
		.comment-list {
			margin-top: 32rpx;
			.comment-item {
				background: #191932;
				border-radius: 8rpx;
				border: 1rpx solid rgba(255, 255, 255, 0.3);
				.comment-item__header {
					padding: 32rpx;
					border-bottom: 1rpx solid #404065;
					.category {
						margin-top: 16rpx;
						font-size: 28rpx;
						line-height: 40rpx;
						color: rgba(255,255,255,0.6);
					}
					.quota {
						margin-top: 16rpx;
						display: flex;
						font-size: 24rpx;
						line-height: 36rpx;
						color: rgba(255,255,255,0.6);
						.quota-item {
							display: flex;
							align-items: center;
							margin-right: 20rpx;
							&:last-child {
								margin-left: auto;
								margin-right: 0;
							}
							.iconfont {
								margin-right: 8rpx;
								font-size: 28rpx;
							}
						}
					}
				}
				.comment-item__body {
					padding: 32rpx;
					.comment-child {
						margin-bottom: 32rpx;
						border-bottom: 1rpx solid rgba(255, 255, 255, 0.3);
						.comment-child__header {
							display: flex;
							align-items: center;
							.advar {
								margin-right: 16rpx;
							}
							.name {
								margin-right: 8rpx;
								font-size: 28rpx;
								line-height: 40rpx;
								color: rgba(255,255,255,0.8);
							}
							.number {
								font-size: 24rpx;
								line-height: 36rpx;
								color: rgba(255,255,255,0.6);
							}
						}
						.comment-child__body {
							margin-top: 24rpx;
							font-size: 32rpx;
							line-height: 40rpx;
							color: rgba(255,255,255,0.8);
						}
						.comment-child__footer {
							padding: 24rpx 0;
							display: flex;
							justify-content: space-between;
							.footer-item {
								display: flex;
								align-items: center;
								font-size: 28rpx;
								line-height: 40rpx;
							}
						}
					}
				}
			}
		}
	}
</style>
