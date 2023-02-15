<!-- 
@author x 
@title 聊天室管理权限更换
 -->
<template>
	<view class="chat-power-change">
		<u-header title=""></u-header>
		<!-- 搜索 -->
		<view class="search">
			<view class="box">
				<u-search color="#ffffff" bgColor="#323256" searchIconColor="#C4C4C4" borderColor="#404065"
					placeholderColor="rgba(255,255,255,0.8)" :placeholder="$t('searchPlace')" height="80" shape="square"
					:showAction="searchWords!=''" v-model="searchWords" border-color @custom="search"></u-search>
			</view>
		</view>
		<u-list @scrolltolower="scrolltolower">
			<view class="list">
				<view v-for="(item,key) in list" :key="key" class="item" @click="confirmChangeMaster(item)">
					<image class="avatar" :src="baseUrl+item.avatar" mode="aspectFill"></image>
					<text class="t">{{item.nickname}}</text>
				</view>
			</view>
			<view class="tips">
				<text>没有更多了~</text>
			</view>
		</u-list>
	</view>
</template>

<script>
	import {
		mapState
	} from 'vuex';
	import {
		baseUrl
	} from '@/api/http.js';
	import {
		getMembers,
		changeMaster
	} from '@/api/chat'
	export default {
		data() {
			return {
				list: [],
				searchWords: '',
				roomId: 0,
				page: 1,
				limit: 10,
				baseUrl
			}
		},
		onLoad({
			roomId
		}) {
			this.roomId = roomId;
			this.getMembers();
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
			confirmChangeMaster(item) {
				let title = '消息提示';
				let content = '是否确认切换当前用户为群主？';
				let confirmText = '确定'
				let cancelText = '取消'
				if (this.localeIndex == 2) {
					title = 'Tips'
					content = 'Are you sure change this user to be master？'
					confirmText = 'Yes'
					cancelText = 'No'
				}
				let that = this
				uni.showModal({
					content: content,
					title: title,
					confirmColor: '#6ab04c',
					cancelText: cancelText,
					confirmText: confirmText,
					success(res) {
						if (res.confirm) {
							that.changeMaster(item.user_id, item.group_id)
						}
					}
				})
			},
			search() {
				this.page = 1;
				this.getMembers();
			},
			/* 滚动到底部 */
			scrolltolower() {
				this.page++;
				this.getMembers();
			},
			changeMaster(user_id, group_id) {
				changeMaster({
					user_id: user_id,
					group_id: group_id
				}).then(res => {
					let title = '操作成功'
					if (this.localeIndex == 2) {
						title = 'Success'
					}
					if (res.code == 1) {
						uni.showToast({
							title: title,
							mask: true,
							duration: 800
						})
					}
				})
			},
			getMembers() {
				getMembers({
					page: this.page,
					limit: this.limit,
					chat_room_id: this.roomId,
					keyword: this.searchWords
				}).then(res => {
					if (this.page == 1) {
						this.list = [];
					}
					if (res.data.list.length <= 0) {
						return
					}
					if (this.page == 1) {
						this.list = res.data.list;
					} else {
						this.list = this.list.concat(res.data.list);
					}
				})
			},
		}
	}
</script>

<style lang="scss" scoped>
	.chat-power-change {
		width: 100%;
		min-height: 100vh;
		background-color: $main-bg-color;

		.list {
			padding: 0 30rpx;
			overflow: hidden;
		}

		.search {
			position: sticky;
			top: 0;
			z-index: 9;
			padding: 30rpx;
			background-color: $main-bg-color;

			.box {
				width: 100%;
				height: 80rpx;
				border-width: 1px;
				border-style: solid;
				border-color: rgba(255, 255, 255, 0.6);
				border-radius: 4px;
			}
		}

		.item {
			margin-bottom: 26rpx;
			padding: 12rpx;
			display: flex;
			align-items: center;
			background-color: #14142B;
			border-radius: 2px;

			.avatar {
				margin-right: 16rpx;
				width: 80rpx;
				height: 80rpx;
				flex-shrink: 0;
				border-radius: 8rpx;
			}

			.t {
				color: #fff;
				font-size: 30rpx;
			}
		}
	}
</style>
