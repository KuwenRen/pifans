<template>
	<u-list class="fans container" @scrolltolower="getListHandler">
		<u-navbar :title="$t('my.fans')" bgColor="#14142B" leftIconColor="#F585FF" autoBack fixed placeholder />
		<view class="warp">
			<sy-tabs type="custom" size="large" :data="config"></sy-tabs>
			<view v-if="list.length != 0" class="fans-list">
				<view class="fans-item" v-for="(item,index) in list" :key="index">
					<u-avatar class="fans-item__avatar" size="64" :src="`/static/img/user/icon${item.avatar_code}.png`" mode=""></u-avatar>
					<view class="fans-item__body">
						<view class="title">{{item.username}}</view>
						<view class="number">{{item.user_number}}</view>
						<view class="item">{{$t('form.email')}}: {{isNaN(item.account)?item.other_account:item.account}}</view>
						<view class="item">{{$t('form.phone')}}: {{isNaN(item.account)?item.account:item.other_account}}</view>
						<view class="item">
							<u-icon style="margin-right: 8rpx;" name="clock" size="26rpx" color="rgba(255, 255, 255, 0.8)"/>
							{{item.createtime}}
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
	import { getFansApi } from '@/api/user.js'
	export default {
		data() {
			return {
				config: [
					{ name: 'my.fans' }
				],
				status: 'none',
				list: [],
				page: 0
			}
		},
		methods: {
			getListHandler() {
				let { list, page } = this;
				(this.status !== 'nomore') && getFansApi({ page: page + 1, listRows: 10 }).then(({code, data:{ data, total_powder_value }, msg}) => {
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
			}
		},
		created() {
			this.getListHandler();
		},
	}
</script>

<style lang="scss" scoped>
	.fans {
		.warp {
			padding-top: 170rpx;
			padding-bottom: 60rpx;			
		}
		.fans-list {
			margin-top: 32rpx;
			.fans-item {
				display: flex;
				margin-bottom: 24rpx;
				padding: 32rpx;
				background: #191932;
				border-radius: 8rpx;
				border: 1rpx solid rgba(255, 255, 255, 0.3);
				.fans-item__avatar {
					margin-right: 32rpx;
					flex: none;
					display: flex;
					justify-content: space-between;
				}
				.fans-item__body {
					flex: 1 1 0;
					font-size: 28rpx;
					line-height: 40rpx;
					color: rgba(255, 255, 255, 0.8);
					.title {
						font-size: 32rpx;
						line-height: 44rpx;
						color: #FFFFFF;
					}
					.number {
						margin-top: 8rpx;
						font-size: 24rpx;
						line-height: 36rpx;
						color: rgba(255,255,255, 0.6);
					}
					.item {
						margin-top: 8rpx;
						display: flex;
						align-items: center;
						font-size: 28rpx;
						line-height: 40rpx;
						color: rgba(255,255,255, 0.8);
					}
				}
			}
		}
	}
</style>
