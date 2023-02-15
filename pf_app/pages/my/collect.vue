<template>
	<u-list class="give container" @scrolltolower="getListHandler">
		<u-navbar :title="$t('my.bookmarks')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view class="warp">
			<sy-tabs type="custom" size="large" :data="config" @tabChange="tabChange"></sy-tabs>
			<view class="give-header">
				<view class="info">
					<view class="lable">{{$t('my.totalBookmarks')}}</view>
					<view class="count">{{count}}</view>
				</view>
				<u-image src="/static/img/my/collect-banner.png" width="104rpx" height="104rpx" mode=""></u-image>
			</view>
			<view class="give-list" v-if="list.length !== 0">
				<view class="give-item" v-for="(item,index) in list" :key="index" @click="toDetail(item)">
					<view class="give-item__header">
						<view class="count">+1</view>
						<view class="date">
							<u-icon style="margin-right: 8rpx;" name="clock" size="26rpx" color="rgba(255, 255, 255, 0.8)"/>
							<u-text class="date" mode="date" text="1612959739" color="rgba(255, 255, 255, 0.8)" size="28rpx" lineHeight="40rpx"></u-text>
						</view>
					</view>
					<view class="give-item__body">
						<u-text color="#FFFFFF" lines="1" size="16" lineHeight="22" :text="item['title'+localeIndex]||item.ecology['title'+localeIndex]" bold block></u-text>
						<view class="category">{{$t('category')}}：{{filterName(item.type)}}</view>
						<view class="quota">
							<view class="quota-item"><i class="iconfont icon-liulanliang"></i> {{item.watchs}}</view>
							<view class="quota-item"><i class="iconfont icon-dianzan"></i>{{item.gives}}</view>
							<!-- <view class="quota-item"><i class="iconfont icon-pinglun"></i>{{item[commentName()].length}}</view> -->
							<view class="quota-item">
								<i class="iconfont icon-date"></i>
								<u-text color="rgba(255,255,255,0.6)" size="24rpx" lineHeight="1" :text="item.createtime"></u-text>
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
	import { getRecordApi } from '@/api/user.js'
	import { newsGetCollectList } from '@/api/index.js'
	
	export default {
		data() {
			return {
				active: 'Information',
				config: [
					{ key:'Information', name: 'my.newsBookmarks' },
					{ key:'Knowledge', name: 'my.skillsBookmarks' },
					{ key:'collect', name: '生态' },
				],
				status: 'none',
				count: 0,
				list: [],
				page: 0
			}
		},
		methods: {
			getListHandler() {
				
				let api =this.active=="collect"?newsGetCollectList:getRecordApi
				let { list, page } = this;
				(this.status !== 'nomore') && api(this.active, { type: 1, page: page + 1, listRows: 10 }).then(({code, data:{ data, total }, msg}) => {
					if (code == 1) {
						if (data.length == 0) {
							this.status = 'nomore';
						} else {
							this.page = page + 1;
							this.list = list.concat(data);
						}
						this.count = total;
					} else {
						uni.$u.toast(msg);
					}
				})
			},
			filterName (type) {
			    if (this.active == 'Information') {
			      return this.$t('menu.information')+' | '+ (type == 1?'PINETWORK':this.$t('article.cryptocurrency'));
			    } else {
			      return this.$t('menu.knowledge')+' | '+ (type == 1?this.$t('article.wallet'):(type == 2?this.$t('article.exchange'):this.$t('article.tools')));
			    }
			},
			tabChange(item) {
				this.active = item.key;
				this.init();
			},
			init() {
				this.status = 'none';
				this.page = 0;
				this.list = [];
				this.getListHandler();
			},
			// commentName() {
			// 	return this.active == 'Information'? 'information_comment' : 'know_ledge_comment';
			// },
			toDetail(item,type){
				if(this.active=='Information'){
					var id = item.information_id
				}
				if(this.active=='Knowledge'){
					var id = item.knowledge_id
				}
				if(this.active=='collect'){
					uni.navigateTo({
						url:"/pages/ecology/detail?id="+item.ecology_id
					})
					return
				}
				uni.navigateTo({
					url:'/pages/article/detail?type='+this.active+'&id='+id
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
		},
		created() {
			this.init();
		},
	}
</script>

<style lang="scss" scoped>
	.give {
		.warp {
			padding-top: 170rpx;
			padding-bottom: 60rpx;			
		}
		.give-header {
			margin-top: 32rpx;
			margin-bottom: 24rpx;
			display: flex;
			justify-content: space-between;
			padding: 32rpx 40rpx;
			background: #191932;
			border-radius: 8rpx;
			border: 1rpx solid rgba(255, 255, 255, 0.3);
			.info {
				display: flex;
				flex-direction: column;
				color: #F585FF;
				.label {
					font-size: 26rpx;
					line-height: 36rpx;
				}
				.count {
					margin-top: 8rpx;
					font-size: 48rpx;
					line-height: 56rpx;
				}
			}
		}
		
		.give-item {
			margin-bottom: 24rpx;
			background: #191932;
			border-radius: 8rpx;
			border: 1rpx solid rgba(255, 255, 255, 0.3);
			.give-item__header {
				padding: 32rpx 32rpx 20rpx;
				display: flex;
				justify-content: space-between;
				align-items: center;
				border-bottom: 1rpx solid rgba(255, 255, 255, 0.3);
				.count {
					font-size: 32rpx;
					line-height: 48rpx;
					color: #F585FF;
				}
				.date {
					display: flex;
					align-items: center;
				}
			}
			.give-item__body {
				padding: 20rpx 32rpx 32rpx;
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
		}
	}
</style>
