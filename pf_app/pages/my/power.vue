<template>
	<u-list class="power container" @scrolltolower="getListHandler">
		<u-navbar :title="$t('my.power')" bgColor="#14142B" leftIconColor="#F585FF" autoBack/>
		
		<view class="my-header">
			<u-avatar class="advar" :src="`/static/img/user/icon${userInfo?userInfo.avatar_code: 0}.png`" size="60"></u-avatar>
			<view class="info" v-if="token">
				<view class="name">
					{{$t('my.Username')}}：{{userInfo.nickname}}
					<image class="icon-img" v-if="userInfo.badge" v-for="item in userInfo.badge" :key="item.id" :src="baseUrl+item.image" mode="aspectFit"></image>
				</view>
				<view class="number">{{$t('my.fansId')}}：{{userInfo.user_number}}</view>
				<view class="number">{{$t('my.Invitation_code')}}：{{userInfo.invite_code}}</view>
			</view>
			<view class="info" v-else>
				<navigator class="name" url="/pages/my/login">{{$t('login')}}</navigator>
			</view>
			<view class="flz">
				<text style="color: #F585FF;font-weight: bold;font-size: 38rpx;">{{count}}</text>
				<text>{{$t("粉力值")}}</text>
			</view>
		</view>
		
		
		<view class="content tabs">
			{{$t("粉力值排行榜")}}
		</view>
		<view class="content list-wapper">
			<view class="list-item" v-for="(item,index) in list2" :key="index">
				<text style="width: 5em;">{{index+1}}</text>
				<text class="list-nickname">{{item.username}}</text>
				<text>{{item.powder_value}}</text>
			</view>
		</view>
		
		<view class="content list-wapper2">
			<view style="font-size: 30rpx;margin-bottom: 20rpx;">{{$t('粉力激励机制')}}</view>
			<view style="color: #CCC;font-size: 24rpx;">
				{{$t('1、评论5个、点评5个、每日签到5个。')}}<br>
				{{$t('2、pifans补填邀请码功能，没有使用邀请码注册的用户， 可以在个人资料中补填邀请码，凡是填完邀请码，则自动 补发粉力给两人。')}}
			</view>
		</view>
		
		<!-- <view class="warp">
			<sy-tabs type="custom" size="large" :data="config"></sy-tabs>
			<view class="power-header">
				<view class="info">
					<view class="lable">粉力值（FansPower）</view>
					<view class="count">{{count}}</view>
				</view>
				<u-image src="/static/img/my/power-banner.png" width="160rpx" height="120rpx" mode=""></u-image>
			</view>
			<view class="power-list" v-if="list.length !== 0">
				<view class="power-item" v-for="(item,index) in list" :key="index">
					<view class="power-item__header">
						<view class="count">+{{item.powder_value}}{{$t('my.fanPwerVal')}}</view>
						<view class="date">
							<u-icon style="margin-right: 8rpx;" name="clock" size="26rpx" color="rgba(255, 255, 255, 0.8)"/>
							<u-text class="date"  :text="item.createtime" color="rgba(255, 255, 255, 0.8)" size="28rpx" lineHeight="40rpx"></u-text>
						</view>
					</view>
					<view class="power-item__body">{{$t('my.power_msg')}}：{{item.user_number}}，{{$t('my.power_msg1')}}{{' '+ item.powder_value+' '}}{{$t('my.fanPwerVal')}}。{{$t('my.power_msg2')}}：{{' '+ item.before_value+' '}}{{$t('my.fanPwerVal')}}，{{$t('my.power_msg3')}}：{{' '+ item.after_value+' '}}{{$t('my.fanPwerVal')}}.</view>
				</view>
				<sy-loadmore v-if="status !== 'none'" :status="status" />
			</view>
			<u-empty v-else style="margin-top: 100rpx;" :text="$t('empty')"/>			
		</view> -->

	</u-list>
</template>

<script>
	import { mapState, mapMutations } from 'vuex';
	import { getPowerApi } from '@/api/user.js'
	import { user_ranking } from '@/api/index.js'
	
	export default {
		data() {
			return {
				config: [
					{ name: 'my.power' }
				],
				status: 'none',
				count: 0,
				list: [],
				list2: [],
				page: 0
			}
		},
		methods: {
			async user_ranking(){
				let res = await user_ranking()
				if(res.code==1){
					this.list2 = res.data
				}
			},
			getListHandler() {
				let { list, page } = this;
				(this.status !== 'nomore') && getPowerApi({ page: page + 1, listRows: 10 }).then(({code, data:{ data, total_powder_value }, msg}) => {
					if (code == 1) {
						if (data.length == 0) {
							this.status = 'nomore';
						} else {
							this.page = page + 1;
							this.list = list.concat(data);
						}
						this.count = total_powder_value;
					} else {
						uni.$u.toast(msg);
					}
				})
			}
		},
		computed: {
			...mapState({
				token: state => state.token,
				userInfo: state => state.userInfo
			})
		},
		created() {
			this.getListHandler();
			this.user_ranking()
		},
	}
</script>

<style lang="scss" scoped>
	.content{
		margin: 30rpx;
	}
	.tabs{
		background-image: linear-gradient(to right,#E323FF,#9041FF);
		padding: 30rpx;
		border-radius: 10rpx;
		color: #FFF;
	}
	.list-wapper{
		background-color: #14142b;
		border-radius: 0px 0px 10rpx 10rpx;
		margin-top: -30rpx;
	}
	.list-wapper2{
		background-color: #14142b;
		border-radius: 0px 0px 10rpx 10rpx;
		color: #FFF;
		padding: 30rpx;
		margin-top: -10rpx;
	}
	.list-nickname{
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		flex: 1;
	}
	.list-item{
		display: flex;
		align-items: center;
		padding: 10rpx 30rpx;
		color: #FFF;
	}
	.my-header {
		display: flex;
		align-items: center;
		padding: 30rpx;
		.advar {
			margin-right: 24rpx;
			width: 128rpx;
			height: 128rpx;
			border-radius: 50%;
			border: 1px solid #FFFFFF;
		}
		.info {
			flex: 1 1 0;
			display: flex;
			flex-direction: column;
			justify-content: space-around;
			.name {
				display: flex;
				font-size: 36rpx;
				align-items: center;
				line-height: 50rpx;
				color: rgba(255, 255, 255, 1);
			}
			.number {
				font-size: 28rpx;
				line-height: 36rpx;
				color: rgba(255, 255, 255, 0.6);
			}
		}
		.set {
			margin-left: auto;
		}
	}
	.flz{
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		color: #FFF;
	}
	
	.power {
		.warp {
			padding-top: 170rpx;
			padding-bottom: 60rpx;
		}
		.power-header {
			margin-top: 32rpx;
			margin-bottom: 24rpx;
			display: flex;
			justify-content: space-between;
			padding: 24rpx 40rpx 40rpx;
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
		
		.power-item {
			margin-bottom: 24rpx;
			padding: 32rpx;
			background: #191932;
			border-radius: 8rpx;
			border: 1rpx solid rgba(255, 255, 255, 0.6);
			.power-item__header {
				display: flex;
				justify-content: space-between;
				align-items: center;
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
			.power-item__body {
				margin-top: 16rpx;
				font-size: 28rpx;
				line-height: 40rpx;
				color: rgba(255, 255, 255, 0.8);
			}
		}
	}
</style>
